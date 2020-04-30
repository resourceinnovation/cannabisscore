<?php
/**
  * ScoreAdminMisc is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class handles the custom needs of printing an individual PowerScore's report.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.4
  */
namespace CannabisScore\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreCalcsPrint;

class ScorePrintReport extends ScoreCalcsPrint
{
    public function printPreviewReport($isAdmin = false)
    {
        return view(
            'vendor.cannabisscore.powerscore-report-preview', 
            [
                "uID"      => $this->v["uID"],
                "sessData" => $this->sessData->dataSets
            ]
        )->render();
    }
    
    public function printPsRankingFilters($nID)
    {
        if (Auth::user() && Auth::user()->hasRole('partner')) {
            $GLOBALS["SL"]->x["partnerVersion"] = true;
        }
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->searcher->v["nID"] = $nID;
        $this->searcher->v["psFiltChks"] = view(
            'vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v
        )->render();
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
                    $deetVal .= (($i > 0) ? ', ' : '') 
                        . $GLOBALS["SL"]->def->getVal(
                            'PowerScore Competitions', 
                            $cup->ps_cup_cup_id
                        );
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
        return ' <!-- customPrint490.start --> ' . $ret . ' <!-- customPrint490.end --> ';
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
        if (!$request->has('refresh') || intVal($request->get('refresh')) == 1) {
            $this->sessData->loadData('powerscore', $this->v["ajax-psid"]);
            if (isset($this->sessData->dataSets["powerscore"]) 
                && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
                // isset($this->sessData->dataSets["powerscore"][0]->ps_email)
                $this->calcCurrSubScores();
                return view(
                    'vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', 
                    [
                        "msg"  => '<i class="slGrey">Recalculating Sub-Scores...',
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
        $this->v["psFiltChks"] = view(
            'vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v
        )->render();
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
            'Facility', 
            'Production', 
            'HVAC', 
            'Lighting', 
            'Water', 
            'Waste'
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
        if ($this->searcher->v["powerscore"] 
            && isset($this->searcher->v["powerscore"]->ps_id)) {
            $this->searcher->v["isPast"] = ($this->searcher->v["powerscore"]->ps_time_type 
                == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
            $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_filters', 
                    $this->searcher->v["urlFlts"])
                ->where('ps_rnk_psid', $this->searcher->v["powerscore"]->ps_id)
                ->first();
            if ($GLOBALS["SL"]->REQ->has('refresh') 
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall)
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall->ps_rnk_overall)) {
                $this->ajaxScorePercentilesCalcRanks();
            }
            $this->searcher->v["currGuage"] = 0;
            $this->searcher->v["hasOverall"] = (isset($this->searcher->v["powerscore"]->ps_effic_facility) 
                && isset($this->searcher->v["powerscore"]->ps_effic_production) 
                && isset($this->searcher->v["powerscore"]->ps_effic_hvac) 
                && isset($this->searcher->v["powerscore"]->ps_effic_lighting) 
                && $this->searcher->v["powerscore"]->ps_effic_facility > 0
                && $this->searcher->v["powerscore"]->ps_effic_production > 0);
            $overRank = round($this->searcher->v["currRanks"]->ps_rnk_overall);
            if ($overRank == 0) {
                $overRank = 1;
            }
            $superscript = $GLOBALS["SL"]->numSupscript($overRank);
            $this->searcher->v["overallScoreTitle"] = '<center><h1 class="m0 scoreBig">' 
                . $overRank . $superscript . '</h1><b>percentile</b></center>';
// number_format($ranksCache->ps_rnk_tot_cnt) }} past growing @if ($ranksCache->ps_rnk_tot_cnt > 1) years @else year @endif of
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
        if (isset($this->searcher->v["powerscore"]->ps_time_type) 
            && $this->searcher->v["powerscore"]->ps_time_type 
                == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
            $ranks = RIIPsRanks::where('ps_rnk_filters', '')
                ->first();
            $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
            $this->searcher->v["powerscore"]->ps_effic_overall = $this->searcher
                ->v["currRanks"]->ps_rnk_overall;
            $this->searcher->v["powerscore"]->save();
        } else {
            $urlFlts = $this->searcher->v["urlFlts"];
            $this->calcAllScoreRanks();
            $this->searcher->v["urlFlts"] = $urlFlts;
            $this->searcher->v["currRanks"] = RIIPsRankings::where(
                    'ps_rnk_psid', 
                    $this->searcher->v["powerscore"]->ps_id
                )
                ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                ->first();
            if (!$this->searcher->v["currRanks"]) {
                $ranks = RIIPsRanks::where('ps_rnk_filters', 
                        $this->searcher->v["urlFlts"])
                    ->first();
                $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
            }
        }
        return true;
    }
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function ajaxScorePercNewRank($ranks)
    {
        $currRanks = new RIIPsRankings;
        $currRanks->ps_rnk_psid = $this->searcher->v["powerscore"]->ps_id;
        if ($ranks && isset($ranks->ps_rnk_facility)) {
            $currRanks->ps_rnk_facility = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_facility, 
                $this->searcher->v["powerscore"]->ps_effic_facility, 
                true
            );
            $currRanks->ps_rnk_production = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_production, 
                $this->searcher->v["powerscore"]->ps_effic_production
            );
            $currRanks->ps_rnk_lighting = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_lighting, 
                $this->searcher->v["powerscore"]->ps_effic_lighting, 
                true
            );
            $currRanks->ps_rnk_hvac = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_hvac, 
                $this->searcher->v["powerscore"]->ps_effic_hvac, 
                true
            );
            $currRanks->ps_rnk_water = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_water, 
                $this->searcher->v["powerscore"]->ps_effic_water, 
                true
            );
            $currRanks->ps_rnk_waste = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_waste, 
                $this->searcher->v["powerscore"]->ps_effic_waste, 
                true
            );
            $currRanks->ps_rnk_overall_avg = ($currRanks->ps_rnk_facility
                +$currRanks->ps_rnk_production+$currRanks->ps_rnk_lighting
                +$currRanks->ps_rnk_hvac+$currRanks->ps_rnk_water
                +$currRanks->ps_rnk_waste)/6;
            $currRanks->ps_rnk_overall = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_overall_avg, 
                $currRanks->ps_rnk_overall_avg
            );
            $currRanks->save();
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
            && intVal($GLOBALS["SL"]->REQ->mine) == 1) {
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

    protected function reportMaMonths($nID)
    {
        return view(
            'vendor.cannabisscore.nodes.1420-ma-compliance-monthly', 
            [
                "rec"    => $this->sessData->dataSets["compliance_ma"][0],
                "months" => $this->sessData->dataSets["compliance_ma_months"]
            ]
        )->render();
    }

    protected function reportMaNextPro($nID)
    {
        return view(
            'vendor.cannabisscore.nodes.1436-ma-compliance-next-go-pro', 
            [ "rec" => $this->sessData->dataSets["compliance_ma"][0] ]
        )->render();
    }

}
