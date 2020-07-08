<?php
/**
  * ScoreCalcsPrint is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which report 
  * Sub-Score calculations for transparency.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\SLNodeSaves;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreCalcRanks;

class ScoreCalcsPrint extends ScoreCalcRanks
{
    protected function getAllReportCalcs()
    {
        $this->loadTotFlwrSqFt();
        $this->calcCurrSubScores();
        $this->loadCalcNicknames();
        $this->prepPrintEfficLgt();
        $this->prepPrintEfficHvac();
        $this->prepPrintEfficWater();
        $this->chkUnprintableSubScores();
        $this->v["sessData"] = $this->sessData->dataSets;
        if (isset($this->sessData->dataSets["powerscore"])) {
            $this->v["psid"] = $this->sessData->dataSets["powerscore"][0]->getKey();
            $this->v["hasRefresh"] = (($GLOBALS["SL"]->REQ->has('refresh')) ? '&refresh=1' : '')
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
                $this->v["roomNicks"][$r] = str_replace(' (', ', ', str_replace(')', '', 
                    $this->getRoomName($room, $r)));
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
    
    protected function prepPrintEfficWater()
    {
        $this->v["printEfficWtr"] = view(
            'vendor.cannabisscore.nodes.490-report-calculations-water', 
            [
                "ps"          => $this->sessData->dataSets["powerscore"][0],
                "areas"       => $this->v["areas"],
                "areaNicks"   => $this->v["areaNicks"],
                "totFlwrSqFt" => $this->v["totFlwrSqFt"]
            ]
        )->render();
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


}