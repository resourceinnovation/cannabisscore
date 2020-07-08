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

            if (!isset($ps->ps_effic_facility) 
                || $GLOBALS["SL"]->REQ->has('refresh') 
                || $GLOBALS["SL"]->REQ->has('recalc')) {
                $this->calcClearScores();
                $this->chkPsType();
                $this->calcConvertGramsDry();
                // Next, Recalculate Raw Efficiency Numbers
                $defSet = 'PowerScore Submission Type';
                $defFut = $GLOBALS["SL"]->def->getID($defSet, 'Future');
                if ($this->sessData->dataSets["powerscore"][0]->ps_time_type == $defFut) {
                    $this->calcFutureYields($this->sessData->dataSets["powerscore"][0]);
                } else {
                    $this->calcSimplScore();
                }
                $this->calcWaterScore();
                if ($this->hasRooms()) { // post-3.0
                    $this->calcEachRoom(); // sq ft and lighting Wh
                    $this->calcHvacScore();
                } else { // pre-3.0
                    $this->calcEachArea();
                }
            }
        }
//echo 'ps: <pre>'; print_r($this->sessData->dataSets["powerscore"][0]); echo '</pre>'; exit;
        return true;
    }

    // First, Determine Farm Type
    protected function chkPsType()
    {
        $tbl = 'powerscore';
        $prevFarmType = $this->sessData->dataSets[$tbl][0]->ps_characterize;
        // default to Indoor
        $this->sessData->dataSets[$tbl][0]->ps_characterize = $this->frmTypIn;
        $found = false;
        $types = [ 'Flower', 'Veg', 'Clone', 'Mother' ];
        foreach ($types as $type) {
            if (!$found) {
                $has = intVal($this->getAreaFld($type, 'ps_area_has_stage'));
                if ($has == 1) {
                    list($sun, $dep, $art, $grn) = $this->chkFlwrTypes($type);
                    if ($sun) { // Uses the Sun during Flowering Stage
                        if ($dep || $art || $grn) {
                            $this->sessData->dataSets[$tbl][0]->ps_characterize 
                                = $this->frmTypGrn;
                        } else {
                            $this->sessData->dataSets[$tbl][0]->ps_characterize 
                                = $this->frmTypOut;
                        }
                    }
                    $found = true;
                }
            }
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    // First, Determine Farm Type
    protected function chkFlwrTypes($type = 'Flower')
    {
        $flwrSun = $flwrDep = $flwrArt = $flwrGrn = false;
        if ($this->hasRooms()) { // post-3.0
            if (isset($this->v["areaTypes"][$type])
                && isset($this->sessData->dataSets["ps_areas"])
                && sizeof($this->sessData->dataSets["ps_areas"]) > 0
                && isset($this->sessData->dataSets["ps_link_room_area"])
                && sizeof($this->sessData->dataSets["ps_link_room_area"]) > 0
                && isset($this->sessData->dataSets["ps_growing_rooms"])
                && sizeof($this->sessData->dataSets["ps_growing_rooms"]) > 0) {
                foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                    if (isset($area->ps_area_type)
                        && $area->ps_area_type == $this->v["areaTypes"][$type]) {
                        foreach ($this->sessData->dataSets["ps_link_room_area"] as $lnk) {
                            if ($lnk->ps_lnk_rm_ar_area_id == $area->getKey()) {
                                foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
                                    if ($lnk->ps_lnk_rm_ar_room_id == $room->getKey()) {
                                        if ((isset($room->ps_room_lgt_sun)
                                                && intVal($room->ps_room_lgt_sun) == 1)
                                            || (isset($room->ps_room_farm_type)
                                                && intVal($room->ps_room_farm_type) 
                                                    == $this->frmTypGrn)) {
                                            $flwrSun = true;
                                        }
                                        if (isset($room->ps_room_lgt_dep)
                                            && intVal($room->ps_room_lgt_dep) == 1) {
                                            $flwrDep = true;
                                        }
                                        if (isset($room->ps_room_lgt_artif)
                                            && intVal($room->ps_room_lgt_artif) == 1) {
                                            $flwrArt = true;
                                        }
                                        if (isset($room->ps_room_farm_type)
                                            && intVal($room->ps_room_farm_type) 
                                                == $this->frmTypGrn) {
                                            $flwrGrn = true;
                                            $flwrSun = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $flwrSun = (intVal($this->getAreaFld($type, 'ps_area_lgt_sun')) == 1);
            $flwrDep = (intVal($this->getAreaFld($type, 'ps_area_lgt_dep')) == 1);
            $flwrArt = (intVal($this->getAreaFld($type, 'ps_area_lgt_artif')) == 1);
        }
        return [ $flwrSun, $flwrDep, $flwrArt, $flwrGrn ];
    }

    protected function calcClearScores()
    {
        $tbl = 'powerscore';
        $this->sessData->dataSets[$tbl][0]->ps_effic_facility
            = $this->sessData->dataSets[$tbl][0]->ps_effic_production
            = $this->sessData->dataSets[$tbl][0]->ps_effic_hvac
            = $this->sessData->dataSets[$tbl][0]->ps_effic_lighting
            = $this->sessData->dataSets[$tbl][0]->ps_effic_carbon
            = $this->sessData->dataSets[$tbl][0]->ps_effic_water
            = $this->sessData->dataSets[$tbl][0]->ps_effic_waste
            = $this->sessData->dataSets[$tbl][0]->ps_lighting_error
            = $this->sessData->dataSets[$tbl][0]->ps_total_canopy_size 
            = $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density 
            = $this->sessData->dataSets[$tbl][0]->ps_effic_hvac_orig
            = $this->sessData->dataSets[$tbl][0]->ps_effic_overall
            = $this->sessData->dataSets[$tbl][0]->ps_effic_over_similar
            = 0;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $this->sessData->dataSets[$tbl][0]->ps_effic_lighting_status 
                = $this->statusComplete;
        }
        $this->sessData->dataSets[$tbl][0]->ps_flower_canopy_size 
            = $this->v["totFlwrSqFt"];
        $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc 
            = $this->sessData->dataSets[$tbl][0]->ps_kwh;
        if (isset($this->sessData->dataSets[$tbl][0]->com_ma_include_renewables)
            && intVal($this->sessData->dataSets[$tbl][0]->com_ma_include_renewables) == 0) {
            if (isset($this->sessData->dataSets["ps_monthly"])
                && sizeof($this->sessData->dataSets["ps_monthly"]) > 0) {
                foreach ($this->sessData->dataSets["ps_monthly"] as $mon) {
                    $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc 
                        += $mon->ps_month_kwh_renewable;
                }
            }
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function loadBldDefs($areaFlwrID)
    {
        $defSet = 'PowerScore Building Types';
        $this->v["bldTypOut"] = $GLOBALS["SL"]->def->getID($defSet, 'Outdoor');
        $this->v["bldTypGrn"] = $GLOBALS["SL"]->def->getID($defSet, 'Greenhouse');
        $this->v["areaFlwrBlds"] 
            = $this->sessData->getChildRows('ps_areas', $areaFlwrID, 'ps_areas_blds');
        return true;
    }

    protected function calcSimplScore()
    {
        $tbl = 'powerscore';
        if (isset($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) 
            && intVal($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) > 0
            && isset($this->sessData->dataSets[$tbl][0]->ps_grams_dry) 
            && intVal($this->sessData->dataSets[$tbl][0]->ps_grams_dry) > 0) {
            $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu(
                $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc
            );
            $this->sessData->dataSets[$tbl][0]->ps_effic_production 
                = $this->sessData->dataSets[$tbl][0]->ps_grams_dry/$btus;
        }
        if (isset($this->v["totFlwrSqFt"]) 
            && intVal($this->v["totFlwrSqFt"]) > 0) {
            if (isset($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) 
                && intVal($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) > 0) {
                $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu(
                    $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc
                );
                $this->sessData->dataSets[$tbl][0]->ps_effic_facility 
                    = $btus/$this->v["totFlwrSqFt"];
            }
            if (isset($this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs) 
                && intVal($this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs) > 0) {
                $this->sessData->dataSets[$tbl][0]->ps_effic_waste 
                    = $this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs
                        /$this->v["totFlwrSqFt"];
            }
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    // Run all the room-based calculations related to scoring
    protected function calcEachRoom()
    {
        $sqft = $watts = $gal = [];
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
            if (isset($room->ps_room_canopy_sqft)) {
                $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size 
                    += intVal($room->ps_room_canopy_sqft);
            }
        }
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $roomInd => $room) {
            $room->ps_room_total_light_watts 
                = $room->ps_room_lighting_effic 
                = $room->ps_room_lgt_fix_size_calced = 0;
            $room = $this->calcEachRoomAddAllLgtW($room, $roomInd);
            if ($room->ps_room_total_light_watts > 0
                && isset($room->ps_room_canopy_sqft) 
                && $room->ps_room_canopy_sqft > 0) {
                $room->ps_room_lpd = $room->ps_room_total_light_watts
                    /$room->ps_room_canopy_sqft;
                $this->sessData->dataSets["powerscore"][0]->ps_lighting_power_density 
                    += $room->ps_room_total_light_watts;
            }
            $this->sessData->dataSets["powerscore"][0]->ps_effic_lighting 
                += $room->ps_room_lighting_effic/1000; // Overall in kWh
            $room->save();
            $this->sessData->dataSets["ps_growing_rooms"][$roomInd] = $room;
        }
        $this->sessData->dataSets["powerscore"][0]->ps_lighting_power_density 
            /= $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size;
        $this->sessData->dataSets["powerscore"][0]->save();
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
                        $room = $this->calcEachRoomAddLgtW($room, $lgt, $hours);
                    }
                }
            }
            $room->ps_room_lgt_fix_size_calced = intVal($room->ps_room_canopy_sqft);
            $room->ps_room_lgt_fix_size_calced /= $lgtCnt;
        }
        $room->save();
        return $room;
    }

    protected function calcEachRoomAddLgtW(&$room, $lgt, $defaultHours = 18)
    {
        $room->ps_room_total_light_watts += $lgt->ps_lg_typ_wattage*$lgt->ps_lg_typ_count;
        $hours = $defaultHours;
        if (isset($lgt->ps_lg_typ_hours) && intVal($lgt->ps_lg_typ_hours) > 0) {
            $hours = $lgt->ps_lg_typ_hours; // grower-entered data is preferred
        }
        $room->ps_room_lighting_effic += $hours*$room->ps_room_total_light_watts;
        $room->save();
        return $room;
    }

    protected function calcWaterScore()
    {
        $this->sessData->dataSets["powerscore"][0]->ps_effic_water 
            = $this->sessData->dataSets["powerscore"][0]->ps_tot_water 
            = 0;
        if (isset($this->sessData->dataSets["ps_monthly"])
            && sizeof($this->sessData->dataSets["ps_monthly"]) > 0) {
            foreach ($this->sessData->dataSets["ps_monthly"] as $mon) {
                $this->sessData->dataSets["powerscore"][0]->ps_tot_water 
                    += $mon->ps_month_water;
            }
        }
        if ($this->sessData->dataSets["powerscore"][0]->ps_tot_water == 0 
            && isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && isset($area->ps_area_gallons) 
                    && intVal($area->ps_area_gallons) > 0) {
                    $this->sessData->dataSets["powerscore"][0]->ps_tot_water 
                        += $area->ps_area_gallons;
                }
            }
        }
        if ($this->v["totFlwrSqFt"] > 0) {
            $this->sessData->dataSets["powerscore"][0]->ps_effic_water 
                = $this->sessData->dataSets["powerscore"][0]->ps_tot_water
                    /$this->v["totFlwrSqFt"];
        }
        $this->sessData->dataSets["powerscore"][0]->save();
        return true;
    }

    protected function calcHvacScore()
    {
        $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac 
            = $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac_orig;
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $roomInd => $room) {
            if (isset($this->sessData->dataSets["ps_link_hvac_room"])
                && sizeof($this->sessData->dataSets["ps_link_hvac_room"]) > 0) {
                foreach ($this->sessData->dataSets["ps_link_hvac_room"] as $hvac) {
                    if ($room->ps_room_id == $hvac->ps_lnk_hv_rm_room_id) {
                        $room->ps_room_hvac_type = $hvac->ps_lnk_hv_rm_hvac;
                        $room->ps_room_hvac_effic 
                            = $GLOBALS["CUST"]->getHvacEffic($room->ps_room_hvac_type);
                        if (isset($room->ps_room_canopy_sqft)
                            && intVal($room->ps_room_canopy_sqft) > 0
                            && isset($hvac->ps_lnk_hv_rm_hvac) 
                            && intVal($hvac->ps_lnk_hv_rm_hvac) > 0
                            && $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size > 0) {
                            $sqftWeight = $room->ps_room_canopy_sqft
                                /$this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size;
                            $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac 
                                += $sqftWeight*$room->ps_room_hvac_effic;
                        }
                        $room->save();
                        $this->sessData->dataSets["ps_growing_rooms"][$roomInd] = $room;
                    }
                }
            }
        }
        $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac_orig
            = $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac;
        $this->sessData->dataSets["powerscore"][0]->save();
        return true;
    }

    // Run all the area-based calculations related to scoring
    protected function calcEachArea()
    {
        $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size 
            = $this->sessData->dataSets["powerscore"][0]->ps_lighting_power_density 
            = 0;
        $sqft = $watts = $wattsHvac = $gal = [];
        if (sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                $area->ps_area_lighting_effic = $area->ps_area_lpd = 0;
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size 
                            += $area->ps_area_size;
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
                            $this->sessData->dataSets["powerscore"][0]->ps_lighting_power_density 
                                += $watts[$typ];
                            if (isset($lgt->ps_lg_typ_hours) 
                                && intVal($lgt->ps_lg_typ_hours) > 0) {
                                // grower-entered data is preferred
                                $hours = $lgt->ps_lg_typ_hours; 
                            }
                            $area->ps_area_lighting_effic = $watts[$typ]*$hours;
                            $this->sessData->dataSets["powerscore"][0]->ps_effic_lighting 
                                += $area->ps_area_lighting_effic/1000; // Overall in kWh
                        }
                        if (isset($this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size)
                            && $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size > 0) {
                            $sqftWeight = $sqft[$typ]
                                /$this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size;
                            if (intVal($sqft[$typ]) > 0) {
                                if (isset($area->ps_area_hvac_type) 
                                    && intVal($area->ps_area_hvac_type) > 0) {
                                    $area->ps_area_hvac_effic = $GLOBALS["CUST"]
                                        ->getHvacEffic($area->ps_area_hvac_type);
                                    $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac_orig 
                                        += $sqftWeight*$area->ps_area_hvac_effic;
                                }
                            }
                        }
                        $area->save();
                        $this->sessData->dataSets["ps_areas"][$a] = $area;
                    }
                }
            }
            if ($hasLights == 0) {
                $this->sessData->dataSets["powerscore"][0]->ps_effic_lighting = 0.00000001; 
                // a real calculation, not zero or null
            } elseif ($this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size > 0) {
                $this->sessData->dataSets["powerscore"][0]->ps_lighting_power_density 
                    /= $this->sessData->dataSets["powerscore"][0]->ps_total_canopy_size;
            }
            $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac 
                = $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac_orig;
        }
        $this->sessData->dataSets["powerscore"][0]->save();
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

    protected function calcConvertGramsDry($tbl = 'powerscore', $abbr = 'ps_')
    {
        $defSet = 'Flower Weight Methods';
        $defWet = $GLOBALS["SL"]->def->getID($defSet, 'Wet flower weight');
        $defFrozen = $GLOBALS["SL"]->def->getID($defSet, 'Fresh frozen weight');
        $flwrType = $abbr . 'flower_weight_type';
        $this->sessData->dataSets[$tbl][0]->{ $abbr . 'grams_dry' }
            = $this->sessData->dataSets[$tbl][0]->{ $abbr . 'grams' };
        if (isset($this->sessData->dataSets[$tbl][0]->{ $flwrType })) {
            if (intVal($this->sessData->dataSets[$tbl][0]->{ $flwrType }) == $defWet) {
                $this->sessData->dataSets[$tbl][0]->{ $abbr . 'grams_dry' } *= 0.2;
            } elseif (intVal($this->sessData->dataSets[$tbl][0]->{ $flwrType }) == $defFrozen) {
                $this->sessData->dataSets[$tbl][0]->{ $abbr . 'grams_dry' } *= 0.2;
            }
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function calcMaCompliance($nID)
    {
        $tbl = 'compliance_ma';
        if (isset($this->sessData->dataSets[$tbl])) {
            $com = $this->sessData->dataSets[$tbl][0];
            $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh 
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_diesel
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_propane
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_fuel_oil
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_water
                = $this->sessData->dataSets[$tbl][0]->com_ma_effic_production
                = 0;
            $this->calcConvertGramsDry('compliance_ma', 'com_ma_');

            if (isset($this->sessData->dataSets["compliance_ma_months"])
                && sizeof($this->sessData->dataSets["compliance_ma_months"]) > 0) {
                foreach ($this->sessData->dataSets["compliance_ma_months"] as $mon) {
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh
                        += $mon->com_ma_month_kwh;
                    if (isset($mon->com_ma_month_renew_kwh)) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew
                            += $mon->com_ma_month_renew_kwh;
                    }
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_diesel
                        += $mon->com_ma_month_diesel_gallons;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel
                        += $mon->com_ma_month_biofuel_wood_tons;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_fuel_oil
                        += $mon->com_ma_month_fuel_oil;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_propane
                        += $mon->com_ma_month_propane;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_water
                        += $mon->com_ma_month_water;
                    if ($this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                        < $mon->com_ma_month_kw) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                            = $mon->com_ma_month_kw;
                    }
                    if ($mon->com_ma_month_natural_gas_therms > 0) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                            += $mon->com_ma_month_natural_gas_therms;
                //} elseif ($mon->com_ma_month_natural_gas_gallons > 0) {
                //    $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                //        += $mon->com_ma_month_natural_gas_gallons;
                    }
                }
            }

            $defCcf = $GLOBALS["SL"]->def->getID('Natural Gas Units', 'CCF');
            if (isset($com->com_ma_unit_natural_gas)
                && $com->com_ma_unit_natural_gas == $defCcf) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas 
                    *= 0.0103412;
            // https://sciencing.com/convert-cubic-feet-therms-8374234.html
            }
            /*
            if (isset($this->sessData->dataSets[$tbl][0]->com_ma_unit_wood)
                && $this->sessData->dataSets[$tbl][0]->com_ma_unit_wood
                    == $GLOBALS["SL"]->def->getID('Biofuel Wood Units', 'Cords')) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel *= 2.6;
            // http://extension.msstate.edu/sites/default/files/publications/publications/P2244_web.pdf
            }
            */
            if (isset($com->com_ma_include_renewables)
                && intVal($com->com_ma_include_renewables) == 0) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh
                    += $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew;
            }
            $btu = 3.412*$this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh;
            if ($btu > 0) {
                $this->sessData->dataSets[$tbl][0]->com_ma_effic_production
                    = $this->sessData->dataSets[$tbl][0]->com_ma_grams_dry/$btu;
            }
//echo '<pre>'; print_r($this->sessData->dataSets[$tbl][0]); echo '</pre>'; exit;
            $this->sessData->dataSets[$tbl][0]->save();
        }
        return true;
    }

    protected function completeMaCompliance($nID)
    {
        if (isset($this->sessData->dataSets["compliance_ma"])) {
            $def = $GLOBALS["SL"]->def->getID('Compliance Status', 'Complete');
            $this->sessData->dataSets["compliance_ma"][0]->com_ma_status = $def;
            $this->sessData->dataSets["compliance_ma"][0]->save();
            $this->calcMaCompliance($nID);
        }
        return true;
    }
    
}