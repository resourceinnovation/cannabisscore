<?php
/**
  * ScoreCalcs is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which collectively create PowerScore
  * calculations, first calculating raw metrics, then relative efficiency rankings.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreFormsCustom;

class ScoreCalcs extends ScoreFormsCustom
{
    protected function calcCurrSubScores()
    {
        $this->loadTotFlwrSqFt();
        if (isset($this->sessData->dataSets["powerscore"]) 
            && isset($this->sessData->dataSets["powerscore"][0])
            && isset($this->sessData->dataSets["ps_areas"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];

            if (!isset($ps->ps_effic_facility) 
                || $GLOBALS["SL"]->REQ->has('refresh') 
                || $GLOBALS["SL"]->REQ->has('recalc')) {
                $this->chkPsType();
                $this->calcClearScores($ps);
                // Next, Recalculate Raw Efficiency Numbers
                $defFut = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future');
                if ($ps->ps_time_type == $defFut) {
                    $ps = $this->calcFutureYields();
                    $ps->save();
                    $this->sessData->dataSets["powerscore"][0] = $ps;
                } else {
                    $this->calcSimplScore($ps);
                }
                if ($this->hasRooms()) { // post-3.0
                    $this->calcEachRoom($ps); // sq ft and lighting Wh
                    $this->calcWaterScore($ps);
                    $this->calcHvacScore($ps);
                } else { // pre-3.0
                    $this->calcEachArea($ps);
                }
            }
        }
        return true;
    }

    // First, Determine Farm Type
    protected function chkPsType()
    {
        $ps = $this->sessData->dataSets["powerscore"][0];
        $prevFarmType = $ps->ps_characterize;
        $ps->ps_characterize = $this->frmTypIn; // default to Indoor
        list($flwrSun, $flwrDep, $flwrArt) = $this->chkFlwrTypes();
        if ($flwrSun) { // Uses the Sun during Flowering Stage
            if ($flwrDep || $flwrArt) {
                $ps->ps_characterize = $this->frmTypGrn;
            } else {
                $ps->ps_characterize = $this->frmTypOut;
            }
            /*
            $flwrOutdoor = $flwrGrnhse = false;
            if ($flwrArt || $flwrDep) {
                $flwrGrnhse = true;
            } elseif ($flwrSun == 1) {
                $flwrOutdoor = true;
            }
            $areaFlwrID = $this->getAreaFld('Flower', 'ps_area_id');
            $this->loadBldDefs($areaFlwrID);
            if (sizeof($this->v["areaFlwrBlds"]) > 0) {
                foreach ($this->v["areaFlwrBlds"] as $bld) {
                    if ($bld->ps_ar_bld_type == $this->v["bldTypOut"]) {
                        $flwrOutdoor = true;
                    } elseif ($bld->ps_ar_bld_type == $this->v["bldTypGrn"]) {
                        $flwrGrnhse = true;
                    }
                }
            }
            if ($flwrGrnhse) {
                $ps->ps_characterize = $this->frmTypGrn;
            } else {
                $ps->ps_characterize = $this->frmTypOut;
            }
            */
        }
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    // First, Determine Farm Type
    protected function chkFlwrTypes()
    {
        $flwrSun = $flwrDep = $flwrArt = false;

        if ($this->hasRooms()) { // post-3.0



        } else {
            $flwrSun = (intVal($this->getAreaFld('Flower', 'ps_area_lgt_sun')) == 1);
            $flwrDep = (intVal($this->getAreaFld('Flower', 'ps_area_lgt_dep')) == 1);
            $flwrArt = (intVal($this->getAreaFld('Flower', 'ps_area_lgt_artif')) == 1);
        }

        return [ $flwrSun, $flwrDep, $flwrArt ];
    }

    protected function calcClearScores(&$ps)
    {
        $ps->ps_effic_facility
            = $ps->ps_effic_production
            = $ps->ps_effic_hvac
            = $ps->ps_effic_lighting
            = $ps->ps_effic_carbon
            = $ps->ps_effic_water
            = $ps->ps_effic_waste
            = $ps->ps_lighting_error
            = $ps->ps_total_canopy_size 
            = $ps->ps_lighting_power_density 
            = $ps->ps_effic_hvac_orig
            = $ps->ps_effic_overall
            = $ps->ps_effic_over_similar
            = 0;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $ps->ps_effic_lighting_status = $this->statusComplete;
        }
        $ps->ps_flower_canopy_size = $this->v["totFlwrSqFt"];
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    protected function loadBldDefs($areaFlwrID)
    {
        $this->v["bldTypOut"] = $GLOBALS["SL"]->def->getID(
            'PowerScore Building Types', 
            'Outdoor'
        );
        $this->v["bldTypGrn"] = $GLOBALS["SL"]->def->getID(
            'PowerScore Building Types', 
            'Greenhouse'
        );
        $this->v["areaFlwrBlds"] = $this->sessData->getChildRows(
            'ps_areas', 
            $areaFlwrID, 
            'ps_areas_blds'
        );
        return true;
    }

    protected function calcSimplScore(&$ps)
    {
        if (isset($ps->ps_kwh) 
            && intVal($ps->ps_kwh) > 0
            && isset($ps->ps_grams) 
            && intVal($ps->ps_grams) > 0) {
            $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh);
            $ps->ps_effic_production = $ps->ps_grams/$btus;
        }
        if (isset($this->v["totFlwrSqFt"]) 
            && intVal($this->v["totFlwrSqFt"]) > 0) {
            if (isset($ps->ps_kwh) && intVal($ps->ps_kwh) > 0) {
                $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh);
                $ps->ps_effic_facility = $btus/$this->v["totFlwrSqFt"];
            }
            if (isset($row->ps_green_waste_lbs) 
                && intVal($row->ps_green_waste_lbs) > 0) {
                $ps->ps_effic_waste = $ps->ps_green_waste_lbs/$this->v["totFlwrSqFt"];
            }
        }
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    // Run all the room-based calculations related to scoring
    protected function calcEachRoom(&$ps)
    {
        $sqft = $watts = $gal = [];
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
            if (isset($room->ps_room_canopy_sqft)) {
                $ps->ps_total_canopy_size += intVal($room->ps_room_canopy_sqft);
            }
        }
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $roomInd => $room) {
            $room->ps_room_total_light_watts 
                = $room->ps_room_lighting_effic 
                = $room->ps_room_lgt_fix_size_calced = 0;
            $this->calcEachRoomAddAllLgtW($room, $roomInd);
            if ($room->ps_room_total_light_watts > 0
                && isset($room->ps_room_canopy_sqft) 
                && $room->ps_room_canopy_sqft > 0) {
                $room->ps_room_lpd = $room->ps_room_total_light_watts
                    /$room->ps_room_canopy_sqft;
                $ps->ps_lighting_power_density += $room->ps_room_total_light_watts;
            }
            $ps->ps_effic_lighting += $room->ps_room_lighting_effic/1000; // Overall in kWh
            $this->calcEachRoomAddAllHvac($ps, $room, $roomInd);
            $room->save();
            $this->sessData->dataSets["ps_growing_rooms"][$roomInd] = $room;
        }
        $ps->ps_lighting_power_density /= $ps->ps_total_canopy_size;
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    protected function calcEachRoomAddAllLgtW(&$room, $roomInd)
    {
        $hours = $this->getRoomIndLightHours($roomInd);
        if (!isset($room->ps_room_lgt_artif) 
            || intVal($room->ps_room_lgt_artif) == 0) {
            $room->ps_room_total_light_watts = 0.0000001;
            $room->ps_room_lighting_effic += 0.0000001;
        } elseif (isset($this->sessData->dataSets["ps_light_types"])
            && sizeof($this->sessData->dataSets["ps_light_types"]) > 0) {
            $lgtCnt = 0;
            foreach ($this->sessData->dataSets["ps_light_types"] as $lgt) {
                if ($lgt->ps_lg_typ_room_id == $room->ps_room_id
                    && isset($lgt->ps_lg_typ_count)
                    && intVal($lgt->ps_lg_typ_count) > 0) {
                    $lgtCnt += intVal($lgt->ps_lg_typ_count);
                    if (isset($lgt->ps_lg_typ_wattage) 
                        && intVal($lgt->ps_lg_typ_wattage) > 0) {
                        $this->calcEachRoomAddLgtW($room, $lgt, $hours);
                    }
                }
            }
            $room->ps_room_lgt_fix_size_calced = intVal($room->ps_room_canopy_sqft);
            $room->ps_room_lgt_fix_size_calced /= $lgtCnt;
        }
        return true;
    }

    protected function calcEachRoomAddLgtW(&$room, $lgt, $defaultHours = 18)
    {
        $room->ps_room_total_light_watts += $lgt->ps_lg_typ_wattage*$lgt->ps_lg_typ_count;
        $hours = $defaultHours;
        if (isset($lgt->ps_lg_typ_hours) && intVal($lgt->ps_lg_typ_hours) > 0) {
            $hours = $lgt->ps_lg_typ_hours; // grower-entered data is preferred
        }
        $room->ps_room_lighting_effic += $hours*$room->ps_room_total_light_watts;
        return true;
    }

    protected function calcEachRoomAddAllHvac(&$ps, &$room, $roomInd)
    {
        if (isset($this->sessData->dataSets["ps_link_hvac_room"])
            && sizeof($this->sessData->dataSets["ps_link_hvac_room"]) > 0) {
            foreach ($this->sessData->dataSets["ps_link_hvac_room"] as $hvac) {
                if ($room->ps_room_id == $hvac->ps_lnk_hv_rm_room_id) {
                    $room->ps_room_hvac_type = $hvac->ps_lnk_hv_rm_hvac;
                    $room->ps_room_hvac_effic = $GLOBALS["CUST"]
                        ->getHvacEffic($room->ps_room_hvac_type);
                    if (isset($room->ps_room_canopy_sqft)
                        && intVal($room->ps_room_canopy_sqft) > 0
                        && isset($hvac->ps_lnk_hv_rm_hvac) 
                        && intVal($hvac->ps_lnk_hv_rm_hvac) > 0
                        && $ps->ps_total_canopy_size > 0) {
                        $sqftWeight = $room->ps_room_canopy_sqft
                            /$ps->ps_total_canopy_size;
                        $ps->ps_effic_hvac_orig += $sqftWeight*$room->ps_room_hvac_effic;
                    }
                }
            }
        }
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    protected function calcWaterScore(&$ps)
    {
        if (!isset($ps->ps_total_canopy_size) || $ps->ps_total_canopy_size <= 0) {
            return false;
        }
        $ps->ps_effic_water = 0;
        if (isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && isset($area->ps_area_gallons) 
                    && intVal($area->ps_area_gallons) > 0) {
                    $ps->ps_effic_water += $area->ps_area_gallons;
                }
            }
        }
        if ($this->v["totFlwrSqFt"] > 0) {
            $ps->ps_effic_water /= $this->v["totFlwrSqFt"];
        }
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    protected function calcHvacScore(&$ps)
    {
        $ps->ps_effic_hvac = $ps->ps_effic_hvac_orig;
        if (!isset($ps->ps_total_canopy_size) 
            || $ps->ps_total_canopy_size <= 0
            || !isset($ps->ps_kwh)
            || $ps->ps_kwh <= 0) {
            return false;
        }
        // TBD
        // $facility = $GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh);
        // $ps->ps_effic_hvac = $facility-$ps->ps_effic_lighting;
        return true;
    }

    // Run all the area-based calculations related to scoring
    protected function calcEachArea(&$ps)
    {
        $ps->ps_total_canopy_size = $ps->ps_lighting_power_density = 0;
        $sqft = $watts = $wattsHvac = $gal = [];
        if (sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                $area->ps_area_lighting_effic = $area->ps_area_lpd = 0;
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $ps->ps_total_canopy_size += $area->ps_area_size;
                        $sqft[$typ] = $area->ps_area_size;
                        $watts[$typ] = $wattsHvac[$typ] = $gal[$typ] = $fixCnt = 0;
                        if (!isset($area->ps_area_lgt_artif) 
                            || intVal($area->ps_area_lgt_artif) == 0) {
                            $watts[$typ] = 0.0000001;
                        } else {
                            $this->chkLgtWatts($area, $typ, $watts, $fixCnt);
                            if ($watts[$typ] <= 0 && !in_array($typ, ['Mother', 'Clone'])) {
                                // give Mothers & Clones a pass for now
                                $this->addLightingError($typ);
                            }
                        }
                        $area->ps_area_total_light_watts = $watts[$typ];
                        $area->ps_area_sq_ft_per_fix2 = 0;
                        if ($fixCnt > 0) {
                            $area->ps_area_sq_ft_per_fix2 = $sqft[$typ]/$fixCnt;
                        }
                        $area->save();
                        $this->sessData->dataSets["ps_areas"][$a] = $area;
                    }
                }
            }
            if (isset($watts["Mother"]) && intVal($watts["Mother"]) > 0) {
                $watts["Clone"] += $watts["Mother"];
                $sqft["Clone"]  += $sqft["Mother"];
                $watts["Mother"] = $sqft["Mother"] = 0;
            }
            $hasLights = 0;
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $hours = (($typ == 'Flower') ? 12 : 18);
                        $area->ps_area_calc_size = $sqft[$typ];
                        $area->ps_area_calc_watts = $watts[$typ];
                        $area->ps_area_lighting_effic = 0;
                        if ($watts[$typ] > 0) { // && $area->ps_area_lgt_artif == 1
                            $hasLights++;
                            if ($sqft[$typ] > 0) {
                                $area->ps_area_lpd = $watts[$typ]/$sqft[$typ];
                            }
                            $ps->ps_lighting_power_density += $watts[$typ];
                            if (isset($lgt->ps_lg_typ_hours) 
                                && intVal($lgt->ps_lg_typ_hours) > 0) {
                                // grower-entered data is preferred
                                $hours = $lgt->ps_lg_typ_hours; 
                            }
                            $area->ps_area_lighting_effic = $watts[$typ]*$hours;
                            $ps->ps_effic_lighting += $area->ps_area_lighting_effic/1000; // Overall in kWh
                        }
                        if (isset($ps->ps_total_canopy_size)
                            && $ps->ps_total_canopy_size > 0) {
                            $sqftWeight = $sqft[$typ]/$ps->ps_total_canopy_size;
                            if (intVal($sqft[$typ]) > 0) {
                                if (isset($area->ps_area_hvac_type) 
                                    && intVal($area->ps_area_hvac_type) > 0) {
                                    $area->ps_area_hvac_effic = $GLOBALS["CUST"]
                                        ->getHvacEffic($area->ps_area_hvac_type);
                                    $ps->ps_effic_hvac_orig += $sqftWeight*$area->ps_area_hvac_effic;
                                }
                            }
                        }
                        if (isset($area->ps_area_gallons) && intVal($area->ps_area_gallons) > 0) {
                            $ps->ps_effic_water += $area->ps_area_gallons;
                        }
                        $area->save();
                        $this->sessData->dataSets["ps_areas"][$a] = $area;
                    }
                }
            }
            if ($hasLights == 0) {
                $ps->ps_effic_lighting = 0.00000001; // a real calculation, not zero or null
            } elseif ($ps->ps_total_canopy_size > 0) {
                $ps->ps_lighting_power_density /= $ps->ps_total_canopy_size;
            }
            $ps->ps_effic_hvac = $ps->ps_effic_hvac_orig;
            if ($this->v["totFlwrSqFt"] > 0) {
                $ps->ps_effic_water /= $this->v["totFlwrSqFt"];
            }
        }
        $ps->save();
        $this->sessData->dataSets["powerscore"][0] = $ps;
        return true;
    }

    protected function chkLgtWatts($area, $typ, &$watts = 0, &$fixCnt = 0)
    {
        if (isset($this->sessData->dataSets["ps_light_types"])) {
            $lgts = $this->sessData->dataSets["ps_light_types"];
            if (sizeof($lgts) > 0) {
                foreach ($lgts as $lgt) {
                    if ($lgt->ps_lg_typ_area_id == $area->getKey()
                        && isset($lgt->ps_lg_typ_count) 
                        && intVal($lgt->ps_lg_typ_count) > 0 
                        && isset($lgt->ps_lg_typ_wattage) 
                        && intVal($lgt->ps_lg_typ_wattage) > 0) {
                        $watts[$typ] += $lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage;
                        $fixCnt += $lgt->ps_lg_typ_count;
                    }
                }
            }
        }
        return true;
    }
    
    protected function addLightingError($typ)
    {
        $this->sessData->dataSets["powerscore"][0]->ps_lighting_error++;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $this->sessData->dataSets["powerscore"][0]
                ->ps_effic_lighting_status = $this->statusArchive;
        }
        return true;
    }
    
}