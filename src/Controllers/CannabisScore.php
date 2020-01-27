<?php
/**
  * CannabisScore extends ScoreImports extends ScoreAdminMisc extends ScoreReports extends ScoreReports
  * extends ScoreCalcs extends ScoreUtils extends ScorePowerUtilities extends ScoreLightModels 
  * extends ScoreVars extends TreeSurvForm. This class contains the majority of 
  * SurvLoop functions which are overwritten, and delegates most of the work.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SLNodeSaves;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRanks;
use App\Models\RIIPSRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPSLicenses;
use CannabisScore\Controllers\ScoreReportFound;
use CannabisScore\Controllers\ScoreReportAvgs;
use CannabisScore\Controllers\ScoreReportHvac;
use CannabisScore\Controllers\ScoreReportLighting;
use CannabisScore\Controllers\ScoreListings;
use CannabisScore\Controllers\ScoreReports;
use CannabisScore\Controllers\ScoreImports;

class CannabisScore extends ScoreImports
{
    protected function customNodePrint($nID = -3, $tmpSubTier = [], $nIDtxt = '', $nSffx = '', $currVisib = 1)
    {
        $ret = '';
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 393) {
            $areas = $this->sessData->getLoopRowIDs('Growth Stages');
            $GLOBALS["SL"]->pageAJAX .= view(
                'vendor.cannabisscore.nodes.393-area-lighting-ajax', 
                [ "areas" => $areas ]
            )->render();
        } elseif (in_array($nID, [74, 396])) {
            $ret .= $this->printGramForm($nID, $nIDtxt);
        } elseif ($nID == 362) {
            $GLOBALS["SL"]->loadStates();
            $this->getStateUtils();
            $ret .= view(
                'vendor.cannabisscore.nodes.362-utilities-by-state', 
                $this->v
            )->render();
        } elseif ($nID == 502) {
            $this->chkUtilityOffers();
            $ret .= view(
                'vendor.cannabisscore.nodes.502-utility-offers', 
                $this->v
            )->render();
        } elseif (in_array($nID, [177, 457, 465, 471])) {
            return $this->printReportBlds($nID);
        } elseif (in_array($nID, [209, 432, 440, 448])) {
            return $this->printReportLgts($nID);
        } elseif ($nID == 536) {
            $this->prepFeedbackSkipBtn();
            $GLOBALS["SL"]->pageJAVA .= view(
                'vendor.cannabisscore.nodes.536-feedback-skip-button-java', 
                $this->v
            )->render();
        } elseif ($nID == 548) {
            $this->prepFeedbackSkipLnk();
            $ret .= view(
                'vendor.cannabisscore.nodes.548-powerscore-feedback-score-link', 
                $this->v
            )->render();
        } elseif ($nID == 148) { // this should be built-in
            $this->sessData->dataSets["powerscore"][0]->ps_status = $this->v["defCmplt"];
            $this->sessData->dataSets["powerscore"][0]->save();
            session()->put('PowerScoreOwner', $this->coreID);
            session()->put(
                'PowerScoreOwner' . $this->coreID, 
                $this->coreID
            );
            
        } elseif ($nID == 490) {
            $ret .= $this->customPrint490($nID);
        } elseif ($nID == 1008) {
            $ret .= view(
                'vendor.cannabisscore.nodes.1008-powerscore-calculations-mockup', 
                $this->v
            )->render();
        } elseif ($nID == 946) {
            $ret .= $this->printPsRankingFilters($nID);
        } elseif ($nID == 878) {
            $this->auditLgtAlerts();
        } elseif ($nID == 860) {
            return $this->printReportForCompetition($nID);
        } elseif ($nID == 861) {
            return $this->printReportGrowingYear($nID);
        } elseif ($nID == 508) {
            $ret .= $this->printReportUtilRef($nID);
            
        // PowerScore Reporting
        } elseif ($nID == 744) {
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $ret .= $report->getCultClassicReport();
        } elseif ($nID == 170) {
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $ret .= $report->getAllPowerScoresPublic($nID);
        } elseif ($nID == 966) {
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $ret .= $report->getPowerScoresOutliers($nID);
        } elseif ($nID == 964) { // Partner Multi-Site Rankings
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->getMultiSiteRankings($nID);
        } elseif ($nID == 799) { // Partner Multi-Site Listings
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->getAllPowerScoresPublic($nID);
        } elseif ($nID == 773) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getAllPowerScoreAvgsPublic();
        } elseif ($nID == 859) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getMorePowerStats();
        } elseif ($nID == 801) {
            $report = new ScoreReportAvgs;
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->getAllPowerScoreAvgsPublic();
        } elseif ($nID == 979) {
            $report = new ScoreListings($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->printCompareLightManu($nID);
        } elseif ($nID == 797) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getPowerScoreFinalReport();
        } elseif ($nID == 853) {
            $this->initSearcher(1);
            $this->searcher->loadAllScoresPublic();
            $report = new ScoreReportFound;
            $ret .= $report->getFoundReport($nID, $this->searcher->v["allscores"]);
        } elseif ($nID == 775) {
            $ret .= $this->checkBadRecs();
        } elseif ($nID == 786) {
            $ret .= $this->adminSearchResults();
        } elseif (in_array($nID, [726, 990])) {
            $ret .= $this->printDashSessGraph();
        } elseif ($nID == 976) {
            $report = new ScoreReports;
            $ret .= $report->printBasicStats($nID);
        } elseif ($nID == 981) {
            $report = new ScoreReportHvac;
            $ret .= $report->getHvacReport($nID);
        } elseif ($nID == 983) {
            $report = new ScoreReportLighting;
            $ret .= $report->getLightingReport($nID);
        } elseif ($nID == 855) {
            $report = new ScoreReportLighting;
            $ret .= $report->printLightingRawCalcs($nID);
            
            
        // Admin Tools
        } elseif ($nID == 914) {
            $ret .= $this->printMgmtManufacturers($nID);
        } elseif ($nID == 917) {
            $ret .= $this->printMgmtLightModels($nID);
        } elseif ($nID == 845) {
            $ret .= $this->printAdminPsComms();
        } elseif ($nID == 637) {
            $ret .= $this->getEmailsList();
        } elseif ($nID == 740) {
            $ret .= $this->getTroubleshoot();
        } elseif ($nID == 742) {
            $ret .= $this->getProccessUploads();
        } elseif ($nID == 777) {
            $ret .= $this->reportPowerScoreFeedback();
        } elseif ($nID == 838) {
            $ret .= $this->reportInSurveyFeedback();
        } elseif ($nID == 808) {
            $ret .= $this->runNwpccImport();
        } elseif ($nID == 968) {
            return view(
                'vendor.cannabisscore.nodes.968-lighting-manufacturers-comparison', 
                [ "nID" => $nID ]
            )->render();
            
        // Misc
        } elseif ($nID == 843) {
            $ret .= $this->printProfileExtraBtns();
        } elseif ($nID == 1039) {
            $ret .= $this->printPartnerProfileDashBtn($nID);
        } elseif ($nID == 1040) {
            $ret .= $this->printPartnerProfileDashHead($nID);

        }
        return $ret;
    }
    
    protected function customResponses($nID, &$curr)
    {
        if (in_array($nID, [57, 1073])) {
            $curr->clearResponses();
            if (isset($this->sessData->dataSets["powerscore"]) 
                && isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                $utIDs = RIIPSUtiliZips::where('ps_ut_zp_zip_code', 
                        $this->sessData->dataSets["powerscore"][0]->ps_zip_code)
                    ->get();
                if ($utIDs->isNotEmpty()) {
                    $ids = [];
                    foreach ($utIDs as $u) {
                        $ids[] = $u->ps_ut_zp_util_id;
                    }
                    $uts = RIIPSUtilities::whereIn('ps_ut_id', $ids)
                        ->get(); // will be upgrade to check for farm's zip code
                    if ($uts->isNotEmpty()) {
                        foreach ($uts as $i => $ut) {
                            $curr->addTmpResponse($ut->ps_ut_id, $ut->ps_ut_name);
                        }
                    }
                }
            }
            $curr->addTmpResponse(0, 'Other:');
            $curr->dataStore = 'powerscore:ps_source_utility';
            $curr->chkFldOther();
            $curr->dataStore = 'ps_utili_links:ps_ut_lnk_utility_id';
        } elseif (in_array($nID, [1074])) {
            $curr->clearResponses();
            $curr->addTmpResponse(1, 'Generated On-site');
            $curr->addTmpResponse(2, 'PEPCO');
            $curr->addTmpResponse(0, 'Other:');
            $curr->dataStore = 'powerscore:ps_source_utility';
            $curr->chkFldOther();
        }
        return $curr;
    }
    
    protected function postNodePublicCustom($nID = -3, $nIDtxt = '', $tmpSubTier = [])
    { 
        if (empty($tmpSubTier)) {
            $tmpSubTier = $this->loadNodeSubTier($nID);
        }
        list($tbl, $fld) = $this->allNodes[$nID]->getTblFld();
        
        if ($nID == 47) {
            if ($GLOBALS["SL"]->REQ->has('n47fld') 
                && trim($GLOBALS["SL"]->REQ->get('n47fld')) != '') {
                $this->sessData->updateZipInfo(
                    $GLOBALS["SL"]->REQ->get('n47fld'), 
                    'powerscore', 
                    'ps_state', 
                    'ps_county', 
                    'ps_ashrae', 
                    'ps_country'
                );
            }
        } elseif ($nID == 1049) {
            return $this->postGrowingStages2020($nID, $nIDtxt);
        } elseif ($nID == 701) {
            return $this->postGrowingStages($nID, $nIDtxt);
        } elseif ($nID == 74) { // dump monthly grams
            $this->postMonthlies($nIDtxt, 'ps_month_grams');
        } elseif ($nID == 70) { // dump monthly energy
            $this->postMonthlies($nIDtxt, 'ps_month_kwh1');
        } elseif ($nID == 949) { // dump monthly green waste pounds
            $this->postMonthlies($nIDtxt, 'ps_month_waste_lbs');
        } elseif (in_array($nID, [57, 1073, 1074])) {
            $foundOther = '';
            for ($i = 0; ($i < 20 && $foundOther == ''); $i++) {
                $fld = 'n' . $nID . 'fldOther' . $i;
                if ($GLOBALS["SL"]->REQ->has($fld) 
                    && trim($GLOBALS["SL"]->REQ->get($fld)) != '') {
                    $foundOther = trim($GLOBALS["SL"]->REQ->get($fld));
                }
            }
            $this->sessData->dataSets["powerscore"][0]->update([
                'ps_source_utility_other' => $foundOther
            ]);
            $this->sessData->dataSets["powerscore"][0]->save();
        } elseif ($nID == 398) {
            if ($GLOBALS["SL"]->REQ->has('n' . $nID . 'fld')) {
                $this->sessData->dataSets["powerscore"][0]->update([
                    'ps_total_size' => $GLOBALS["SL"]->REQ->get('n' . $nID . 'fld')
                ]);
            }
        } elseif (in_array($nID, [59, 80, 60, 61, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["ps_renewables"]) 
                && sizeof($this->sessData->dataSets["ps_renewables"]) > 0) {
                foreach ($this->sessData->dataSets["ps_renewables"] as $ind => $row) {
                    if (isset($row->ps_rnw_renewable) 
                        && $row->ps_rnw_renewable == $sourceID) {
                        $fld = 'n' . $nID . 'fld';
                        $perc = (($GLOBALS["SL"]->REQ->has($fld)) 
                            ? intVal($GLOBALS["SL"]->REQ->get($fld)) : 0);
                        $this->sessData->dataSets["ps_renewables"][$ind]->update([
                            'ps_rnw_load_percent' => $perc
                        ]);
                    }
                }
            }
            return true;
            
            
        } elseif ($nID == 914) {
            return $this->addManufacturers($nID);
        } elseif ($nID == 917) {
            return $this->addLightModels($nID);
        }
        return false; // false to continue standard post processing
    }

    protected function postGrowingStages2020($nID, $nIDtxt = '')
    {
        if (isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $a => $area) {
                if ($area->ps_area_type != 163) {
                    $hasStage = 0;
                    if ($GLOBALS["SL"]->REQ->has('n1049fld')
                        && is_array($GLOBALS["SL"]->REQ->n1049fld)
                        && sizeof($GLOBALS["SL"]->REQ->n1049fld) > 0
                        && in_array($area->ps_area_type, $GLOBALS["SL"]->REQ->n1049fld)) {
                        $hasStage = 1;
                    }
                    $this->sessData->dataSets["ps_areas"][$a]->update([
                        'ps_area_has_stage' => $hasStage
                    ]);
                }
            }
        }
        return false;
    }

    protected function postGrowingStages($nID, $nIDtxt = '')
    {
        if (isset($this->sessData->dataSets["ps_areas"])) {
            $areas = $this->sessData->dataSets["ps_areas"];
            if (sizeof($areas) > 0) {
                foreach ($areas as $a => $area) {
                    $nick = $this->getStageNick($area->ps_area_type);
                    $area->ps_area_has_stage = 0;
                    if (in_array($nick, ['Mother', 'Clone'])) {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 493) {
                            if ($GLOBALS["SL"]->REQ->has('n1033fld')
                                && is_array($GLOBALS["SL"]->REQ->n1033fld)
                                && in_array($area->ps_area_type, 
                                    $GLOBALS["SL"]->REQ->n1033fld)) {
                                $area->ps_area_has_stage = 1;
                            }
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    } elseif ($nick == 'Veg') {
                        $area = $this->postGrowingStagesOther($area);
                    } elseif ($nick == 'Flower') {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                            $area->ps_area_has_stage = 1;
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    } elseif ($nick == 'Dry') {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                            if ($GLOBALS["SL"]->REQ->has('n1029fld')
                                && is_array($GLOBALS["SL"]->REQ->n1029fld)
                                && in_array($area->ps_area_type, 
                                    $GLOBALS["SL"]->REQ->n1029fld)) {
                                $area->ps_area_has_stage = 1;
                            }
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    }
                    $area->save();
                    $this->sessData->dataSets["ps_areas"][$a] = $area;
                }
            }
            $this->sessData->dataSets["powerscore"][0]->ps_processing_onsite = 0;
            $this->sessData->dataSets["powerscore"][0]->ps_extracting_onsite = 0;
            if ($GLOBALS["SL"]->REQ->has('n701fld')
                && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n1029fld)
                    && in_array(11, $GLOBALS["SL"]->REQ->n1029fld)) {
                    $this->sessData->dataSets["powerscore"][0]->ps_processing_onsite = 1;
                }
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n1029fld)
                    && in_array(12, $GLOBALS["SL"]->REQ->n1029fld)) {
                    $this->sessData->dataSets["powerscore"][0]->ps_extracting_onsite = 1;
                }
            } elseif ($GLOBALS["SL"]->REQ->has('n701fld')
                && intVal($GLOBALS["SL"]->REQ->n701fld) == 495) {
                if ($GLOBALS["SL"]->REQ->has('n575fld')
                    && is_array($GLOBALS["SL"]->REQ->n575fld)
                    && in_array(11, $GLOBALS["SL"]->REQ->n575fld)) {
                    $this->sessData->dataSets["powerscore"][0]->ps_processing_onsite = 1;
                }
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n575fld)
                    && in_array(12, $GLOBALS["SL"]->REQ->n575fld)) {
                    $this->sessData->dataSets["powerscore"][0]->ps_extracting_onsite = 1;
                }
            }
            $this->sessData->dataSets["powerscore"][0]->save();
        }
        return false;
    }

    protected function postGrowingStagesOther($area)
    {
        if ($GLOBALS["SL"]->REQ->has('n701fld')
            && intVal($GLOBALS["SL"]->REQ->n701fld) == 495) {
            if ($GLOBALS["SL"]->REQ->has('n575fld')
                && is_array($GLOBALS["SL"]->REQ->n575fld)
                && in_array($area->ps_area_type, $GLOBALS["SL"]->REQ->n575fld)) {
                $area->ps_area_has_stage = 1;
            }
        }
        return $area;
    }
    
    protected function postMonthlies($nIDtxt, $fld2)
    {
        $powerMonths = $this->sortMonths();
        foreach ($powerMonths as $i => $row) {
            $row->ps_month_month = (1+$i);
            $fldName = 'month' . $nIDtxt . 'ly' . $row->ps_month_month;
            $row->{ $fld2 }  = (($GLOBALS["SL"]->REQ->has($fldName)) 
                ? intVal($GLOBALS["SL"]->REQ->get($fldName)) : null);
            $row->save();
        }
        return true;
    }
    
    public function monthlyCalcPreselections($nID, $nIDtxt = '')
    {
        $ret = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $fld = (($nID == 70) ? 'ps_month_kwh1' 
            : (($nID == 74) ? 'ps_month_grams' : 'ps_month_waste_lbs'));
        $powerMonths = $this->sortMonths();
        if ($powerMonths->isNotEmpty()) {
            foreach ($powerMonths as $i => $row) {
                $ret[$i] = $row->{ $fld };
            }
        }
        return $ret;
    }
    
    public function printGramForm($nID, $nIDtxt)
    {
        $this->v["nID"] = $nID;
        $this->v["currSessData"] = 0;
        if (isset($this->sessData->dataSets["powerscore"]) 
            && isset($this->sessData->dataSets["powerscore"][0])
            && isset($this->sessData->dataSets["powerscore"][0]->ps_grams)) {
            $this->v["currSessData"] = $this->sessData->dataSets["powerscore"][0]->ps_grams;
        }
        $this->pageJSvalid .= "addReqNodeRadio('" . $nIDtxt 
            . "', 'reqFormFldGreater', 0.00000001);\n";
        $presel = $this->monthlyCalcPreselections($nID, $nIDtxt);
        $this->v["gramFormMonths"] = $this->printMonthlyCalculator(
            $nIDtxt, 
            $presel, 
            'convertGrams();'
        );
        return view('vendor.cannabisscore.nodes.74-total-grams', $this->v)->render();
    }
    
    public function ajaxChecksCustom(Request $request, $type = '')
    {
        if ($type == 'report-ajax') {
            return $this->ajaxReportRefresh($request);
        } elseif ($type == 'powerscore-rank') {
            return $this->ajaxScorePercentiles();
        } elseif ($type == 'powerscore-uploads') {
            return $this->getProccessUploadsAjax();
        } elseif ($type == 'future-look') {
            return $this->ajaxFutureYields();
        } elseif ($type == 'adm-comms') {
            return $this->admCommsForm($request);
        } elseif ($type == 'light-search') {
            return $this->ajaxLightSearch($request);
        }
        return '';
    }
    
    protected function ajaxReportRefresh(Request $request)
    {
        $this->v["ajax-psid"] = -3;
        if ($GLOBALS["SL"]->REQ->has('psid') 
            && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) {
            $this->v["ajax-psid"] = intVal($GLOBALS["SL"]->REQ->get('psid'));
        }
        if (!$request->has('refresh') || intVal($request->get('refresh')) == 1) {
            $this->sessData->loadData('PowerScore', $this->v["ajax-psid"]);
            if (isset($this->sessData->dataSets["powerscore"]) 
                && sizeof($this->sessData->dataSets["powerscore"]) > 0
                && isset($this->sessData->dataSets["powerscore"][0]->ps_email)) {
                $this->calcCurrSubScores();
                return view(
                    'vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', 
                    [
                        "msg"  => '<i class="slGrey">Recalculating Sub-Scores...',
                        "psid" => $this->v["ajax-psid"]
                    ]
                )->render();
            }
            return '<b>Error 420: PowerScore Not Found</b>';
        }
        return $this->calcAllScoreRanks('report-ajax');
    }
    
    protected function ajaxFutureYields()
    {
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
        $this->initSearcher();
        $this->searcher->searchResultsXtra(1);
        $this->searcher->searchFiltsURLXtra();
        if ($this->searcher->v["powerscore"] 
            && isset($this->searcher->v["powerscore"]->ps_id)) {
            $this->searcher->v["isPast"] = ($this->searcher->v["powerscore"]->ps_time_type 
                == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
            $this->searcher->v["currRanks"] = RIIPSRankings::where('ps_rnk_filters', 
                    $this->searcher->v["urlFlts"])
                ->where('ps_rnk_psid', $this->searcher->v["powerscore"]->ps_id)
                ->first();
            if ($GLOBALS["SL"]->REQ->has('refresh') 
                || !$this->searcher->v["currRanks"]->ps_rnk_overall
                || !isset($this->searcher->v["currRanks"]->ps_rnk_overall->ps_rnk_overall)) {
                if (isset($this->searcher->v["powerscore"]->ps_time_type) 
                    && $this->searcher->v["powerscore"]->ps_time_type 
                        == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                    $ranks = RIIPSRanks::where('ps_rnk_filters', '')
                        ->first();
                    $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
                    $this->searcher->v["powerscore"]->ps_effic_overall = $this->searcher
                        ->v["currRanks"]->ps_rnk_overall;
                    $this->searcher->v["powerscore"]->save();
                } else {
                    $urlFlts = $this->searcher->v["urlFlts"];
                    //$this->calcAllScoreRanks();
                    $this->searcher->v["urlFlts"] = $urlFlts;
                    $this->searcher->v["currRanks"] = RIIPSRankings::where(
                            'ps_rnk_psid', 
                            $this->searcher->v["powerscore"]->ps_id
                        )
                        ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                        ->first();
                    if (!$this->searcher->v["currRanks"]) {
                        $ranks = RIIPSRanks::where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                            ->first();
                        $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
                    }
                }
            }
            $this->searcher->v["currGuage"] = 0;
            $this->searcher->v["hasOverall"] = (isset($this->searcher->v["powerscore"]->ps_effic_facility) 
                && isset($this->searcher->v["powerscore"]->ps_effic_production) 
                && isset($this->searcher->v["powerscore"]->ps_effic_hvac) 
                && isset($this->searcher->v["powerscore"]->ps_effic_lighting) 
                && $this->searcher->v["powerscore"]->ps_effic_facility > 0
                && $this->searcher->v["powerscore"]->ps_effic_production > 0);
            $superscript = $GLOBALS["SL"]->numSupscript(round($this->searcher
                ->v["currRanks"]->ps_rnk_overall));
            $this->searcher->v["overallScoreTitle"] = '<center><h1 class="m0 scoreBig">' 
                . round($this->searcher->v["currRanks"]->ps_rnk_overall) .  $superscript
                . '</h1><b>percentile</b></center>';
// number_format($ranksCache->ps_rnk_tot_cnt) }} past growing @if ($ranksCache->ps_rnk_tot_cnt > 1) years @else year @endif of
            $this->searcher->v["withinFilters"] = '<div id="efficBlockOverGuageTitle">' 
                . '<h5>Overall: '
                . (($this->searcher->v["currRanks"]->ps_rnk_overall > 66) ? 'Leader' 
                    : (($this->searcher->v["currRanks"]->ps_rnk_overall > 33) 
                        ? 'Middle-of-the-Pack' : 'Upgrade Candidate')) 
                . '</h5></div><div class="efficGuageTxtOverall4">'
                . 'Your farm\'s overall performance within the data set of ';
            if ($this->searcher->v["fltFarm"] == 0) {
                $this->searcher->v["withinFilters"] .= 'all farm types';
            } else {
                $farmType = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $this->searcher->v["fltFarm"]);
                $farmType = str_replace('Greenhouse/Hybrid/Mixed Light', 'Greenhouse/ Hybrid', $farmType);
                $this->searcher->v["withinFilters"] .= strtolower($farmType) . ' farms';
            }
            $this->searcher->v["withinFilters"] .= $this->searcher->v["xtraFltsDesc"];
            if ($this->searcher->v["fltState"] == '' 
                && $this->searcher->v["fltClimate"] == '') {
                $this->searcher->v["withinFilters"] .= ' in the U.S. and Canada:';
            } elseif ($this->searcher->v["fltState"] != '') {
                if ($this->searcher->v["fltState"] == 'US') {
                    $this->searcher->v["withinFilters"] .= ' in the United States.';
                } elseif ($this->searcher->v["fltState"] == 'Canada') {
                    $this->searcher->v["withinFilters"] .= ' in Canada:';
                } else {
                    $this->searcher->v["withinFilters"] .= ' in <span class="slBlueDark">' 
                        . $GLOBALS["SL"]->getState($this->searcher->v["fltState"]) . ':';
                }
            } else {
                if ($this->searcher->v["fltClimate"] == 'US') {
                    $this->searcher->v["withinFilters"] .= ' in the United States.';
                } elseif ($this->searcher->v["fltClimate"] == 'Canada') {
                    $this->searcher->v["withinFilters"] .= ' in Canada:.';
                } else {
                    $this->searcher->v["withinFilters"] .= ' in <span class="slBlueDark">'
                        . 'ASHRAE Climate Zone ' . $this->searcher->v["fltClimate"] . ':';
                }
            }
            $this->searcher->v["withinFilters"] .= '</div>';
            return view(
                'vendor.cannabisscore.nodes.490-report-calculations-load-all-js', 
                $this->searcher->v
            )->render();
        }
        return '';
    }
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function ajaxScorePercNewRank($ranks)
    {
        $currRanks = new RIIPSRankings;
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
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function printNodeSessDataOverride($nID = -3, $tmpSubTier = [], $nIDtxt = '', $currNodeSessionData = '')
    {
        if (sizeof($this->sessData->dataSets) == 0) {
            return [];
        }
        if ($nID == 49) {
            if (isset($this->sessData->dataSets["ps_farm"]) 
                && isset($this->sessData->dataSets["ps_farm"][0]->ps_frm_type)) {
                return [$this->sessData->dataSets["ps_farm"][0]->ps_frm_type];
            }
        } elseif (in_array($nID, [864, 865])) {
            if (!isset($this->sessData->dataSets["powerscore"]) 
                || !isset($this->sessData->dataSets["powerscore"][0]->ps_year)
                || trim($this->sessData->dataSets["powerscore"][0]->ps_year) == '') {
                if (intVal(date("n")) <= 6) {
                    return [intVal(date("Y"))-1];
                } else {
                    return [intVal(date("Y"))];
                }
            }
        } elseif ($nID == 1033) { // Nursery growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Mother', 'ps_area_has_stage')) == 1) {
                $ret[] = 237;
            }
            if (intVal($this->getAreaFld('Clone', 'ps_area_has_stage')) == 1) {
                $ret[] = 160;
            }
            return $ret;
        } elseif ($nID == 1029) { // Flowering-only growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Dry', 'ps_area_has_stage')) == 1) {
                $ret[] = 163;
            }
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_processing_onsite) && intVal($ps->ps_processing_onsite) == 1) {
                $ret[] = 11;
            }
            if (isset($ps->ps_extracting_onsite) && intVal($ps->ps_extracting_onsite) == 1) {
                $ret[] = 12;
            }
            return $ret;
        } elseif ($nID == 575) {  // Other growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Mother', 'ps_area_has_stage')) == 1) {
                $ret[] = 237;
            }
            if (intVal($this->getAreaFld('Clone', 'ps_area_has_stage')) == 1) {
                $ret[] = 160;
            }
            if (intVal($this->getAreaFld('Veg', 'ps_area_has_stage')) == 1) {
                $ret[] = 161;
            }
            if (intVal($this->getAreaFld('Flower', 'ps_area_has_stage')) == 1) {
                $ret[] = 162;
            }
            if (intVal($this->getAreaFld('Dry', 'ps_area_has_stage')) == 1) {
                $ret[] = 163;
            }
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_processing_onsite) && intVal($ps->ps_processing_onsite) == 1) {
                $ret[] = 11;
            }
            if (isset($ps->ps_extracting_onsite) && intVal($ps->ps_extracting_onsite) == 1) {
                $ret[] = 12;
            }
            return $ret;

        } elseif (in_array($nID, [57, 1073, 1074])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_source_utility_other) 
                && trim($ps->ps_source_utility_other) != '') {
                $GLOBALS["SL"]->pageJAVA .= 'function fillUtilOther() { '
                    . 'for (var i=0; i<20; i++) { '
                    . 'if (document.getElementById("n' . $nID . 'fldOtherID"+i+"")) {'
                        . 'document.getElementById("n' . $nID . 'fldOtherID"+i+"").value="'
                        . str_replace('"', '\\"', $this->sessData
                            ->dataSets["powerscore"][0]->ps_source_utility_other)
                    . '"; } } return true; } setTimeout("fillUtilOther()", 10);';
            }
        } elseif (in_array($nID, [59, 80, 61, 60, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["ps_renewables"]) 
                && sizeof($this->sessData->dataSets["ps_renewables"]) > 0) {
                foreach ($this->sessData->dataSets["ps_renewables"] as $ind => $row) {
                    if (isset($row->ps_rnw_renewable) && $row->ps_rnw_renewable == $sourceID) {
                        $perc = 0;
                        if (isset($row->ps_rnw_load_percent)) {
                            $perc = intVal($row->ps_rnw_load_percent);
                        }
                        return [$perc];
                    }
                }
            }
        }
        return [];
    }
    
    protected function checkNodeConditionsCustom($nID, $condition = '')
    {
        if ($condition == '#Competitor') {
            if ($GLOBALS["SL"]->REQ->has('cups')) {
                return 1;
            }
        } elseif ($condition == '#MotherHas') {
            $area = $this->getArea('Mother');
            if (!isset($area) 
                || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_has_stage);
        } elseif ($condition == '#CloneHas') {
            $area = $this->getArea('Clone');
            if (!isset($area) 
                || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_has_stage);
        } elseif ($condition == '#VegHas') {
            $area = $this->getArea('Veg');
            if (!isset($area) 
                || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_has_stage);
        } elseif ($condition == '#FlowerHas') {
            $area = $this->getArea('Flower');
            if (!isset($area) 
                || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_has_stage);
        } elseif ($condition == '#DryingOnSite') {
            $area = $this->getArea('Dry');
            if (!isset($area) 
                || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_has_stage);
        } elseif ($condition == '#MotherArtificialLight') {
            return $this->runCondArtifArea('Mother');
        } elseif ($condition == '#CloneArtificialLight') {
            return $this->runCondArtifArea('Clone');
        } elseif ($condition == '#VegArtificialLight') {
            return $this->runCondArtifArea('Veg');
        } elseif ($condition == '#FlowerArtificialLight') {
            return $this->runCondArtifArea('Flower');
        } elseif ($condition == '#HasArtificialLight') { // could be replaced by OR functionality
            if ($this->runCondArtifArea('Mother') == 1 
                || $this->runCondArtifArea('Clone') == 1
                || $this->runCondArtifArea('Veg') == 1 
                || $this->runCondArtifArea('Flower') == 1) {
                return 1;
            }
            return 0;
        } elseif ($condition == '#MotherSunlight') {
            $area = $this->getArea('Mother');
            if (!isset($area) || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_lgt_sun);
        } elseif ($condition == '#CloneSunlight') {
            $area = $this->getArea('Clone');
            if (!isset($area) || !isset($area->ps_area_has_stage)) {
                return 0;
            }
            return intVal($area->ps_area_lgt_sun);
        } elseif ($condition == '#VegSunlight') {
            $area = $this->getArea('Veg');
            if (!isset($area) || !isset($area->ps_area_has_stage)) return 0;
            return intVal($area->ps_area_lgt_sun);
        } elseif ($condition == '#FlowerSunlight') {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->ps_area_has_stage)) return 0;
            return intVal($area->ps_area_lgt_sun);
        } elseif ($condition == '#SunlightVegOrFlower') { // could be replaced by OR functionality
            if (isset($this->sessData->dataSets["powerscore"])) {
                $ps = $this->sessData->dataSets["powerscore"][0];
                if (isset($ps)) {
                    if (isset($ps->ps_veg_sun) && intVal($ps->ps_veg_sun) == 1) {
                        return 1;
                    }
                    if (isset($ps->ps_flower_sun) && intVal($ps->ps_flower_sun) == 1) {
                        return 1;
                    }
                }
            }
            return 0;
        } elseif (in_array($condition, [
                '#IndoorFlower5Ksf', 
                '#IndoorFlower10Ksf', 
                '#IndoorFlower30Ksf', 
                '#IndoorFlower50Ksf', 
                '#IndoorFlowerOver50Ksf'
                ])) {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->ps_area_has_stage) 
                || !isset($area->ps_area_size) || intVal($area->ps_area_size) == 0) {
                return 0;
            }
            switch ($condition) {
                case '#IndoorFlower5Ksf':
                    return ($area->ps_area_size < 5000);
                case '#IndoorFlower10Ksf':
                    return ($area->ps_area_size <= 5000 && $area->ps_area_size < 10000);
                case '#IndoorFlower30Ksf':
                    return ($area->ps_area_size <= 10000 && $area->ps_area_size < 30000);
                case '#IndoorFlower50Ksf':
                    return ($area->ps_area_size <= 30000 && $area->ps_area_size < 50000);
                case '#IndoorFlowerOver50Ksf': 
                    return ($area->ps_area_size >= 50000);
            }
            return 0;
            
        } elseif ($condition == '#HasUniqueness') {
            for ($i = 1; $i < 9; $i++) {
                if (isset($this->sessData->dataSets["powerscore"][0]->{ 'ps_uniqueness' . $i })
                    && trim($this->sessData->dataSets["powerscore"][0]->{ 'ps_uniqueness' . $i }) != '') {
                    return 1;
                }
            }
            return 0;
        } elseif ($condition == '#HasFeedback') {
            for ($i = 1; $i < 9; $i++) {
                if (isset($this->sessData->dataSets["powerscore"][0]->{ 'ps_feedback' . $i })
                    && trim($this->sessData->dataSets["powerscore"][0]->{ 'ps_feedback' . $i }) != '') {
                    return 1;
                }
            }
            return 0;
        } elseif ($condition == '#ScoreNotLeader') {
            if (isset($this->sessData->dataSets["powerscore"][0]->ps_effic_overall)) {
                if (round($this->sessData->dataSets["powerscore"][0]->ps_effic_overall) < 67) {
                    return 1;
                } else {
                    return 0;
                }
            }
            return -1;
        } elseif ($condition == '#ReportDetailsPublic') { 
            return 1;

            // could be replaced by OR functionality
            if (isset($this->v["user"]) 
                && $this->v["user"] 
                && $this->v["user"]->hasRole('administrator|staff|partner')) {
                return 1;
            }
            $privDef = $GLOBALS["SL"]->def->getID('PowerScore Privacy Options', 'Private');
            if (isset($this->sessData->dataSets["powerscore"][0]->ps_privacy)
                && intVal($this->sessData->dataSets["powerscore"][0]->ps_privacy) == $privDef) {
                return 0;
            }
            return 1;
        }
        return -1;
    }
    
    private function runCondArtifArea($areaName)
    {
        $area = $this->getArea($areaName);
        if (!isset($area) || !isset($area->ps_area_has_stage)) {
            return 0;
        }
        return intVal($area->ps_area_lgt_artif);
    }
    
    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        if (!isset($this->sessData->dataSets["powerscore"])) {
            return $emailBody;
        }
        $rankSim = $this->getSimilarStats();
        $dynamos = [
            '[{ PowerScore }]',
            '[{ PowerScore Percentile }]',
            '[{ PowerScore Report Link Similar }]',
            '[{ PowerScore Similar }]',
            '[{ PowerScore Dashboard Similar }]',
            '[{ Production Score }]',
            '[{ PowerScore Total Submissions }]',
            '[{ Zip Code }]',
            '[{ Farm Name }]',
            '[{ Farm Type }]'
        ];
        foreach ($dynamos as $dy) {
            if (strpos($emailBody, $dy) !== false) {
                $swap = $dy;
                $dyCore = str_replace('[{ ', '', str_replace(' }]', '', $dy));
                switch ($dy) {
                    case '[{ PowerScore }]': 
                    case '[{ PowerScore Percentile }]': 
                        $swap = round($this->sessData->dataSets["powerscore"][0]
                                ->ps_effic_overall) 
                            . $GLOBALS["SL"]->numSupscript(
                                round($this->sessData->dataSets["powerscore"][0]
                                    ->ps_effic_overall)) . ' percentile';
                        break;
                    case '[{ Production Score }]':
                        $swap = $GLOBALS["CUST"]->cnvrtLbs2Grm(
                            $this->sessData->dataSets["powerscore"][0]->ps_effic_production);
                        $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        break;
                    case '[{ PowerScore Total Submissions }]': 
                        $chk = RIIPowerScore::where('ps_email', 'NOT LIKE', '')
                            ->get();
                        $swap = $chk->count();
                        break;
                    case '[{ PowerScore Report Link Similar }]':
                        $swap = $GLOBALS["SL"]->sysOpts["app-url"] 
                            . '/calculated/read-' . $this->coreID . '?fltFarm='
                            . $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                        $swap = '<a href="' . $swap . '" target="_blank">' . $swap . '</a>';
                        break;
                    case '[{ PowerScore Similar }]':
                        $swap = round($rankSim->ps_rnk_overall)
                            . $GLOBALS["SL"]->numSupscript(round($rankSim->ps_rnk_overall)) 
                            . ' percentile';
                        break;
                    case '[{ PowerScore Dashboard Similar }]':
                        $fltDesc = $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                        $fltDesc = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $fltDesc);
                        $fltDesc = str_replace('/', '/ ', strtolower($fltDesc));
                        $swap = view(
                            'vendor.cannabisscore.nodes.490-report-calculations-preview', 
                            [
                                "ps"       => $this->sessData->dataSets["powerscore"][0],
                                "rank"     => $rankSim,
                                "filtDesc" => $fltDesc
                            ]
                        )->render();
                        break;
                    case '[{ Zip Code }]': 
                        if (isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_zip_code;
                        }
                        break;
                    case '[{ Farm Name }]': 
                        if (isset($this->sessData->dataSets["powerscore"][0]->ps_name)) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_name;
                        } elseif (isset($this->sessData->dataSets["powerscore"][0]->ps_email)) {
                            $chkEma = User::where('email', 
                                    $this->sessData->dataSets["powerscore"][0]->ps_email)
                                ->first();
                            if ($chkEma && isset($chkEma->name) && trim($chkEma->name) != '') {
                                $swap = $chkEma->name;
                            }
                        }
                        if (in_array(trim($swap), ['', $dy])) {
                            $swap = 'Resource Innovator';
                        }
                        break;
                    case '[{ Farm Type }]': 
                        $swap = $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                        $swap = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $swap);
                        $swap = str_replace('/', '/ ', strtolower($swap));
                        break;
                }
                $emailBody = str_replace($dy, $swap, $emailBody);
            }
        }
        return $emailBody;
    }
    
    protected function prepFeedbackSkipBtn()
    {
        $this->v["psOwner"] = (($GLOBALS["SL"]->REQ->has('psid') 
            && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) 
            ? intVal($GLOBALS["SL"]->REQ->get('psid')) 
            : ((session()->has('PowerScoreOwner')) 
                ? intVal(session()->get('PowerScoreOwner')) : -3));
        if (intVal($this->v["psOwner"]) > 0 
            && isset($this->sessData->dataSets["ps_feedback"])
            && isset($this->sessData->dataSets["ps_feedback"][0])) {
            $this->sessData->dataSets["ps_feedback"][0]->psf_ps_id = $this->v["psOwner"];
            $this->sessData->dataSets["ps_feedback"][0]->save();
        }
        return true;
    }
    
    protected function prepUtilityRefTitle()
    {
        if (isset($this->sessData->dataSets["ps_referral"]) 
            && sizeof($this->sessData->dataSets["ps_referral"]) > 0) {
            if ($GLOBALS["SL"]->REQ->has('u') 
                && intVal($GLOBALS["SL"]->REQ->get('u')) > 0) {
                $this->sessData->dataSets["ps_referral"][0]
                    ->ps_ref_utility = intVal($GLOBALS["SL"]->REQ->get('u'));
                $this->sessData->dataSets["ps_referral"][0]->save();
            }
            if ($GLOBALS["SL"]->REQ->has('s') 
                && intVal($GLOBALS["SL"]->REQ->get('s')) > 0) {
                $scoreID = intVal($GLOBALS["SL"]->REQ->get('s'));
                $this->sessData->dataSets["ps_referral"][0]->ps_ref_power_score = $scoreID;
                $this->sessData->loadData('powerscore', $scoreID);
                if (isset($this->sessData->dataSets["powerscore"])) {
                    $ps = $this->sessData->dataSets["powerscore"];
                    if (sizeof($ps) > 0 && isset($ps[0]->ps_email)) {
                        $powerscoreOwner = false;
                        if ($this->v["uID"] == $ps[0]->ps_user_id) {
                            $powerscoreOwner = true;
                        }
                        $sess = 'PowerScoreOwner' . $scoreID;
                        if (session()->has($sess) 
                            && intVal(session()->get($sess)) == $scoreID) {
                            $powerscoreOwner = true;
                        }
                        if ($powerscoreOwner) {
                            $this->sessData->dataSets["ps_referral"][0]
                                ->ps_ref_email = $ps[0]->ps_email;
                        }
                    }
                }
                $this->sessData->dataSets["ps_referral"][0]->save();
            }
        }
        $this->chkUtilityOffers();
        return true;
    }

    protected function auditLgtAlerts()
    {
        $auditFailed = false;
        $auditAreas = $lgts = $watts = $this->v["areaCnts"] = [];
        if (isset($this->sessData->dataSets["ps_light_types"])) {
            $lgts = $this->sessData->dataSets["ps_light_types"];
        }
        if (isset($this->sessData->dataSets["ps_areas"])) {
            $printables = [];
            $areas = $this->sessData->dataSets["ps_areas"];
            if (sizeof($areas) > 0) {
                foreach ($areas as $a => $area) {
                    $auditAreas[$a] = '';
                    if (isset($area->ps_area_has_stage) 
                        && intVal($area->ps_area_has_stage) == 1
                        && isset($area->ps_area_size) 
                        && intVal($area->ps_area_size) > 0
                        && isset($area->ps_area_lgt_artif) 
                        && intVal($area->ps_area_lgt_artif) == 1) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->ps_area_type == $defID && $typ != 'Dry') {
                                $printables[] = $area;
                                $auditAreas[$a] = trim($this->auditLgtAlertArea($a, $area, $typ));
                                if ($auditAreas[$a] != '') {
                                    $auditFailed = true;
                                }
                            }
                        }
                    }
                }
                $printable = [];
                foreach ($auditAreas as $a) {
                    if (trim($a) != '') {
                        $printable[] = $a;
                    }
                }
                $auditTbl = view(
                    'vendor.cannabisscore.nodes.878-lighting-audit-tots', 
                    [
                        "areas"    => $areas,
                        "areaCnts" => $this->v["areaCnts"]
                    ]
                )->render();
                $GLOBALS["SL"]->pageJAVA .= view(
                    'vendor.cannabisscore.nodes.878-lighting-audit-js', 
                    [
                        "auditAreas" => $printable,
                        "areas"      => $areas,
                        "auditTbl"   => $auditTbl,
                        "areaCnts"   => $this->v["areaCnts"]
                    ]
                )->render();
            }
        }
        return $auditFailed;
    }

    protected function auditLgtAlertArea($a, $area, $typ)
    {
        $ret = '';
        $watts[$typ] = $fixCnt = 0;
        $this->chkLgtWatts($area, $typ, $watts, $fixCnt);
        $area->ps_area_total_light_watts = $watts[$typ];
        $area->ps_area_sq_ft_per_fix2 = (($fixCnt > 0) ? $area->ps_area_size/$fixCnt : 0);
        $area->save();
        $this->sessData->dataSets["ps_areas"][$a] = $area;
        $this->v["areaCnts"][$area->ps_area_id] = $fixCnt;
        if ($fixCnt == 0) {
            $ret = 'No lights were added for your ' . strtolower($typ) . ' stage.';
        } elseif ($area->ps_area_sq_ft_per_fix2 < 4) {
            $ret = 'You only listed ' . $fixCnt . ' lighting fixture' 
                . (($fixCnt == 1) ? '' : 's') . ' in your ' . strtolower($typ) . ' stage. '
                . round($area->ps_area_sq_ft_per_fix2) . ' square feet per fixture is very low. ';
        } elseif ($area->ps_area_sq_ft_per_fix2 > 81) {
            $ret = 'You listed ' . $fixCnt . ' lighting fixtures in your ' 
                . strtolower($typ) . ' stage. ' . round($area->ps_area_sq_ft_per_fix2)
                . ' square feet per fixture is very high. ';
        }
        return $ret;
    }
    
}