<?php
/**
  * ScoreCondsCustom is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains functions used to check custom conditions.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use ResourceInnovation\CannabisScore\Controllers\ScoreUtils;

class ScoreCondsCustom extends ScoreUtils
{
    
    protected function checkNodeConditionsCustom($nID, $condition = '')
    {
        return $this->checkNodeConditionsCustomPowerScore($nID, $condition);
    }
    
    protected function checkNodeConditionsCustomPowerScore($nID, $condition = '')
    {
        if ($condition == '#Competitor') {
            if ($GLOBALS["SL"]->REQ->has('cups')) {
                return 1;
            }
        } elseif ($condition == '#IsPowerScoreGrow') {
            return $this->runCondIsPowerScoreGrow();
        } elseif ($condition == '#IsPowerScoreFlow') {
            return $this->runCondIsPowerScoreFlow();
        } elseif ($condition == '#GeneratesRenewable') {
            return $this->hasRenewable();
        } elseif ($condition == '#MotherHas') {
            return $this->runCondHasArea('Mother');
        } elseif ($condition == '#CloneHas') {
            return $this->runCondHasArea('Clone');
        } elseif ($condition == '#VegHas') {
            return $this->runCondHasArea('Veg');
        } elseif ($condition == '#FlowerHas') {
            return $this->runCondHasArea('Flower');
        } elseif ($condition == '#DryingOnSite') {
            return $this->runCondHasArea('Dry');
        } elseif ($condition == '#GrowthStageProLoop') {
            return $this->runCondGrowthStageProLoop();
        } elseif ($condition == '#HasNoGrowthStages') {
            return $this->runCondHasNoGrowthStages();

        } elseif ($condition == '#MotherArtificialLight') {
            return $this->runCondArtifArea('Mother');
        } elseif ($condition == '#CloneArtificialLight') {
            return $this->runCondArtifArea('Clone');
        } elseif ($condition == '#VegArtificialLight') {
            return $this->runCondArtifArea('Veg');
        } elseif ($condition == '#FlowerArtificialLight') {
            return $this->runCondArtifArea('Flower');
        } elseif ($condition == '#HasArtificialLight') {
            return $this->runCondArtifLight();

        } elseif ($condition == '#MotherSunlight') {
            return $this->runCondSunArea('Mother');
        } elseif ($condition == '#CloneSunlight') {
            return $this->runCondSunArea('Clone');
        } elseif ($condition == '#VegSunlight') {
            return $this->runCondSunArea('Veg');
        } elseif ($condition == '#FlowerSunlight') {
            return $this->runCondSunArea('Flower');
        } elseif ($condition == '#SunlightVegOrFlower') {
            // could be replaced by OR functionality
            if ($this->runCondSunArea('Veg') == 1 
                || $this->runCondSunArea('Flower') == 1) {
                return 1;
            }
            return 0;

        } elseif ($condition == '#GrowthStageIsOutdoor') {
            return $this->runCondGrowthStageIsOutdoor();
        } elseif ($condition == '#IsFullyOutdoor') {
            return $this->runCondIsFullyOutdoor();
        } elseif ($condition == '#ShowWaterMonthlyTbl') {
            return $this->runCondShowWaterMonthlyTbl();

        } elseif ($condition == '#HasUniqueness') {
            return $this->runCondHasFeedback('uniqueness');
        } elseif ($condition == '#HasFeedback') {
            return $this->runCondHasFeedback('feedback');
        } elseif ($condition == '#ScoreNotLeader') {
            return $this->runCondScoreNotLeader();
        } elseif ($condition == '#ShowReportCureDeets') {
            return $this->runCondShowReportCureDeets();
        } elseif ($condition == '#ShowReportWater') {
            return $this->runCondShowReportWater();
        } elseif ($condition == '#ShowReportWaste') {
            return $this->runCondShowReportWaste();
        } elseif ($condition == '#ReportDetailsPublic') { 
            return $this->runCondReportDetailsPublic();

        } elseif (in_array($condition, [
                '#IndoorFlower5Ksf', 
                '#IndoorFlower10Ksf', 
                '#IndoorFlower30Ksf', 
                '#IndoorFlower50Ksf', 
                '#IndoorFlowerOver50Ksf'
            ])) {
            return $this->runCondIndoorFlowerSizes($condition);

        } elseif ($condition == '#UsesBiofuels') {
            return $this->runCondUsesOtherFuel('Biofuels (cord wood, wood pellets, other)');
        } elseif ($condition == '#UsesGenerator') {
            return $this->runCondUsesOtherFuel('Back up generator');
        } elseif ($condition == '#UsesPropane') {
            return $this->runCondUsesOtherFuel('Propane');
        } elseif ($condition == '#UsesFuelOil') {
            return $this->runCondUsesOtherFuel('Fuel oil');
        } elseif ($condition == '#UsesAnyOtherFuels') {
            return $this->runCondUsesAnyOtherFuels();

        }
        return -1;
    }

    private function runCondIsPowerScoreGrow()
    {
        if (isset($this->sessData->dataSets["powerscore"])
            && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if ((isset($ps->ps_is_pro) && intVal($ps->ps_is_pro) == 1)
                || (isset($ps->ps_is_flow) && intVal($ps->ps_is_flow) == 1)) {
                return 0;
            }
            return 1;
        }
        return 0;
    }

    private function runCondIsPowerScoreFlow()
    {
        if (isset($this->sessData->dataSets["powerscore"])
            && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_is_flow) && intVal($ps->ps_is_flow) == 1) {
                return 1;
            }
        }
        return 0;
    }
    
    private function hasRenewable()
    {
        if (isset($this->sessData->dataSets["ps_renewables"])
            && sizeof($this->sessData->dataSets["ps_renewables"]) > 0) {
            $renewables = $this->getRenewableDefs();
            foreach ($this->sessData->dataSets["ps_renewables"] as $renew) {
                if (isset($renew->ps_rnw_renewable)) {
                    $renewDef = intVal($renew->ps_rnw_renewable);
                    if (in_array($renewDef, $renewables)) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    private function runCondGrowthStageProLoop($area = null)
    {
        if ($area === null) {
            $area = $this->sessData->getDataBranchRow('ps_areas');
        }
        $defs = [
            $this->v["areaTypes"]["Flower"],
            $this->v["areaTypes"]["Veg"],
            $this->v["areaTypes"]["Clone"]
        ];
//echo '<pre>'; print_r($area); echo '</pre>'; exit;
        if ($area
            && isset($area->ps_area_has_stage)
            && intVal($area->ps_area_has_stage) == 1
            && isset($area->ps_area_type)
            && in_array($area->ps_area_type, $defs)) {
            return 1;
        }
//echo '<pre>'; print_r($area); echo '</pre>'; exit;
        return 0;
    }

    private function runCondHasNoGrowthStages()
    {
        $found = false;
        if (isset($this->sessData->dataSets["ps_areas"]) 
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            $defs = [
                $this->v["areaTypes"]["Flower"],
                $this->v["areaTypes"]["Veg"],
                $this->v["areaTypes"]["Clone"]
            ];
            foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                if (isset($area->ps_area_has_stage)
                    && intVal($area->ps_area_has_stage) == 1
                    && isset($area->ps_area_type)
                    && in_array($area->ps_area_type, $defs)) {
                    $found = true;
                }
            }
        }
        if ($found) {
            return 0;
        }
        return 1;
    }
    
    private function runCondHasArea($areaName)
    {
        $area = $this->getArea($areaName);
        if (!isset($area) || !isset($area->ps_area_has_stage)) {
            return 0;
        }
        return intVal($area->ps_area_has_stage);
    }
    
    private function runCondSunArea($areaName)
    {
        $area = $this->getArea($areaName);
        if (!isset($area) || !isset($area->ps_area_has_stage)) {
            return 0;
        }
        return intVal($area->ps_area_lgt_sun);
    }
    
    private function runCondArtifArea($areaName)
    {
        $area = $this->getArea($areaName);
        if (!isset($area) || !isset($area->ps_area_has_stage)) {
            return 0;
        }
        return intVal($area->ps_area_lgt_artif);
    }
    
    private function runCondArtifLight()
    {
        if (isset($this->sessData->dataSets["ps_growing_rooms"])
            && sizeof($this->sessData->dataSets["ps_growing_rooms"]) > 0) {
            foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
                if (isset($room->ps_room_lgt_artif) && intVal($room->ps_room_lgt_artif) == 1) {
                    return 1;
                }
            }
        } elseif ($this->runCondArtifArea('Mother') == 1 
            || $this->runCondArtifArea('Clone') == 1
            || $this->runCondArtifArea('Veg') == 1 
            || $this->runCondArtifArea('Flower') == 1) {
            // could be replaced by OR functionality
            return 1;
        }
        return 0;
    }

    protected function stageIsOutdoor($area)
    {
        if ($area
            && isset($area->ps_area_has_stage)
            && intVal($area->ps_area_has_stage) == 1
            && $area->ps_area_type != $this->v["areaTypes"]["Dry"]) {
            if (isset($this->sessData->dataSets["ps_growing_rooms"])
                && sizeof($this->sessData->dataSets["ps_growing_rooms"]) > 0) {
                $rooms = DB::table('rii_ps_link_room_area')
                    ->join('rii_ps_growing_rooms', 'rii_ps_link_room_area.ps_lnk_rm_ar_room_id', 
                        '=', 'rii_ps_growing_rooms.ps_room_id')
                    ->where('rii_ps_link_room_area.ps_lnk_rm_ar_area_id', $area->getKey())
                    ->select('rii_ps_growing_rooms.ps_room_farm_type')
                    ->get();
                foreach ($rooms as $room) {
                    if (isset($room->ps_room_farm_type)
                        && intVal($room->ps_room_farm_type) == $this->frmTypOut) {
                        return true;
                    }
                }
                return false;
            }
            return $this->stageIsOutdoorNoRooms($area);
        }
        return false;
    }

    protected function stageIsOutdoorNoRooms($area)
    {
        if (isset($room->ps_area_farm_type)
            && intVal($room->ps_area_farm_type) > 0) {
            if (intVal($room->ps_area_farm_type) == $this->frmTypOut) {
                return true;
            }
            return false;
        }
        // Counting as Outdoor if they ONLY selected Outdoor
        $outCnt = 0;
        $bldTypOut = $GLOBALS["SL"]->def->getID('PowerScore Building Types', 'Outdoor');
        $blds = $this->sessData->getChildRows('ps_areas', $area->getKey(), 'ps_areas_blds');
        foreach ($blds as $bld) {
            if (isset($bld->ps_ar_bld_type) && intVal($bld->ps_ar_bld_type) > 0) {
                if (intVal($bld->ps_ar_bld_type) == $bldTypOut) {
                    $outCnt++;
                } else {
                    $outCnt--;
                }
            }
        }
        if ($outCnt > 0) {
            return true;
        }
        return false;
    }

    protected function runCondGrowthStageIsOutdoor()
    {
        $area = $this->sessData->getDataBranchRow('ps_areas');
        if ($this->stageIsOutdoor($area)) {
            return 1;
        }
        return 0;
    }

    protected function runCondIsFullyOutdoor()
    {
        if (isset($this->sessData->dataSets["ps_growing_rooms"])
            && sizeof($this->sessData->dataSets["ps_growing_rooms"]) > 0) {
            $outdoorRooms = 0;
            foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
                if (isset($room->ps_room_farm_type)
                    && intVal($room->ps_room_farm_type) == $this->frmTypOut) {
                    $outdoorRooms++;
                }
            }
            if ($outdoorRooms == sizeof($this->sessData->dataSets["ps_growing_rooms"])) {
                return 1;
            }
            return 0;
        } elseif (isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            $hasCnt = $outdoorCnt = 0;
            foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                if (isset($area->ps_area_has_stage)
                    && intVal($area->ps_area_has_stage) == 1
                    && $area->ps_area_type != $this->v["areaTypes"]["Dry"]
                    && $area->ps_area_type != $this->v["areaTypes"]["Mother"]) {
                    $hasCnt++;
                    if ($this->stageIsOutdoor($area)) {
                        $outdoorCnt++;
                    }
                }
            }
            if ($hasCnt == $outdoorCnt) {
                return 1;
            }
        }
        return 0;
    }

    private function runCondShowWaterMonthlyTbl()
    {

        if (isset($this->sessData->dataSets["ps_onsite"])
            && isset($this->sessData->dataSets["ps_onsite"][0]->ps_on_id)) {
            if (isset($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_store_source)
                && intVal($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_store_source) == 1) {
                return 1;
            }
            if (isset($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_store_recirc)
                && intVal($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_store_recirc) == 1) {
                return 1;
            }
            if (isset($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_by_months)
                && intVal($this->sessData->dataSets["ps_onsite"][0]->ps_on_water_by_months) == 1) {
                return 1;
            }
        }
        return 0;
    }

    private function runCondHasFeedback($type = 'feedback')
    {
        if (isset($this->sessData->dataSets["ps_page_feedback"])) {
            $feed = $this->sessData->dataSets["ps_page_feedback"][0];
            for ($i = 1; $i < 9; $i++) {
                $fld = 'ps_pag_feed_' . $type . $i;
                if (isset($feed->{ $fld }) && trim($feed->{ $fld }) != '') {
                    return 1;
                }
            }
        }
        return 0;
    }

    private function runCondScoreNotLeader()
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_effic_overall)) {
                if (round($ps->ps_effic_overall) < 67) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
        return -1;
    }

    private function runCondShowReportCureDeets()
    {
        $area = $this->getArea('Dry');
        if (isset($area) 
            && isset($area->ps_area_has_stage) 
            && intVal($area->ps_area_has_stage) == 1) {
            if (isset($area->ps_area_size) && intVal($area->ps_area_size) > 0) {
                return 1;
            }
            if (isset($area->ps_ar_bld_type) && intVal($area->ps_ar_bld_type) > 0) {
                return 1;
            }
        }
        return 0;
    }

    private function runCondShowReportWater()
    {
        if (isset($this->sessData->dataSets["ps_water_sources"])
            && isset($this->sessData->dataSets["ps_water_sources"][0]->ps_wtr_src_source)
            && intVal($this->sessData->dataSets["ps_water_sources"][0]->ps_wtr_src_source) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_water_holding"])
            && isset($this->sessData->dataSets["ps_water_holding"][0]->ps_wtr_hld_holding)
            && intVal($this->sessData->dataSets["ps_water_holding"][0]->ps_wtr_hld_holding) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_water_filter"])
            && isset($this->sessData->dataSets["ps_water_filter"][0]->ps_wtr_flt_filter)
            && intVal($this->sessData->dataSets["ps_water_filter"][0]->ps_wtr_flt_filter) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_grow_media"])
            && isset($this->sessData->dataSets["ps_grow_media"][0]->ps_grw_med_growing)
            && intVal($this->sessData->dataSets["ps_grow_media"][0]->ps_grw_med_growing) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_waste_water"])
            && isset($this->sessData->dataSets["ps_waste_water"][0]->ps_wst_wtr_method)
            && intVal($this->sessData->dataSets["ps_waste_water"][0]->ps_wst_wtr_method) > 0) {
            return 1;
        }
        return 0;
    }

    private function runCondShowReportWaste()
    {
        $ps = $this->sessData->dataSets["powerscore"][0];
        if (isset($ps->ps_green_waste_lbs) 
            && intVal($ps->ps_green_waste_lbs) > 0) {
            return 1;
        }
        if (isset($ps->ps_green_waste_mixed) 
            && intVal($ps->ps_green_waste_mixed) > 0) {
            return 1;
        }
        if (isset($ps->ps_compliance_waste_track) 
            && trim($ps->ps_compliance_waste_track) != '') {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_waste_green"])
            && isset($this->sessData->dataSets["ps_waste_green"][0]->ps_wst_grn_method)
            && intVal($this->sessData->dataSets["ps_waste_green"][0]->ps_wst_grn_method) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_waste_ag"])
            && isset($this->sessData->dataSets["ps_waste_ag"][0]->ps_wst_ag_method)
            && intVal($this->sessData->dataSets["ps_waste_ag"][0]->ps_wst_ag_method) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_waste_supplies"])
            && isset($this->sessData->dataSets["ps_waste_supplies"][0]->ps_wst_sup_method)
            && intVal($this->sessData->dataSets["ps_waste_supplies"][0]->ps_wst_sup_method) > 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_waste_process"])
            && isset($this->sessData->dataSets["ps_waste_process"][0]->ps_wst_prcs_method)
            && intVal($this->sessData->dataSets["ps_waste_process"][0]->ps_wst_prcs_method) > 0) {
            return 1;
        }
        return 0;
    }

    private function runCondReportDetailsPublic()
    {
        // For now, all records default to public (but no farm names, etc)
        return 1;

        // could be replaced by OR functionality
        if (isset($this->v["user"]) 
            && $this->v["user"] 
            && $this->v["user"]->hasRole('administrator|staff|partner')) {
            return 1;
        }
        $privDef = $GLOBALS["SL"]->def->getID('PowerScore Privacy Options', 'Private');
        if (isset($this->sessData->dataSets["powerscore"][0]->ps_privacy)
            && intVal($this->sessData->dataSets["powerscore"][0]->ps_privacy) == $privDef) {
            return 0;
        }
        return 1;
    }

    private function runCondIndoorFlowerSizes($condition)
    {
        $area = $this->getArea('Flower');
        if (!isset($area) 
            || !isset($area->ps_area_has_stage) 
            || !isset($area->ps_area_size) 
            || intVal($area->ps_area_size) == 0) {
            return 0;
        }
        $ret = 0;
        if ($condition == '#IndoorFlower5Ksf') {
            if ($area->ps_area_size < 5000) {
                $ret = 1;
            }
        } elseif ($condition == '#IndoorFlower10Ksf') {
            if ($area->ps_area_size <= 5000 && $area->ps_area_size < 10000) {
                $ret = 1;
            }
        } elseif ($condition == '#IndoorFlower30Ksf') {
            if ($area->ps_area_size <= 10000 && $area->ps_area_size < 30000) {
                $ret = 1;
            }
        } elseif ($condition == '#IndoorFlower50Ksf') {
            if ($area->ps_area_size <= 30000 && $area->ps_area_size < 50000) {
                $ret = 1;
            }
        } elseif ($condition == '#IndoorFlowerOver50Ksf') {
            if ($area->ps_area_size >= 50000) {
                $ret = 1;
            }
        }
        return $ret;
    }

    private function runCondUsesOtherFuel($defVal = '')
    {
        $defID = $GLOBALS["SL"]->def->getID('Compliance MA Delivered Fuels', $defVal);
        if (isset($this->sessData->dataSets["ps_onsite_fuels"])
            && sizeof($this->sessData->dataSets["ps_onsite_fuels"]) > 0) {
            foreach ($this->sessData->dataSets["ps_onsite_fuels"] as $fuel) {
                if (isset($fuel->ps_fuel_type) && $fuel->ps_fuel_type == $defID) {
                    return 1;
                }
            }
        }
        return 0;
    }

    private function runCondUsesAnyOtherFuels()
    {
        if (isset($this->sessData->dataSets["powerscore"])
            && isset($this->sessData->dataSets["powerscore"][0]->ps_no_natural_gas)
            && intVal($this->sessData->dataSets["powerscore"][0]->ps_no_natural_gas) == 0) {
            return 1;
        }
        if (isset($this->sessData->dataSets["ps_onsite_fuels"])
            && sizeof($this->sessData->dataSets["ps_onsite_fuels"]) > 0) {
            return 1;
        }
        return 0;
    }

    protected function parseConditionsCustom($loop, $recObj)
    {
        if ($loop->data_loop_plural == 'Growth Stages') {
            return $this->runCondGrowthStageProLoop($recObj);
        }
        return -1;
    }

}
