<?php
/**
  * ScoreReportLighting generates the entire a breakdown of different lighting, using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.3
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use SurvLoop\Controllers\Stats\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreReportStats;

class ScoreReportLighting extends ScoreReportStats
{
    public $statScoreSets = [];
    
    public function getLightingReport($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('rawCalcs')) {
            echo '<script type="text/javascript"> setTimeout("'
                . 'window.location=\'/dash/raw-lighting-calculations\''
                . '", 1); </script>';
        }
        $this->initClimateFilts();
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
                $this->v["lgtSqft"][$farmDef][$areaDef] = [
                    0, 
                    0, 
                    [] 
                ];
                foreach ($this->v["sfLgts"][0] as $i => $lgtDef) {
                    $this->v["lgtSqft"][$farmDef][$areaDef][2][$lgtDef] = [
                        0, 
                        [] 
                    ];
                }
            }
        }

        $this->initSearcher(1);
        if ($GLOBALS["SL"]->x["partnerLevel"] > 2) {
            $this->searcher->v["fltPartner"] = 0;
        }
        $qry = "->where('ps_effic_lighting', '>', 0.000001)"
            . "->where('ps_effic_lighting_status', '=', " 
            . $this->v["psComplete"] . ")";
        $this->searcher->loadAllScoresPublic($qry);
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->whereIn('ps_area_type', [
                        $this->v["areaTypes"]["Flower"],
                        $this->v["areaTypes"]["Veg"],
                        $this->v["areaTypes"]["Clone"],
                        $this->v["areaTypes"]["Mother"]
                    ])
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $set = "statScorLgt";
                        switch ($area->ps_area_type) {
                            case $this->v["areaTypes"]["Flower"]:
                                $set .= "F"; break;
                            case $this->v["areaTypes"]["Veg"]:
                                $set .= "V"; break;
                            case $this->v["areaTypes"]["Clone"]:
                                $set .= "C"; break;
                            case $this->v["areaTypes"]["Mother"]:
                                $set .= "M"; break;
                        }
                        $set .= $ps->ps_characterize;
                        if (isset($this->v["scoreSets"][$set])) {
                            $lgts = RIIPsLightTypes::where(
                                'ps_lg_typ_area_id', $area->ps_area_id)
                                ->get();
                            if ($lgts->isNotEmpty()) {
                                foreach ($lgts as $lgt) {
                                    if (isset($lgt->ps_lg_typ_light) 
                                        && intVal($lgt->ps_lg_typ_light) > 0
                                        && isset($lgt->ps_lg_typ_count) 
                                        && intVal($lgt->ps_lg_typ_count) > 0) {
                                        $this->v["scoreSets"][$set]->addRecFilt(
                                            'lgt' . $lgt->ps_lg_typ_light, 
                                            1, 
                                            $ps->ps_id
                                        );
                                    }
                                }
                            }
                            $this->v["scoreSets"][$set]->addScoreData($ps);
                            $this->v["scoreSets"][$set]->resetRecFilt();
                        }
                    }
                }
                unset($areas);
            }
        }
        unset($allScores);
        foreach ($this->statScoreSets as $set) {
            $this->v["scoreSets"][$set[0]]->calcStats();
        }
        $this->v["reportTitles"] = [
            144 => 'a. Indoor', 
            145 => 'b. Greenhouse/Mixed', 
            143 => 'c. Outdoor'
        ];

        if ($GLOBALS["SL"]->REQ->has('excel') 
            && intVal($GLOBALS["SL"]->REQ->excel) == 1
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.983-lighting-report-excel', 
                $this->v
            )->render();
            $excelFile = $this->getExportFilename() . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool(
                $innerTable, 
                $excelFile
            );
            exit;
        }
        $GLOBALS["SL"]->x["needsCharts"] = true;
        $ret = view(
            'vendor.cannabisscore.nodes.983-lighting-report', 
            $this->v
        )->render();
        //$GLOBALS["SL"]->x["baseUrl"] = '/dash/compare-powerscores';
        if (isset($GLOBALS["SL"]->x["partnerID"]) 
            && intVal($GLOBALS["SL"]->x["partnerID"]) > 0) {
            //$GLOBALS["SL"]->x["baseUrl"] = '/dash/partner-compare-ranked-powerscores';
            $ret = str_replace(
                'compare-powerscores', 
                'partner-compare-official-powerscores', 
                str_replace(
                    'partner-compare-powerscores', 
                    'partner-compare-official-powerscores', 
                    $ret
                )
            );
        }
        return $ret;
    }

    public function printLightingRawCalcs($nID)
    {
        $this->initClimateFilts();
        $this->initSearcher(1); 
        $this->searcher->loadAllScoresPublic(
            "->where('ps_effic_lighting', '>', 0.00001)"
            . "->where('ps_effic_lighting_status', '=', " 
            . $this->v["psComplete"] . ")"
        );
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        $blank = '<span class="slGrey">-</span>';
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $id = '<a href="/calculated/u-' . $ps->ps_id 
                    . '" target="_blank">#' . $ps->ps_id . '</a>';
                $farmType = $GLOBALS["SL"]->def->getVal(
                    'PowerScore Farm Types', 
                    $ps->ps_characterize
                );
                $farmType = str_replace(
                    'Greenhouse/Hybrid/Mixed Light', 
                    'Greenhouse', 
                    $farmType
                );
                $lgtErr = '<span class="slGrey">-</span>';
                if ($ps->ps_lighting_error > 0) {
                    $lgtErr = '<span class="red">' . $ps->ps_lighting_error . '</span>';
                }
                $lgtRank = round($ps->ps_effic_over_similar) 
                    . $GLOBALS["SL"]->numSupscript(round($ps->ps_effic_over_similar));
                $lgtScore = '0';
                if ($ps->ps_effic_lighting > 0.00001) {
                    $lgtScore = $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3);
                } elseif ($ps->ps_effic_lighting > 0) {
                    $lgtScore = '<span class="slGrey">-</span>';
                }
                $row = [
                    $id,
                    $ps->ps_state,
                    $farmType,
                    $lgtErr,
                    $lgtRank,
                    $lgtScore,
                    $GLOBALS["SL"]->sigFigs($ps->ps_lighting_power_density, 3)
                ];
                $totSqFt = 0;
                $areaCols = $areaSqFtEffic[] = $areaSqFtPercs[] = $areaSqFtWeighted[] = [];
                $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->where('ps_area_type', '>', 0)
                    ->where('ps_area_type', 'NOT LIKE', 163)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $totSqFt += intVal($area->ps_area_size);
                        $areaCols[$area->ps_area_type] = [
                            (($area->ps_area_has_stage == 1) ? 'Y' : 'N'),
                            (($area->ps_area_lgt_artif == 1) ? 'Y' : 'N'),
                            $area->ps_area_size,
                            number_format($area->ps_area_calc_watts),
                            number_format($area->ps_area_sq_ft_per_fix2),
                            $GLOBALS["SL"]->sigFigs($area->ps_area_lpd, 3),
                            '',
                            '',
                            '',
                            ''
                        ];
                        $areaSqFtEffic[$area->ps_area_type] = $area->ps_area_lighting_effic;
                        $lgts = RIIPsLightTypes::where('ps_lg_typ_area_id', $area->ps_area_id)
                            ->get();
                        if ($lgts->isNotEmpty()) {
                            $lgtInd = 0;
                            foreach ($lgts as $i => $lgt) {
                                if ($lgtInd < 3 && isset($lgt->ps_lg_typ_count) 
                                    && intVal($lgt->ps_lg_typ_count) > 0 
                                    && isset($lgt->ps_lg_typ_wattage) 
                                    && intVal($lgt->ps_lg_typ_wattage) > 0) {
                                    $areaCols[$area->ps_area_type][(6+$lgtInd)] 
                                        .= '<nobr>' . $lgt->ps_lg_typ_count . ' * ' 
                                        . number_format($lgt->ps_lg_typ_wattage) 
                                        . '<span class="slGrey fPerc66">W</span> ' 
                                        . ((intVal($lgt->ps_lg_typ_light) > 0) 
                                            ? $GLOBALS["SL"]->def->getVal(
                                                'PowerScore Light Types', 
                                                $lgt->ps_lg_typ_light
                                            ) : '') 
                                        . '</nobr>';
                                    $lgtInd++;
                                }
                            }
                        }
                    }
                }

                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    $areaSqFtPercs[$areaType] = 0;
                    if ($totSqFt > 0 && isset($areaCols[$areaType])) {
                        $areaSqFtPercs[$areaType] = $areaCols[$areaType][2]/$totSqFt;
                    }
                    if (isset($areaSqFtPercs[$areaType])
                        && isset($areaSqFtEffic[$areaType])) {
                        $areaSqFtWeighted[$areaType] = $areaSqFtPercs[$areaType]
                            *$areaSqFtEffic[$areaType];
                    }
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    if (isset($areaSqFtWeighted[$areaType])
                        && $areaSqFtWeighted[$areaType] > 0.00001) {
                        $row[] = number_format($areaSqFtWeighted[$areaType]);
                    } else {
                        $row[] = $blank;
                    }
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    if (isset($areaSqFtPercs[$areaType])) {
                        $row[] = '<nobr>' . round(100*$areaSqFtPercs[$areaType]) 
                            . '<span class="slGrey fPerc66">%</span></nobr>';
                    } else {
                        $row[] = '<span class="slGrey fPerc66">-</span>';
                    }
                }
                foreach ($this->v["sfAreasGrow"][0] as $areaType) {
                    if (isset($areaSqFtEffic[$areaType])
                        && $areaSqFtEffic[$areaType] > 0.00001) {
                        $row[] = number_format($areaSqFtEffic[$areaType]);
                    } else {
                        $row[] = $blank;
                    }
                }
                foreach ($this->v["sfAreasGrow"][0] as $a => $areaType) {
                    if (isset($areaCols[$areaType])) {
                        foreach ($areaCols[$areaType] as $c => $col) {
                            if ($c == 2) {
                                if ($a == 3) {
                                    $row[] = $GLOBALS["SL"]->def->getVal(
                                        'PowerScore Mother Location', 
                                        $ps->ps_mother_loc
                                    );
                                }
                                $row[] = number_format($col);
                            } else {
                                $row[] = $col;
                            }
                        }
                    }
                }
                $this->v["tbl"][] = $row;

            }
        }

        if ($GLOBALS["SL"]->REQ->has('excel') 
            && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.983-lighting-raw-calcs-excel', 
                $this->v
            )->render();
            $GLOBALS["SL"]->exportExcelOldSchool(
                $innerTable, 
                $this->getExportFilename('_Raw') . '.xls'
            );
            exit;
        }

        return view(
            'vendor.cannabisscore.nodes.983-lighting-raw-calcs', 
            $this->v
        )->render();
    }

    protected function getExportFilename($extra = '')
    {
        $ret = 'PowerScore_Averages-Lighting' . $extra;
        if (trim($this->v["fltStateClim"]) != '') {
            $ret .= '-' . str_replace(' ', '_', $this->v["fltStateClim"]);
        }
        if (isset($this->searcher->v["fltNoNWPCC"]) 
            && trim($this->searcher->v["fltNoNWPCC"]) != '') {
            $ret .= '-No_NWPCC';
        }
        if (isset($this->searcher->v["fltNoLgtError"]) 
            && trim($this->searcher->v["fltNoLgtError"]) != '') {
            $ret .= '-No_Obvious_Lighting_Errors';
        }
        return $ret . '-' . date("ymd");
    }


}
