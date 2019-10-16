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
use CannabisScore\Controllers\ScoreReportLightingManu;
use CannabisScore\Controllers\ScoreReports;
use CannabisScore\Controllers\ScoreImports;

class CannabisScore extends ScoreImports
{
    public function printPreviewReport($isAdmin = false)
    {
        return view('vendor.cannabisscore.powerscore-report-preview', [
            "uID"      => $this->v["uID"],
            "sessData" => $this->sessData->dataSets
        ])->render();
    }
    
    protected function customNodePrint($nID = -3, $tmpSubTier = [], $nIDtxt = '', $nSffx = '', $currVisib = 1)
    {
        $ret = '';
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 393) {
            $areas = $this->sessData
                ->getLoopRowIDs('Growth Stages');
            $GLOBALS["SL"]->pageAJAX .= view(
                'vendor.cannabisscore.nodes.'
                    . '393-area-lighting-ajax', 
                [ "areas" => $areas ]
            )->render();
        } elseif (in_array($nID, [74, 396])) {
            $ret .= $this->printGramForm($nID, $nIDtxt);
        } elseif ($nID == 362) {
            $GLOBALS["SL"]->loadStates();
            $this->getStateUtils();
            $ret .= view(
                'vendor.cannabisscore.nodes.'
                    . '362-utilities-by-state', 
                $this->v
            )->render();
        } elseif ($nID == 502) {
            $this->chkUtilityOffers();
            $ret .= view(
                'vendor.cannabisscore.nodes.'
                    . '502-utility-offers', 
                $this->v
            )->render();
        } elseif (in_array($nID, [177, 457, 465, 471])) {
            return $this->printReportBlds($nID);
        } elseif (in_array($nID, [209, 432, 440, 448])) {
            return $this->printReportLgts($nID);
        } elseif ($nID == 536) {
            $this->prepFeedbackSkipBtn();
            $GLOBALS["SL"]->pageJAVA .= view(
                'vendor.cannabisscore.nodes.'
                    . '536-feedback-skip-button-java', 
                $this->v
            )->render();
        } elseif ($nID == 548) {
            $this->prepFeedbackSkipLnk();
            $ret .= view(
                'vendor.cannabisscore.nodes.'
                    . '548-powerscore-feedback-score-link', 
                $this->v
            )->render();
        } elseif ($nID == 148) { // this should be built-in
            $this->sessData->dataSets["PowerScore"][0]
                ->PsStatus = $this->v["defCmplt"];
            $this->sessData->dataSets["PowerScore"][0]
                ->save();
            session()->put('PowerScoreOwner', $this->coreID);
            session()->put(
                'PowerScoreOwner' . $this->coreID, 
                $this->coreID
            );
            
        } elseif ($nID == 490) {
            $ret .= $this->customPrint490($nID);
        } elseif ($nID == 1008) {
            $ret .= view(
                'vendor.cannabisscore.nodes.'
                    . '1008-powerscore-calculations-mockup', 
                $this->v
            )->render();
        } elseif ($nID == 946) {
            $ret .= $this->printPsRankingFilters($nID);
        } elseif ($nID == 878) {
            $this->auditLgtAlerts();
        } elseif ($nID == 860) {
            if (isset($this->sessData->dataSets["PSForCup"])) {
                $deetVal = '';
                foreach ($this->sessData->dataSets["PSForCup"] 
                    as $i => $cup) {
                    if (isset($cup->PsCupCupID) 
                        && intVal($cup->PsCupCupID) > 0) {
                        $deetVal .= (($i > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->def->getVal(
                                'PowerScore Competitions', 
                                $cup->PsCupCupID
                            );
                    }
                }
                return ['Competition', $deetVal, $nID];
            }
        } elseif ($nID == 861) {
            if (isset($this->sessData->dataSets["PowerScore"])) {
                $ps = $this->sessData->dataSets["PowerScore"][0];
                if (isset($ps->PsYear)) {
                    return [
                        'Growing Year', 
                        $ps->PsYear, 
                        $nID
                    ];
                }
            }
        } elseif ($nID == 508) {
            $this->prepUtilityRefTitle();
            $ret .= view(
                'vendor.cannabisscore.nodes.'
                    . '508-utility-referral-title', 
                $this->v
            )->render();
            
        // PowerScore Reporting
        } elseif ($nID == 744) {
            $report = new ScoreReports;
            $ret .= $report->getCultClassicReport();
        } elseif ($nID == 170) {
            $report = new ScoreReports;
            $ret .= $report->getAllPowerScoresPublic($nID);
        } elseif ($nID == 966) {
            $report = new ScoreReports;
            $ret .= $report->getPowerScoresOutliers($nID);
        } elseif ($nID == 964) { // Partner Multi-Site Rankings
            $report = new ScoreReports;
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->getMultiSiteRankings($nID);
        } elseif ($nID == 799) { // Partner Multi-Site Listings
            $report = new ScoreReports;
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
            $report = new ScoreReportLightingManu;
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $report->printCompareLightManu();
        } elseif ($nID == 797) {
            $report = new ScoreReportAvgs;
            $ret .= $report->getPowerScoreFinalReport();
        } elseif ($nID == 853) {
            $this->initSearcher(1);
            $this->searcher->loadAllScoresPublic();
            $report = new ScoreReportFound;
            $ret .= $report->getFoundReport(
                $nID, 
                $this->searcher->v["allscores"]
            );
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
                'vendor.cannabisscore.nodes.'
                    . '968-lighting-manufacturers-comparison', 
                [ "nID" => $nID ]
            )->render();
            
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
                $str = str_replace('Does your', 'Will your', 
                    str_replace('does your', 'will your', $str));
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
    
    protected function postNodePublicCustom($nID = -3, $nIDtxt = '', $tmpSubTier = [])
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
        } elseif ($nID == 701) {
            return $this->postGrowingStages($nID, $nIDtxt);
        } elseif ($nID == 74) { // dump monthly grams
            $this->postMonthlies($nIDtxt, 'PsMonthGrams');
        } elseif ($nID == 70) { // dump monthly energy
            $this->postMonthlies($nIDtxt, 'PsMonthKWH1');
        } elseif ($nID == 949) { // dump monthly green waste pounds
            $this->postMonthlies($nIDtxt, 'PsMonthWasteLbs');
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
                    if (isset($row->PsRnwRenewable) 
                        && $row->PsRnwRenewable == $sourceID) {
                        $fld = 'n' . $nID . 'fld';
                        $this->sessData->dataSets["PSRenewables"][$ind]->update([
                            'PsRnwLoadPercent' 
                                => (($GLOBALS["SL"]->REQ->has($fld)) 
                                    ? intVal($GLOBALS["SL"]->REQ->get($fld)) 
                                    : 0)
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

    protected function postGrowingStages($nID, $nIDtxt = '')
    {
        if (isset($this->sessData->dataSets["PSAreas"])) {
            $areas = $this->sessData->dataSets["PSAreas"];
            if (sizeof($areas) > 0) {
                foreach ($areas as $a => $area) {
                    $nick = $this->getStageNick($area->PsAreaType);
                    $area->PsAreaHasStage = 0;
                    if (in_array($nick, ['Mother', 'Clone'])) {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 493) {
                            if ($GLOBALS["SL"]->REQ->has('n1033fld')
                                && is_array($GLOBALS["SL"]->REQ->n1033fld)
                                && in_array($area->PsAreaType, 
                                    $GLOBALS["SL"]->REQ->n1033fld)) {
                                $area->PsAreaHasStage = 1;
                            }
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    } elseif ($nick == 'Veg') {
                        $area = $this->postGrowingStagesOther($area);
                    } elseif ($nick == 'Flower') {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                            $area->PsAreaHasStage = 1;
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    } elseif ($nick == 'Dry') {
                        if ($GLOBALS["SL"]->REQ->has('n701fld')
                            && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                            if ($GLOBALS["SL"]->REQ->has('n1029fld')
                                && is_array($GLOBALS["SL"]->REQ->n1029fld)
                                && in_array($area->PsAreaType, 
                                    $GLOBALS["SL"]->REQ->n1029fld)) {
                                $area->PsAreaHasStage = 1;
                            }
                        } else {
                            $area = $this->postGrowingStagesOther($area);
                        }
                    }
                    $area->save();
                    $this->sessData->dataSets["PSAreas"][$a] = $area;
                }
            }
            $this->sessData->dataSets["PowerScore"][0]
                ->PsProcessingOnsite = 0;
            $this->sessData->dataSets["PowerScore"][0]
                ->PsExtractingOnsite = 0;
            if ($GLOBALS["SL"]->REQ->has('n701fld')
                && intVal($GLOBALS["SL"]->REQ->n701fld) == 494) {
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n1029fld)
                    && in_array(11, $GLOBALS["SL"]->REQ->n1029fld)) {
                    $this->sessData->dataSets["PowerScore"][0]
                        ->PsProcessingOnsite = 1;
                }
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n1029fld)
                    && in_array(12, $GLOBALS["SL"]->REQ->n1029fld)) {
                    $this->sessData->dataSets["PowerScore"][0]
                        ->PsExtractingOnsite = 1;
                }
            } elseif ($GLOBALS["SL"]->REQ->has('n701fld')
                && intVal($GLOBALS["SL"]->REQ->n701fld) == 495) {
                if ($GLOBALS["SL"]->REQ->has('n575fld')
                    && is_array($GLOBALS["SL"]->REQ->n575fld)
                    && in_array(11, $GLOBALS["SL"]->REQ->n575fld)) {
                    $this->sessData->dataSets["PowerScore"][0]
                        ->PsProcessingOnsite = 1;
                }
                if ($GLOBALS["SL"]->REQ->has('n1029fld')
                    && is_array($GLOBALS["SL"]->REQ->n575fld)
                    && in_array(12, $GLOBALS["SL"]->REQ->n575fld)) {
                    $this->sessData->dataSets["PowerScore"][0]
                        ->PsExtractingOnsite = 1;
                }
            }
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        return false;
    }

    protected function postGrowingStagesOther($area)
    {
        if ($GLOBALS["SL"]->REQ->has('n701fld')
            && intVal($GLOBALS["SL"]->REQ->n701fld) == 495) {
            if ($GLOBALS["SL"]->REQ->has('n575fld')
                && is_array($GLOBALS["SL"]->REQ->n575fld)
                && in_array($area->PsAreaType, 
                    $GLOBALS["SL"]->REQ->n575fld)) {
                $area->PsAreaHasStage = 1;
            }
        }
        return $area;
    }
    
    protected function postMonthlies($nIDtxt, $fld2)
    {
        $powerMonths = $this->sortMonths();
        foreach ($powerMonths as $i => $row) {
            $row->PsMonthMonth = (1+$i);
            $fldName = 'month' . $nIDtxt . 'ly' . $row->PsMonthMonth;
            $row->{ $fld2 }  = (($GLOBALS["SL"]->REQ->has($fldName)) 
                ? intVal($GLOBALS["SL"]->REQ->get($fldName)) : null);
            $row->save();
        }
        return true;
    }
    
    public function monthlyCalcPreselections($nID, $nIDtxt = '')
    {
        $ret = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $fld = (($nID == 70) ? 'PsMonthKWH1' 
            : (($nID == 74) ? 'PsMonthGrams' : 'PsMonthWasteLbs'));
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
        if (isset($this->sessData->dataSets["PowerScore"]) 
            && isset($this->sessData->dataSets["PowerScore"][0])
            && isset($this->sessData->dataSets["PowerScore"][0]->PsGrams)) {
            $this->v["currSessData"] = $this->sessData
                ->dataSets["PowerScore"][0]->PsGrams;
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
                ->dataSets["PowerScore"][0]->PsCharacterize;
            $this->searcher->searchFiltsURLXtra();
        }
        $this->searcher->v["nID"] = 490;
        $this->v["isPast"] = ($this->sessData->dataSets["PowerScore"][0]->PsTimeType 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        return view('vendor.cannabisscore.nodes.490-report-calculations', $this->v)
            ->render();
    }
    
    public function printPsRankingFilters($nID)
    {
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->searcher->v["nID"] = $nID;
        $this->searcher->v["psFiltChks"] = view('vendor.cannabisscore.inc-filter-powerscores-checkboxes', $this->searcher->v)
            ->render();
        return '<div id="scoreRankFiltWrap"><h5 class="mT0">Compare to other farms</h5>'
            . '<div class="pT5"></div>' . view(
                'vendor.cannabisscore.inc-filter-powerscores', 
                $this->searcher->v
            )->render() . '</div>';
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
        $this->v["ajax-psid"] = (($GLOBALS["SL"]->REQ->has('psid') 
            && intVal($GLOBALS["SL"]->REQ->get('psid')) > 0) 
            ? intVal($GLOBALS["SL"]->REQ->get('psid')) : -3);
        if (!$request->has('refresh') || intVal($request->get('refresh')) == 1) {
            $this->sessData->loadData('PowerScore', $this->v["ajax-psid"]);
            if (isset($this->sessData->dataSets["PowerScore"]) 
                && sizeof($this->sessData->dataSets["PowerScore"]) > 0
                && isset($this->sessData->dataSets["PowerScore"][0]->PsEmail)) {
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
        $this->v["isPast"] = ($this->sessData->dataSets["PowerScore"][0]->PsTimeType 
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
        return view('vendor.cannabisscore.nodes.490-report-calculations-frame-guage', [
            "perc" => $perc,
            "size" => $size
        ])->render();
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
        return view('vendor.cannabisscore.nodes.490-report-calculations-frame-meter', [
            "perc"   => $perc,
            "width"  => round($width),
            "height" => round($height),
            "bg"     => $bg
        ])->render();
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
            && isset($this->searcher->v["powerscore"]->PsID)) {
            $this->searcher->v["isPast"] = ($this->searcher->v["powerscore"]->PsTimeType 
                == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
            $this->searcher->v["currRanks"] = RIIPSRankings::where(
                    'PsRnkFilters', 
                    $this->searcher->v["urlFlts"]
                )
                ->where('PsRnkPSID', $this->searcher->v["powerscore"]->PsID)
                ->first();
            if ($GLOBALS["SL"]->REQ->has('refresh') 
                || !$this->searcher->v["currRanks"]->PsRnkOverall
                || !isset($this->searcher->v["currRanks"]->PsRnkOverall->PsRnkOverall)) {
                if (isset($this->searcher->v["powerscore"]->PsTimeType) 
                    && $this->searcher->v["powerscore"]->PsTimeType 
                        == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                    $ranks = RIIPSRanks::where('PsRnkFilters', '')
                        ->first();
                    $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
                    $this->searcher->v["powerscore"]->PsEfficOverall = $this->searcher
                        ->v["currRanks"]->PsRnkOverall;
                    $this->searcher->v["powerscore"]->save();
                } else {
                    $urlFlts = $this->searcher->v["urlFlts"];
                    //$this->calcAllScoreRanks();
                    $this->searcher->v["urlFlts"] = $urlFlts;
                    $this->searcher->v["currRanks"] = RIIPSRankings::where(
                            'PsRnkPSID', 
                            $this->searcher->v["powerscore"]->PsID
                        )
                        ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                        ->first();
                    if (!$this->searcher->v["currRanks"]) {
                        $ranks = RIIPSRanks::where(
                            'PsRnkFilters', 
                            $this->searcher->v["urlFlts"]
                        )->first();
                        $this->searcher->v["currRanks"] = $this->ajaxScorePercNewRank($ranks);
                    }
                }
            }
            $this->searcher->v["currGuage"] = 0;
            $this->searcher->v["hasOverall"] = (isset($this->searcher->v["powerscore"]->PsEfficFacility) 
                && isset($this->searcher->v["powerscore"]->PsEfficProduction) 
                && isset($this->searcher->v["powerscore"]->PsEfficHvac) 
                && isset($this->searcher->v["powerscore"]->PsEfficLighting) 
                && $this->searcher->v["powerscore"]->PsEfficFacility > 0
                && $this->searcher->v["powerscore"]->PsEfficProduction > 0);
            $this->searcher->v["overallScoreTitle"] = '<center><h1 class="m0 scoreBig">' 
                . round($this->searcher->v["currRanks"]->PsRnkOverall) 
                . $GLOBALS["SL"]->numSupscript(round($this->searcher
                    ->v["currRanks"]->PsRnkOverall)) 
                . '</h1><b>percentile</b></center>';
// number_format($ranksCache->PsRnkTotCnt) }} past growing @if ($ranksCache->PsRnkTotCnt > 1) years @else year @endif of
            $this->searcher->v["withinFilters"] = '<div id="efficBlockOverGuageTitle">' 
                . '<h5>Overall: '
                . (($this->searcher->v["currRanks"]->PsRnkOverall > 66) ? 'Leader' 
                    : (($this->searcher->v["currRanks"]->PsRnkOverall > 33) 
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
        $currRanks->PsRnkPSID = $this->searcher->v["powerscore"]->PsID;
        if ($ranks && isset($ranks->PsRnkFacility)) {
            $currRanks->PsRnkFacility = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkFacility, 
                $this->searcher->v["powerscore"]->PsEfficFacility, 
                true
            );
            $currRanks->PsRnkProduction = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkProduction, 
                $this->searcher->v["powerscore"]->PsEfficProduction
            );
            $currRanks->PsRnkLighting = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkLighting, 
                $this->searcher->v["powerscore"]->PsEfficLighting, 
                true
            );
            $currRanks->PsRnkHVAC = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkHVAC, 
                $this->searcher->v["powerscore"]->PsEfficHvac, 
                true
            );
            $currRanks->PsRnkWater = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkWater, 
                $this->searcher->v["powerscore"]->PsEfficWater, 
                true
            );
            $currRanks->PsRnkWaste = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkWaste, 
                $this->searcher->v["powerscore"]->PsEfficWaste, 
                true
            );
            $currRanks->PsRnkOverallAvg = ($currRanks->PsRnkFacility
                +$currRanks->PsRnkProduction+$currRanks->PsRnkLighting
                +$currRanks->PsRnkHVAC+$currRanks->PsRnkWater
                +$currRanks->PsRnkWaste)/6;
            $currRanks->PsRnkOverall = $GLOBALS["SL"]->getArrPercentileStr(
                $ranks->PsRnkOverallAvg, 
                $currRanks->PsRnkOverallAvg
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
        } elseif ($nID == 1033) { // Nursery growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Mother', 'PsAreaHasStage')) == 1) {
                $ret[] = 237;
            }
            if (intVal($this->getAreaFld('Clone', 'PsAreaHasStage')) == 1) {
                $ret[] = 160;
            }
            return $ret;
        } elseif ($nID == 1029) { // Flowering-only growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Dry', 'PsAreaHasStage')) == 1) {
                $ret[] = 163;
            }
            $ps = $this->sessData->dataSets["PowerScore"][0];
            if (isset($ps->PsProcessingOnsite) 
                && intVal($ps->PsProcessingOnsite) == 1) {
                $ret[] = 11;
            }
            if (isset($ps->PsExtractingOnsite) 
                && intVal($ps->PsExtractingOnsite) == 1) {
                $ret[] = 12;
            }
            return $ret;
        } elseif ($nID == 575) {  // Other growing stage options
            $ret = [];
            if (intVal($this->getAreaFld('Mother', 'PsAreaHasStage')) == 1) {
                $ret[] = 237;
            }
            if (intVal($this->getAreaFld('Clone', 'PsAreaHasStage')) == 1) {
                $ret[] = 160;
            }
            if (intVal($this->getAreaFld('Veg', 'PsAreaHasStage')) == 1) {
                $ret[] = 161;
            }
            if (intVal($this->getAreaFld('Flower', 'PsAreaHasStage')) == 1) {
                $ret[] = 162;
            }
            if (intVal($this->getAreaFld('Dry', 'PsAreaHasStage')) == 1) {
                $ret[] = 163;
            }
            $ps = $this->sessData->dataSets["PowerScore"][0];
            if (isset($ps->PsProcessingOnsite) 
                && intVal($ps->PsProcessingOnsite) == 1) {
                $ret[] = 11;
            }
            if (isset($ps->PsExtractingOnsite) 
                && intVal($ps->PsExtractingOnsite) == 1) {
                $ret[] = 12;
            }
            return $ret;

        } elseif ($nID == 57) {
            $ps = $this->sessData->dataSets["PowerScore"][0];
            if (isset($ps->PsSourceUtilityOther)
                && trim($ps->PsSourceUtilityOther) != '') {
                $GLOBALS["SL"]->pageJAVA .= 'function fillUtilOther() { '
                    . 'for (var i=0; i<20; i++) { '
                    . 'if (document.getElementById("n57fldOtherID"+i+"")) {'
                        . 'document.getElementById("n57fldOtherID"+i+"").value="'
                        . str_replace('"', '\\"', $this->sessData
                            ->dataSets["PowerScore"][0]->PsSourceUtilityOther)
                    . '"; } } return true; } setTimeout("fillUtilOther()", 10);';
            }
        } elseif (in_array($nID, [59, 80, 61, 60, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["PSRenewables"]) 
                && sizeof($this->sessData->dataSets["PSRenewables"]) > 0) {
                foreach ($this->sessData->dataSets["PSRenewables"] as $ind => $row) {
                    if (isset($row->PsRnwRenewable) 
                        && $row->PsRnwRenewable == $sourceID) {
                        $perc = 0;
                        if (isset($row->PsRnwLoadPercent)) {
                            $perc = intVal($row->PsRnwLoadPercent);
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
                || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#CloneHas') {
            $area = $this->getArea('Clone');
            if (!isset($area) 
                || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#VegHas') {
            $area = $this->getArea('Veg');
            if (!isset($area) 
                || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#FlowerHas') {
            $area = $this->getArea('Flower');
            if (!isset($area) 
                || !isset($area->PsAreaHasStage)) {
                return 0;
            }
            return intVal($area->PsAreaHasStage);
        } elseif ($condition == '#DryingOnSite') {
            $area = $this->getArea('Dry');
            if (!isset($area) 
                || !isset($area->PsAreaHasStage)) {
                return 0;
            }
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
            if ($this->runCondArtifArea('Mother') == 1 
                || $this->runCondArtifArea('Clone') == 1
                || $this->runCondArtifArea('Veg') == 1 
                || $this->runCondArtifArea('Flower') == 1) {
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
            if (isset($this->sessData->dataSets["PowerScore"])) {
                $ps = $this->sessData->dataSets["PowerScore"][0];
                if (isset($ps)) {
                    if (isset($ps->PsVegSun)
                        && intVal($ps->PsVegSun) == 1) {
                        return 1;
                    }
                    if (isset($ps->PsFlowerSun) 
                        && intVal($ps->PsFlowerSun) == 1) {
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
            if (!isset($area) || !isset($area->PsAreaHasStage) 
                || !isset($area->PsAreaSize) || intVal($area->PsAreaSize) == 0) {
                return 0;
            }
            switch ($condition) {
                case '#IndoorFlower5Ksf':
                    return ($area->PsAreaSize < 5000);
                case '#IndoorFlower10Ksf':
                    return ($area->PsAreaSize <= 5000 && $area->PsAreaSize < 10000);
                case '#IndoorFlower30Ksf':
                    return ($area->PsAreaSize <= 10000 && $area->PsAreaSize < 30000);
                case '#IndoorFlower50Ksf':
                    return ($area->PsAreaSize <= 30000 && $area->PsAreaSize < 50000);
                case '#IndoorFlowerOver50Ksf': 
                    return ($area->PsAreaSize >= 50000);
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
                        $swap = round($this->sessData->dataSets["PowerScore"][0]
                                ->PsEfficOverall) 
                            . $GLOBALS["SL"]->numSupscript(
                                round($this->sessData->dataSets["PowerScore"][0]
                                    ->PsEfficOverall)) . ' percentile';
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
                        $swap = $GLOBALS["SL"]->sysOpts["app-url"] 
                            . '/calculated/read-' . $this->coreID . '?fltFarm='
                            . $this->sessData->dataSets["PowerScore"][0]->PsCharacterize;
                        $swap = '<a href="' . $swap . '" target="_blank">' . $swap . '</a>';
                        break;
                    case '[{ PowerScore Similar }]':
                        $swap = round($rankSim->PsRnkOverall)
                            . $GLOBALS["SL"]->numSupscript(round($rankSim->PsRnkOverall)) 
                            . ' percentile';
                        break;
                    case '[{ PowerScore Dashboard Similar }]':
                        $swap = view('vendor.cannabisscore.nodes.490-report-calculations-preview', [
                            "ps"       => $this->sessData->dataSets["PowerScore"][0],
                            "rank"     => $rankSim,
                            "filtDesc" => str_replace('/', '/ ', strtolower(
                                $GLOBALS["SL"]->def->getVal(
                                    'PowerScore Farm Types', 
                                    $this->sessData->dataSets["PowerScore"][0]->PsCharacterize
                                )
                            ))
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
                            $chkEma = User::where(
                                    'email', 
                                    $this->sessData->dataSets["PowerScore"][0]->PsEmail
                                )->first();
                            if ($chkEma && isset($chkEma->name) && trim($chkEma->name) != '') {
                                $swap = $chkEma->name;
                            }
                        }
                        if (in_array(trim($swap), ['', $dy])) {
                            $swap = 'Resource Innovator';
                        }
                        break;
                    case '[{ Farm Type }]': 
                        $swap = str_replace('/', '/ ', strtolower(
                            $GLOBALS["SL"]->def->getVal(
                                'PowerScore Farm Types', 
                                $this->sessData->dataSets["PowerScore"][0]->PsCharacterize
                            )
                        ));
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
            && isset($this->sessData->dataSets["PsFeedback"])
            && isset($this->sessData->dataSets["PsFeedback"][0])) {
            $this->sessData->dataSets["PsFeedback"][0]->PsfPsID = $this->v["psOwner"];
            $this->sessData->dataSets["PsFeedback"][0]->save();
        }
        return true;
    }
    
    protected function prepUtilityRefTitle()
    {
        if (isset($this->sessData->dataSets["PsReferral"]) 
            && sizeof($this->sessData->dataSets["PsReferral"]) > 0) {
            if ($GLOBALS["SL"]->REQ->has('u') 
                && intVal($GLOBALS["SL"]->REQ->get('u')) > 0) {
                $this->sessData->dataSets["PsReferral"][0]
                    ->PsRefUtility = intVal($GLOBALS["SL"]->REQ->get('u'));
                $this->sessData->dataSets["PsReferral"][0]->save();
            }
            if ($GLOBALS["SL"]->REQ->has('s') 
                && intVal($GLOBALS["SL"]->REQ->get('s')) > 0) {
                $scoreID = intVal($GLOBALS["SL"]->REQ->get('s'));
                $this->sessData->dataSets["PsReferral"][0]
                    ->PsRefPowerScore = $scoreID;
                $this->sessData->loadData('PowerScore', $scoreID);
                if (isset($this->sessData->dataSets["PowerScore"])) {
                    $ps = $this->sessData->dataSets["PowerScore"];
                    if (sizeof($ps) > 0 && isset($ps[0]->PsEmail)) {
                        $powerscoreOwner = false;
                        if ($this->v["uID"] == $ps[0]->PsUserID) {
                            $powerscoreOwner = true;
                        }
                        $sess = 'PowerScoreOwner' . $scoreID;
                        if (session()->has($sess) 
                            && intVal(session()->get($sess)) == $scoreID) {
                            $powerscoreOwner = true;
                        }
                        if ($powerscoreOwner) {
                            $this->sessData->dataSets["PsReferral"][0]
                                ->PsRefEmail = $ps[0]->PsEmail;
                        }
                    }
                }
                $this->sessData->dataSets["PsReferral"][0]->save();
            }
        }
        $this->chkUtilityOffers();
        return true;
    }

    protected function auditLgtAlerts()
    {
        $auditFailed = false;
        $auditAreas = $lgts = $watts 
            = $this->v["areaCnts"] = [];
        if (isset($this->sessData->dataSets["PSLightTypes"])) {
            $lgts = $this->sessData->dataSets["PSLightTypes"];
        }
        if (isset($this->sessData->dataSets["PSAreas"])) {
            $printables = [];
            $areas = $this->sessData->dataSets["PSAreas"];
            if (sizeof($areas) > 0) {
                foreach ($areas as $a => $area) {
                    $auditAreas[$a] = '';
                    if (isset($area->PsAreaHasStage) 
                        && intVal($area->PsAreaHasStage) == 1
                        && isset($area->PsAreaSize) 
                        && intVal($area->PsAreaSize) > 0
                        && isset($area->PsAreaLgtArtif) 
                        && intVal($area->PsAreaLgtArtif) == 1) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->PsAreaType == $defID 
                                && $typ != 'Dry') {
                                $printables[] = $area;
                                $auditAreas[$a] = trim($this
                                    ->auditLgtAlertArea($a, $area, $typ));
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
        $area->PsAreaTotalLightWatts = $watts[$typ];
        $area->PsAreaSqFtPerFix2 = (($fixCnt > 0) 
            ? $area->PsAreaSize/$fixCnt : 0);
        $area->save();
        $this->sessData->dataSets["PSAreas"][$a] = $area;
        $this->v["areaCnts"][$area->PsAreaID] = $fixCnt;
        if ($fixCnt == 0) {
            $ret = 'No lights were added for your ' 
                . strtolower($typ) . ' stage.';
        } elseif ($area->PsAreaSqFtPerFix2 < 4) {
            $ret = 'You only listed ' . $fixCnt 
                . ' lighting fixture' 
                . (($fixCnt == 1) ? '' : 's') . ' in your '
                . strtolower($typ) . ' stage. '
                . round($area->PsAreaSqFtPerFix2)
                . ' square feet per fixture is very low. ';
        } elseif ($area->PsAreaSqFtPerFix2 > 81) {
            $ret = 'You listed ' . $fixCnt 
                . ' lighting fixtures in your '
                . strtolower($typ) . ' stage. '
                . round($area->PsAreaSqFtPerFix2)
                . ' square feet per fixture is very high. ';
        }
        return $ret;
    }
    
}