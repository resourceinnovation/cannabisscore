<?php
/**
  * ScoreReportFound generates the entire PowerScore Resource Benchmarking Report, 
  * using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use RockHopSoft\Survloop\Controllers\Stats\SurvStatsGraph;
use ResourceInnovation\CannabisScore\Controllers\ScoreStats;
use ResourceInnovation\CannabisScore\Controllers\ScoreReportStats;

class ScoreReportFound extends ScoreReportStats
{
    public $statScoreSets = [];
    
    public function getFoundReport($nID, $allScores)
    {
        $this->prepStatFilts();
        $this->statScoreSets = [
            ['statScorSize144', 'size'],
            ['statScorSize145', 'size'],
            ['statScorSize143', 'size'],
            ['statScorAuto',    'auto'],
            ['statScorVert',    'vert']
        ];
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]] = new ScoreStats([$set[1]]);
            $this->v["scoreSets"][$set[0]]->loadMap();
        }
        $this->v["vertDense"] = [
            [ 0, [] ],
            [ 0, [] ],
            0
        ];

        $this->v["bldDats"] = [];
        $this->v["statEnv"] = new ScoreStatEnvs;
        $this->v["statEnv"]->addFilt(
            'farm', 
            'Farm Type', 
            $this->v["sfFarms"][0], 
            $this->v["sfFarms"][1]
        );
        $this->v["statEnv"]->addFilt(
            'area', 
            'Growth Stage', 
            $this->v["sfAreasAlt"][0], 
            $this->v["sfAreasAlt"][1]
        );
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
            $this->v["statEnv"]->addDataType('bld' . $bld->def_id, $bld->def_value);
            $this->v["bldDats"][] = 'bld' . $bld->def_id;
        }
        $this->v["statEnv"]->loadMap();
        $this->v["statEnv"]->initEnvs();
        
        $this->v["statLeads"] = new SurvStatsGraph;
        $this->v["statLeads"]->addFilt(
            'farm', 
            'Farm Type', 
            $this->v["sfFarms"][0], 
            $this->v["sfFarms"][1]
        );
        $this->v["statLeads"]->addDataType(
            'count', 
            'Total count of records'
        );
        $this->v["statLeads"]->addDataType(
            'nonfarm', 
            'Reported electricity consumption includes non-farm usage, 
            such as a residential dwelling or unrelated business'
        );
        $this->v["statLeads"]->addDataType(
            'upgrade', 
            'Considering a lighting, HVAC and/or dehumidification 
            upgrade over the next 12 months');
        $this->v["statLeads"]->addDataType(
            'incent',  
            'Have used incentives from a utility program'
        );
        $this->v["statLeads"]->addDataType(
            'contact', 
            'Would like to be contacted by their utility to 
            learn more about incentives for which they may be eligible'
        );
        $this->v["statLeads"]->loadMap();
        
        if ($allScores->isNotEmpty()) {
            foreach ($allScores as $cnt => $ps) {
                $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->where('ps_area_type', '>', 0)
                    ->get();
                if ($areas->isNotEmpty()) {
                    $this->v["scoreSets"]["statScorSize" . $ps->ps_characterize]
                        ->applyScoreFilts($ps, $this->getFlowerSize($areas));
                    if ($ps->ps_characterize == 144) {
                        $this->v["scoreSets"]["statScorAuto"]
                            ->applyScoreFilts($ps, $this->getFlowerSize($areas));
                        $this->v["scoreSets"]["statScorVert"]
                            ->applyScoreFilts($ps, $this->getFlowerSize($areas));
                    }
                    foreach ($areas as $area) {
                        $areaType = $this->motherToClone($area->ps_area_type);
                        $this->v["statEnv"]->addDataEnvs($ps, $areaType, $area->ps_area_id);
                    }
                    foreach ($this->statScoreSets as $set) {
                        if (in_array($set[0], ['statScorAuto', 'statScorVert'])) {
                            if ($ps->ps_characterize == 144) {
                                $this->v["scoreSets"][$set[0]]->addScoreData($ps);
                                $this->v["scoreSets"][$set[0]]->resetRecFilt();
                            }
                        } elseif (intVal(substr($set[0], strlen($set[0])-3)) 
                            == $ps->ps_characterize) {
                            $this->v["scoreSets"][$set[0]]->addScoreData($ps);
                            $this->v["scoreSets"][$set[0]]->resetRecFilt();
                        }
                    }

                    if ($ps->ps_characterize == 144 
                        && isset($ps->ps_effic_production) 
                        && $ps->ps_effic_production > 0) {
                        $vert = 0;
                        if (isset($ps->ps_vertical_stack)) {
                            $vert = intVal($ps->ps_vertical_stack);
                        }
                        $area = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                            ->where('ps_area_type', 
                                $GLOBALS["CUST"]->getAreaTypeFromNick('Flower'))
                            ->first();
                        if ($area 
                            && isset($area->ps_area_size) 
                            && $area->ps_area_size > 0) {
                            $this->v["vertDense"][$vert][1][] = $ps->ps_effic_production
                                /$area->ps_area_size;
                        }
                    }

                    $this->v["statLeads"]->addRecFilt('farm', $ps->ps_characterize, $ps->ps_id);
                    $this->v["statLeads"]->addRecDat('count', 1, $ps->ps_id);
                    if (isset($ps->ps_energy_non_farm) 
                        && intVal($ps->ps_energy_non_farm) == 1) {
                        $this->v["statLeads"]->addRecDat('nonfarm', 1, $ps->ps_id);
                    }
                    if (isset($ps->ps_consider_upgrade) 
                        && intVal($ps->ps_consider_upgrade) == 1) {
                        $this->v["statLeads"]->addRecDat('upgrade', 1, $ps->ps_id);
                    }
                    if (isset($ps->ps_incentive_used) 
                        && intVal($ps->ps_incentive_used) == 1) {
                        $this->v["statLeads"]->addRecDat('incent', 1, $ps->ps_id);
                    }
                    if (isset($ps->ps_incentive_wants) 
                        && intVal($ps->ps_incentive_wants) == 1) {
                        $this->v["statLeads"]->addRecDat('contact', 1, $ps->ps_id);
                    }
                    $this->v["statLeads"]->resetRecFilt();
                }
                unset($areas);
            }
        }
        unset($allScores);
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]]->calcStats();
        }
        $this->v["statLeads"]->calcStats();
        $this->v["statEnv"]->calcStats();
        $this->v["statEnv"]->calcBlds();

        foreach ([0, 1] as $vert) {
            if (sizeof($this->v["vertDense"][$vert][1]) > 0) {
                foreach ($this->v["vertDense"][$vert][1] as $val) {
                    $this->v["vertDense"][$vert][0] += $val;
                    $this->v["vertDense"][2] += $val;
                }
                $this->v["vertDense"][$vert][0] = $this->v["vertDense"][$vert][0]
                    /sizeof($this->v["vertDense"][$vert][1]);
            }
        }
        $cnt = sizeof($this->v["vertDense"][0][1])+sizeof($this->v["vertDense"][1][1]);
        if ($cnt > 0) {
            $this->v["vertDense"][2] = $this->v["vertDense"][2]/$cnt;
        }
        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view(
            'vendor.cannabisscore.nodes.853-founders-circle-report', 
            $this->v
        )->render();
    }
}