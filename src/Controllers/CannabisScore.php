<?php
/**
  * CannabisScore extends ScoreImports extends ScoreAdminMisc extends ScoreReports extends ScoreListings
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
use CannabisScore\Controllers\ScoreListings;
use CannabisScore\Controllers\ScoreImports;

class CannabisScore extends ScoreImports
{
    public function printPreviewReport($isAdmin = false)
    {
        return view('vendor.cannabisscore.powerscore-report-preview', [
            "uID"      => $this->v["uID"],
            "sessData" => $this->sessData->dataSets
            ]);
    }
    
    protected function customNodePrint($nID = -3, $tmpSubTier = [], $nIDtxt = '', $nSffx = '', $currVisib = 1)
    {
        $ret = '';
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 701) {
            $this->pageJSvalid .= "errorFocus[errorFocus.length] = new Array('701', 'n575fld0'); var idList = "
                . "new Array('n575fld0', 'n577fld0', 'n578fld0', 'n579fld0', 'n306fld0', 'n495fld0', 'n574fld0'); "
                . "function tryRadioCustom() { if (typeof addReqNodeRadioCustom === \"function\") "
                . "addReqNodeRadioCustom('701', 'reqFormFldRadioCustom', idList); \n"
                . "else setTimeout(\"tryRadioCustom()\", 500); return !0 \n } \n"
                . "setTimeout(\"tryRadioCustom()\", 100); \n";
        } elseif ($nID == 393) {
            $GLOBALS["SL"]->pageAJAX .= view('vendor.cannabisscore.nodes.393-area-lighting-ajax', [
                "areas" => $this->sessData->getLoopRowIDs('Growth Stages')
                ])->render();
        } elseif (in_array($nID, [74, 396])) {
            $ret .= $this->printGramForm($nID, $nIDtxt);
        } elseif (in_array($nID, [70, 397])) {
            $ret .= $this->printKwhForm($nID, $nIDtxt);
        } elseif ($nID == 362) {
            $GLOBALS["SL"]->loadStates();
            $this->getStateUtils();
            $ret .= view('vendor.cannabisscore.nodes.362-utilities-by-state', $this->v)->render();
        } elseif ($nID == 502) {
            $this->chkUtilityOffers();
            $ret .= view('vendor.cannabisscore.nodes.502-utility-offers', $this->v)->render();
        } elseif (in_array($nID, [177, 457, 465, 471])) {
            return $this->printReportBlds($nID);
        } elseif (in_array($nID, [209, 432, 440, 448])) {
            return $this->printReportLgts($nID);
        } elseif ($nID == 536) {
            $this->prepFeedbackSkipBtn();
            $GLOBALS["SL"]->pageJAVA .= view('vendor.cannabisscore.nodes.536-feedback-skip-button-java', $this->v)
                ->render();
        } elseif ($nID == 548) {
            $this->prepFeedbackSkipLnk();
            $ret .= view('vendor.cannabisscore.nodes.548-powerscore-feedback-score-link', $this->v)->render();
        } elseif ($nID == 148) { // this should be built-in
            $this->sessData->dataSets["PowerScore"][0]->PsStatus = $this->v["defCmplt"];
            $this->sessData->dataSets["PowerScore"][0]->save();
            session()->put('PowerScoreOwner', $this->coreID);
            session()->put('PowerScoreOwner' . $this->coreID, $this->coreID);
            
        } elseif ($nID == 490) {
            $ret .= $this->customPrint490($nID);
        } elseif ($nID == 860) {
            if (isset($this->sessData->dataSets["PSForCup"])) {
                $deetVal = '';
                foreach ($this->sessData->dataSets["PSForCup"] as $i => $cup) {
                    if (isset($cup->PsCupCupID) && intVal($cup->PsCupCupID) > 0) {
                        $deetVal .= (($i > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->def->getVal('PowerScore Competitions', $cup->PsCupCupID);
                    }
                }
                return ['Competition', $deetVal, $nID];
            }
        } elseif ($nID == 861) {
            if (isset($this->sessData->dataSets["PowerScore"])
                && isset($this->sessData->dataSets["PowerScore"][0]->PsYear)) {
                return ['Growing Year', $this->sessData->dataSets["PowerScore"][0]->PsYear, $nID];
            }
        } elseif ($nID == 508) {
            $this->prepUtilityRefTitle();
            $ret .= view('vendor.cannabisscore.nodes.508-utility-referral-title', $this->v)->render();
            
        // PowerScore Reporting
        } elseif ($nID == 744) {
            $report = new ScoreListings;
            $ret .= $report->getCultClassicReport();
        } elseif ($nID == 170) {
            $report = new ScoreListings;
            $ret .= $report->getAllPowerScoresPublic($nID);
        } elseif ($nID == 799) {
            $report = new ScoreListings;
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
        } elseif ($nID == 726) {
            $ret .= $this->printDashSessGraph();
            
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
            
        // Misc
        } elseif ($nID == 843) {
            $ret .= $this->printProfileExtraBtns();
        }
        return $ret;
    }
    
    protected function customCleanLabel($str = '', $nIDtxt = '')
    {
        if ($this->treeID == 1) {
            if (isset($this->sessData->dataSets["PowerScore"]) 
                && isset($this->sessData->dataSets["PowerScore"][0]->PsTimeType)
                && $this->sessData->dataSets["PowerScore"][0]->PsTimeType 
                    == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                $str = str_replace('Does your', 'Will your', str_replace('does your', 'will your', $str));
                $str = str_replace('do you ', 'will you ', $str);
            }
        }
        return $str; 
    }
    
    protected function customResponses($nID, $curr)
    {
        if ($nID == 57) {
            $curr->clearResponses();
            if (isset($this->sessData->dataSets["PowerScore"]) 
                && isset($this->sessData->dataSets["PowerScore"][0]->PsZipCode)) {
                $utIDs = RIIPSUtiliZips::where('PsUtZpZipCode', $this->sessData->dataSets["PowerScore"][0]->PsZipCode)
                    ->get();
                if ($utIDs->isNotEmpty()) {
                    $ids = [];
                    foreach ($utIDs as $u) {
                        $ids[] = $u->PsUtZpUtilID;
                    }
                    $uts = RIIPSUtilities::whereIn('PsUtID', $ids)
                        ->get(); // will be upgrade to check for farm's zip code
                    if ($uts->isNotEmpty()) {
                        foreach ($uts as $i => $ut) {
                            $curr->addTmpResponse($ut->PsUtID, $ut->PsUtName);
                        }
                    }
                }
            }
            $curr->addTmpResponse(0, 'Other:');
            $curr->dataStore = 'PowerScore:PsSourceUtility';
            $curr->chkFldOther();
            $curr->dataStore = 'PSUtiliLinks:PsUtLnkUtilityID';
        }
        return $curr;
    }
    
    protected function postNodePublicCustom($nID = -3, $tmpSubTier = [])
    { 
        if (empty($tmpSubTier)) {
            $tmpSubTier = $this->loadNodeSubTier($nID);
        }
        list($tbl, $fld) = $this->allNodes[$nID]->getTblFld();
        
        if ($nID == 47) {
            if ($GLOBALS["SL"]->REQ->has('n47fld') && trim($GLOBALS["SL"]->REQ->get('n47fld')) != '') {
                $this->sessData->updateZipInfo($GLOBALS["SL"]->REQ->get('n47fld'), 
                    'PowerScore', 'PsState', 'PsCounty', 'PsAshrae', 'PsCountry');
            }
        } elseif ($nID == 70) { // dump monthly energy notes
            $currMonth = (($GLOBALS["SL"]->REQ->has('elecMonth')) ? intVal($GLOBALS["SL"]->REQ->elecMonth) : 1);
            $powerMonths = $this->sortMonths();
            foreach ($powerMonths as $i => $row) {
                $row->PsMonthMonth = $currMonth;
                $f = 'elec' . (1+$i);
                $row->PsMonthKWH1  = (($GLOBALS["SL"]->REQ->has($f . 'a')) ? intVal($GLOBALS["SL"]->REQ->get($f . 'a')) 
                    : null);
                $row->PsMonthNotes = (($GLOBALS["SL"]->REQ->has($f . 'd')) ? trim($GLOBALS["SL"]->REQ->get($f . 'd')) 
                    : null);
                $row->save();
                $currMonth++;
                if ($currMonth == 13) $currMonth = 1;
            }
        } elseif ($nID == 57) {
            $foundOther = '';
            for ($i = 0; ($i < 20 && $foundOther == ''); $i++) {
                if ($GLOBALS["SL"]->REQ->has('n57fldOther' . $i) 
                    && trim($GLOBALS["SL"]->REQ->get('n57fldOther' . $i)) != '') {
                    $foundOther = trim($GLOBALS["SL"]->REQ->get('n57fldOther' . $i));
                }
            }
            $this->sessData->dataSets["PowerScore"][0]->PsSourceUtilityOther = $foundOther;
            $this->sessData->dataSets["PowerScore"][0]->save();
        } elseif ($nID == 398) {
            if ($GLOBALS["SL"]->REQ->has('n' . $nID . 'fld')) {
                $this->sessData->dataSets["PowerScore"][0]->update([
                    'PsTotalSize' => $GLOBALS["SL"]->REQ->get('n' . $nID . 'fld') ]);
            }
        } elseif (in_array($nID, [59, 80, 60, 61, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["PSRenewables"]) 
                && sizeof($this->sessData->dataSets["PSRenewables"]) > 0) {
                foreach ($this->sessData->dataSets["PSRenewables"] as $ind => $row) {
                    if (isset($row->PsRnwRenewable) && $row->PsRnwRenewable == $sourceID) {
                        $this->sessData->dataSets["PSRenewables"][$ind]->update([
                            'PsRnwLoadPercent' => (($GLOBALS["SL"]->REQ->has('n' . $nID . 'fld')) 
                                ? intVal($GLOBALS["SL"]->REQ->get('n' . $nID . 'fld')) : 0)
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
    
    public function printGramForm($nID, $nIDtxt)
    {
        $this->v["nID"] = $nID;
        $this->v["currSessData"] = 0;
        if (isset($this->sessData->dataSets["PowerScore"]) && isset($this->sessData->dataSets["PowerScore"][0])
            && isset($this->sessData->dataSets["PowerScore"][0]->PsGrams)) {
            $this->v["currSessData"] = $this->sessData->dataSets["PowerScore"][0]->PsGrams;
        }
        $this->pageJSvalid .= "addReqNodeRadio('" . $nIDtxt . "', 'reqFormFldGreater', 0.00000001);\n";
        return view('vendor.cannabisscore.nodes.74-total-grams', $this->v)->render();
    }
    
    public function printKwhForm($nID, $nIDtxt)
    {
        $this->v["nID"]         = $nID;
        $this->v["powerScore"]  = $this->sessData->dataSets["PowerScore"][0];
        $this->v["powerMonths"] = $this->sortMonths();
        $this->pageJSvalid .= "addReqNodeRadio('" . $nIDtxt . "', 'reqFormFldGreater', 0);\n";
        return view('vendor.cannabisscore.nodes.70-total-kwh', $this->v)->render();
    }
    
    public function customPrint490($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('isPreview')) {
            return '<style> #blockWrap492, #blockWrap501, #blockWrap727 { display: none; } </style>';
        }
        $ret = '';
        $this->v["nID"] = $nID;
        if ($GLOBALS["SL"]->REQ->has('refresh')) {
            $ret .= view('vendor.cannabisscore.nodes.490-report-calculations-top-refresh', [
                "psid" => $this->coreID
                ])->render();
        } else {
            $ret .= $this->printReport490();
        }
        return $ret;
    }
    
    public function printReport490()
    {
        if ($GLOBALS["SL"]->REQ->has('step') && $GLOBALS["SL"]->REQ->has('postAction')
            && trim($GLOBALS["SL"]->REQ->get('postAction')) != '') {
            return $this->redir($GLOBALS["SL"]->REQ->get('postAction'), true);
        }
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->getAllReportCalcs();
        if (!$GLOBALS["SL"]->REQ->has('fltFarm')) {
            $this->searcher->v["fltFarm"] = $this->sessData->dataSets["PowerScore"][0]->PsCharacterize;
            $this->searcher->searchFiltsURLXtra();
        }
        $this->searcher->v["nID"] = 490;
        $this->v["isPast"] = ($this->sessData->dataSets["PowerScore"][0]->PsTimeType 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        $this->v["psFiltChks"] = view('vendor.cannabisscore.inc-filter-powerscores-checkboxes', $this->searcher->v)
            ->render();
        $this->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->searcher->v)->render();
        return view('vendor.cannabisscore.nodes.490-report-calculations', $this->v)->render();
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
        $this->v["ajax-psid"] = (($GLOBALS["SL"]->REQ->has('psid') && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) 
            ? intVal($GLOBALS["SL"]->REQ->get('psid')) : -3);
        if (!$request->has('refresh') || intVal($request->get('refresh')) == 1) {
            $this->sessData->loadData('PowerScore', $this->v["ajax-psid"]);
            if (isset($this->sessData->dataSets["PowerScore"]) && sizeof($this->sessData->dataSets["PowerScore"]) > 0
                && isset($this->sessData->dataSets["PowerScore"][0]->PsEmail)) {
                $this->calcCurrSubScores();
                return view('vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', [
                    "msg"  => '<i class="slGrey">Recalculating Sub-Scores...',
                    "psid" => $this->v["ajax-psid"]
                    ])->render();
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
        $this->v["isPast"] = ($this->sessData->dataSets["PowerScore"][0]->PsTimeType 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        $this->v["psFiltChks"] = view('vendor.cannabisscore.inc-filter-powerscores-checkboxes', $this->searcher->v)
            ->render();
        $this->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->searcher->v)->render();
        return view('vendor.cannabisscore.nodes.490-report-calculations', $this->v)->render();
    }
    
    protected function ajaxScorePercentiles()
    {
        if (!$GLOBALS["SL"]->REQ->has('ps') || intVal($GLOBALS["SL"]->REQ->get('ps')) <= 0 
            || !$GLOBALS["SL"]->REQ->has('eff') || !in_array(trim($GLOBALS["SL"]->REQ->get('eff')), 
                ['Overall', 'Facility', 'Production', 'HVAC', 'Lighting'])) {
            return '';
        }
        $this->initSearcher();
        $this->searcher->searchResultsXtra(1);
        $this->searcher->searchFiltsURLXtra();
        if ($this->searcher->v["powerscore"] && isset($this->searcher->v["powerscore"]->PsID)) {
            $this->searcher->v["isPast"] = ($this->searcher->v["powerscore"]->PsTimeType 
                == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
            $currRanks = RIIPSRankings::where('PsRnkPSID', $this->searcher->v["powerscore"]->PsID)
                ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                ->first();
            if (!$currRanks || !isset($currRanks->PsRnkOverall) || $GLOBALS["SL"]->REQ->has('refresh')) {
                if (isset($this->searcher->v["powerscore"]->PsTimeType) && $this->searcher->v["powerscore"]->PsTimeType 
                    == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                    $ranks = RIIPSRanks::where('PsRnkFilters', '')
                        ->first();
                    $currRanks = new RIIPSRankings;
                    $currRanks->PsRnkPSID = $this->searcher->v["powerscore"]->PsID;
                    $currRanks->PsRnkFacility = $GLOBALS["SL"]->getArrPercentileStr(
                        $ranks->PsRnkFacility, $this->searcher->v["powerscore"]->PsEfficFacility);
                    $currRanks->PsRnkProduction = $GLOBALS["SL"]->getArrPercentileStr(
                        $ranks->PsRnkProduction, $this->searcher->v["powerscore"]->PsEfficProduction, true);
                    $currRanks->PsRnkLighting = $GLOBALS["SL"]->getArrPercentileStr(
                        $ranks->PsRnkLighting, $this->searcher->v["powerscore"]->PsEfficLighting);
                    $currRanks->PsRnkHVAC = $GLOBALS["SL"]->getArrPercentileStr(
                        $ranks->PsRnkHVAC, $this->searcher->v["powerscore"]->PsEfficHvac);
                    $currRanks->PsRnkOverallAvg = ($currRanks->PsRnkFacility+$currRanks->PsRnkProduction
                        +$currRanks->PsRnkLighting+$currRanks->PsRnkHVAC)/4;
                    $currRanks->PsRnkOverall = $GLOBALS["SL"]->getArrPercentileStr(
                        $ranks->PsRnkOverallAvg, $currRanks->PsRnkOverallAvg);
                    $currRanks->save();
                    $this->searcher->v["powerscore"]->PsEfficOverall = $currRanks->PsRnkOverall;
                    $this->searcher->v["powerscore"]->save();
                } else {
                    $urlFlts = $this->searcher->v["urlFlts"];
                    // $this->calcAllScoreRanks();
                    $this->searcher->v["urlFlts"] = $urlFlts;
                    $currRanks = RIIPSRankings::where('PsRnkPSID', $this->searcher->v["powerscore"]->PsID)
                        ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                        ->first();
                }
            }
            $this->searcher->v["currGuage"] = 0;
            $this->searcher->v["hasOverall"] = false;
            if (isset($currRanks->{ 'PsRnk' . $this->searcher->v["eff"] })) {
                $this->searcher->v["currGuage"] = round($currRanks->{ 'PsRnk' . $this->searcher->v["eff"] });
                $this->searcher->v["hasOverall"] = (isset($this->searcher->v["powerscore"]->PsEfficFacility) 
                    && isset($this->searcher->v["powerscore"]->PsEfficProduction) 
                    && isset($this->searcher->v["powerscore"]->PsEfficHvac) 
                    && isset($this->searcher->v["powerscore"]->PsEfficLighting) 
                    && $this->searcher->v["powerscore"]->PsEfficFacility > 0
                    && $this->searcher->v["powerscore"]->PsEfficProduction > 0 
                    && $this->searcher->v["powerscore"]->PsEfficHvac > 0
                    && $this->searcher->v["powerscore"]->PsEfficLighting > 0);
            }
            return view('vendor.cannabisscore.nodes.490-report-calculations-ajax-graphs', $this->searcher->v)->render();
        }
        return '';
    }
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function printNodeSessDataOverride($nID = -3, $tmpSubTier = [], $nIDtxt = '', $currNodeSessionData = '')
    {
        if (sizeof($this->sessData->dataSets) == 0) {
            return [];
        }
        if ($nID == 49) {
            if (isset($this->sessData->dataSets["PSFarm"]) 
                && isset($this->sessData->dataSets["PSFarm"][0]->PsFrmType)) {
                return [$this->sessData->dataSets["PSFarm"][0]->PsFrmType];
            }
        } elseif (in_array($nID, [864, 865])) {
            if (!isset($this->sessData->dataSets["PowerScore"]) 
                || !isset($this->sessData->dataSets["PowerScore"][0]->PsYear)
                || trim($this->sessData->dataSets["PowerScore"][0]->PsYear) == '') {
                if (intVal(date("n")) <= 6) {
                    return [intVal(date("Y"))-1];
                } else {
                    return [intVal(date("Y"))];
                }
            }
        } elseif ($nID == 57) {
            if (isset($this->sessData->dataSets["PowerScore"][0]->PsSourceUtilityOther)
                && trim($this->sessData->dataSets["PowerScore"][0]->PsSourceUtilityOther) != '') {
                $GLOBALS["SL"]->pageJAVA .= 'function fillUtilOther() { for (var i=0; i<20; i++) { '
                    . 'if (document.getElementById("n57fldOtherID"+i+"")) {'
                        . 'document.getElementById("n57fldOtherID"+i+"").value="'
                        . str_replace('"', '\\"', $this->sessData->dataSets["PowerScore"][0]->PsSourceUtilityOther)
                    . '"; } } return true; } setTimeout("fillUtilOther()", 10);';
            }
        } elseif (in_array($nID, [59, 80, 61, 60, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["PSRenewables"]) 
                && sizeof($this->sessData->dataSets["PSRenewables"]) > 0) {
                foreach ($this->sessData->dataSets["PSRenewables"] as $ind => $row) {
                    if (isset($row->PsRnwRenewable) && $row->PsRnwRenewable == $sourceID) {
                        $perc = 0;
                        if (isset($row->PsRnwLoadPercent)) $perc = intVal($row->PsRnwLoadPercent);
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
            if ($GLOBALS["SL"]->REQ->has('cups')) return 1;
        } elseif ($condition == '#MotherHas') {
            $area = $this->getArea('Mother');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#CloneHas') {
            $area = $this->getArea('Clone');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#VegHas') {
            $area = $this->getArea('Veg');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#FlowerHas') {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#DryingOnSite') {
            $area = $this->getArea('Dry');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#MotherArtificialLight') {
            return $this->runCondArtifArea('Mother');
        } elseif ($condition == '#CloneArtificialLight') {
            return $this->runCondArtifArea('Clone');
        } elseif ($condition == '#VegArtificialLight') {
            return $this->runCondArtifArea('Veg');
        } elseif ($condition == '#FlowerArtificialLight') {
            return $this->runCondArtifArea('Flower');
        } elseif ($condition == '#HasArtificialLight') { // could be replaced by OR functionality
            if ($this->runCondArtifArea('Mother') == 1 || $this->runCondArtifArea('Clone') == 1
                || $this->runCondArtifArea('Veg') == 1 || $this->runCondArtifArea('Flower') == 1) {
                return 1;
            }
            return 0;
        } elseif ($condition == '#MotherSunlight') {
            $area = $this->getArea('Mother');
            if (!isset($area) || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaLgtSun);
        } elseif ($condition == '#CloneSunlight') {
            $area = $this->getArea('Clone');
            if (!isset($area) || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaLgtSun);
        } elseif ($condition == '#VegSunlight') {
            $area = $this->getArea('Veg');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtSun);
        } elseif ($condition == '#FlowerSunlight') {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtSun);
        } elseif ($condition == '#SunlightVegOrFlower') { // could be replaced by OR functionality
            if (isset($this->sessData->dataSets["PowerScore"]) && isset($this->sessData->dataSets["PowerScore"][0])) {
                if (isset($this->sessData->dataSets["PowerScore"][0]->PsVegSun)
                    && intVal($this->sessData->dataSets["PowerScore"][0]->PsVegSun) == 1) {
                    return 1;
                }
                if (isset($this->sessData->dataSets["PowerScore"][0]->PsFlowerSun)
                    && intVal($this->sessData->dataSets["PowerScore"][0]->PsFlowerSun) == 1) {
                    return 1;
                }
            }
            return 0;
        } elseif (in_array($condition, [
                '#IndoorFlower5Ksf', 
                '#IndoorFlower10Ksf', 
                '#IndoorFlower50Ksf', 
                '#IndoorFlowerOver50Ksf'
                ])) {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->PsAreaHasStage) || !isset($area->PsAreaSize)
                || intVal($area->PsAreaSize) == 0) {
                return 0;
            }
            switch ($condition) {
                case '#IndoorFlower5Ksf':      return ($area->PsAreaSize < 5000); break;
                case '#IndoorFlower10Ksf':     return ($area->PsAreaSize <= 5000 && $area->PsAreaSize < 1000); break;
                case '#IndoorFlower50Ksf':     return ($area->PsAreaSize <= 10000 && $area->PsAreaSize < 50000); break;
                case '#IndoorFlowerOver50Ksf': return ($area->PsAreaSize >= 50000); break;
            }
            return 0;
            
        } elseif ($condition == '#HasUniqueness') {
            for ($i = 1; $i < 9; $i++) {
                if (isset($this->sessData->dataSets["PowerScore"][0]->{ 'PsUniqueness' . $i })
                    && trim($this->sessData->dataSets["PowerScore"][0]->{ 'PsUniqueness' . $i }) != '') {
                    return 1;
                }
            }
            return 0;
        } elseif ($condition == '#HasFeedback') {
            for ($i = 1; $i < 9; $i++) {
                if (isset($this->sessData->dataSets["PowerScore"][0]->{ 'PsFeedback' . $i })
                    && trim($this->sessData->dataSets["PowerScore"][0]->{ 'PsFeedback' . $i }) != '') {
                    return 1;
                }
            }
            return 0;
        } elseif ($condition == '#ScoreNotLeader') {
            if (isset($this->sessData->dataSets["PowerScore"][0]->PsEfficOverall)) {
                if (round($this->sessData->dataSets["PowerScore"][0]->PsEfficOverall) < 67) return 1;
                else return 0;
            }
            return -1;
        } elseif ($condition == '#ReportDetailsPublic') { // could be replaced by OR functionality
            if ($this->v["user"] && $this->v["user"]->hasRole('administrator|staff')) {
                return 1;
            }
            if (isset($this->sessData->dataSets["PowerScore"][0]->PsPrivacy)
                && intVal($this->sessData->dataSets["PowerScore"][0]->PsPrivacy) 
                == $GLOBALS["SL"]->def->getID('PowerScore Privacy Options', 'Private')) {
                return 0;
            }
            return 1;
        }
        return -1;
    }
    
    private function runCondArtifArea($areaName)
    {
        $area = $this->getArea($areaName);
        if (!isset($area) || !isset($area->PsAreaHasStage)) {
            return 0;
        }
        return intVal($area->PsAreaLgtArtif);
    }
    
    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        if (!isset($this->sessData->dataSets["PowerScore"])) {
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
                        $swap = round($this->sessData->dataSets["PowerScore"][0]->PsEfficOverall) 
                            . $GLOBALS["SL"]->numSupscript(
                                round($this->sessData->dataSets["PowerScore"][0]->PsEfficOverall)) . ' percentile';
                        break;
                    case '[{ Production Score }]':
                        $swap = $GLOBALS["CUST"]->cnvrtLbs2Grm(
                            $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction);
                        $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        break;
                    case '[{ PowerScore Total Submissions }]': 
                        $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
                            ->get();
                        $swap = $chk->count();
                        break;
                    case '[{ PowerScore Report Link Similar }]':
                        $swap = $GLOBALS["SL"]->sysOpts["app-url"] . '/calculated/read-' . $this->coreID . '?fltFarm='
                            . $this->sessData->dataSets["PowerScore"][0]->PsCharacterize;
                        $swap = '<a href="' . $swap . '" target="_blank">' . $swap . '</a>';
                        break;
                    case '[{ PowerScore Similar }]':
                        $swap = round($rankSim->PsRnkOverall)
                            . $GLOBALS["SL"]->numSupscript(round($rankSim->PsRnkOverall)) . ' percentile';
                        break;
                    case '[{ PowerScore Dashboard Similar }]':
                        $swap = view('vendor.cannabisscore.nodes.490-report-calculations-preview', [
                            "ps"       => $this->sessData->dataSets["PowerScore"][0],
                            "rank"     => $rankSim,
                            "filtDesc" => str_replace('/', '/ ', strtolower(
                                $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', 
                                    $this->sessData->dataSets["PowerScore"][0]->PsCharacterize)))
                            ])->render();
                        break;
                    case '[{ Zip Code }]': 
                        if (isset($this->sessData->dataSets["PowerScore"][0]->PsZipCode)) {
                            $swap = $this->sessData->dataSets["PowerScore"][0]->PsZipCode;
                        }
                        break;
                    case '[{ Farm Name }]': 
                        if (isset($this->sessData->dataSets["PowerScore"][0]->PsName)) {
                            $swap = $this->sessData->dataSets["PowerScore"][0]->PsName;
                        } elseif (isset($this->sessData->dataSets["PowerScore"][0]->PsEmail)) {
                            $chkEma = User::where('email', $this->sessData->dataSets["PowerScore"][0]->PsEmail)
                                ->first();
                            if ($chkEma && isset($chkEma->name) && trim($chkEma->name) != '') $swap = $chkEma->name;
                        }
                        if (in_array(trim($swap), ['', $dy])) $swap = 'Resource Innovator';
                        break;
                    case '[{ Farm Type }]': 
                        $swap = str_replace('/', '/ ', strtolower($GLOBALS["SL"]->def->getVal('PowerScore Farm Types', 
                            $this->sessData->dataSets["PowerScore"][0]->PsCharacterize)));
                        break;
                }
                $emailBody = str_replace($dy, $swap, $emailBody);
            }
        }
        return $emailBody;
    }
    
    protected function prepFeedbackSkipBtn()
    {
        $this->v["psOwner"] = (($GLOBALS["SL"]->REQ->has('psid') && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) 
            ? intVal($GLOBALS["SL"]->REQ->get('psid')) 
            : ((session()->has('PowerScoreOwner')) ? intVal(session()->get('PowerScoreOwner')) : -3));
        if (intVal($this->v["psOwner"]) > 0 && isset($this->sessData->dataSets["PsFeedback"])
            && isset($this->sessData->dataSets["PsFeedback"][0])) {
            $this->sessData->dataSets["PsFeedback"][0]->PsfPsID = $this->v["psOwner"];
            $this->sessData->dataSets["PsFeedback"][0]->save();
        }
        return true;
    }
    
    protected function prepUtilityRefTitle()
    {
        if (isset($this->sessData->dataSets["PsReferral"]) && sizeof($this->sessData->dataSets["PsReferral"]) > 0) {
            if ($GLOBALS["SL"]->REQ->has('u') && intVal($GLOBALS["SL"]->REQ->get('u')) > 0) {
                $this->sessData->dataSets["PsReferral"][0]->PsRefUtility = intVal($GLOBALS["SL"]->REQ->get('u'));
                $this->sessData->dataSets["PsReferral"][0]->save();
            }
            if ($GLOBALS["SL"]->REQ->has('s') && intVal($GLOBALS["SL"]->REQ->get('s')) > 0) {
                $scoreID = intVal($GLOBALS["SL"]->REQ->get('s'));
                $this->sessData->dataSets["PsReferral"][0]->PsRefPowerScore = $scoreID;
                $this->sessData->loadData('PowerScore', $scoreID);
                if (isset($this->sessData->dataSets["PowerScore"]) 
                    && sizeof($this->sessData->dataSets["PowerScore"]) > 0
                    && isset($this->sessData->dataSets["PowerScore"][0]->PsEmail)) {
                    $powerscoreOwner = false;
                    if ($this->v["uID"] == $this->sessData->dataSets["PowerScore"][0]->PsUserID) {
                        $powerscoreOwner = true;
                    }
                    if (session()->has('PowerScoreOwner' . $scoreID) 
                        && intVal(session()->get('PowerScoreOwner' . $scoreID)) == $scoreID) {
                        $powerscoreOwner = true;
                    }
                    if ($powerscoreOwner) {
                        $this->sessData->dataSets["PsReferral"][0]->PsRefEmail 
                            = $this->sessData->dataSets["PowerScore"][0]->PsEmail;
                    }
                }
                $this->sessData->dataSets["PsReferral"][0]->save();
            }
        }
        $this->chkUtilityOffers();
        return true;
    }
    
}