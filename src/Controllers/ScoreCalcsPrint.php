<?php
/**
  * ScoreCalcsPrint is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains the majority of processes which report 
  * Sub-Score calculations for transparency.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use App\Models\SLNodeSaves;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use RockHopSoft\Survloop\Controllers\Globals\Globals;
use ResourceInnovation\CannabisScore\Controllers\ScoreCalcRanks;

class ScoreCalcsPrint extends ScoreCalcRanks
{
    protected function getAllReportCalcs()
    {
        $this->loadTotFlwrSqFt();
        $this->calcCurrSubScores();
        $this->loadCalcNicknames();
        $this->prepPrintEfficLgt();
        $this->prepPrintEfficHvac();
        $this->prepPrintEfficFacNon();
        $this->prepPrintEfficWater();
        $this->prepPrintEfficWaste();
        $this->chkUnprintableSubScores();
        $this->prepScoreYearMonths();
        $this->v["sessData"] = $this->sessData->dataSets;
        if (isset($this->sessData->dataSets["powerscore"])) {
            $this->v["psid"] = $this->sessData->dataSets["powerscore"][0]->getKey();
            $this->v["hasRefresh"] = (($GLOBALS["SL"]->REQ->has('refresh')) 
                    ? '&refresh=1' : '')
                . (($GLOBALS["SL"]->REQ->has('print')) ? '&print=1' : '');
            $GLOBALS["SL"]->loadStates();
            return true;
        }
        return false;
    }
    
    protected function loadCalcNicknames()
    {
        $this->v["roomNicks"] = $this->v["areas"] = $this->v["areaNicks"] = [];
        if (isset($this->sessData->dataSets["ps_growing_rooms"])) {
            $rooms = $this->sessData->dataSets["ps_growing_rooms"];
            foreach ($rooms as $r => $room) {
                $this->v["roomNicks"][$r] = str_replace(' (', ', ', 
                    str_replace(')', '', $this->getRoomName($room, $r)));
            }
        }
        if (isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && isset($area->ps_area_size)
                    && $area->ps_area_size > 0
                    && $area->ps_area_hvac_effic > 0) {
                    $this->v["areaNicks"][sizeof($this->v["areas"])] = '';
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($defID == $area->ps_area_type) {
                            $this->v["areaNicks"][sizeof($this->v["areas"])] = $typ;
                        }
                    }
                    $this->v["areas"][] = $area;
                }
            }
        }
        return true;
    }
    
    protected function prepPrintEfficLgt()
    {
        $this->loadTotFlwrSqFt();
        $this->getLookupLgtNicknames();
        $ps = $this->sessData->dataSets["powerscore"][0];
        $lgts = [];
        if (isset($this->sessData->dataSets["ps_light_types"])) {
            $lgts = $this->sessData->dataSets["ps_light_types"];
        }
        $this->v["printEfficLgt"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-lighting', 
            [
                "ps"        => $ps,
                "lgts"      => $lgts,
                "lgtNicks"  => $this->v["lgtNicknames"],
                "lgtHours"  => $this->v["lgtHours"],
                "lgtTotKwh" => $this->v["lgtTotKwh"]
            ]
        )->render();
        return $this->v["printEfficLgt"];
    }
    
    protected function prepPrintEfficFacNon()
    {
        $ps = $this->sessData->dataSets["powerscore"][0];
        $addLines = $addLines2 = [];
        if (isset($ps->ps_tot_natural_gas) && $ps->ps_tot_natural_gas > 0) {
            $addLines[] = '( ' . number_format($ps->ps_tot_natural_gas) . ' Natural Gas Therms x <a '
                . 'href="https://www.convertunits.com/from/therm+%5bU.S.%5d/to/Btu"'
                . ' target="_blank">99.976</a> ) kBtu';
            $addLines2[] = number_format($ps->ps_tot_natural_gas*99.97612449) 
                . ' Natural Gas kBtu';
        }
        if (isset($ps->ps_tot_generator) && $ps->ps_tot_generator > 0) {
            $set = 'Compliance MA Generator Units';
            $unit = $GLOBALS["SL"]->def->getID($set, 'Diesel (Gallons)');
            if (isset($ps->ps_unit_generator) && intVal($ps->ps_unit_generator) == $unit) {
                $addLines[] = '( ' . number_format($ps->ps_tot_generator) . ' Diesel Gallons x <a '
                    . 'href="https://www.convertunits.com/from/gallon+%5bU.S.%5d+of+diesel+oil/to/Btu"'
                    . ' target="_blank">138.87</a> ) kBtu';
                $addLines2[] = number_format($ps->ps_tot_generator*138.87415823) . ' Diesel kBtu';
            } else {
                $addLines[] = '( ' . number_format($ps->ps_tot_generator) . ' Gasoline Gallons x <a '
                    . 'href="https://www.convertunits.com/from/gallon+[U.S.]+of+automotive+gasoline/to/Btu+[thermochemical]"'
                    . ' target="_blank">124.97</a> ) kBtu';
                $addLines2[] = number_format($ps->ps_tot_generator*124.9679542) . ' Gasoline kBtu';
            }
        }
        if (isset($ps->ps_tot_fuel_oil) && $ps->ps_tot_fuel_oil > 0) {
            $addLines[] = '( ' . number_format($ps->ps_tot_fuel_oil) . ' Fuel Oil Gallons x '
                . '<a href="https://www.convertunits.com/from/gallon+%5BU.S.%5D+of'
                . '+distillate+no.+2+fuel+oil/to/Btus" target="_blank">138.87</a> ) kBtu';
                $addLines2[] = number_format($ps->ps_tot_generator*138.87415823) . ' Fuel Oil kBtu';
        }
        if (isset($ps->ps_tot_propane) && $ps->ps_tot_propane > 0) {
            $addLines[] = '( ' . number_format($ps->ps_tot_propane) . ' Propane Gallons x <a '
                . 'href="https://www.convertunits.com/from/gallon+[U.S.]+of+LPG/to/Btu"'
                . ' target="_blank">95.500</a> ) kBtu';
            $addLines2[] = number_format($ps->ps_tot_propane*95.500) . ' Propane kBtu';
        }
        return $this->prepPrintEfficFacNonViews($addLines, $addLines2);
    }
    
    protected function prepPrintEfficFacNonViews($addLines, $addLines2)
    {
        $this->v["printEfficFacNon"] 
            = $this->v["printEfficFacAll"]
            = $this->v["printEfficProdNon"]
            = $this->v["printEfficProdAll"]
            = '';
        $this->v["printEfficFac"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-facility', 
            [
                "ps"          => $this->sessData->dataSets["powerscore"][0],
                "totFlwrSqFt" => $this->v["totFlwrSqFt"]
            ]
        )->render();
        $this->v["printEfficProd"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-production', 
            [
                "ps"          => $this->sessData->dataSets["powerscore"][0],
                "totFlwrSqFt" => $this->v["totFlwrSqFt"]
            ]
        )->render();
        if (sizeof($addLines) > 0) {
            $this->v["printEfficFacNon"] = view(
                'vendor.cannabisscore.nodes.490-report-calculations-fac-non', 
                [
                    "ps"        => $this->sessData->dataSets["powerscore"][0],
                    "addLines"  => $addLines,
                    "addLines2" => $addLines2
                ]
            )->render();
            $this->v["printEfficFacAll"] = view(
                'vendor.cannabisscore.nodes.490-report-calculations-fac-all', 
                [
                    "ps"        => $this->sessData->dataSets["powerscore"][0],
                    "addLines"  => $addLines,
                    "addLines2" => $addLines2
                ]
            )->render();
            $this->v["printEfficProdNon"] = view(
                'vendor.cannabisscore.nodes.490-report-calculations-prod-non', 
                [
                    "ps"        => $this->sessData->dataSets["powerscore"][0],
                    "addLines"  => $addLines,
                    "addLines2" => $addLines2
                ]
            )->render();
            $this->v["printEfficProdAll"] = view(
                'vendor.cannabisscore.nodes.490-report-calculations-prod-all', 
                [
                    "ps"        => $this->sessData->dataSets["powerscore"][0],
                    "addLines"  => $addLines,
                    "addLines2" => $addLines2
                ]
            )->render();
        }
        return true;
    }
    
    protected function prepPrintEfficHvac()
    {
        if (!isset($this->v["printEfficHvac"])) {
            $this->v["printEfficHvac"] = [];
        }
        $rooms = [];
        if (isset($this->sessData->dataSets["ps_growing_rooms"])) {
            $rooms = $this->sessData->dataSets["ps_growing_rooms"];
        }
        $this->v["printEfficHvac"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-hvac', 
            [
                "ps"             => $this->sessData->dataSets["powerscore"][0],
                "areas"          => $this->v["areas"],
                "areaNicks"      => $this->v["areaNicks"],
                "rooms"          => $rooms,
                "roomNicks"      => $this->v["roomNicks"],
                "hasRooms"       => $this->hasRooms(),
                "printEfficHvac" => $this->v["printEfficHvac"]
            ]
        )->render();
        return true;
    }
    
    protected function prepPrintEfficWater()
    {
        $this->v["printEfficWtr"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-water', 
            [
                "ps"          => $this->sessData->dataSets["powerscore"][0],
                "areas"       => $this->v["areas"],
                "areaNicks"   => $this->v["areaNicks"]
            ]
        )->render();
        return true;
    }
    
    protected function prepPrintEfficWaste()
    {
        $this->v["printEfficWst"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-waste', 
            [ "ps" => $this->sessData->dataSets["powerscore"][0] ]
        )->render();
        return true;
    }

    private function prepScoreYearMonths($ps = null)
    {
        if ($ps === null && isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
        }
        if (!isset($this->v["scoreYearMonths"])) {
            $this->v["scoreYearMonths"] = [];
        }
        if (!isset($this->v["scoreYearMonths"][$ps->ps_id])) {
            $this->v["scoreYearMonths"][$ps->ps_id] 
                = $GLOBALS["SL"]->lastMonths12($ps, 'ps_start_month');
        }
        return $this->v["scoreYearMonths"][$ps->ps_id];
    }


}