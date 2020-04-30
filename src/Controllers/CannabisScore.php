<?php
/**
  * CannabisScore extends ScoreImports extends ScoreAdminMisc extends ScoreReports extends ScoreReports
  * extends ScoreCalcs extends ScoreUtils extends ScorePowerUtilities extends ScoreLightModels 
  * extends ScoreVars extends TreeSurvForm. This class contains the majority of 
  * SurvLoop functions which are overwritten, and delegates most of the work.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SLNodeSaves;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIPsRenewables;
use App\Models\RIIPsUtilities;
use App\Models\RIIPsUtiliZips;
use App\Models\RIIPsForCup;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPsLicenses;
use App\Models\RIIUserInfo;
use CannabisScore\Controllers\ScoreFormsCustom;
use CannabisScore\Controllers\ScoreReportFound;
use CannabisScore\Controllers\ScoreReportAvgs;
use CannabisScore\Controllers\ScoreReportHvac;
use CannabisScore\Controllers\ScoreReportLighting;
use CannabisScore\Controllers\ScoreListings;
use CannabisScore\Controllers\ScoreReports;
use CannabisScore\Controllers\ScoreAdminManageManu;
use CannabisScore\Controllers\ScoreImports;

class CannabisScore extends ScoreImports
{
    protected function customNodePrint(&$curr = null)
    {
        $ret = '';
        $nID = $curr->nID;
        $nIDtxt = $curr->nIDtxt;
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 393) {
            $areas = $this->sessData->getLoopRowIDs('Growth Stages');
            $GLOBALS["SL"]->pageAJAX .= view(
                'vendor.cannabisscore.nodes.393-area-lighting-ajax', 
                [ "areas" => $areas ]
            )->render();
        } elseif (in_array($nID, [74, 396, 1124])) {
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
            if ($GLOBALS["SL"]->REQ->has('recalc')) {
                $this->calcCurrSubScores();
            }
            if (Auth::user() 
                && Auth::user()->hasRole('administrator|staff|partner')) {
                $GLOBALS["SL"]->x["indivFilters"] = true;
            }
            $ret .= $this->customPrint490($nID);
        } elseif ($nID == 1008) {
            $ret .= view(
                'vendor.cannabisscore.nodes.1008-powerscore-calculations-mockup', 
                $this->v
            )->render();
        } elseif ($nID == 946) {
            $ret .= $this->printPsRankingFilters($nID);
        } elseif (in_array($nID, [878])) { // , 1273
            $this->auditLgtAlerts($nID);
        } elseif (in_array($nID, [1089, 1090, 1091, 1092, 1093])) {
            return $this->printHvacInfoAccord($nID, $nIDtxt);
        } elseif ($nID == 860) {
            return $this->printReportForCompetition($nID);
        } elseif ($nID == 861) {
            return $this->printReportGrowingYear($nID);
        } elseif ($nID == 508) {
            $ret .= $this->printReportUtilRef($nID);
            
        // PowerScore Reporting
        } elseif (in_array($nID, [744, 1381])) {
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            if ($nID == 744) {
                $ret .= $report->getCultClassicReport();
            } else {
                $ret .= $report->getCultClassicMultiYearReport();
            }
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
        } elseif (in_array($nID, [799, 1373])) { // Partner Multi-Site Listings
            $report = new ScoreReports($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            if ($nID == 1373) {
                $GLOBALS["SL"]->x["officialSet"] = true;
            }
            $ret .= $report->getAllPowerScoresPublic($nID);
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
        } elseif ($nID == 773) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getAllPowerScoreAvgsPublic();
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
        } elseif ($nID == 859) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getMorePowerStats();
        } elseif ($nID == 801) {
            $report = new ScoreReportAvgs;
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->getAllPowerScoreAvgsPublic();
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
        } elseif ($nID == 979) {
            $report = new ScoreListings(
                $this->v["uID"], 
                $this->v["user"], 
                $this->v["usrInfo"]
            );
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->printCompetitiveReport($nID);
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
        } elseif ($nID == 797) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getPowerScoreFinalReport();
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
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
            $GLOBALS["SL"]->pageJAVA .= ' openAdmMenuOnLoad = false; ';
            
        // MA
        } elseif ($nID == 1403) {
            $this->calcMaCompliance($nID);
        } elseif ($nID == 1420) {
            $ret .= $this->reportMaMonths($nID);
        } elseif ($nID == 1436) {
            $ret .= $this->reportMaNextPro($nID);
            
/*
        } elseif ($nID == 1120) {
//echo 'currNodeSessData <pre>'; print_r($this->v["currNodeSessData"]); echo '</pre>'; exit;
            //$ret .= $GLOBALS["SL"]->num2Month3($this->v["currNodeSessData"]);
        } elseif ($nID == 1103) {
            $ret .= $this->maMonthTblElectric($nID);
        } elseif ($nID == 1121) {
            $ret .= $this->maMonthTblDelivered($nID);
        } elseif ($nID == 1122) {
            $ret .= $this->maMonthTblWater($nID);
        } elseif ($nID == 1123) {
            $ret .= $this->maMonthTblRenew($nID);
*/
            
        // Admin Tools
        } elseif ($nID == 914) {
            $this->initManuAdmin();
            $ret .= $this->v["manuAdmin"]->printMgmtManufacturers($nID);
        } elseif ($nID == 1293) {
            $this->initManuAdmin();
            $ret .= $this->v["manuAdmin"]->printAddManufacturers($nID);
        } elseif ($nID == 915) {
            $this->initManuAdmin();
            $ret .= $this->v["manuAdmin"]->printMgmtPartners($nID);
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
        } elseif ($nID == 1276) {
            $this->excelExportMyScores($nID);
        } elseif ($nID == 843) {
            $ret .= $this->printProfileExtraBtns();
        } elseif ($nID == 1039) {
            $ret .= $this->printPartnerProfileDashBtn($nID);
        } elseif ($nID == 1040) {
            $ret .= $this->printPartnerProfileDashHead($nID);

        }
        return $ret;
    }

    protected function initManuAdmin()
    {
        if (!isset($this->v["manuAdmin"])) {
            $this->v["manuAdmin"] = new ScoreAdminManageManu;
        }
        return true;
    }
    
    protected function customResponses($nID, &$curr)
    {
        if (in_array($nID, [57, 1073])) {
            $curr->clearResponses();
            if (isset($this->sessData->dataSets["powerscore"]) 
                && isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                $utIDs = RIIPsUtiliZips::where('ps_ut_zp_zip_code', 
                        $this->sessData->dataSets["powerscore"][0]->ps_zip_code)
                    ->get();
                if ($utIDs->isNotEmpty()) {
                    $ids = [];
                    foreach ($utIDs as $u) {
                        $ids[] = $u->ps_ut_zp_util_id;
                    }
                    $uts = RIIPsUtilities::whereIn('ps_ut_id', $ids)
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

    protected function postNodePublicCustom(&$curr)
    { 
        $nID = $curr->nID;
        if (empty($tmpSubTier)) {
            $tmpSubTier = $this->loadNodeSubTier($nID);
        }
        
        if ($nID == 47) {
            $this->postZipCode($nID);
        } elseif ($nID == 1244) {
            $this->postRoomCnt($nID);
        } elseif ($nID == 1233) {
            $this->postRoomLightCnt($nID);
        } elseif ($nID == 1274) {
            return $this->postRoomLightTypeComplete($nID, $curr->nIDtxt);
        } elseif ($nID == 1292) {
            return $this->postRoomHvacType($nID, $curr->nIDtxt);
        } elseif ($nID == 1083) {
            $this->sessData->refreshDataSets();
        } elseif ($nID == 74) { // dump monthly grams
            $this->postMonthlies($curr->nIDtxt, 'ps_month_grams');
        } elseif ($nID == 70) { // dump monthly energy
            $this->postMonthlies($curr->nIDtxt, 'ps_month_kwh1');
        } elseif ($nID == 949) { // dump monthly green waste pounds
            $this->postMonthlies($curr->nIDtxt, 'ps_month_waste_lbs');
        } elseif (in_array($nID, [57, 1073, 1074])) {
            $foundOther = '';
            for ($i = 0; ($i < 20 && $foundOther == ''); $i++) {
                $fld = 'n' . $curr->nIDtxt . 'fldOther' . $i;
                if ($GLOBALS["SL"]->REQ->has($fld) 
                    && trim($GLOBALS["SL"]->REQ->get($fld)) != '') {
                    $foundOther = trim($GLOBALS["SL"]->REQ->get($fld));
                }
            }
            $this->sessData->dataSets["powerscore"][0]->update([
                'ps_source_utility_other' => $foundOther
            ]);
            $this->sessData->dataSets["powerscore"][0]->save();
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
            
          
        /*  
        } elseif ($nID == 914) {
            if (!isset($this->v["manuAdmin"])) {
                $this->v["manuAdmin"] = new ScoreAdminManageManu;
            }
            return $this->v["manuAdmin"]->addManufacturers($nID);
        */
        } elseif ($nID == 917) {
            return $this->addLightModels($nID);
        }
        return false; // false to continue standard post processing
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
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function printNodeSessDataOverride(&$curr)
    {
        if (sizeof($this->sessData->dataSets) == 0) {
            return [];
        }
        $nID = $curr->nID;
        $nIDtxt = $curr->nIDtxt;
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
        } elseif ($nID == 1088) {
            return $this->printNodeSessRoomHvacType($nID, $nIDtxt);
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
        } elseif (in_array($nID, [1307, 1365, 1336, 1335, 1333, 1334])) {
            if ($curr->sessData < 0.00001) {
                return [0];
            }
        }
        return [];
    }
    
    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $rankSim = $this->getSimilarStats();
        }
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
            '[{ Farm Type }]',
            '[{ Partner Name }]',
            '[{ Partner Slug }]'
        ];
        foreach ($dynamos as $dy) {
            if (strpos($emailBody, $dy) !== false) {
                $swap = $dy;
                $dyCore = str_replace('[{ ', '', str_replace(' }]', '', $dy));
                switch ($dy) {
                    case '[{ PowerScore }]': 
                    case '[{ PowerScore Percentile }]': 
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = round($this->sessData->dataSets["powerscore"][0]
                                    ->ps_effic_overall) 
                                . $GLOBALS["SL"]->numSupscript(
                                    round($this->sessData->dataSets["powerscore"][0]
                                        ->ps_effic_overall)) . ' percentile';
                        }
                        break;
                    case '[{ Production Score }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $GLOBALS["SL"]->cnvrtLbs2Grm(
                                $this->sessData->dataSets["powerscore"][0]->ps_effic_production);
                            $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        }
                        break;
                    case '[{ PowerScore Total Submissions }]': 
                        $chk = RIIPowerscore::where('ps_email', 'NOT LIKE', '')
                            ->get();
                        $swap = $chk->count();
                        break;
                    case '[{ PowerScore Report Link Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $GLOBALS["SL"]->sysOpts["app-url"] 
                                . '/calculated/read-' . $this->coreID . '?fltFarm='
                                . $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                            $swap = '<a href="' . $swap . '" target="_blank">' . $swap . '</a>';
                        }
                        break;
                    case '[{ PowerScore Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = round($rankSim->ps_rnk_overall)
                                . $GLOBALS["SL"]->numSupscript(round($rankSim->ps_rnk_overall)) 
                                . ' percentile';
                        }
                        break;
                    case '[{ PowerScore Dashboard Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
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
                        }
                        break;
                    case '[{ Zip Code }]': 
                        if (isset($this->sessData->dataSets["powerscore"])
                            && isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_zip_code;
                        }
                        break;
                    case '[{ Farm Name }]': 
                        if (isset($this->sessData->dataSets["powerscore"])) {
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
                        }
                        if (in_array(trim($swap), ['', $dy])) {
                            $swap = 'Resource Innovator';
                        }
                        break;
                    case '[{ Farm Type }]': 
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                            $swap = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $swap);
                            $swap = str_replace('/', '/ ', strtolower($swap));
                        }
                        break;
                    case '[{ Partner Name }]': 
                        if (isset($this->v["partnerRec"]) 
                            && isset($this->v["partnerRec"]->usr_company_name)
                            && trim($this->v["partnerRec"]->usr_company_name) != '') {
                            $swap = $this->v["partnerRec"]->usr_company_name;
                        }
                        break;
                    case '[{ Partner Slug }]': 
                        if (isset($this->v["partnerRec"]) 
                            && isset($this->v["partnerRec"]->usr_referral_slug)
                            && trim($this->v["partnerRec"]->usr_referral_slug) != '') {
                            $swap = $this->v["partnerRec"]->usr_referral_slug;
                        }
                        break;
                }
                $emailBody = str_replace($dy, $swap, $emailBody);
            }
        }
        return $emailBody;
    }

    public function startForPartner(Request $request, $prtnSlug)
    {
        $this->loadPageVariation($request, 1, 89, '/start-for-');
        $this->v["partnerRec"] = RIIUserInfo::where('usr_referral_slug', $prtnSlug)
            ->first();
        return $this->index($request);
    }

}