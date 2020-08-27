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
use App\Models\RIIPsWaterSources;
use App\Models\RIIPsGrowMedia;
use App\Models\RIIPsGrowMediaArea;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreFormsCustom;

class ScoreCalcs extends ScoreFormsCustom
{
    protected function calcCurrSubScores($force = false)
    {
        $this->loadTotFlwrSqFt();
        if (isset($this->sessData->dataSets["powerscore"]) 
            && isset($this->sessData->dataSets["powerscore"][0])
            && isset($this->sessData->dataSets["ps_areas"])) {

            if ($force
                || !isset($ps->ps_effic_facility) 
                || $GLOBALS["SL"]->REQ->has('refresh') 
                || $GLOBALS["SL"]->REQ->has('recalc')) {
                $this->calcClearScores();
                $this->calcMonthly();
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
                $this->calcNonElectric();
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
            = $this->sessData->dataSets[$tbl][0]->ps_effic_non_electric
            = $this->sessData->dataSets[$tbl][0]->ps_effic_production
            = $this->sessData->dataSets[$tbl][0]->ps_effic_prod_non
            = $this->sessData->dataSets[$tbl][0]->ps_effic_hvac
            = $this->sessData->dataSets[$tbl][0]->ps_effic_hvac_orig
            = $this->sessData->dataSets[$tbl][0]->ps_effic_lighting
            = $this->sessData->dataSets[$tbl][0]->ps_effic_carbon
            = $this->sessData->dataSets[$tbl][0]->ps_effic_water
            = $this->sessData->dataSets[$tbl][0]->ps_effic_waste
            = $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density 
            = $this->sessData->dataSets[$tbl][0]->ps_lpd_flower
            = $this->sessData->dataSets[$tbl][0]->ps_lpd_veg
            = $this->sessData->dataSets[$tbl][0]->ps_lpd_clone
            = $this->sessData->dataSets[$tbl][0]->ps_hlpd_ma
            = $this->sessData->dataSets[$tbl][0]->ps_lighting_error
            = $this->sessData->dataSets[$tbl][0]->ps_total_canopy_size 
            = $this->sessData->dataSets[$tbl][0]->ps_tot_btu_non_electric
            = $this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs
            = $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc
            = 0;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $this->sessData->dataSets[$tbl][0]->ps_effic_lighting_status 
                = $this->statusComplete;
        }
        $this->loadTotFlwrSqFt();
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function calcMonthly()
    {
        $tbl = 'powerscore';
        $flds = [
            'natural_gas', 
            'generator',
            'biofuel_wood',
            'propane',
            'fuel_oil',
            'water',
            'water_storage_source',
            'water_storage_recirc'
        ];
        foreach ($flds as $fld) {
            $this->sessData->dataSets[$tbl][0]->{ 'ps_tot_' . $fld } = 0;
        }
        $addWaste = false;
        if (isset($this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs)
            && intVal($this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs) > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs 
                = $this->sessData->dataSets[$tbl][0]->ps_green_waste_lbs;
            $addWaste = true;
        }
        
        $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc = $renew = 0;
        if (isset($this->sessData->dataSets["ps_monthly"])
            && sizeof($this->sessData->dataSets["ps_monthly"]) > 0) {
            foreach ($this->sessData->dataSets["ps_monthly"] as $mon) {
                if (isset($mon->ps_month_kwh1)) {
                    $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc 
                        += intVal($mon->ps_month_kwh1);
                }
                if (isset($mon->ps_month_kwh_renewable)
                    && intVal($mon->ps_month_kwh_renewable) > 0) {
                    $renew += $mon->ps_month_kwh_renewable;
                }
                if ($addWaste 
                    && isset($mon->ps_month_waste_lbs)
                    && intVal($mon->ps_month_waste_lbs) > 0) {
                    $this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs
                        += $mon->ps_month_waste_lbs;
                }
                foreach ($flds as $fld) {
                    if (isset($mon->{ 'ps_month_' . $fld })
                        && intVal($mon->{ 'ps_month_' . $fld }) > 0) {
                        $this->sessData->dataSets[$tbl][0]->{ 'ps_tot_' . $fld }
                            += $mon->{ 'ps_month_' . $fld };
                    }
                }
            }
        }
        if ($this->sessData->dataSets[$tbl][0]->ps_kwh 
            != $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) {
            if ($this->sessData->dataSets[$tbl][0]->ps_kwh > 0) {
                $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc 
                    = $this->sessData->dataSets[$tbl][0]->ps_kwh;
            } elseif ($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc > 0) {
                $this->sessData->dataSets[$tbl][0]->ps_kwh 
                    = $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc;
            }
        }
        if (isset($this->sessData->dataSets[$tbl][0]->ps_kwh_include_renewables)
            && intVal($this->sessData->dataSets[$tbl][0]->ps_kwh_include_renewables) == 0) {
            $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc += $renew;
        }
        $defCCF = $GLOBALS["SL"]->def->getID('Natural Gas Units', 'CCF');
        if (isset($ps->ps_unit_natural_gas) 
            && intVal($ps->ps_unit_natural_gas) == $defCCF) {
            $this->sessData->dataSets[$tbl][0]->ps_tot_natural_gas *= 0.0103412;
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function loadBldDefs($areaFlwrID)
    {
        $defSet = 'PowerScore Building Types';
        $this->v["bldTypOut"] = $GLOBALS["SL"]->def->getID($defSet, 'Outdoor');
        $this->v["bldTypGrn"] = $GLOBALS["SL"]->def->getID($defSet, 'Greenhouse');
        $this->v["areaFlwrBlds"] = $this->sessData
            ->getChildRows('ps_areas', $areaFlwrID, 'ps_areas_blds');
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
        if (isset($this->v["totFlwrSqFt"]) && intVal($this->v["totFlwrSqFt"]) > 0) {
            if (isset($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) 
                && intVal($this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc) > 0) {
                $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu(
                    $this->sessData->dataSets[$tbl][0]->ps_kwh_tot_calc
                );
                $this->sessData->dataSets[$tbl][0]->ps_effic_facility 
                    = $btus/$this->v["totFlwrSqFt"];
            }
            if (isset($this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs) 
                && intVal($this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs) > 0) {
                $this->sessData->dataSets[$tbl][0]->ps_effic_waste 
                    = $this->sessData->dataSets[$tbl][0]->ps_tot_waste_lbs
                        /$this->v["totFlwrSqFt"];
            }
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    // Run all the room-based calculations related to scoring
    protected function calcEachRoom()
    {
        $tbl = 'powerscore';
        $canopyTot = 0;
        $sqft 
            = $watts 
            = $gal 
            = [ "Flower" => 0, "Veg" => 0, "Clone" => 0, "Mother" => 0 ];
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
            if (isset($room->ps_room_canopy_sqft)) {
                $canopyTot += intVal($room->ps_room_canopy_sqft);
            }
        }
        if ($this->isDistanceInMeters()) {
            $canopyTot = $GLOBALS["SL"]->cnvrtSqFt2SqMeters($canopyTot);
        }
        foreach ($this->sessData->dataSets["ps_growing_rooms"] as $roomInd => $room) {
            $room->ps_room_total_light_watts 
                = $room->ps_room_lighting_effic 
                = $room->ps_room_lgt_fix_size_calced = 0;
            $room = $this->calcEachRoomAddAllLgtW($room, $roomInd);
            if ($room->ps_room_total_light_watts > 0
                && isset($room->ps_room_canopy_sqft) 
                && $room->ps_room_canopy_sqft > 0) {
                $canopySfFt = $room->ps_room_canopy_sqft;
                if ($this->isDistanceInMeters()) {
                    $canopySfFt = $GLOBALS["SL"]->cnvrtSqFt2SqMeters($canopySfFt);
                }
                $room->ps_room_lpd = $room->ps_room_total_light_watts/$canopySfFt;
                $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density 
                    += $room->ps_room_total_light_watts;
                $areaType = $this->getRoomAreaType($room->getKey());
                $watts[$areaType] = $room->ps_room_total_light_watts;
                $sqft[$areaType]  = $canopySfFt;
            }
            $this->sessData->dataSets[$tbl][0]->ps_effic_lighting 
                += $room->ps_room_lighting_effic/1000; // Overall in kWh
            $room->save();
            $this->sessData->dataSets["ps_growing_rooms"][$roomInd] = $room;
        }

        if ($canopyTot > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density /= $canopyTot;
        } else {
            $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density = 0;
        }
        if ($this->sessData->dataSets[$tbl][0]->ps_lighting_power_density  < 0.000001) {
            $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density  = 0;
        }
        $types = [ 'Flower', 'Veg', 'Clone' ];
        foreach ($types as $typ) {
            if ($sqft[$typ] > 0) {
                $fld = 'ps_lpd_' . strtolower($typ);
                $this->sessData->dataSets[$tbl][0]->{ $fld } = $watts[$typ]/$sqft[$typ];
                if ($this->sessData->dataSets[$tbl][0]->{ $fld } < 0.000001) {
                    $this->sessData->dataSets[$tbl][0]->{ $fld } = 0;
                }
            }
        }
        $this->calcHLPD($watts, $sqft);
        $this->sessData->dataSets[$tbl][0]->ps_total_canopy_size = $canopyTot;
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function getRoomAreaType($roomID)
    {
        $types = [ 'Flower', 'Veg', 'Clone' ];
        foreach ($types as $typ) {
            foreach ($this->sessData->dataSets["ps_link_room_area"] as $lnk) {
                if ($lnk->ps_lnk_rm_ar_room_id == $roomID) {
                    foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                        if (isset($area->ps_area_type)
                            && $area->ps_area_type == $this->v["areaTypes"][$typ]
                            && $lnk->ps_lnk_rm_ar_area_id == $area->getKey()) {
                            return $typ;
                        }
                    }
                }
            }
        }
        return '';
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

    protected function chkWaterSourceResponses()
    {
        if (isset($this->sessData->dataSets["ps_water_sources"])
            && sizeof($this->sessData->dataSets["ps_water_sources"]) > 0) {
            $this->v["chkWtrSources"] = [];
            foreach ($this->sessData->dataSets["ps_water_sources"] as $source) {
                if (isset($source->ps_wtr_src_source)
                    && intVal($source->ps_wtr_src_source) > 0) {
                    $this->v["chkWtrSources"][] = intVal($source->ps_wtr_src_source);
                }
            }
            foreach ($this->sessData->dataSets["ps_water_sources"] as $source) {
                if (isset($source->ps_wtr_src_source)) {
                    $curr = intVal($source->ps_wtr_src_source);
                    if ($curr == 384) { // Municipal Potable Water
                        $this->addWaterSourceResponses(605);
                    } elseif ($curr == 602) { // Groundwater
                        $this->addWaterSourceResponses(605);
                        $this->addWaterSourceResponses(384);
                    } elseif ($curr == 603) { // Reservoir
                        $this->addWaterSourceResponses(605);
                        $this->addWaterSourceResponses(384);
                    } elseif ($curr == 386) { // Well/Bore
                        $this->addWaterSourceResponses(605);
                    } elseif ($curr == 604) { // Natural Surface
                        $this->addWaterSourceResponses(606);
                    } elseif ($curr == 387) { // River/Stream
                        $this->addWaterSourceResponses(606);
                        $this->addWaterSourceResponses(604);
                    } elseif ($curr == 389) { // Pond/Lake
                        $this->addWaterSourceResponses(606);
                        $this->addWaterSourceResponses(604);
                    } elseif ($curr == 607) { // Municipal Recycled Water
                        $this->addWaterSourceResponses(606);
                    } elseif ($curr == 608) { // Reclaimed
                        $this->addWaterSourceResponses(606);
                    } elseif ($curr == 609) { // Condensate
                        $this->addWaterSourceResponses(606);
                        $this->addWaterSourceResponses(608);
                    } elseif ($curr == 388) { // Rainwater
                        $this->addWaterSourceResponses(606);
                        $this->addWaterSourceResponses(608);
                    } elseif ($curr == 610) { // Irrigation Runoff
                        $this->addWaterSourceResponses(606);
                        $this->addWaterSourceResponses(608);
                    }
                }
            }
        }
        return true;
    }

    private function addWaterSourceResponses($source)
    {
        if (!in_array($source, $this->v["chkWtrSources"])) {
            $this->v["chkWtrSources"][] = $source;
            $chk = RIIPsWaterSources::where('ps_wtr_src_psid', $this->coreID)
                ->where('ps_wtr_src_source', $source)
                ->first();
            if (!$chk || !isset($chk->ps_wtr_src_id)) {
                $newSource = new RIIPsWaterSources;
                $newSource->ps_wtr_src_psid = $this->coreID;
                $newSource->ps_wtr_src_source = $source;
                $newSource->save();
            }
        }
        return true;
    }

    protected function chkGrowingMediaResponses()
    {
        if (isset($this->sessData->dataSets["ps_grow_media"])
            && sizeof($this->sessData->dataSets["ps_grow_media"]) > 0) {
            $this->v["chkGrowMedia"] = [];
            foreach ($this->sessData->dataSets["ps_grow_media"] as $media) {
                if (isset($media->ps_grw_med_growing)) {
                    $media = intVal($media->ps_grw_med_growing);
                    $this->v["chkGrowMedia"][] = $media;
                    $this->addGrowingMediaResponseTypes($media);
                }
            }
        }
        $this->v["chkGrowMedia"] = [];
        if (isset($this->sessData->dataSets["ps_grow_media_area"])
            && sizeof($this->sessData->dataSets["ps_grow_media_area"]) > 0) {
            foreach ($this->sessData->dataSets["ps_grow_media_area"] as $areaMedia) {
                if (isset($areaMedia->ps_ar_grw_med_media)
                    && intVal($areaMedia->ps_ar_grw_med_media) > 0
                    && isset($areaMedia->ps_ar_grw_med_area_id)
                    && intVal($areaMedia->ps_ar_grw_med_area_id) > 0) {
                    $areaID = intVal($areaMedia->ps_ar_grw_med_area_id);
                    if (!isset($this->v["chkGrowMedia"][$areaID])) {
                        $this->v["chkGrowMedia"][$areaID] = [];
                    }
                    $media = intVal($areaMedia->ps_ar_grw_med_media);
                    $this->v["chkGrowMedia"][$areaID][] = $media;
                    $this->addGrowingMediaResponseTypes($media, $areaID);
                }
            }
        }
        return true;
    }

    private function addGrowingMediaResponseTypes($media, $areaID = 0)
    {
        $addMedia = 0;
        if (in_array($media, [
                636, // Deep Water Culture
                637, // Nutrient Film Technique
                638 // Other Hydroponics
            ])) {
            $addMedia = 635; // Hydroponics
        } elseif (in_array($media, [
                640, // Living Soil
                641, // Field Soil
                642, // Top Soil
                643, // Mineral Soil
                405, // Rockwool
                401, // Coco
                408, // Peat
                402, // Perlite
                644, // Expanded Clay
                645, // Engineered Foam
                403, // Vermiculite
                406, // Clay Pebbles / Clay Balls
                407, // Growstones
                409 // Sand
            ])) {
            $addMedia = 400; // Soil Mixture
        }
        if ($addMedia > 0) {
            if ($areaID > 0) {
                $this->addGrowingMediaAreaResponses($addMedia, $areaID);
            } else {
                $this->addGrowingMediaResponses($addMedia);
            }
        }
        return true;
    }

    private function addGrowingMediaAreaResponses($media, $areaID)
    {
        if (!in_array($media, $this->v["chkGrowMedia"][$areaID])) {
            $this->v["chkGrowMedia"][$areaID][] = $media;
            $chk = RIIPsGrowMediaArea::where('ps_ar_grw_med_area_id', $areaID)
                ->where('ps_ar_grw_med_media', $media)
                ->first();
            if (!$chk || !isset($chk->ps_ar_grw_med_id)) {
                $newMedia = new RIIPsGrowMediaArea;
                $newMedia->ps_ar_grw_med_area_id = $areaID;
                $newMedia->ps_ar_grw_med_media   = $media;
                $newMedia->save();
            }
        }
        return true;
    }

    private function addGrowingMediaResponses($media)
    {
        if (!in_array($media, $this->v["chkGrowMedia"])) {
            $this->v["chkGrowMedia"][] = $media;
            $chk = RIIPsGrowMedia::where('ps_grw_med_psid', $this->coreID)
                ->where('ps_grw_med_growing', $media)
                ->first();
            if (!$chk || !isset($chk->ps_grw_med_id)) {
                $newMedia = new RIIPsGrowMedia;
                $newMedia->ps_grw_med_psid    = $this->coreID;
                $newMedia->ps_grw_med_growing = $media;
                $newMedia->save();
            }
        }
        return true;
    }

    protected function calcWaterScore()
    {
        $this->chkWaterSourceResponses();
        $this->chkGrowingMediaResponses();
        $this->sessData->dataSets["powerscore"][0]->ps_effic_water 
            = $this->sessData->dataSets["powerscore"][0]->ps_tot_water 
            = $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_source
            = $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_recirc
            = $totGal
            = 0;
        if (isset($this->sessData->dataSets["ps_monthly"])
            && sizeof($this->sessData->dataSets["ps_monthly"]) > 0) {
            foreach ($this->sessData->dataSets["ps_monthly"] as $mon) {
                if (isset($mon->ps_month_water) && intVal($mon->ps_month_water) > 0) {
                    $totGal += $mon->ps_month_water;
                }
                if (isset($mon->ps_month_water_storage_source) 
                    && intVal($mon->ps_month_water_storage_source) > 0) {
                    $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_source 
                        += $mon->ps_month_water_storage_source;
                }
                if (isset($mon->ps_month_water_storage_recirc) 
                    && intVal($mon->ps_month_water_storage_recirc) > 0) {
                    $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_recirc 
                        += $mon->ps_month_water_storage_recirc;
                }
            }
        }
        if ($totGal == 0
            && $this->sessData->dataSets["powerscore"][0]->ps_tot_water == 0 
            && isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && isset($area->ps_area_gallons) 
                    && intVal($area->ps_area_gallons) > 0) {
                    $totGal += $area->ps_area_gallons;
                }
            }
        }
        if ($this->isWaterInLiters()) {
            $totGal = $GLOBALS["SL"]->cnvrtLiter2Gal($totGal);
            $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_source 
                = $GLOBALS["SL"]->cnvrtLiter2Gal(
                    $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_source);
            $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_recirc 
                = $GLOBALS["SL"]->cnvrtLiter2Gal(
                    $this->sessData->dataSets["powerscore"][0]->ps_tot_water_storage_recirc);
        }
        $this->sessData->dataSets["powerscore"][0]->ps_tot_water = $totGal;
        if ($this->v["totFlwrSqFt"] > 0) {
            $this->sessData->dataSets["powerscore"][0]->ps_effic_water 
                = $totGal/$this->v["totFlwrSqFt"];
        }
        $this->sessData->dataSets["powerscore"][0]->save();
        return true;
    }

    protected function calcHvacScore()
    {
        $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac = 0;
            //= $this->sessData->dataSets["powerscore"][0]->ps_effic_hvac_orig;
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
        $tbl = 'powerscore';
        $canopyTot = 0;
        $sqft 
            = $watts 
            = $wattsHvac 
            = $gal 
            = [ "Flower" => 0, "Veg" => 0, "Clone" => 0, "Mother" => 0 ];
        if (sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                $area->ps_area_lighting_effic = $area->ps_area_lpd = 0;
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $canopySfFt = $area->ps_area_size;
                        if ($this->isDistanceInMeters()) {
                            $canopySfFt = $GLOBALS["SL"]->cnvrtSqFt2SqMeters($canopySfFt);
                        }
                        $canopyTot += $canopySfFt;
                        $sqft[$typ] = $canopySfFt;
                        $watts[$typ] 
                            = $wattsHvac[$typ] 
                            = $gal[$typ] 
                            = $fixCnt 
                            = 0;
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
                        if ($sqft[$typ] > 0 && $typ != 'Mother') {
                            $fld = 'ps_lpd_' . strtolower($typ);
                            $this->sessData->dataSets[$tbl][0]->{ $fld } = $watts[$typ]/$sqft[$typ];
                            if ($this->sessData->dataSets[$tbl][0]->{ $fld } < 0.000001) {
                                $this->sessData->dataSets[$tbl][0]->{ $fld } = 0;
                            }
                        }
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
                            $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density 
                                += $watts[$typ];
                            if (isset($lgt->ps_lg_typ_hours) 
                                && intVal($lgt->ps_lg_typ_hours) > 0) {
                                // grower-entered data is preferred
                                $hours = $lgt->ps_lg_typ_hours; 
                            }
                            $area->ps_area_lighting_effic = $watts[$typ]*$hours;
                            $this->sessData->dataSets[$tbl][0]->ps_effic_lighting 
                                += $area->ps_area_lighting_effic/1000; // Overall in kWh
                        }
                        if ($canopyTot > 0) {
                            $sqftWeight = $sqft[$typ]/$canopyTot;
                            if (intVal($sqft[$typ]) > 0) {
                                if (isset($area->ps_area_hvac_type) 
                                    && intVal($area->ps_area_hvac_type) > 0) {
                                    $area->ps_area_hvac_effic = $GLOBALS["CUST"]
                                        ->getHvacEffic($area->ps_area_hvac_type);
                                    $this->sessData->dataSets[$tbl][0]->ps_effic_hvac_orig 
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
                $this->sessData->dataSets[$tbl][0]->ps_effic_lighting = 0.00000001; 
                // a real calculation, not zero or null
            } elseif ($canopyTot > 0) {
                $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density /= $canopyTot;
            }
            if ($this->sessData->dataSets[$tbl][0]->ps_lighting_power_density < 0.000001) {
                $this->sessData->dataSets[$tbl][0]->ps_lighting_power_density = 0;
            }
            $this->calcHLPD($watts, $sqft);
            $this->sessData->dataSets[$tbl][0]->ps_effic_hvac 
                = $this->sessData->dataSets[$tbl][0]->ps_effic_hvac_orig;
        }
        $this->sessData->dataSets[$tbl][0]->ps_total_canopy_size = $canopyTot;
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }

    protected function calcHLPD($watts = [], $sqft = [])
    {
        $tbl = 'powerscore';
        if ($sqft["Mother"] == 0) {
            $sqft["Mother"] = intVal($this->getAreaFld('Mother', 'ps_area_size'));
            if ($this->isDistanceInMeters()) {
                $sqft["Mother"] = $GLOBALS["SL"]->cnvrtSqFt2SqMeters($sqft["Mother"]);
            }
        }
        $hlpdSqft = $sqft["Flower"]+$sqft["Veg"]+$sqft["Mother"];
        if ($hlpdSqft > 0) {
            $hlpdWatts = $watts["Flower"]+$watts["Veg"]+$watts["Clone"]+$watts["Mother"];
            $this->sessData->dataSets[$tbl][0]->ps_hlpd_ma = $hlpdWatts/$hlpdSqft;
        }
        $this->sessData->dataSets[$tbl][0]->save();
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
            $this->sessData->dataSets["powerscore"][0]->ps_effic_lighting_status 
                = $this->statusArchive;
        }
        return true;
    }

    protected function calcNonElectric()
    {
        $tbl = 'powerscore';
        $ps = $this->sessData->dataSets[$tbl][0];
        if (isset($ps->ps_tot_natural_gas) && $ps->ps_tot_natural_gas > 0) {
            $this->sessData->dataSets["powerscore"][0]->ps_tot_btu_non_electric
                += $ps->ps_tot_natural_gas*99.97612449;
// https://www.convertunits.com/from/therm+%5bU.S.%5d/to/Btu
        }
        if (isset($ps->ps_tot_generator) && $ps->ps_tot_generator > 0) {
            $set = 'Compliance MA Generator Units';
            $unit = $GLOBALS["SL"]->def->getID($set, 'Diesel (Gallons)');
            if (isset($ps->ps_unit_generator) && intVal($ps->ps_unit_generator) == $unit) {
                $this->sessData->dataSets[$tbl][0]->ps_tot_btu_non_electric
                    += $ps->ps_tot_generator*138.87415823;
// https://www.convertunits.com/from/gallon+%5bU.S.%5d+of+diesel+oil/to/Btu
            } else {
                $this->sessData->dataSets[$tbl][0]->ps_tot_btu_non_electric
                    += $ps->ps_tot_generator*124.9679542;
// https://www.convertunits.com/from/gallon+[U.S.]+of+automotive+gasoline/to/Btu+[thermochemical]
            }
        }
        if (isset($ps->ps_tot_fuel_oil) && $ps->ps_tot_fuel_oil > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_tot_btu_non_electric
                += $ps->ps_tot_fuel_oil*138.87415823;
// https://www.convertunits.com/from/gallon+%5BU.S.%5D+of+distillate+no.+2+fuel+oil/to/Btus
        }
        if (isset($ps->ps_tot_propane) && $ps->ps_tot_propane > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_tot_btu_non_electric
                += $ps->ps_tot_propane*95.500;
// https://www.convertunits.com/from/gallon+[U.S.]+of+LPG/to/Btu
        }

        $this->sessData->dataSets[$tbl][0]->save();
        $ps = $this->sessData->dataSets[$tbl][0];
        
        $btus = $ps->ps_tot_btu_non_electric+$GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc);
        if (isset($this->v["totFlwrSqFt"]) && intVal($this->v["totFlwrSqFt"]) > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_effic_non_electric
                = $ps->ps_tot_btu_non_electric/$this->v["totFlwrSqFt"];
            $this->sessData->dataSets[$tbl][0]->ps_effic_fac_all = $btus/$this->v["totFlwrSqFt"];
        }
        if ($ps->ps_tot_btu_non_electric > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_effic_prod_non 
                = $ps->ps_grams_dry/$ps->ps_tot_btu_non_electric;
        }
        if ($btus > 0) {
            $this->sessData->dataSets[$tbl][0]->ps_effic_prod_all = $ps->ps_grams_dry/$btus;
        }
        $this->sessData->dataSets[$tbl][0]->save();
        return true;
    }
    
}
