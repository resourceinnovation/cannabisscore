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
use ResourceInnovation\CannabisScore\Controllers\ScoreUserCompanies;
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
            'Facility', 
            'FacNon', 
            'FacAll', 
            'Production', 
            'ProdNon', 
            'ProdAll', 
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
        if (isset($this->searcher->v["powerscore"])
            && isset($this->searcher->v["powerscore"]->ps_id)) {
            $pastDef = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
            $this->searcher->v["isPast"] = ($this->searcher->v["powerscore"]->ps_time_type == $pastDef);
            $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_filters', 
                    $this->searcher->v["urlFlts"])
                ->where('ps_rnk_psid', $this->searcher->v["powerscore"]->ps_id)
                ->first();
            if ($GLOBALS["SL"]->REQ->has('refresh') 
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall)
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall->ps_rnk_overall)) {
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
            $this->searcher->v["hasOverall"] = (isset($this->searcher->v["powerscore"]->ps_effic_facility) 
                && isset($this->searcher->v["powerscore"]->ps_effic_production) 
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
            $this->searcher->v["powerscore"]->ps_effic_overall 
                = $this->searcher->v["currRanks"]->ps_rnk_overall;
            $this->searcher->v["powerscore"]->save();
        } else {
            $urlFlts = $this->searcher->v["urlFlts"];
            $psid = $this->searcher->v["powerscore"]->ps_id;
            $this->calcAllScoreRanks();
            $this->searcher->v["urlFlts"] = $urlFlts;
            $this->searcher->v["currRanks"] = RIIPsRankings::where('ps_rnk_psid', $psid)
                ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                ->first();
            if (!$this->searcher->v["currRanks"] || $GLOBALS["SL"]->REQ->has('refresh')) {
                $ranks = RIIPsRanks::where('ps_rnk_filters', $this->searcher->v["urlFlts"])
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
            $currRanks->ps_rnk_fac_non = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_fac_non, 
                $this->searcher->v["powerscore"]->ps_effic_fac_non, 
                true
            );
            $currRanks->ps_rnk_fac_all = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_fac_all, 
                $this->searcher->v["powerscore"]->ps_effic_fac_all, 
                true
            );
            $currRanks->ps_rnk_production = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_production, 
                $this->searcher->v["powerscore"]->ps_effic_production
            );
            $currRanks->ps_rnk_prod_non = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_prod_non, 
                $this->searcher->v["powerscore"]->ps_effic_prod_non
            );
            $currRanks->ps_rnk_prod_all = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->ps_rnk_prod_all, 
                $this->searcher->v["powerscore"]->ps_effic_prod_all
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
        $report->addCol('ps_month_kw', 'Peak Electricity Usage', 'kW');
        $report->addCol('ps_month_kwh_renewable', 'Renewable Electricity Generated', 'kWh');
        $unit = 'Gallons';
        if ($this->isWaterInLiters()) {
            $unit = 'Liters';
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
        return $report->printTables() . '<!-- end printTables() -->';
    }

    // Override PDF filename to be used for delivery to user.
    public function customPdfFilename()
    {
        if (in_array($this->treeID, [1, 22])) {
            $GLOBALS["SL"]->x["pdfFilename"] = 'PowerScore_Report.pdf';
        } elseif (in_array($this->treeID, [71, 93])) {
            $GLOBALS["SL"]->x["pdfFilename"] 
                = 'Massachusetts_PowerScore_Comply_Report.pdf';
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