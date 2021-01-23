<?php
/**
  * ScoreAdminMisc is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class handles the custom needs of printing an individual PowerScore's report.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.4
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use App\Models\RIIUserPsPerms;
use App\Models\RIIUserCompanies;
use RockHopSoft\Survloop\Controllers\Globals\Globals;
use RockHopSoft\Survloop\Controllers\Tree\Report\ReportMonthly;
use ResourceInnovation\CannabisScore\Controllers\ScoreCalcsPrint;

class ScorePrintReport extends ScoreCalcsPrint
{
    public function printPreviewReport($isAdmin = false)
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $this->chkPowerScoreRanked();
            return view(
                'vendor.cannabisscore.powerscore-report-preview', 
                [
                    "uID"      => $this->v["uID"],
                    "sessData" => $this->sessData->dataSets
                ]
            )->render();
        }
        return '';
    }
    
    public function printPsRankingFilters($nID)
    {
        if (Auth::user() && Auth::user()->hasRole('partner')) {
            $GLOBALS["SL"]->x["partnerVersion"] = true;
        }
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->searcher->v["nID"] = $nID;
        $ret = view(
            'vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v
        )->render();
        return '<div id="scoreRankFiltWrap" '
            . 'style="margin: 20px -15px 0px -15px;">'
            . '<h5 class="mT5">Compare to other farms</h5>'
            . '<div class="pT5"></div>' . $ret . '</div>';
    }

    protected function printReportForCompetition($nID)
    {
        if (isset($this->sessData->dataSets["ps_for_cup"])) {
            $deetVal = '';
            foreach ($this->sessData->dataSets["ps_for_cup"] as $i => $cup) {
                if (isset($cup->ps_cup_cup_id) 
                    && intVal($cup->ps_cup_cup_id) > 0) {
                    $defSet = 'PowerScore Competitions';
                    $deetVal .= (($i > 0) ? ', ' : '') 
                        . $GLOBALS["SL"]->def->getVal($defSet, $cup->ps_cup_cup_id);
                }
            }
            return [ 'Competition', $deetVal, $nID ];
        }
        return [];
    }

    protected function printReportGrowingYear($nID)
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_year)) {
                return [ 'Growing Year', $ps->ps_year, $nID ];
            }
        }
        return [];
    }

    protected function printReportUtilRef($nID)
    {
        $this->prepUtilityRefTitle();
        $ret = view(
            'vendor.cannabisscore.nodes.508-utility-referral-title', 
            $this->v
        )->render();
        return $ret;
    }

    protected function printReportRoomArea($nID)
    {
        $ret = '';
        $roomID = $this->sessData->getLatestDataBranchID();
        $lnks = $this->sessData->getChildRows(
            'ps_growing_rooms', 
            $roomID, 
            'ps_link_room_area'
        );
        if (sizeof($lnks) > 0) {
            foreach ($lnks as $i => $lnk) {
                $area = $this->sessData->getRowById(
                    'ps_areas', 
                    $lnk->ps_lnk_rm_ar_area_id
                );
                if ($area 
                    && isset($area->ps_area_type) 
                    && intVal($area->ps_area_type) > 0) {
                    $areaName = $this->getAreaAbbr($area->ps_area_type);
                    $areaName = str_replace('Clone', 'Clone or Mother', $areaName);
                    $ret .= (($ret != '') ? ', ' : '') . $areaName;
                }
            }
        }
        if (strpos($ret, ',') !== false) {
            return [ 'Stages of Plant Growth', $ret ];
        }
        return [ 'Stage of Plant Growth', $ret ];
    }
    
    public function customPrint490($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('isPreview')) {
            return '<style> #blockWrap492, #blockWrap501, #blockWrap727 '
                . '{ display: none; } </style>';
        }
        $ret = '';
        $this->v["nID"] = $nID;
        $GLOBALS["SL"]->addHshoo('#');
        if ($GLOBALS["SL"]->REQ->has('refresh')) {
            $ret .= view(
                'vendor.cannabisscore.nodes.490-report-calculations-top-refresh', 
                [ "psid" => $this->coreID ]
            )->render();
        } else {
            $ret .= $this->printReport490();
        }
        return ' <!-- customPrint490.start --> ' 
            . $ret . ' <!-- customPrint490.end --> ';
    }
    
    public function printReport490()
    {
        if ($GLOBALS["SL"]->REQ->has('step') 
            && $GLOBALS["SL"]->REQ->has('postAction')
            && trim($GLOBALS["SL"]->REQ->get('postAction')) != '') {
            return $this->redir($GLOBALS["SL"]->REQ->get('postAction'), true);
        }
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->getAllReportCalcs();
        if (!$GLOBALS["SL"]->REQ->has('fltFarm')) {
            $this->searcher->v["fltFarm"] = $this->sessData
                ->dataSets["powerscore"][0]->ps_characterize;
            $this->searcher->searchFiltsURLXtra();
        }
        $this->searcher->v["nID"] = 490;
        $this->v["isPast"] = ($this->sessData->dataSets["powerscore"][0]->ps_time_type 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        $this->v["usr"] = null;
        if (Auth::user()) {
            $this->v["usr"] = Auth::user();
        }
        $this->v["canEdit"] = $this->isPartnerStaffAdminOrOwner();

        $GLOBALS["SL"]->pageJAVA .= view(
            'vendor.cannabisscore.nodes.490-report-calculations-js',
            $this->v
        )->render();
        $ret = view(
            'vendor.cannabisscore.nodes.490-report-calculations', 
            $this->v
        )->render();
        return ' <!-- printReport490.start --> ' 
            . $ret . ' <!-- printReport490.end --> ';
    }


    
    protected function ajaxReportRefresh(Request $request)
    {
        $this->v["ajax-psid"] = -3;
        if ($GLOBALS["SL"]->REQ->has('psid') 
            && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) {
            $this->v["ajax-psid"] = intVal($GLOBALS["SL"]->REQ->get('psid'));
        }
        if ($request->has('refresh') && intVal($request->get('refresh')) == 1) {
            $this->sessData->loadData('powerscore', $this->v["ajax-psid"]);
            if (isset($this->sessData->dataSets["powerscore"]) 
                && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
                // isset($this->sessData->dataSets["powerscore"][0]->ps_email)
                $this->calcCurrSubScores();
                return view(
                    'vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', 
                    [
                        "msg"  => '<i class="slGrey">Recalculating KPIs & Scores...',
                        "psid" => $this->v["ajax-psid"]
                    ]
                )->render();
            }
            return '<div class="slGrey">Error 420: PowerScore #' 
                . $this->v["ajax-psid"] . ' Not Found</div>';
        }
        return $this->calcAllScoreRanks('report-ajax');
    }
    
    protected function ajaxFutureYields()
    {
        if (Auth::user() && Auth::user()->hasRole('partner')) {
            $GLOBALS["SL"]->x["partnerVersion"] = true;
        }
        $this->v["nID"] = 20202020;
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->getAllReportCalcs();
        $this->getSimilarStats();
        $this->loadAreaLgtTypes();
        $this->v["isPast"] = ($this->sessData->dataSets["powerscore"][0]->ps_time_type 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        $this->v["psFilters"] = view(
            'vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v
        )->render();
        return view(
            'vendor.cannabisscore.nodes.490-report-calculations', 
            $this->v
        )->render();
    }
    
    public function printFrameAnimPerc(Request $request, $perc = 0)
    {
        $size = 100;
        if ($request->has('size') && intVal($request->get('size')) > 0) {
            $size = intVal($request->get('size'));
        }
        return view(
            'vendor.cannabisscore.nodes.490-report-calculations-frame-guage', 
            [
                "perc" => $perc,
                "size" => $size
            ]
        )->render();
    }
    
    public function printFrameAnimPercMeter(Request $request, $perc = 0, $row = 0)
    {
        $width = 190;
        if ($request->has('width') && intVal($request->get('width')) > 0) {
            $width = intVal($request->get('width'));
        }
        $height = 30;
        if ($request->has('height') && intVal($request->get('height')) > 0) {
            $height = intVal($request->get('height'));
        }
        $bg = '#ebeee7';
        if ($request->has('bg') && intVal($request->get('bg')) > 0) {
            $bg = trim($request->get('bg'));
        }
        return view(
            'vendor.cannabisscore.nodes.490-report-calculations-frame-meter', 
            [
                "perc"   => $perc,
                "width"  => round($width),
                "height" => round($height),
                "bg"     => $bg
            ]
        )->render();
    }
    
    protected function ajaxScorePercentiles()
    {
        $effTypes = [
            'Overall', 
            'CatEnr', 
            'Facility', 
            'FacNon', 
            'FacAll', 
            'Production', 
            'ProdNon', 
            'ProdAll', 
            'HVAC', 
            'Lighting', 
            'CatWtr', 
            'Water', 
            'WaterProd', 
            'CatWst', 
            'Waste',
            'WasteProd'
        ];
        if (!$GLOBALS["SL"]->REQ->has('ps') 
            || intVal($GLOBALS["SL"]->REQ->get('ps')) <= 0 
            || !$GLOBALS["SL"]->REQ->has('eff') 
            || !in_array(trim($GLOBALS["SL"]->REQ->get('eff')), $effTypes)) {
            return '';
        }

        $GLOBALS["SL"]->x["indivFilters"] = true;
        $this->initSearcher();
        $this->searcher->searchResultsXtra(1);
        $this->searcher->searchFiltsURL();
        if (isset($this->searcher->v["powerscore"])
            && isset($this->searcher->v["powerscore"]->ps_id)) {
            $pastDef = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
            $this->searcher->v["isPast"] 
                = ($this->searcher->v["powerscore"]->ps_time_type == $pastDef);
            $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_filters', 
                    $this->searcher->v["urlFlts"])
                ->where('ps_rnk_psid', $this->searcher->v["powerscore"]->ps_id)
                ->first();
            if ($GLOBALS["SL"]->REQ->has('refresh') 
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall)
                || in_array(intVal($this->searcher->v["currRanks"]->ps_rnk_overall), [0, 100])) {
                $this->ajaxScorePercentilesCalcRanks();
            }

            $myFilt = '&fltFarm=' . $this->searcher->v["powerscore"]->ps_characterize;
            if ($this->searcher->v["urlFlts"] == $myFilt
                && (!isset($this->searcher->v["powerscore"]->ps_effic_over_similar)
                    || $this->searcher->v["powerscore"]->ps_effic_over_similar <= 0.000000001)) {
                $this->searcher->v["powerscore"]->ps_effic_over_similar
                    = $this->searcher->v["currRanks"]->ps_rnk_overall;
                $this->searcher->v["powerscore"]->save();
            }

            $this->searcher->v["currGuage"] = 0;
            $this->searcher->v["hasOverall"] 
                = (isset($this->searcher->v["powerscore"]->ps_effic_facility) 
                    && isset($this->searcher->v["powerscore"]->ps_effic_production) 
                    && $this->searcher->v["powerscore"]->ps_effic_facility > 0
                    && $this->searcher->v["powerscore"]->ps_effic_production > 0);
            $overRank = round($this->searcher->v["currRanks"]->ps_rnk_overall);
            if ($overRank == 0) {
                $overRank = 1;
            }
            $superscript = $GLOBALS["SL"]->numSupscript($overRank);
            $this->searcher->v["overallScoreTitle"] = '<center>'
                . '<h1 class="m0 scoreBig">' . $overRank . $superscript
                . '</h1><b>percentile</b></center>';
// number_format($ranksCache->ps_rnk_tot_cnt) }} past growing @if ($ranksCache->ps_rnk_tot_cnt > 1) years @else year @endif of
//echo '<pre>'; print_r($this->searcher->v["currRanks"]); echo '</pre>'; exit;
            $this->searcher->searchResultsXtra(1);
            $this->searcher->searchFiltsURL();
            $this->searcher->loadFiltersDesc();
            return view(
                'vendor.cannabisscore.nodes.490-report-calculations-load-all-js', 
                $this->searcher->v
            )->render();
        }
        return '';
    }
    
    protected function ajaxScorePercentilesCalcRanks()
    {
        $futDef = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future');
        if (isset($this->searcher->v["powerscore"]->ps_time_type) 
            && $this->searcher->v["powerscore"]->ps_time_type == $futDef) {
            $ranks = RIIPsRanks::where('ps_rnk_filters', '')
                ->first();
            $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
            $this->searcher->v["powerscore"]->ps_effic_overall 
                = $this->searcher->v["currRanks"]->ps_rnk_overall;
            $this->searcher->v["powerscore"]->save();
        } else {
            $urlFlts = $this->searcher->v["urlFlts"];
            $psid = $this->searcher->v["powerscore"]->ps_id;
            $this->calcAllScoreRanks();
            $urlFlts = str_replace('&fltFut=232', '', $urlFlts);
            $this->searcher->v["urlFlts"] = $urlFlts;
            if (trim($urlFlts) != '') {
                $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_psid', $psid)
                    ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                    ->first();
            } else {
                $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_psid', $psid)
                    ->where(function($query) {
                        $query->where('ps_rnk_filters', 'LIKE', '')
                              ->whereNull('ps_rnk_filters');
                    })
                    ->first();
            }
            if ($GLOBALS["SL"]->REQ->has('refresh')
                || !$this->searcher->v["currRanks"] 
                || (isset($this->searcher->v["currRanks"]->ps_rnk_overall)
                    && in_array(
                        intVal($this->searcher->v["currRanks"]->ps_rnk_overall), 
                        [0, 100]
                    ))) {
                if ($this->searcher->v["currRanks"]) {
                    RIIPsRankings::find($this->searcher->v["currRanks"]->getKey())
                        ->delete();
                }
                $ranks = RIIPsRanks::where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                    ->first();
                $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
            }
//echo '<h2>ajaxScorePercentilesCalcRanks(' . $this->searcher->v["urlFlts"] . '</h2><pre>'; print_r($this->searcher->v["currRanks"]); echo '</pre>'; exit;
        }
        return true;
    }
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function ajaxScorePercNewRank($ranks)
    {
        $ps = $this->searcher->v["powerscore"];
        RIIPsRankings::where('ps_rnk_psid', $ps->ps_id)
            ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
            ->delete();
        $currRanks = new RIIPsRankings;
        $currRanks->ps_rnk_psid = $ps->ps_id;
        $currRanks->ps_rnk_filters = $this->searcher->v["urlFlts"];
//echo '<h2>ajaxScorePercNewRank(PSID#' . $ps->ps_id . '</h2> <pre>'; print_r($ranks); echo '</pre>';
        if ($ranks && isset($ranks->ps_rnk_facility)) {
            $efficsOver = $this->basicEfficFlds();
            foreach ($efficsOver as $effic) {
                $rank = 0;
                $list = $ranks->{ 'ps_rnk_' . $effic[0] };
                if (isset($ps->{ 'ps_effic_' . $effic[0] })
                    && $ps->{ 'ps_effic_' . $effic[0] } > 0) {
                    $val = $ps->{ 'ps_effic_' . $effic[0] };
                    if ($val < 0.000001) {
                        $val = 0;
                    }
                    $isGolf = (strpos($effic[0], 'prod') === false);
                    $rank = $GLOBALS["SL"]->getArrPercentileStr($list, $val, $isGolf);
                }
                $list = $GLOBALS["SL"]->mexplode(',', $list);
                $currRanks->{ 'ps_rnk_' . $effic[0] } = $rank;
                $currRanks->{ 'ps_rnk_' . $effic[0] . '_cnt' } = sizeof($list);
            }
            $currRanks->ps_rnk_cat_energy
                = $currRanks->ps_rnk_cat_water
                = $currRanks->ps_rnk_cat_waste
                = $cntOenr
                = $cntOwtr
                = $cntOwst
                = 0;
            foreach ($efficsOver as $effic) {
                if (isset($currRanks->{ 'ps_rnk_' . $effic[0] })
                    && $currRanks->{ 'ps_rnk_' . $effic[0] } > 0) {
                    if (in_array($effic[1], ['faci', 'facN', 'prod', 'proN'])) {
                        $currRanks->ps_rnk_cat_energy 
                            += $currRanks->{ 'ps_rnk_' . $effic[0] };
                        $cntOenr++;
//echo 'adding energy ' . $effic[1] . ' += ' . $currRanks->{ 'ps_rnk_' . $effic[0] } . ' (' . $cntOenr . ')<br />';
                    } elseif (in_array($effic[1], ['watr', 'watP'])) {
                        $currRanks->ps_rnk_cat_water 
                            += $currRanks->{ 'ps_rnk_' . $effic[0] };
                        $cntOwtr++;
                    } elseif (in_array($effic[1], ['wste', 'wstP'])) {
                        $currRanks->ps_rnk_cat_waste
                            += $currRanks->{ 'ps_rnk_' . $effic[0] };
                        $cntOwst++;
                    }
                }
            }
            if ($cntOenr > 0) {
                $currRanks->ps_rnk_cat_energy 
                    = $currRanks->ps_rnk_cat_energy/$cntOenr;
                if (isset($ps->ps_dlc_bonus)) {
                    $currRanks->ps_rnk_cat_energy += $ps->ps_dlc_bonus;
                }
            }
            if ($cntOwtr > 0) {
                $currRanks->ps_rnk_cat_water
                    = $currRanks->ps_rnk_cat_water/$cntOwtr;
            }
            if ($cntOwst > 0) {
                $currRanks->ps_rnk_cat_waste
                    = $currRanks->ps_rnk_cat_waste/$cntOwst;
            }

            $currRanks->ps_rnk_cat_energy = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_cat_energy, 
                $currRanks->ps_rnk_cat_energy
            );
            $list = $GLOBALS["SL"]->mexplode(',', $ranks->ps_rnk_cat_energy);
            $currRanks->ps_rnk_cat_energy_cnt = sizeof($list);

            $currRanks->ps_rnk_cat_water = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_cat_water, 
                $currRanks->ps_rnk_cat_water
            );
            $list = $GLOBALS["SL"]->mexplode(',', $ranks->ps_rnk_cat_water);
            $currRanks->ps_rnk_cat_water_cnt = sizeof($list);

            $currRanks->ps_rnk_cat_waste = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_cat_waste, 
                $currRanks->ps_rnk_cat_waste
            );
            $list = $GLOBALS["SL"]->mexplode(',', $ranks->ps_rnk_cat_waste);
            $currRanks->ps_rnk_cat_waste_cnt = sizeof($list);

            $currRanks->ps_rnk_overall_avg = $this->calcOverallRawScore(
                $currRanks->ps_rnk_cat_energy, 
                $currRanks->ps_rnk_cat_water, 
                $currRanks->ps_rnk_cat_waste
            );
            $currRanks->ps_rnk_overall = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_overall_avg, 
                $currRanks->ps_rnk_overall_avg
            );
            $list = $GLOBALS["SL"]->mexplode(',', $ranks->ps_rnk_overall_avg);
            $currRanks->ps_rnk_tot_cnt = sizeof($list);

            $currRanks->save();
            if ($this->searcher->v["urlFlts"] 
                == '&fltFarm=' . $ps->ps_characterize) {
                $ps->ps_effic_cat_energy   = $currRanks->ps_rnk_cat_energy;
                $ps->ps_effic_cat_water    = $currRanks->ps_rnk_cat_water;
                $ps->ps_effic_cat_waste    = $currRanks->ps_rnk_cat_waste;
                $ps->ps_effic_over_similar = $currRanks->ps_rnk_overall;
                $ps->save();
                $this->searcher->v["powerscore"] = $ps;
            }
        }
        return $currRanks;
    }

    protected function excelExportMyScores($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('myExport')
            && $GLOBALS["SL"]->REQ->has('excel')
            && $GLOBALS["SL"]->REQ->has('mine')
            && intVal($GLOBALS["SL"]->REQ->myExport) == 1
            && intVal($GLOBALS["SL"]->REQ->excel) == 1
            && intVal($GLOBALS["SL"]->REQ->mine) == 1
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, 1, 1);
            $this->initSearcher();
            $this->searcher->getSearchFilts(1);
            $this->searcher->v["fltCmpl"] = 1;
            $this->searcher->loadAllScoresPublic();
            $this->searcher->processSearchFilts();
            $this->searcher->v["allPublicFiltIDs"] = $this->searcher->allPublicFiltIDs;
            $innerTable = view(
                'vendor.cannabisscore.nodes.1276-all-my-powerscores-excel', 
                $this->searcher->v
            )->render();
            $exportFile = 'My_PowerScores-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
            exit;
        }
        return true;
    }

    protected function reportPsMonths($nID)
    {
        if (!isset($this->sessData->dataSets["powerscore"])
            || !isset($this->sessData->dataSets["ps_monthly"])
            || sizeof($this->sessData->dataSets["ps_monthly"]) < 12) {
            return '<!-- no months -->';
        }
        $ps     = $this->sessData->dataSets["powerscore"][0];
        $title  = '<h4 class="mT0 mB15 slBlueDark">Monthly Breakdowns</h4>';
        $footer = '';
        $report = new ReportMonthly($this->sessData->dataSets["ps_monthly"]);
        $report->setMonthFld('ps_month_month');
        $startMonth = 1;
        if (isset($ps->ps_start_month) && intVal($ps->ps_start_month) > 0) {
            $startMonth = intVal($ps->ps_start_month);
        }
        $report->setStartMonth($startMonth);
        $report->addCol('ps_month_grams', 'Dried Flower Produced', 'Grams');
        $report->addCol('ps_month_kwh1', 'Electricity Usage', 'kWh');
        $unit = 'Therms';
        if (isset($ps->ps_unit_peak) && intVal($ps->ps_unit_peak) > 0) {
            $unit = $GLOBALS["SL"]->def->getVal(
                'Peak Electricity Measurement Unit', 
                $ps->ps_unit_peak
            );
        }
        $report->addCol('ps_month_kw', 'Peak Electricity Usage', $unit);
        $report->addCol('ps_month_kwh_renewable', 'Renewable Electricity Generated', 'kWh');
        $unit = 'Gallons';
        if ($this->isWaterInLiters()) {
            $unit = 'Liters';
        } elseif ($this->isWaterInCF()) {
            $unit = 'CF';
        } elseif ($this->isWaterInCCF()) {
            $unit = 'CCF';
        }
        $report->addCol('ps_month_water', 'Water Usage', $unit);
        $report->addCol('ps_month_water_storage_source', 'Source Water Storage', $unit);
        $report->addCol('ps_month_water_storage_recirc', 'Recirculated Water Storage', $unit);
        $unit = 'Therms';
        if (isset($ps->ps_unit_natural_gas) && intVal($ps->ps_unit_natural_gas) > 0) {
            $unit = $GLOBALS["SL"]->def->getVal('Natural Gas Units', $ps->ps_unit_natural_gas);
            if ($unit == 'CCF') {
                $footer .= '<p class="slGrey">1 Therm is around 100 CCF</p>';
            }
        }
        $report->addCol('ps_month_natural_gas', 'Natural Gas', $unit);
        $unit = 'Gasoline Gallons';
        if (isset($ps->ps_unit_generator) && intVal($ps->ps_unit_generator) > 0) {
            $unit = $GLOBALS["SL"]->def->getVal(
                'Compliance MA Generator Units', 
                $ps->ps_unit_generator
            );
            $unit = str_replace('(', '', str_replace(')', '', $unit));
        }
        $report->addCol('ps_month_generator', 'Back Up Generator', $unit);
        $unit = 'Cords';
        if (isset($ps->ps_unit_wood) && intVal($ps->ps_unit_wood) > 0) {
            $unit = $GLOBALS["SL"]->def->getVal('Biofuel Wood Units', $ps->ps_unit_wood);
        }
        $report->addCol('ps_month_biofuel_wood', 'Biofuels', $unit);
        $report->addCol('ps_month_propane', 'Propane', 'Gallons');
        $report->addCol('ps_month_fuel_oil', 'Fuel Oil', 'Gallons');
        $report->setTitle($title, $footer);

        $peakKW = 0;
        foreach ($this->sessData->dataSets["ps_monthly"] as $month) {
            if (isset($month->ps_month_kw) 
                && $peakKW < $month->ps_month_kw) {
                $peakKW = $month->ps_month_kw;
            }
        }
        $GLOBALS["SL"]->pageJAVA .= ' setTimeout("document.getElementById'
            . '(\'colTotpeakelectricityusage\').innerHTML=\'<b>' 
            . number_format($peakKW) . '</b>\'", 1); ';
        
        return $report->printTables() . '<!-- end printTables() -->';
    }

    // Override PDF filename to be used for delivery to user.
    public function customPdfFilename()
    {
        if (in_array($this->treeID, [1, 22])) {
            $GLOBALS["SL"]->x["pdfFilename"] = 'PowerScore_Report.pdf';
        } elseif (in_array($this->treeID, [71, 93])) {
            $GLOBALS["SL"]->x["pdfFilename"] 
                = 'Massachusetts_PowerScore_Comply_Report-'
                    . date('Y_m_d-H_i_s') . '.pdf';
        }
        return true;
    }

    protected function saveAdminChangeScoreFacility(Request $request)
    {

        if (!Auth::user() 
            || !Auth::user()->hasRole('administrator|staff')
            || !$request->has('psid')
            || intVal($request->get('psid')) <= 0) {
            return '<!-- No Thank You -->';
        }
        RIIUserPsPerms::where('usr_perm_psid', intVal($request->psid))
            ->where('usr_perm_company_id', '>', 0)
            ->delete();
        RIIUserPsPerms::where('usr_perm_psid', intVal($request->psid))
            ->where('usr_perm_facility_id', '>', 0)
            ->delete();
        if ($request->has('fac') && trim($request->fac) != '') {
            $type = substr(trim($request->fac), 0, 1);
            $id   = intVal(substr(trim($request->fac), 1));
            $defOwn = $GLOBALS["SL"]->def->getID('Permissions', 'Own');
            $perm = new RIIUserPsPerms;
            $perm->usr_perm_psid = intVal($request->psid);
            $perm->usr_perm_permissions = $defOwn;
            if ($type == 'C') {
                $perm->usr_perm_company_id = $id;
            } elseif ($type == 'F') {
                $perm->usr_perm_facility_id = $id;
            }
            $perm->save();
        }
        return '<div class="hidSelf"><b>Saved</b> '
            . '<i class="fa fa-floppy-o mL3" aria-hidden="true"></i></div>';
    }


}