<?php
/**
  * ScoreReportFound generates the entire PowerScore Founders Report, using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use SurvLoop\Controllers\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreReportStats;

class ScoreReportFound extends ScoreReportStats
{
    public $statScoreSets = [];
    
    public function getFoundReport($nID, $allScores)
    {
        $this->prepStatFilts();
        $this->statScoreSets = [
            ['statScorSize', 'size'],
            ['statScorAuto', 'auto'],
            ['statScorVert', 'vert'],
            ['statScorHvcF', 'hvac'],
            ['statScorHvcV', 'hvac'],
            ['statScorHvcC', 'hvac']
            ];
        foreach ($this->statScoreSets as $set) {
            $this->v[$set[0]] = new ScoreStats([$set[1]]);
            $this->v[$set[0]]->loadMap();
        }
        $this->v["bldDats"] = [];
        $this->v["statEnv"] = new ScoreStatEnvs;
        $this->v["statEnv"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]);
        $this->v["statEnv"]->addFilt('area', 'Growth Stage', $this->v["sfAreasAlt"][0], $this->v["sfAreasAlt"][1]);
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
            $this->v["statEnv"]->addDataType('bld' . $bld->DefID, $bld->DefValue);
            $this->v["bldDats"][] = 'bld' . $bld->DefID;
        }
        $this->v["statEnv"]->loadMap();
        $this->v["statEnv"]->initEnvs();
        
        $this->v["statLeads"] = new SurvStatsGraph;
        $this->v["statLeads"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]);
        $this->v["statLeads"]->addDataType('count', 'Total count of records');
        $this->v["statLeads"]->addDataType('nonfarm', 'Reported electricity consumption includes non-farm usage, 
            such as a residential dwelling or unrelated business');
        $this->v["statLeads"]->addDataType('upgrade', 
            'Considering a lighting, HVAC and/or dehumidification upgrade over the next 12 months');
        $this->v["statLeads"]->addDataType('incent',  'Have used incentives from a utility program');
        $this->v["statLeads"]->addDataType('contact', 'Would like to be contacted by their utility to 
            learn more about incentives for which they may be eligible');
        $this->v["statLeads"]->loadMap();
        
        if ($allScores->isNotEmpty()) {
            foreach ($allScores as $cnt => $ps) {
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', '>', 0)
                    ->get();
                if ($areas->isNotEmpty()) {
                    if ($ps->PsCharacterize == 144) {
                        $this->v["statScorSize"]->applyScoreFilts($ps, $this->getFlowerSize($areas));
                        $this->v["statScorAuto"]->applyScoreFilts($ps, $this->getFlowerSize($areas));
                        $this->v["statScorVert"]->applyScoreFilts($ps, $this->getFlowerSize($areas));
                    }
                    foreach ($areas as $area) {
                        $areaType = $this->motherToClone($area->PsAreaType);
                        $this->v["statEnv"]->addDataEnvs($ps, $areaType, $area->PsAreaID);
                        if ($ps->PsCharacterize == 144) {
                            if ($this->v["areaTypes"]["Flower"] == $area->PsAreaType) {
                                $this->v["statScorHvcF"]->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                            } else if ($this->v["areaTypes"]["Veg"] == $area->PsAreaType) {
                                $this->v["statScorHvcV"]->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                            } else if ($this->v["areaTypes"]["Clone"] == $area->PsAreaType) {
                                $this->v["statScorHvcC"]->addRecFilt('hvac', $area->PsAreaHvacType, $ps->PsID);
                            }
                        }
                    }
                    if ($ps->PsCharacterize == 144) {
                        foreach ($this->statScoreSets as $set) {
                            $this->v[$set[0]]->addScoreData($ps);
                            $this->v[$set[0]]->resetRecFilt();
                        }
                    }
                    $this->v["statLeads"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                    $this->v["statLeads"]->addRecDat('count', 1, $ps->PsID);
                    if (isset($ps->PsEnergyNonFarm) && intVal($ps->PsEnergyNonFarm) == 1) {
                        $this->v["statLeads"]->addRecDat('nonfarm', 1, $ps->PsID);
                    }
                    if (isset($ps->PsConsiderUpgrade) && intVal($ps->PsConsiderUpgrade) == 1) {
                        $this->v["statLeads"]->addRecDat('upgrade', 1, $ps->PsID);
                    }
                    if (isset($ps->PsIncentiveUsed) && intVal($ps->PsIncentiveUsed) == 1) {
                        $this->v["statLeads"]->addRecDat('incent', 1, $ps->PsID);
                    }
                    if (isset($ps->PsIncentiveWants) && intVal($ps->PsIncentiveWants) == 1) {
                        $this->v["statLeads"]->addRecDat('contact', 1, $ps->PsID);
                    }
                    $this->v["statLeads"]->resetRecFilt();
                }
                unset($areas);
            }
        }
        unset($allScores);
        foreach ($this->statScoreSets as $set) {
            $this->v[$set[0]]->calcStats();
        }
        $this->v["statScorHvcF"]->addCurrFilt('farm', 144);
        $this->v["statScorHvcV"]->addCurrFilt('farm', 144);
        $this->v["statScorHvcC"]->addCurrFilt('farm', 144);
        $this->v["statLeads"]->calcStats();
        $this->v["statEnv"]->calcStats();
        $this->v["statEnv"]->calcBlds();
        return view('vendor.cannabisscore.nodes.853-founders-circle-report', $this->v)->render();
    }
}