<?php
/**
  * ScoreReportHvac generates the entire a breakdown of different lighting, using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since v0.2.3
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use SurvLoop\Controllers\Stats\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreReportStats;

class ScoreReportHvac extends ScoreReportStats
{
    public $statScoreSets = [];
    
    public function getHvacReport($nID)
    {
        $this->initClimateFilts();

        $this->statScoreSets = [
            ['statScorHvcF144', 'hvac'],
            ['statScorHvcF145', 'hvac'],
            ['statScorHvcF143', 'hvac'],
            ['statScorHvcV144', 'hvac'],
            ['statScorHvcV145', 'hvac'],
            ['statScorHvcV143', 'hvac'],
            ['statScorHvcC144', 'hvac'],
            ['statScorHvcC145', 'hvac'],
            ['statScorHvcC143', 'hvac']
        ];
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]] = new ScoreStats([$set[1]]);
            $this->v["scoreSets"][$set[0]]->loadMap();
        }

        $this->v["hvacSqft"] = [];
        foreach ($this->v["sfFarms"][0] as $i => $farmDef) {
            if ($this->v["sfFarms"][1][$i] != 'Outdoor') {
                $this->v["hvacSqft"][$farmDef] = [];
                foreach ($this->v["sfAreasGrow"][0] as $i => $areaDef) {
                    $this->v["hvacSqft"][$farmDef][$areaDef] = [ 0, 0, [] ];
                    foreach ($this->v["sfHvac"][0] as $i => $hvacDef) {
                        $this->v["hvacSqft"][$farmDef][$areaDef][2][$hvacDef] = [
                            0, 
                            [] 
                        ];
                    }
                }
            }
        }

        $this->v["totCnt"] = 0;
        $this->initSearcher(1);
        $this->searcher->loadAllScoresPublic("->where('PsEfficHvac', '>', 0.00001)"
            . "->where('PsEfficHvacStatus', '=', " . $this->v["psComplete"] . ")");
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            $this->v["totCnt"] = $this->searcher->v["allscores"]->count();
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', '>', 0)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $areaType = $this->motherToClone($area->PsAreaType);
                        if ($this->v["areaTypes"]["Flower"] == $area->PsAreaType) {
                            $this->v["scoreSets"]["statScorHvcF" . $ps->PsCharacterize]
                                ->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                        } elseif ($this->v["areaTypes"]["Veg"] == $area->PsAreaType) {
                            $this->v["scoreSets"]["statScorHvcV" . $ps->PsCharacterize]
                                ->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                        } elseif ($this->v["areaTypes"]["Clone"] == $area->PsAreaType) {
                            $this->v["scoreSets"]["statScorHvcC" . $ps->PsCharacterize]
                                ->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                        }
                        if ($ps->PsCharacterize != 143 && isset($area->PsAreaHvacType) 
                            && isset($area->PsAreaSize)
                            && isset($this->v["hvacSqft"][$ps->PsCharacterize][
                                $area->PsAreaType])
                            && isset($this->v["hvacSqft"][$ps->PsCharacterize][
                                $area->PsAreaType][2][$area->PsAreaHvacType])) {
                            $this->v["hvacSqft"][$ps->PsCharacterize][
                                $area->PsAreaType][2][$area->PsAreaHvacType][1][] 
                                = $area->PsAreaSize;
                        }
                    }
                    foreach ($this->statScoreSets as $set) {
                        if (intVal(substr($set[0], strlen($set[0])-3)) 
                            == $ps->PsCharacterize) {
                            $this->v["scoreSets"][$set[0]]->addScoreData($ps);
                            $this->v["scoreSets"][$set[0]]->resetRecFilt();
                        }
                    }
                }
                unset($areas);
            }
            foreach ($this->v["sfFarms"][0] as $i => $farmDef) {
                if ($this->v["sfFarms"][1][$i] != 'Outdoor') {
                    foreach ($this->v["sfAreasGrow"][0] as $i => $areaDef) {
                        foreach ($this->v["sfHvac"][0] as $i => $hvacDef) {
                            if (sizeof($this->v["hvacSqft"][
                                $farmDef][$areaDef][2][$hvacDef][1]) > 0) {
                                $this->v["hvacSqft"][$farmDef][$areaDef][2][$hvacDef][0] 
                                    = $GLOBALS["SL"]->arrAvg($this->v["hvacSqft"][
                                        $farmDef][$areaDef][2][$hvacDef][1]);
                                $this->v["hvacSqft"][$farmDef][$areaDef][0]
                                    += array_sum($this->v["hvacSqft"][
                                        $farmDef][$areaDef][2][$hvacDef][1]);
                                $this->v["hvacSqft"][$farmDef][$areaDef][1]
                                    += count($this->v["hvacSqft"][
                                        $farmDef][$areaDef][2][$hvacDef][1]);
                            }
                        }
                        if ($this->v["hvacSqft"][$farmDef][$areaDef][1] > 0) {
                            $this->v["hvacSqft"][$farmDef][$areaDef][0] 
                                /= $this->v["hvacSqft"][$farmDef][$areaDef][1];
                        }
                    }
                }
            }
        }
        unset($allScores);
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]]->calcStats();
        }
        //$this->v["scoreSets"]["statScorHvcF"]->addCurrFilt('farm', 144);
        //$this->v["scoreSets"]["statScorHvcV"]->addCurrFilt('farm', 144);
        //$this->v["scoreSets"]["statScorHvcC"]->addCurrFilt('farm', 144);

        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.981-hvac-report-excel', $this->v)->render();
            $filename = 'PowerScore_Averages-HVAC' . ((trim($this->v["fltStateClim"]) != '') 
                ? '-' . str_replace(' ', '_', $this->v["fltStateClim"]) : '')
                . '-' . date("ymd") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $filename);
            exit;
        }

        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view('vendor.cannabisscore.nodes.981-hvac-report', $this->v)->render();
    }
}