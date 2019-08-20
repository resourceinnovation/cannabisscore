<?php
/**
  * ScoreReportLighting generates the entire a breakdown of different lighting, using ScoreStats and SurvStats.
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
use App\Models\RIIPSLightTypes;
use SurvLoop\Controllers\Stats\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreReportStats;

class ScoreReportLighting extends ScoreReportStats
{
    public $statScoreSets = [];
    
    public function getLightingReport($nID)
    {
        $this->initClimateFilts();

        if ($GLOBALS["SL"]->REQ->has('rawCalcs')) {
            return $this->printLightingRawCalcs($nID);
        }

        $this->statScoreSets = [
            ['statScorLgtF144', ['flw-lgty']],
            ['statScorLgtV144', ['veg-lgty']],
            ['statScorLgtC144', ['cln-lgty']],
            ['statScorLgtM144', ['mth-lgty']],
            ['statScorLgtF145', ['flw-lgty']],
            ['statScorLgtV145', ['veg-lgty']],
            ['statScorLgtC145', ['cln-lgty']],
            ['statScorLgtM145', ['mth-lgty']],
            ['statScorLgtF143', ['flw-lgty']],
            ['statScorLgtV143', ['veg-lgty']],
            ['statScorLgtC143', ['cln-lgty']],
            ['statScorLgtM143', ['mth-lgty']]
        ];
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]] = new ScoreStats($set[1]);
            $this->v["scoreSets"][$set[0]]->loadMap();
        }

        $this->v["lgtSqft"] = [];
        foreach ($this->v["sfFarms"][0] as $i => $farmDef) {
            $this->v["lgtSqft"][$farmDef] = [];
            foreach ($this->v["sfAreasGrow"][0] as $i => $areaDef) {
                $this->v["lgtSqft"][$farmDef][$areaDef] = [ 0, 0, [] ];
                foreach ($this->v["sfLgts"][0] as $i => $lgtDef) {
                    $this->v["lgtSqft"][$farmDef][$areaDef][2][$lgtDef] = [ 0, [] ];
                }
            }
        }

        $this->initSearcher(1);
        $this->searcher->loadAllScoresPublic("->where('PsEfficLighting', '>', 0.00001)");
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', '>', 0)
                    ->where('PsAreaType', 'NOT LIKE', 163)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $setFlt = "";
                        if ($this->v["areaTypes"]["Flower"] == $area->PsAreaType) {
                            $set = "statScorLgtF" . $ps->PsCharacterize;
                        } elseif ($this->v["areaTypes"]["Veg"] == $area->PsAreaType) {
                            $set = "statScorLgtV" . $ps->PsCharacterize;
                        } elseif ($this->v["areaTypes"]["Clone"] == $area->PsAreaType) {
                            $set = "statScorLgtC" . $ps->PsCharacterize;
                        } elseif ($this->v["areaTypes"]["Mother"] == $area->PsAreaType) {
                            $set = "statScorLgtM" . $ps->PsCharacterize;
                        }
                        $lgts = RIIPSLightTypes::where('PsLgTypAreaID', $area->PsAreaID)
                            ->get();
                        if ($lgts->isNotEmpty()) {
                            foreach ($lgts as $lgt) {
                                if (isset($lgt->PsLgTypLight) && intVal($lgt->PsLgTypLight) > 0
                                    && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0) {
                                    $this->v["scoreSets"][$set]->addRecFilt('lgt' . $lgt->PsLgTypLight, 1, $ps->PsID);
                                }
                            }
                        }
                        $this->v["scoreSets"][$set]->addScoreData($ps);
                        $this->v["scoreSets"][$set]->resetRecFilt();
                    }
                }
                unset($areas);
            }
        }
        unset($allScores);
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]]->calcStats();
        }

        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.983-lighting-report-excel', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $this->getExportFilename() . '.xls');
            exit;
        }

        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view('vendor.cannabisscore.nodes.983-lighting-report', $this->v)->render();
    }

    protected function printLightingRawCalcs($nID)
    {
        $this->v["tbl"] = [];
        $this->initSearcher(1); 
        $this->searcher->loadAllScoresPublic("->where('PsEfficLighting', '>', 0.00001)");
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        $blank = '<span class="slGrey">-</span>';
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $row = [
                    '<a href="/calculated/u-' . $ps->PsID . '" target="_blank">#'
                        . $ps->PsID . '</a>',
                    $ps->PsState,
                    str_replace('Greenhouse/Hybrid/Mixed Light', 'Greenhouse', 
                        $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', 
                        $ps->PsCharacterize)),
                    (($ps->PsLightingError > 0) ? '<span class="red">' . $ps->PsLightingError . '</span>'
                            : '<span class="slGrey">-</span>'),
                    round($ps->PsEfficOverSimilar) 
                        . $GLOBALS["SL"]->numSupscript(round($ps->PsEfficOverSimilar)),
                    (($ps->PsEfficLighting > 0.00001) 
                        ? '<nobr>' . number_format($ps->PsEfficLighting)
                        . ' <span class="slGrey fPerc66">W/SqFt</span></nobr>' : $blank)
                ];
                $totSqFt = 0;
                $areaCols = $areaSqFtEffic[] = $areaSqFtPercs[] = $areaSqFtWeighted[] = [];
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', '>', 0)
                    ->where('PsAreaType', 'NOT LIKE', 163)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $totSqFt += intVal($area->PsAreaSize);
                        $areaCols[$area->PsAreaType] = [
                            (($area->PsAreaHasStage == 1) ? 'Y' : 'N'),
                            (($area->PsAreaLgtArtif == 1) ? 'Y' : 'N'),
                            $area->PsAreaSize,
                            '<nobr>' . number_format($area->PsAreaCalcWatts) 
                                . ' <span class="slGrey fPerc66">W</span></nobr>',
                            ''
                        ];
                        $areaSqFtEffic[$area->PsAreaType] = $area->PsAreaLightingEffic;
                        $lgts = RIIPSLightTypes::where('PsLgTypAreaID', $area->PsAreaID)
                            ->get();
                        if ($lgts->isNotEmpty()) {
                            foreach ($lgts as $i => $lgt) {
                                if (isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0 
                                    && isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) {
                                    $areaCols[$area->PsAreaType][4] 
                                        .= ((trim($areaCols[$area->PsAreaType][4]) != '') ? '<br />' : '')
                                        . '<nobr>' . $lgt->PsLgTypCount . ' * ' 
                                        . number_format($lgt->PsLgTypWattage) 
                                        . '<span class="slGrey fPerc66">W</span> ' 
                                        . ((intVal($lgt->PsLgTypLight) > 0) 
                                            ? $GLOBALS["SL"]->def->getVal('PowerScore Light Types', 
                                                $lgt->PsLgTypLight) 
                                            : '') . '</nobr>';
                                }
                            }
                        }
                    }
                }

                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    $areaSqFtPercs[$areaType] = (($totSqFt > 0) ? ($areaCols[$areaType][2]/$totSqFt) : 0);
                    $areaSqFtWeighted[$areaType] = $areaSqFtPercs[$areaType]*$areaSqFtEffic[$areaType];
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    $row[] = (($areaSqFtWeighted[$areaType] > 0.00001) 
                        ? '<nobr>' . number_format($areaSqFtWeighted[$areaType])
                        . ' <span class="slGrey fPerc66">W/SqFt</span></nobr>' : $blank);
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    $row[] = '<nobr>' . round(100*$areaSqFtPercs[$areaType]) 
                        . '<span class="slGrey fPerc66">%</span></nobr>';
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    $row[] = (($areaSqFtEffic[$areaType] > 0.00001) 
                        ? '<nobr>' . number_format($areaSqFtEffic[$areaType])
                        . ' <span class="slGrey fPerc66">W/SqFt</span></nobr>' : $blank);
                }
                foreach ($this->v["sfAreasGrow"][0] as $a => $areaType) {
                    foreach ($areaCols[$areaType] as $c => $col) {
                        if ($c == 2) {
                            if ($a == 3) {
                                $row[] = $GLOBALS["SL"]->def->getVal('PowerScore Mother Location', $ps->PsMotherLoc);
                            }
                            $row[] = '<nobr>' . number_format($col) 
                                . ' <span class="slGrey fPerc66">SqFt</span></nobr>';
                        } else {
                            $row[] = $col;
                        }
                    }
                }
                $this->v["tbl"][] = $row;
            }
        }

        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.983-lighting-raw-calcs-excel', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $this->getExportFilename('_Raw') . '.xls');
            exit;
        }

        return view('vendor.cannabisscore.nodes.983-lighting-raw-calcs', $this->v)->render();
    }

    protected function getExportFilename($extra = '')
    {
        return 'PowerScore_Averages-Lighting' . $extra
            . ((trim($this->v["fltStateClim"]) != '') 
                ? '-' . str_replace(' ', '_', $this->v["fltStateClim"]) : '')
            . ((isset($this->searcher->v["fltNoNWPCC"]) && trim($this->searcher->v["fltNoNWPCC"]) != '')
                ? '-No_NWPCC' : '')
            . ((isset($this->searcher->v["fltNoLgtError"]) && trim($this->searcher->v["fltNoLgtError"]) != '')
                ? '-No_Obvious_Lighting_Errors' : '')
            . '-' . date("ymd");
    }


}