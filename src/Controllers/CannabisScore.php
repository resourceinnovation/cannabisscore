<?php
namespace CannabisScore\Controllers;

use DB;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\User;

use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSMonthly;
use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRanks;
use App\Models\RIIPSRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPSLicenses;
use App\Models\RIIPsFeedback;
use App\Models\SLZipAshrae;
use App\Models\SLZips;
use App\Models\SLSess;
use App\Models\SLNodeSavesPage;
use App\Models\SLUploads;

use CannabisScore\Controllers\CannabisScoreReport;

use SurvLoop\Controllers\SurvLoopStat;
use SurvLoop\Controllers\SurvFormTree;

class CannabisScore extends SurvFormTree
{
    
    public $classExtension         = 'CannabisScore';
    public $treeID                 = 1;
    
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function initExtra(Request $request)
    {
        // Establishing Main Navigation Organization, with Node ID# and Section Titles
        $this->majorSections = [];
        
        // Shortcuts...
        $this->v["defCmplt"] = $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete');
        $this->v["farmTypes"] = [
            'Indoor'           => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Indoor'),
            'Greenhouse/Mixed' => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Greenhouse/Hybrid/Mixed Light'),
            'Outdoor'          => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Outdoor')
            ];
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone Plants'),
            'Veg'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing')
            ];
        $this->v["areaTypesFilt"] = [
            'Flower' => $this->v["areaTypes"]["Flower"],
            'Veg'    => $this->v["areaTypes"]["Veg"],
            'Clone'  => $this->v["areaTypes"]["Clone"],
            'Mother' => $this->v["areaTypes"]["Mother"]
            ];
        $this->v["psTechs"] = [
            'PsHarvestBatch'  => 'Perpetual Harvesting',
            'PsHasWaterPump'  => 'Water Pumps',
            'PsHeatWater'     => 'Mechanical Water Heating',
            'PsControls'      => 'Manual Environmental Controls',
            'PsControlsAuto'  => 'Automatic Environmental Controls',
            'PsVerticalStack' => 'Vertical Stacking'
            ];
        $this->v["psContact"] = [
            'PsConsiderUpgrade' => 'Considering Upgrade Next 12 Months',
            'PsIncentiveWants'  => 'Wants Utility Incentives',
            'PsIncentiveUsed'   => 'Has Used Utility Incentives',
            'PsNewsletter'      => 'RII Newsletter'
            ];
            
            
        // Establishing Main Navigation Organization, with Node ID# and Section Titles
        $this->majorSections = [];
        if ($GLOBALS["SL"]->treeID == 1) {
            $this->majorSections[] = array(45,  'Your Farm',            'active');
            $this->majorSections[] = array(64,  'Growing Environments', 'active');
            $this->majorSections[] = array(608, 'Lighting',             'active');
            $this->majorSections[] = array(609, 'HVAC',                 'active');
            $this->majorSections[] = array(65,  'Annual Totals',        'active');
            $this->majorSections[] = array(610, 'Other Techniques',     'active');
            //$this->majorSections[] = array(67,  'Contact',              'active');
            $this->minorSections = [ [], [], [], [], [], [], [] ];
        }
        
        //$GLOBALS["SL"]->addTopNavItem('Calculate PowerScore', '/start/calculator');
        return true;
    }
    
    protected function tblsInPackage()
    {
        if ($this->dbID == 1) return ['PSUtilities', 'PSUtiliZips'];
        return [];
    }
    
    public function getStageNick($defID)
    {
        switch ($defID) {
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'):     return 'Mother';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone Plants'):      return 'Clone';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'): return 'Veg';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'):  return 'Flower';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing'):     return 'Dry';
        }
        return '';
    }
    
    public function getAllPublicCoreIDs($coreTbl = '')
    {
        if (trim($coreTbl) == '') $coreTbl = $GLOBALS["SL"]->coreTbl;
        $this->allPublicCoreIDs = [];
        $list = NULL;
        if ($coreTbl == 'PowerScore') {
            $list = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if ($list->isNotEmpty()) {
            foreach ($list as $l) $this->allPublicCoreIDs[] = $l->getKey();
        }
        return $this->allPublicCoreIDs;
    }
    
    
        
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function loadExtra()
    {
        if (!session()->has('PowerScoreChecks') || $GLOBALS["SL"]->REQ->has('refresh')) {
            
            $chk = RIIPowerScore::where('PsSubmissionProgress', 'LIKE', '147') // redirection page
                ->where('PsStatus', '=', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
                ->update([ 'PsStatus' => $this->v["defCmplt"] ]);
            
            $chk = RIIPowerScore::where('PsZipCode', 'NOT LIKE', '')
                ->whereNull('PsAshrae')
                ->get();
            if ($chk->isNotEmpty()) {
                $GLOBALS["SL"]->loadStates();
                foreach ($chk as $score) {
                    $zipRow = $GLOBALS["SL"]->states->getZipRow($score->PsZipCode);
                    $score->PsAshrae = $GLOBALS["SL"]->states->getAshrae($zipRow);
                    if (!isset($score->PsState) || trim($score->PsState) == '') {
                        if ($zipRow && isset($zipRow->ZipZip)) {
                            $score->PsState  = $zipRow->ZipState;
                            $score->PsCounty = $zipRow->ZipCounty;
                        }
                    }
                    $score->save();
                }
            }
        
            /*
            // start of 2/2/18 Reassignment of PowerScores to users with matching emails. Due to reported issue.
            $chk = RIIPowerScore::where('PsUserID', '>', '0')
                ->select('PsID', 'PsUserID', 'PsEmail', 'PsIPaddy', 'PsStatus', 'PsSubmissionProgress')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $i => $row) {
                    if (isset($row->PsEmail) && trim($row->PsEmail) != '') {
                        $chkUsr = User::where('id', $row->PsUserID)
                            ->where('email', $row->PsEmail)
                            ->first();
                        if (!$chkUsr || !isset($chkUsr->id)) {
                            $row->update([ "PsUserID" => null ]);
                        }
                    } else {
                        $row->update([ "PsUserID" => null ]);
                    }
                }
            }
            $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
                ->select('PsID', 'PsUserID', 'PsEmail')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $i => $row) {
                    if (isset($row->PsEmail) && trim($row->PsEmail) != '') {
                        $chkUsr = User::where('email', $row->PsEmail)
                            ->first();
                        if ($chkUsr && isset($chkUsr->id)) {
                            if (isset($row->PsUserID) && intVal($row->PsUserID) == intVal($chkUsr->id)) {
                                
                            } else {
                                $row->update([ "PsUserID" => $chkUsr->id ]);
                            }
                        }
                    }
                }
            }
            $chkSess = SLSess::where('SessTree', 1)
                ->where('SessCoreID', '>', 0)
                ->where('SessUserID', '>', 0)
                ->get();
            if ($chkSess->isNotEmpty()) {
                foreach ($chkSess as $j => $sess) {
                    $saveChk = SLNodeSavesPage::where('PageSaveSession', $sess->getKey())
                        ->get();
                    $saveTot = $saveChk->count();
                    $chk = RIIPowerScore::find($sess->SessCoreID);
                    if ($chk && (!isset($chk->PsUserID) || intVal($chk->PsUserID) <= 0)) {
                        if ($saveTot > 0) {
//echo '<div class="p20"> - reassigning score#' . $sess->SessCoreID . ' to user#' . $sess->SessUserID . ' <i class="slGrey mL10">after ' . $saveTot . ' page saves</i></div>';
                            $chk->update([ "PsUserID" => $sess->SessUserID ]);
                        }
                    }
                }
            }
            // end of 2/2/18
            */
            
            session()->put('PowerScoreChecks', true);
        }
        
        return true;
    }
    
    protected function checkScore()
    {
    	if (isset($this->sessData->dataSets["PowerScore"]) 
    		&& isset($this->sessData->dataSets["PowerScore"][0]->PsZipCode)) {
			$this->sessData->updateZipInfo($this->sessData->dataSets["PowerScore"][0]->PsZipCode, 
				'PowerScore', 'PsState', 'PsCounty', 'PsAshrae', 'PsCountry');
		}
		return true;
	}
    
    protected function firstPageChecks()
    {
        if (!isset($this->sessData->dataSets["PowerScore"]) || !isset($this->sessData->dataSets["PowerScore"][0])) {
            return false;
        }
        if ($GLOBALS["SL"]->REQ->has('time') && trim($GLOBALS["SL"]->REQ->get('time')) != '') {
            $this->sessData->dataSets["PowerScore"][0]->PsTimeType = intVal($GLOBALS["SL"]->REQ->get('time'));
            $this->sessData->dataSets["PowerScore"][0]->save();
        } elseif (!isset($this->sessData->dataSets["PowerScore"][0]->PsTimeType)
            || intVal($this->sessData->dataSets["PowerScore"][0]->PsTimeType) <= 0) {
            $this->sessData->dataSets["PowerScore"][0]->PsTimeType 
                = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        if ($GLOBALS["SL"]->REQ->has('cups') && trim($GLOBALS["SL"]->REQ->get('cups')) != '') {
            $cupsIn = $GLOBALS["SL"]->mexplode(',', urldecode($GLOBALS["SL"]->REQ->get('cups')));
            $cupList = $GLOBALS["SL"]->def->getSet('PowerScore Competitions');
            if (sizeof($cupList) > 0) {
                foreach ($cupList as $c) {
                    if (in_array($c->DefID, $cupsIn)) {
                        $chk = RIIPSForCup::where('PsCupPSID', $this->coreID)
                            ->where('PsCupCupID', $c->DefID)
                            ->first();
                        if (!$chk || !isset($chk->PsCupCupID)) {
                            $chk = new RIIPSForCup;
                            $chk->PsCupPSID  = $this->coreID;
                            $chk->PsCupCupID = $c->DefID;
                            $chk->save();
                        }
                    } else {
                        RIIPSForCup::where('PsCupPSID', $this->coreID)
                            ->where('PsCupCupID', $c->DefID)
                            ->delete();
                    }
                }
            }
        }
        return true;
    }
    
    protected function customNodePrint($nID = -3, $tmpSubTier = [], $nIDtxt = '', $nSffx = '', $currVisib = 1)
    {
        $ret = '';
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 701) {
            $this->pageJSvalid .= "errorFocus[errorFocus.length] = new Array('701', 'n575fld0'); var idList = "
                . "new Array('n575fld0', 'n577fld0', 'n578fld0', 'n579fld0', 'n306fld0', 'n495fld0', 'n574fld0'); "
                . "reqFormFldRadioCustom('701', idList); ";
        } elseif ($nID == 393) {
            $GLOBALS["SL"]->pageAJAX .= view('vendor.cannabisscore.nodes.393-area-lighting-ajax', [
                "areas" => $this->sessData->getLoopRowIDs('Growth Stages')
                ])->render();
        } elseif (in_array($nID, [74, 396])) {
            $this->v["nID"] = $nID;
            $this->v["currSessData"] = 0;
            if (isset($this->sessData->dataSets["PowerScore"]) && isset($this->sessData->dataSets["PowerScore"][0])
                && isset($this->sessData->dataSets["PowerScore"][0]->PsGrams)) {
                $this->v["currSessData"] = $this->sessData->dataSets["PowerScore"][0]->PsGrams;
            }
            $this->pageJSvalid .= "if (document.getElementById('n" . $nIDtxt 
                . "VisibleID') && document.getElementById('n" . $nIDtxt 
                . "VisibleID').value == 1) reqFormFldGreater('" . $nIDtxt . "', 0.00000001);\n";
            $ret .= view('vendor.cannabisscore.nodes.74-total-grams', $this->v)->render();
        } elseif (in_array($nID, [70, 397])) {
            $this->v["nID"]         = $nID;
            $this->v["powerScore"]  = $this->sessData->dataSets["PowerScore"][0];
            $this->v["powerMonths"] = $this->sortMonths();
            $this->pageJSvalid .= "if (document.getElementById('n" . $nIDtxt 
                . "VisibleID') && document.getElementById('n" . $nIDtxt 
                . "VisibleID').value == 1) reqFormFldGreater('" . $nIDtxt . "', 0);\n";
            $ret .= view('vendor.cannabisscore.nodes.70-total-kwh', $this->v)->render();
        } elseif ($nID == 362) {
            $GLOBALS["SL"]->loadStates();
            $this->getStateUtils();
            $ret .= view('vendor.cannabisscore.nodes.362-utilities-by-state', $this->v)->render();
        } elseif ($nID == 490) {
            $this->v["nID"] = $nID;
            if ($GLOBALS["SL"]->REQ->has('refresh')) $this->checkScore();
            $this->chkScoreFilters();
            $this->getAllReportCalcs();
            $this->getSimilarStats();
            $this->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->v)->render();
            $ret .= view('vendor.cannabisscore.nodes.490-report-calculations', $this->v)->render();
        } elseif ($nID == 502) {
            $this->chkUtilityOffers();
            $ret .= view('vendor.cannabisscore.nodes.502-utility-offers', $this->v)->render();
        } elseif (in_array($nID, [177, 457, 465, 471])) {
            return $this->printReportBlds($nID);
        } elseif (in_array($nID, [209, 432, 440, 448])) {
            return $this->printReportLgts($nID);
        } elseif ($nID == 536) {
            $this->prepFeedbackSkipBtn();
            $GLOBALS["SL"]->pageJAVA .= view('vendor.cannabisscore.nodes.536-feedback-skip-button-java', $this->v)->render();
        } elseif ($nID == 548) {
            $this->prepFeedbackSkipLnk();
            $ret .= view('vendor.cannabisscore.nodes.548-powerscore-feedback-score-link', $this->v)->render();
        } elseif ($nID == 508) {
            $this->prepUtilityRefTitle();
            $ret .= view('vendor.cannabisscore.nodes.508-utility-referral-title', $this->v)->render();
        } elseif ($nID == 148) { // this should be built-in
            $this->sessData->dataSets["PowerScore"][0]->update([ 'PsStatus' => $this->v["defCmplt"] ]);
            $this->sessData->dataSets["PowerScore"][0]->save();
            session()->put('PowerScoreOwner', $this->coreID);
            session()->put('PowerScoreOwner' . $this->coreID, $this->coreID);
        } elseif ($nID == 637) {
            $ret .= $this->getEmailsList();
        } elseif ($nID == 740) {
            $ret .= $this->getTroubleshoot();
        } elseif ($nID == 742) {
            $ret .= $this->getProccessUploads();
        } elseif ($nID == 744) {
            $ret .= $this->getCultClassicReport();
        } elseif ($nID == 170) {
            $ret .= $this->getAllPowerScoresPublic();
        } elseif ($nID == 799) {
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $this->getAllPowerScoresPublic();
        } elseif ($nID == 773) {
            $ret .= $this->getAllPowerScoreAvgsPublic();
        } elseif ($nID == 801) {
            $GLOBALS["SL"]->x["partnerVersion"] = true;
            $ret .= $this->getAllPowerScoreAvgsPublic();
        } elseif ($nID == 797) {
            $ret .= $this->getPowerScoreFinalReport();
        } elseif ($nID == 775) {
            $ret .= $this->checkBadRecs();
        } elseif ($nID == 777) {
            $ret .= $this->reportPowerScoreFeedback();
        } elseif ($nID == 786) {
            $ret .= $this->adminSearchResults();
        } elseif ($nID == 726) {
            $ret .= '<div class="p20"></div><div id="726graph" class="w100" style="height: 600px;"></div>';
            $GLOBALS["SL"]->pageAJAX .= '$("#726graph").load("/dashboard/surv-1/sessions/graph-daily"); ';
            $GLOBALS["SL"]->x["needsCharts"] = true;
        } elseif ($nID == 808) {
            $ret .= $this->runNwpccImport();
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
                    foreach ($utIDs as $u) $ids[] = $u->PsUtZpUtilID;
                    $uts = RIIPSUtilities::whereIn('PsUtID', $ids)
                        ->get(); // will be upgrade to check for farm's zip code
                    if ($uts->isNotEmpty()) {
                        foreach ($uts as $i => $ut) $curr->addTmpResponse($ut->PsUtID, $ut->PsUtName);
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
        if (empty($tmpSubTier)) $tmpSubTier = $this->loadNodeSubTier($nID);
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
        }
        return false; // false to continue standard post processing
    }
    
    public function ajaxChecksCustom(Request $request, $type = '')
    {
        if ($type == 'powerscore-rank') {
            return $this->ajaxScorePercentiles($request);
        } elseif ($type == 'powerscore-uploads') {
            return $this->getProccessUploadsAjax($request);
        }
        return '';
    }
    
    protected function chkScoreCalcs()
    {
        $this->loadTotFlwrSqFt();
        if (isset($this->sessData->dataSets["PowerScore"]) && isset($this->sessData->dataSets["PowerScore"][0])
            && (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficFacility) 
                || $GLOBALS["SL"]->REQ->has('refresh'))) {
            $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility   = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac       = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting   = 0;
            if ($this->v["totFlwrSqFt"] > 0 && (!isset($this->sessData->dataSets["PowerScore"][0]->PsTotalSize)
                || intVal($this->sessData->dataSets["PowerScore"][0]->PsTotalSize) == 0)) {
                $this->sessData->dataSets["PowerScore"][0]->PsTotalSize = $this->v["totFlwrSqFt"];
            }
            $row = $this->sessData->dataSets["PowerScore"][0];
            if (isset($row->PsKWH) && intVal($row->PsKWH) > 0 
                && isset($this->v["totFlwrSqFt"]) && intVal($this->v["totFlwrSqFt"]) > 0) {
                $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility = $row->PsKWH/$this->v["totFlwrSqFt"];
            }
            if (isset($row->PsKWH) && intVal($row->PsKWH) > 0 && isset($row->PsGrams) && intVal($row->PsGrams) > 0) {
                $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction = $row->PsGrams/$row->PsKWH;
            }
            $area = $this->getArea('Flower');
            if ($area && (!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0)
                && (isset($row->PsIsIntegrated) && intVal($row->PsIsIntegrated) > 0)) {
                $area->PsAreaHvacType  = $row->PsIsIntegrated;
                $area->PsAreaHvacOther = $row->PsHvacOther;
                $area->save();
            }
            if (!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0) {
                $area = $this->getArea('Veg');
                if (!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0) {
                    $area = $this->getArea('Clone');
                    if (!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0) {
                        $area = $this->getArea('Veg');
                    }
                }
            }
            if (isset($area->PsAreaHvacType) && intVal($area->PsAreaHvacType) > 0) {
                $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac = $this->getHvacEffic($area->PsAreaHvacType);
            }
            $watts = $sqft = [];
            if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
                foreach ($this->sessData->dataSets["PSAreas"] as $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
                            $sqft[$typ] = $area->PsAreaSize;
                            $watts[$typ] = 0;
                            if (isset($this->sessData->dataSets["PSLightTypes"]) 
                                && sizeof($this->sessData->dataSets["PSLightTypes"]) > 0) {
                                foreach ($this->sessData->dataSets["PSLightTypes"] as $lgt) {
                                    if ($lgt->PsLgTypAreaID == $area->getKey() 
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0 
                                        && isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) {
                                        $watts[$typ] += ($lgt->PsLgTypCount*$lgt->PsLgTypWattage);
//echo $typ . '<sup>' . $area->getKey() . '</sup> => ' . $watts[$typ] . ' (' . $lgt->PsLgTypCount . ' * ' . $lgt->PsLgTypWattage . ')<sup>' . $lgt->getKey() . '</sup> / ' . $area->PsAreaSize . '<br />';
                                    }
                                }
                            }
                        }
                    }
                }
//echo '<pre>'; print_r($watts); print_r($sqft); echo '</pre>';
                if (isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)) {
                    if ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones')) {
                        if ($sqft["Mother"] < $sqft["Clone"]) {
                            $watts["Clone"] += $watts["Mother"];
                            $sqft["Clone"]  += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        } else {
                            $watts["Mother"] += $watts["Clone"];
                            $sqft["Mother"]  += $sqft["Clone"];
                            $watts["Clone"]   = $sqft["Clone"] = 0;
                        }
                    } elseif ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room')) {
                        if ($sqft["Mother"] < $sqft["Veg"]) {
                            $watts["Veg"]   += $watts["Mother"];
                            $sqft["Veg"]    += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        } else {
                            $watts["Mother"] += $watts["Veg"];
                            $sqft["Mother"]  += $sqft["Veg"];
                            $watts["Veg"]     = $sqft["Veg"] = 0;
                        }
                    }
                }
//echo '<pre>'; print_r($watts); print_r($sqft); echo '</pre>';
                foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
                            $this->sessData->dataSets["PSAreas"][$i]->PsAreaTotalLightWatts = $watts[$typ];
                            $this->sessData->dataSets["PSAreas"][$i]->PsAreaLightingEffic = 0;
                            if ($watts[$typ] > 0 && isset($area->PsAreaSize) && intVal($area->PsAreaSize) > 0) {
                                $hours = $this->getTypeHours($typ);
//echo '(' . $watts[$typ] . ' * ' . $hours . ') / ' . $sqft[$typ] . '<br />';
                                $this->sessData->dataSets["PSAreas"][$i]->PsAreaLightingEffic
                                    = ($watts[$typ]*$hours*365)/$sqft[$typ];
                                $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting
                                    += $this->sessData->dataSets["PSAreas"][$i]->PsAreaLightingEffic;
                            }
                            $this->sessData->dataSets["PSAreas"][$i]->save();
                        }
                    }
                }
            }
            $this->sessData->dataSets["PowerScore"][0]->save();
            if ($this->chkEfficAvgCnt() > 0) {
                $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting
                    = $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting/(1000*$this->v["efficAvgCnt"]);
            }
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        return true;
    }
    
    protected function chkUnprintableSubScores()
    {
        $this->v["noprints"] = '';
        $noprints = [];
        if (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficFacility)
            || !$this->sessData->dataSets["PowerScore"][0]->PsEfficFacility 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility == 0) {
            $noprints[] = 'facility';
        }
        if (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficProduction) 
            || !$this->sessData->dataSets["PowerScore"][0]->PsEfficProduction 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction == 0) {
            $noprints[] = 'production';
        }
        if (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficHvac) 
            || !$this->sessData->dataSets["PowerScore"][0]->PsEfficHvac 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac == 0) {
            $noprints[] = 'HVAC';
        }
        if (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficLighting) 
            || !$this->sessData->dataSets["PowerScore"][0]->PsEfficLighting 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting == 0) {
            $noprints[] = 'lighting';
        }
        if (sizeof($noprints) > 0) {
            foreach ($noprints as $i => $no) {
                if ($i == 0) {
                    $this->v["noprints"] .= $no;
                } else {
                    if (sizeof($noprints) == 2) {
                        $this->v["noprints"] .= ' and ' . $no;
                    } else {
                        $this->v["noprints"] .= ', ' . (($i == (sizeof($noprints)-1)) ? 'and ' : '') . $no;
                    }
                }
            }
        }
        return $this->v["noprints"];
    }
    
    protected function chkScoreFiltCombs()
    {
        $this->v["fltComb"] = [
            'fltFarm' => [ 0, 143, 144, 145 ],
            'fltClimate' => [ '', 
                '1A', '2A', '2B', '3A', '3B', '3C', '4A', '4B', '4C', '5A', '5B', '6A', '6B', '7A', '7B'
                ],
            'fltLght' => [ [0, 0],
                [237, 168], [237, 169], [237, 170], [237, 171], [237, 164], [237, 165], [237, 203], 
                [160, 168], [160, 169], [160, 170], [160, 171], [160, 164], [160, 165], [160, 203], 
                [161, 168], [161, 169], [161, 170], [161, 171], [161, 164], [161, 165], [161, 203], 
                [162, 168], [162, 169], [162, 170], [162, 171], [162, 164], [162, 165], [162, 203], 
                [163, 168], [163, 169], [163, 170], [163, 171], [163, 164], [163, 165], [163, 203] 
                ],
            'fltHvac' => [ [0, 0],
                [237, 247], [237, 248], [237, 249], [237, 250], [237, 356], [237, 357], [237, 251], [237, 360], 
                [160, 247], [160, 248], [160, 249], [160, 250], [160, 356], [160, 357], [160, 251], [160, 360], 
                [161, 247], [161, 248], [161, 249], [161, 250], [161, 356], [161, 357], [161, 251], [161, 360], 
                [162, 247], [162, 248], [162, 249], [162, 250], [162, 356], [162, 357], [162, 251], [162, 360], 
                [163, 247], [163, 248], [163, 249], [163, 250], [163, 356], [163, 357], [163, 251], [163, 360]
                ],
            'fltRenew' => [ [], [149], [159], [151], [150], [158], [153], [154], [155], [156], [157] ],
            'fltPerp' => [ 0, 1 ],
            'fltWtrh' => [ 0, 1 ],
            'fltManu' => [ 0, 1 ],
            'fltAuto' => [ 0, 1 ],
            'fltVert' => [ 0, 1 ]
            ];
        return true;
    }
    
    protected function getFiltUrl()
    {
        $this->v["sort"] = [ 'PsEfficOverall', 'desc' ];
        if ($GLOBALS["SL"]->REQ->has('srt') && trim($GLOBALS["SL"]->REQ->get('srt')) != '') {
            $this->v["sort"][0] = $GLOBALS["SL"]->REQ->get('srt');
            if ($GLOBALS["SL"]->REQ->has('srta') && in_array(trim($GLOBALS["SL"]->REQ->get('srta')), ['asc', 'desc'])) {
                $this->v["sort"][1] = $GLOBALS["SL"]->REQ->get('srta');
            }
        }
        $this->v["urlFlts"] = $this->v["xtraFltsDesc"] = '';
        //if ($this->v["psid"] > 0) $this->v["urlFlts"] .= '&ps=' . $this->v["psid"];
        if ($this->v["fltFarm"] != '') $this->v["urlFlts"] .= '&fltFarm' . '=' . $this->v["fltFarm"];
        if ($this->v["fltState"] != '') $this->v["urlFlts"] .= '&fltState' . '=' . $this->v["fltState"];
        if ($this->v["fltClimate"] != '') $this->v["urlFlts"] .= '&fltClimate' . '=' . $this->v["fltClimate"];
        if ($this->v["fltLght"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltLght"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltLght"][0]) : '')
                . $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $this->v["fltLght"][1]);
            $this->v["urlFlts"] .= '&fltLght' . '=' . $this->v["fltLght"][0] . '-' . $this->v["fltLght"][1];
        }
        if ($this->v["fltHvac"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltHvac"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltHvac"][0]) : '')
                . strtolower($GLOBALS["SL"]->def->getVal('PowerScore HVAC Systems', $this->v["fltHvac"][1]));
            $this->v["urlFlts"] .= '&fltHvac' . '=' . $this->v["fltHvac"][0] . '-' . $this->v["fltHvac"][1];
        }
        if ($this->v["fltPerp"] > 0) {
            $this->v["xtraFltsDesc"] .= ', perpetual farming';
            $this->v["urlFlts"] .= '&fltPerp' . '=' . $this->v["fltPerp"];
        }
        if ($this->v["fltPump"] > 0) {
            $this->v["xtraFltsDesc"] .= ', water pumps';
            $this->v["urlFlts"] .= '&fltPump' . '=' . $this->v["fltPump"];
        }
        if ($this->v["fltWtrh"] > 0) {
            $this->v["xtraFltsDesc"] .= ', mechanical water heating';
            $this->v["urlFlts"] .= '&fltWtrh' . '=' . $this->v["fltWtrh"];
        }
        if ($this->v["fltManu"] > 0) {
            $this->v["xtraFltsDesc"] .= ', manual environmental controls';
            $this->v["urlFlts"] .= '&fltManu' . '=' . $this->v["fltManu"];
        }
        if ($this->v["fltAuto"] > 0) {
            $this->v["xtraFltsDesc"] .= ', automatic environmental controls';
            $this->v["urlFlts"] .= '&fltAuto' . '=' . $this->v["fltAuto"];
        }
        if ($this->v["fltVert"] > 0) {
            $this->v["xtraFltsDesc"] .= ', vertical stacking';
            $this->v["urlFlts"] .= '&fltVert' . '=' . $this->v["fltVert"];
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            foreach ($this->v["fltRenew"] as $renew) {
                $this->v["xtraFltsDesc"] .= ', ' 
                    . $GLOBALS["SL"]->def->getVal('PowerScore Onsite Power Sources', $renew);
            }
            $this->v["urlFlts"] .= '&fltRenew' . '=' . implode(',', $this->v["fltRenew"]);
        }
        if ($this->v["xtraFltsDesc"] != '') {
            $this->v["xtraFltsDesc"] = ' using <span class="wht">' . substr($this->v["xtraFltsDesc"], 2) . '</span>';
        }
        return true;
    }
    
    protected function chkScoreFilters()
    {
        $this->v["eff"] = (($GLOBALS["SL"]->REQ->has('eff')) 
            ? trim($GLOBALS["SL"]->REQ->get('eff')) : 'Overall');
        $this->v["psid"] = (($GLOBALS["SL"]->REQ->has('ps')) 
            ? intVal($GLOBALS["SL"]->REQ->get('ps')) : 0);
        $this->v["powerscore"] = RIIPowerScore::find($this->v["psid"]);
        $this->v["fltFarm"] = (($GLOBALS["SL"]->REQ->has('fltFarm')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltFarm')) : 0);
        $this->v["fltState"] = (($GLOBALS["SL"]->REQ->has('fltState')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltState')) : '');
        $this->v["fltClimate"] = (($GLOBALS["SL"]->REQ->has('fltClimate')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltClimate')) : '');
        $this->v["fltLght"] = (($GLOBALS["SL"]->REQ->has('fltLght')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLght')) : [ 0, 0 ]);
        $this->v["fltHvac"] = (($GLOBALS["SL"]->REQ->has('fltHvac')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltHvac')) : [ 0, 0 ]);
        $this->v["fltPerp"] = (($GLOBALS["SL"]->REQ->has('fltPerp')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltPerp')) : 0);
        $this->v["fltPump"] = (($GLOBALS["SL"]->REQ->has('fltPump')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltPump')) : 0);
        $this->v["fltWtrh"] = (($GLOBALS["SL"]->REQ->has('fltWtrh')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltWtrh')) : 0);
        $this->v["fltManu"] = (($GLOBALS["SL"]->REQ->has('fltManu')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltManu')) : 0);
        $this->v["fltAuto"] = (($GLOBALS["SL"]->REQ->has('fltAuto')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltAuto')) : 0);
        $this->v["fltVert"] = (($GLOBALS["SL"]->REQ->has('fltVert')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltVert')) : 0);
        $this->v["fltRenew"] = (($GLOBALS["SL"]->REQ->has('fltRenew')) 
            ? $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('fltRenew')) : []);
        $this->v["fltCmpl"] = (($GLOBALS["SL"]->REQ->has('fltCmpl')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltCmpl')) : 243);
        $this->v["fltCup"] = (($GLOBALS["SL"]->REQ->has('fltCup')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltCup')) : 0);
        $this->getFiltUrl();
        return true;
    }
    
    // New ranking procedures built in Aug '18
    protected function calcAllScoreRanks()
    {
        $this->chkScoreFilters();
        $this->calcCurrScoreRanks();
        $this->chkScoreFiltCombs();
        foreach ($this->v["fltComb"] as $flt => $opts) {
            foreach ($this->v["fltComb"] as $f => $o) $this->v[$f] = $o[0];
            foreach ($opts as $j => $opt) {
                $this->v[$flt] = $opt;
                $this->getFiltUrl();
                $this->calcCurrScoreRanks();
            }
        }
        return '<br /><br />Recalculations Complete<br />';
    }
    
    protected function calcCurrScoreRanks()
    {
        $this->v["ranksCache"] = RIIPSRanks::where('PsRnkFilters', $this->v["urlFlts"])
            ->first();
        if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->PsRnkID)) {
            $this->v["ranksCache"] = new RIIPSRanks;
            $this->v["ranksCache"]->PsRnkFilters = $this->v["urlFlts"];
        }
        $eval = "\$this->v['allscores'] = " . $GLOBALS["SL"]->modelPath('PowerScore') . "::" 
            . $this->filterAllPowerScoresPublic() . "->where('PsEfficFacility', '>', 0)"
                . "->where('PsEfficProduction', '>', 0)->where('PsEfficLighting', '>', 0)"
                . "->where('PsEfficHvac', '>', 0)->orderBy(\$this->v['sort'][0], \$this->v['sort'][1])->get();";
        eval($eval);
        $this->v["ranksCache"]->PsRnkTotCnt = $this->v["allscores"]->count();
        $r = [];
        $l = [ "over" => [], "oraw" => [], "faci" => [], "prod" => [], "ligh" => [], "hvac" => [] ];
        $avg = [ "kwh" => 0, "g" => 0 ];
        if ($this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $i => $ps) {
                $sqft = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', $this->v["areaTypes"]["Flower"])
                    ->select('PsAreaSize')
                    ->first();
                if ($sqft && isset($sqft->PsAreaSize)) {
                    $avg["kwh"] += $ps->PsKWH/$sqft->PsAreaSize;
                    $avg["g"] += $ps->PsGrams/$sqft->PsAreaSize;
                }
                $l["faci"][] = $ps->PsEfficFacility;
                $l["prod"][] = $ps->PsEfficProduction;
                $l["ligh"][] = $ps->PsEfficLighting;
                $l["hvac"][] = $ps->PsEfficHvac;
            }
            sort($l["faci"], SORT_NUMERIC);
            sort($l["prod"], SORT_NUMERIC);
            sort($l["ligh"], SORT_NUMERIC);
            sort($l["hvac"], SORT_NUMERIC);
            foreach ($this->v["allscores"] as $i => $ps) {
                $r[$ps->PsID] = [ "over" => 0, "oraw" => 0, "faci" => 0, "prod" => 0, "ligh" => 0, "hvac" => 0 ];
                $r[$ps->PsID]["faci"] = $GLOBALS["SL"]->getArrPercentile($l["faci"], $ps->PsEfficFacility);
                $r[$ps->PsID]["prod"] = $GLOBALS["SL"]->getArrPercentile($l["prod"], $ps->PsEfficProduction, true);
                $r[$ps->PsID]["ligh"] = $GLOBALS["SL"]->getArrPercentile($l["ligh"], $ps->PsEfficLighting);
                $r[$ps->PsID]["hvac"] = $GLOBALS["SL"]->getArrPercentile($l["hvac"], $ps->PsEfficHvac);
                $r[$ps->PsID]["oraw"] = ($r[$ps->PsID]["faci"]+$r[$ps->PsID]["prod"]+$r[$ps->PsID]["ligh"]
                    +$r[$ps->PsID]["hvac"])/4;
                $l["oraw"][] = $r[$ps->PsID]["oraw"];
            }
            sort($l["oraw"], SORT_NUMERIC);
            foreach ($this->v["allscores"] as $i => $ps) {
                $r[$ps->PsID]["over"] = $GLOBALS["SL"]->getArrPercentile($l["oraw"], $r[$ps->PsID]["oraw"], true);
            }
            
            // Now store calculated ranks for individual scores...
            foreach ($this->v["allscores"] as $i => $ps) {
                if (trim($this->v["urlFlts"]) == '') {
                    RIIPowerScore::find($ps->PsID)->update([
                        'PsEfficOverall' => $r[$ps->PsID]["over"]
                        ]);
                }
                if (trim($this->v["urlFlts"]) == '&fltFarm=' . $ps->PsCharacterize) {
                    RIIPowerScore::find($ps->PsID)->update([
                        'PsEfficOverSimilar' => $r[$ps->PsID]["over"]
                        ]);
                }
                $tmp = RIIPSRankings::where('PsRnkPSID', $ps->PsID)
                    ->where('PsRnkFilters', $this->v["urlFlts"])
                    ->first();
                if (!$tmp) {
                    $tmp = new RIIPSRankings;
                    $tmp->PsRnkPSID = $ps->PsID;
                    $tmp->PsRnkFilters = $this->v["urlFlts"];
                    $tmp->save();
                }
                $tmp->PsRnkOverall    = $r[$ps->PsID]["over"];
                $tmp->PsRnkOverallAvg = $r[$ps->PsID]["oraw"];
                $tmp->PsRnkFacility   = $r[$ps->PsID]["faci"];
                $tmp->PsRnkProduction = $r[$ps->PsID]["prod"];
                $tmp->PsRnkLighting   = $r[$ps->PsID]["ligh"];
                $tmp->PsRnkHVAC       = $r[$ps->PsID]["hvac"];
                $tmp->save();
            }
        }
        // Now store listed raw sub-score values for filter...
        $tmp = RIIPSRanks::where('PsRnkFilters', $this->v["urlFlts"])
            ->first();
        if (!$tmp) {
            $tmp = new RIIPSRanks;
            $tmp->PsRnkFilters = $this->v["urlFlts"];
            $tmp->save();
        }
        $tmp->PsRnkTotCnt = $this->v["allscores"]->count();
        $tmp->PsRnkOverallAvg = implode(',', $l["oraw"]);
        $tmp->PsRnkFacility   = implode(',', $l["faci"]);
        $tmp->PsRnkProduction = implode(',', $l["prod"]);
        $tmp->PsRnkLighting   = implode(',', $l["ligh"]);
        $tmp->PsRnkHVAC       = implode(',', $l["hvac"]);
        if ($tmp->PsRnkTotCnt > 0) {
            $tmp->PsRnkAvgSqftKwh = $avg["kwh"]/$tmp->PsRnkTotCnt;
            $tmp->PsRnkAvgSqftGrm = $avg["g"]/$tmp->PsRnkTotCnt;
        }
        $tmp->save();
        return true;
    }
    
    protected function ajaxScorePercentiles(Request $request)
    {
        if (!$request->has('ps') || intVal($request->get('ps')) <= 0 || !$request->has('eff') 
            || !in_array(trim($request->get('eff')), ['Overall', 'Facility', 'Production', 'HVAC', 'Lighting'])) {
            return '';
        }
        $this->chkScoreFilters();
        if ($this->v["powerscore"] && isset($this->v["powerscore"]->PsID)) {
            $updateCutoff = mktime(date("H"), date("i"), date("s"), date("n"), date("j")-2, date("Y"));
            //$coreRec
            $this->v["ranksCache"] = RIIPSRankings::where('PsRnkPSID', $this->v["powerscore"]->PsID)
                ->where('PsRnkFilters', $this->v["urlFlts"])
                ->first();
            if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->PsRnkID)) {
                $this->v["ranksCache"] = new RIIPSRankings;
                $this->v["ranksCache"]->PsRnkPSID            = $this->v["powerscore"]->PsID;
                $this->v["ranksCache"]->PsRnkFilters         = $this->v["urlFlts"];
            } elseif (strtotime($this->v["ranksCache"]->updated_at) < $updateCutoff || $request->has('refresh')) {
                $this->v["ranksCache"]->PsRnkOverall = $this->v["ranksCache"]->PsRnkOverallAvg 
                    = $this->v["ranksCache"]->PsRnkFacility = $this->v["ranksCache"]->PsRnkProduction 
                    = $this->v["ranksCache"]->PsRnkHVAC = $this->v["ranksCache"]->PsRnkLighting = 0;
            }
            if (!isset($this->v["ranksCache"]->PsRnkOverall) || $this->v["ranksCache"]->PsRnkOverall == 0
                || $this->v["ranksCache"]->PsRnkOverall === null) {
                $this->v["ranksCache"]->PsRnkOverall = $this->v["ranksCache"]->PsRnkOverallAvg = $overAvgCnt = 0;
                $this->rankAllScores();
                if (isset($this->v["allRnks"][$this->v["powerscore"]->PsID])) {
                    $this->v["ranksCache"] = $this->v["allRnks"][$this->v["powerscore"]->PsID];
                } else {
                    // current PowerScore is "better" than X others, and "worse" than Y others
                    $this->v["efficPercs"] = [
                        "Overall"    => [ "better" => 0, "worse" => 0 ], 
                        "Facility"   => [ "better" => 0, "worse" => 0 ], 
                        "Production" => [ "better" => 0, "worse" => 0 ], 
                        "HVAC"       => [ "better" => 0, "worse" => 0 ], 
                        "Lighting"   => [ "better" => 0, "worse" => 0 ]
                        ];                
                    $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') 
                        . "::" . $this->filterAllPowerScoresPublic() . "->where('PsTimeType', "
                        . $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past') . ")"
                        . "->where('PsID', 'NOT LIKE', " . $this->coreID . ")->where('PsEfficFacility', '>', 0)"
                        . "->where('PsEfficProduction', '>', 0)->where('PsEfficHvac', '>', 0)"
                        . "->where('PsEfficLighting', '>', 0)->select('PsID', 'PsEfficOverall', 'PsEfficFacility', "
                        . "'PsEfficProduction', 'PsEfficHvac', 'PsEfficLighting')"
                        . "->get();";
                    eval($eval);
                    if ($allscores->isNotEmpty()) {
                        foreach ($allscores as $s) {
                            if (isset($this->v["powerscore"]->PsEfficFacility) 
                                && $this->v["powerscore"]->PsEfficFacility > 0) { // kWh/sqft
                                $sort = (($this->v["powerscore"]->PsEfficFacility <= $s->PsEfficFacility) 
                                    ? "better" : "worse");
                                $this->v["efficPercs"]["Facility"][$sort]++;
                            }
                            if (isset($this->v["powerscore"]->PsEfficProduction) 
                                && $this->v["powerscore"]->PsEfficProduction > 0) { // grams/kWh
                                $sort = (($this->v["powerscore"]->PsEfficProduction >= $s->PsEfficProduction) 
                                    ? "better" : "worse");
                                $this->v["efficPercs"]["Production"][$sort]++;
                            }
                            if (isset($this->v["powerscore"]->PsEfficHvac) 
                                && $this->v["powerscore"]->PsEfficHvac > 0) { // kWh/sqft
                                $sort = (($this->v["powerscore"]->PsEfficHvac <= $s->PsEfficHvac) 
                                    ? "better" : "worse");
                                $this->v["efficPercs"]["HVAC"][$sort]++;
                            }
                            if (isset($this->v["powerscore"]->PsEfficLighting) 
                                && $this->v["powerscore"]->PsEfficLighting > 0) { // kWh/sqft
                                $sort = (($this->v["powerscore"]->PsEfficLighting <= $s->PsEfficLighting) 
                                    ? "better" : "worse");
                                $this->v["efficPercs"]["Lighting"][$sort]++;
                            }
                        }
                    }
                    $overAvgCnt = 0;
                    foreach ($this->v["efficPercs"] as $type => $percs) {
                        $tot = 1+$percs["better"]+$percs["worse"];
                        if ($tot > 1) {
                            $this->v["ranksCache"]->{ 'PsRnk' . $type } = 100*($percs["better"]/$tot);
                            $this->v["ranksCache"]->PsRnkOverallAvg += $this->v["ranksCache"]->{ 'PsRnk' . $type };
                            $overAvgCnt++;
                        }
                    }
                    if ($this->v["ranksCache"]->PsRnkOverallAvg > 0) { // $overAvgCnt > 0
                        $this->v["ranksCache"]->PsRnkOverallAvg = $this->v["ranksCache"]->PsRnkOverallAvg/4;
                    }
                    foreach ($allscores as $s) {
                        $this->v["efficPercs"]["Overall"][($this->v["ranksCache"]->PsRnkOverallAvg 
                            >= $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg) ? "better" : "worse"]++;
                    }
                    $this->v["ranksCache"]->PsRnkTotCnt = sizeof($allscores)+1;
                    $this->v["ranksCache"]->PsRnkOverall 
                        = 100*($this->v["efficPercs"]["Overall"]["better"]/$this->v["ranksCache"]->PsRnkTotCnt);
                    $this->v["ranksCache"]->save();
                }
            }
            if ($this->v["urlFlts"] == '') {
                $this->v["powerscore"]->PsEfficOverall = $this->v["ranksCache"]->PsRnkOverall;
                $this->v["powerscore"]->save();
            } elseif ($this->v["urlFlts"] == ('&fltFarm=' . $this->v["powerscore"]->PsCharacterize)) {
                $this->v["powerscore"]->PsEfficOverSimilar = $this->v["ranksCache"]->PsRnkOverall;
                $this->v["powerscore"]->save();
            }
            $this->v["currGuage"]    = round($this->v["ranksCache"]->{ 'PsRnk' . $this->v["eff"] });
            $this->v["currGuageTot"] = $this->v["ranksCache"]->{ 'PsRnk' . $this->v["eff"] . 'Cnt' };
            if ($this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkProductionCnt
                && $this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkHVACCnt
                && $this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkLightingCnt) {
                $this->v["currGuageTot"] = -3;
            }
            $this->v["hasOverall"] = (isset($this->v["powerscore"]->PsEfficFacility) 
                && isset($this->v["powerscore"]->PsEfficProduction) && isset($this->v["powerscore"]->PsEfficHvac) 
                && isset($this->v["powerscore"]->PsEfficLighting) && $this->v["powerscore"]->PsEfficFacility > 0
                && $this->v["powerscore"]->PsEfficProduction > 0 && $this->v["powerscore"]->PsEfficHvac > 0
                && $this->v["powerscore"]->PsEfficLighting > 0);
            return view('vendor.cannabisscore.nodes.490-report-calculations-ajax-graphs', $this->v)->render();
        }
        return '';
    }
    
    protected function rankAllScores()
    {
        $this->v["allRnks"] = [];
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') 
            . "::" . $this->filterAllPowerScoresPublic() . "->where('PsTimeType', "
            . $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past') . ")->where('PsEfficFacility', '>', 0)"
            . "->where('PsEfficProduction', '>', 0)->where('PsEfficHvac', '>', 0)->where('PsEfficLighting', '>', 0)"
            . "->select('PsID', 'PsEfficOverall', 'PsEfficFacility', 'PsEfficProduction', 'PsEfficHvac', "
            . "'PsEfficLighting')->get();";
        eval($eval);
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $s) {
                $efficPercs = [ // current PowerScore is "better" than X others, and "worse" than Y others
                    "Facility"   => [ "better" => 0, "worse" => 0 ], 
                    "Production" => [ "better" => 0, "worse" => 0 ], 
                    "HVAC"       => [ "better" => 0, "worse" => 0 ], 
                    "Lighting"   => [ "better" => 0, "worse" => 0 ]
                    ];
                foreach ($allscores as $s2) {
                    $efficPercs["Facility"][($s->PsEfficFacility <= $s2->PsEfficFacility) ? "better" : "worse"]++;
                    $efficPercs["Production"][($s->PsEfficProduction >= $s2->PsEfficProduction) ? "better" : "worse"]++;
                    $efficPercs["HVAC"][($s->PsEfficHvac <= $s2->PsEfficHvac) ? "better" : "worse"]++;
                    $efficPercs["Lighting"][($s->PsEfficLighting <= $s2->PsEfficLighting) ? "better" : "worse"]++;
                }
                $this->v["allRnks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', $this->v["urlFlts"])
                    ->first();
                if (!$this->v["allRnks"][$s->PsID] || !isset($this->v["allRnks"][$s->PsID]->PsRnkID)) {
                    $this->v["allRnks"][$s->PsID] = new RIIPSRankings;
                    $this->v["allRnks"][$s->PsID]->PsRnkPSID    = $s->PsID;
                    $this->v["allRnks"][$s->PsID]->PsRnkFilters = $this->v["urlFlts"];
                    $this->v["allRnks"][$s->PsID]->PsRnkTotCnt  = $allscores->count();
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg = 0;
                foreach ($efficPercs as $type => $percs) {
                    $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type } 
                        = 100*($percs["better"]/$this->v["allRnks"][$s->PsID]->PsRnkTotCnt);
                    $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg += $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type};
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg = $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg/4;
                $this->v["allRnks"][$s->PsID]->save();
            }
            foreach ($allscores as $s) {
                $efficPercs = [ "better" => 0, "worse" => 0 ];
                foreach ($allscores as $s2) {
                    $efficPercs[($this->v["allRnks"][$s->PsID]->PsRnkOverallAvg 
                        >= $this->v["allRnks"][$s2->PsID]->PsRnkOverallAvg) ? "better" : "worse"]++;
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverall 
                    = 100*($efficPercs["better"]/$this->v["allRnks"][$s->PsID]->PsRnkTotCnt);
                $this->v["allRnks"][$s->PsID]->save();
                if ($this->v["urlFlts"] == '') {
                    $s->PsEfficOverall = $this->v["allRnks"][$s->PsID]->PsRnkOverall;
                    $s->save();
                } elseif ($this->v["urlFlts"] == ('&fltFarm=' . $s->PsCharacterize)) {
                    $s->PsEfficOverSimilar = $this->v["allRnks"][$s->PsID]->PsRnkOverall;
                    $s->save();
                }
            }
        }
        return true;
    }
    
    protected function chkEfficAvgCnt()
    {
        $this->v["efficAvgCnt"] = 0;
        if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                if (isset($area->PsAreaLightingEffic) && $area->PsAreaLightingEffic > 0) $this->v["efficAvgCnt"]++;
            }
        }
        return $this->v["efficAvgCnt"];
    }
    
    protected function prepPrintEfficLgt()
    {
        $this->chkEfficAvgCnt();
        $this->v["printEfficLgt"] = $sqft = $watt = $lightBreakdown = [];
        if (isset($this->sessData->dataSets["PSAreas"])) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->PsAreaType == $defID && $typ != 'Dry') {
                        $sqft[$typ] = $area->PsAreaSize;
                        $watt[$typ] = $area->PsAreaTotalLightWatts;
                    }
                }
            }
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->PsAreaType == $defID && $typ != 'Dry') {
                        $lightBreakdown[$typ] = '';
                        if (isset($area->PsAreaLightingEffic) && $area->PsAreaLightingEffic > 0) {
                            //  (Clone watts x # of lights x 24 hrs) / Clone sq ft)
                            if (isset($this->sessData->dataSets["PSLightTypes"]) 
                                && sizeof($this->sessData->dataSets["PSLightTypes"]) > 0) {
                                foreach ($this->sessData->dataSets["PSLightTypes"] as $lgt) {
                                    $areaIDs = [$area->getKey()];
                                    if (isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)
                                        && (($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                                            == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones')
                                            && $typ == 'Clone') || ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                                            == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room')
                                            && $typ == 'Veg'))) {
                                        $areaIDs[] = $this->getAreaFld('Mother', 'PsAreaID');
                                    }
                                    if (in_array($lgt->PsLgTypAreaID, $areaIDs) 
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0 
                                        && isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) {
                                        if ($lightBreakdown[$typ] != '') $lightBreakdown[$typ] .= ' + ';
                                        $lightBreakdown[$typ] .= '<nobr>( ' . number_format($lgt->PsLgTypCount) 
                                            . ' fixtures x ' . number_format($lgt->PsLgTypWattage) . ' W )</nobr>';
                                    }
                                }
                            }
                            if (strpos($lightBreakdown[$typ], '+') === false) {
                                $lightBreakdown[$typ] = str_replace('(', '', str_replace(')', '', 
                                    $lightBreakdown[$typ]));
                            }
                            $curr = [ $typ, $sqft[$typ], $watt[$typ], $lightBreakdown[$typ] ];
                            if (isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)) {
                                switch ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc) {
                                    case $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones'):
                                        if (in_array($typ, ['Mother', 'Clone'])) {
                                            $curr[0] = 'Mother & Clones';
                                            $curr[1] = $sqft["Mother"]+$sqft["Clone"];
                                        }
                                        break;
                                    case $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room'):
                                        if (in_array($typ, ['Mother', 'Veg'])) {
                                            $curr[0] = 'Mother & Veg';
                                            $curr[1] = $sqft["Mother"]+$sqft["Veg"];
                                        }
                                        break;
                                }
                            }
                            $this->v["printEfficLgt"][] = [
                                "typ" => $typ,
                                "eng" => '( (' . $curr[0] . ' <nobr>' . number_format($curr[2]/1000) 
                                    . ' kW</nobr> <nobr>x ' . $this->getTypeHours($typ) . ' hrs x 365)</nobr> / <nobr>' 
                                    . number_format($curr[1]) . ' sq ft )</nobr>',
                                "lgt" => $curr[0] . ': ' . $lightBreakdown[$typ],
                                "num" => $curr[0] . ' ' . $GLOBALS["SL"]->sigFigs($area->PsAreaLightingEffic/1000, 3) 
                                    . ' kWh / sq ft'
                                ];
                        }
                    }
                }
            }
        }
        return $this->v["printEfficLgt"];
    }
    
    protected function getTypeHours($typ)
    {
        return (($typ == 'Veg') ? 18 : (($typ == 'Flower') ? 12 : 24));
    }
    
    protected function chkUtilityOffers()
    {
        $this->v["utilOffer"] = ['', ''];
        $GLOBALS["SL"]->loadStates();
        /*
        $this->v["utilOffer"][0] = $GLOBALS["SL"]->states->getState($this->sessData->dataSets["PowerScore"][0]->PsState)
            . ' Energy Group';
        $this->v["utilOffer"][1] = '/start/referral/?new=1&u=6&s=' . $this->coreID;
        */
        return $this->v["utilOffer"];
    }
    
    protected function getLoopItemLabelCustom($loop, $itemRow = [], $itemInd = -3)
    {
        if ($loop == 'Growth Stages') {
            switch (intVal($itemRow->PsAreaType)) {
                case 237: return 'mother plants';     break;
                case 160: return 'clone plants';      break;
                case 161: return 'vegetating plants'; break;
                case 162: return 'flowering plants';  break;
                case 163: return 'drying / curing';   break;
            }
        }
        return '';
    }
    
    protected function postTableHvac($nID, $hvacSet = 'Cooling')
    {
        $set = 'PowerScore HVAC ' . $hvacSet;
        
    }
    
    // returns an array of overrides for ($currNodeSessionData, ???... 
    protected function printNodeSessDataOverride($nID = -3, $tmpSubTier = [], $currNodeSessionData = '')
    {
        if (sizeof($this->sessData->dataSets) == 0) return [];
        if ($nID == 49) {
            if (isset($this->sessData->dataSets["PSFarm"]) 
                && isset($this->sessData->dataSets["PSFarm"][0]->PsFrmType)) {
                return [$this->sessData->dataSets["PSFarm"][0]->PsFrmType];
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
            $area = $this->getArea('Mother');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtArtif);
        } elseif ($condition == '#CloneArtificialLight') {
            $area = $this->getArea('Clone');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtArtif);
        } elseif ($condition == '#VegArtificialLight') {
            $area = $this->getArea('Veg');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtArtif);
        } elseif ($condition == '#FlowerArtificialLight') {
            $area = $this->getArea('Flower');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtArtif);
        } elseif ($condition == '#MotherSunlight') {
            $area = $this->getArea('Mother');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
            return intVal($area->PsAreaLgtSun);
        } elseif ($condition == '#CloneSunlight') {
            $area = $this->getArea('Clone');
            if (!isset($area) || !isset($area->PsAreaHasStage)) return 0;
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
    
    protected function nIDgetRenew($nID)
    {
        switch ($nID) {
            case 59:
            case 78: return $GLOBALS["SL"]->def->getID('PowerScore Renewables', 'Solar PV');
            case 80: return $GLOBALS["SL"]->def->getID('PowerScore Renewables', 'Wind');
            case 61: return $GLOBALS["SL"]->def->getID('PowerScore Renewables', 'Biomass');
            case 60: return $GLOBALS["SL"]->def->getID('PowerScore Renewables', 'Geothermal');
            case 81: return $GLOBALS["SL"]->def->getID('PowerScore Renewables', 'Pelton Wheel');
        }
        return -3;
    }
    
    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        if (!isset($this->sessData->dataSets["PowerScore"])) return $emailBody;
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
                        $swap = $this->cnvrtLbs2Grm($this->sessData->dataSets["PowerScore"][0]->PsEfficProduction);
                        $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        break;
                    case '[{ PowerScore Total Submissions }]': 
                        $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
                            ->get();
                        $swap = $chk->count();
                        break;
                    case '[{ PowerScore Report Link Similar }]':
                        $swap = $GLOBALS["SL"]->sysOpts["app-url"] . '/calculated/u-' . $this->coreID . '?fltFarm='
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
    
    
    public function loadUtils()
    {
        $this->v["powerUtils"] = $this->v["powerUtilsInd"] = [];
        $chk = RIIPSUtilities::orderBy('PsUtName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $u) {
                $this->v["powerUtilsInd"][$u->PsUtID] = sizeof($this->v["powerUtils"]);
                $this->v["powerUtils"][] = [
                    "id"     => $u->PsUtID, 
                    "name"   => $u->PsUtName, 
                    "zips"   => [], 
                    "states" => []
                    ];
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilZips()
    {
        $this->loadUtils();
        $chk = RIIPSUtiliZips::get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $uz) {
                if (isset($uz->PsUtZpZipCode) && isset($this->v["powerUtilsInd"][$uz->PsUtZpUtilID])) {
                    $ind = $this->v["powerUtilsInd"][$uz->PsUtZpUtilID];
                    if (!in_array($uz->PsUtZpZipCode, $this->v["powerUtils"][$ind]["zips"])) {
                        $this->v["powerUtils"][$ind]["zips"][] = $uz->PsUtZpZipCode;
                    }
                }
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilStates()
    {
        $this->getUtilZips();
        if (sizeof($this->v["powerUtils"]) > 0) {
            foreach ($this->v["powerUtils"] as $ind => $u) {
                $chk = SLZips::whereIn('ZipZip', $u["zips"])
                    ->select('ZipState')
                    ->distinct()
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $i => $z) {
                        if (!in_array($z->ZipState, $this->v["powerUtils"][$ind]["states"])) {
                            $this->v["powerUtils"][$ind]["states"][] = $z->ZipState;
                        }
                    }
                }
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getStateUtils()
    {
        $this->getUtilStates();
        $this->v["statePowerUtils"] = [];
        if (sizeof($this->v["powerUtils"]) > 0) {
            $GLOBALS["SL"]->loadStates();
            foreach ($this->v["powerUtils"] as $ind => $u) {
                if (sizeof($u["states"]) > 0) {
                    foreach ($u["states"] as $s) {
                        $s = $GLOBALS["SL"]->states->getState($s);
                        if (!isset($this->v["statePowerUtils"][$s])) $this->v["statePowerUtils"][$s] = [];
                        if (!in_array($u["id"], $this->v["statePowerUtils"][$s])) {
                            $this->v["statePowerUtils"][$s][] = $u["id"];
                        }
                    }
                }
            }
        }
        return ksort($this->v["statePowerUtils"]);
    }
    
    protected function getArea($type = 'Mother') 
    {
        if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                if (isset($area->PsAreaType) && $area->PsAreaType == $this->v["areaTypes"][$type]) {
                    return $area;
                }
            }
        }
        return [];
    }
    
    protected function sortAreas() 
    {
        $this->v["psAreas"] = [];
        if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $type => $defID) {
                    if (isset($area->PsAreaType) && $area->PsAreaType == $this->v["areaTypes"][$type]) {
                        $this->v["psAreas"][$type] = $area;
                    }
                }
            }
        }
        return true;
    }
    
    protected function getAreaFld($type, $fldName)
    {
        if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($type == $typ && $area->PsAreaType == $defID) return $area->{ $fldName };
                }
            }
        }
        return false;
    }
    
    protected function loadTotFlwrSqFt()
    {
        $this->v["totFlwrSqFt"] = $this->getAreaFld('Flower', 'PsAreaSize');
        return $this->v["totFlwrSqFt"];
    }
    
    protected function sortMonths()
    {
        if (!isset($this->sessData->dataSets["PSMonthly"]) || sizeof($this->sessData->dataSets["PSMonthly"]) == 0) {
            $this->sessData->dataSets["PSMonthly"] = [];
            for ($m = 1; $m <= 12; $m++) {
                $new = new RIIPSMonthly;
                $new->PsMonthPSID  = $this->coreID;
                $new->PsMonthMonth = $m;
                $new->PsMonthOrder = $m-1;
                $new->save();
                $this->sessData->dataSets["PSMonthly"][] = $new;
            }
        }
        return RIIPSMonthly::where('PsMonthPSID', $this->coreID)
            ->orderBy('PsMonthOrder', 'asc')
            ->get();
    }
    
    protected function getAreaLights($areaID = -3, $areaType = '')
    {
        $ret = [];
        if ($areaID <= 0 && trim($areaType) != '') $areaID = $this->getAreaFld($areaType, 'PsAreaID');
        if ($areaID <= 0) return [];
        return $this->sessData->getRowIDsByFldVal('PSLightTypes', [ 'PsLgTypAreaID' => $areaID ], true);
    }
    
    protected function recordIsIncomplete($coreTbl, $coreID, $coreRec = NULL)
    {
        if ($coreID > 0) {
            if (!isset($coreRec->PsID)) $coreRec = RIIPowerScore::find($coreID);
//echo 'recordIsIncomplete(' . $coreTbl . ', ' . $coreID . ', status#' . $coreRec->PsStatus . '<br />';
            return (!isset($coreRec->PsStatus) 
                || $coreRec->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'));
        }
        return false;
    }
    
    public function multiRecordCheckIntro($cnt = 1)
    {
        return '<a id="hidivBtnUnfinished' . $this->currNode() . '" class="btn btn-lg btn-default w100 hidivBtn" '
            . 'href="javascript:;">You Have ' . (($cnt == 1) ? 'An Unfinished PowerScore' : 'Unfinished PowerScores') 
            . '</a>';
    }
    
    public function multiRecordCheckRowTitle($coreRecord)
    {
        return 'PowerScore #' . $coreRecord[1]->getKey();
    }
    
    public function multiRecordCheckRowSummary($coreRecord)
    {
        return '<div class="mT5 mB5 slGrey">Last Edited: ' . date('n/j/y, g:ia', strtotime($coreRecord[1]->updated_at)) 
            . '<br />Percent Complete: ' . $this->rawOrderPercent($coreRecord[1]->PsSubmissionProgress) . '%</div>';
    }
    
    
    public function cnvrtSqFt2Acr($squareFeet = 0)
    {
        return $squareFeet*0.000022956841138659;
    }
    
    public function cnvrtAcr2SqFt($acres = 0)
    {
        return $acres*43560;
    }
    
    public function cnvrtLbs2Grm($lbs = 0)
    {
        return $lbs*453.59237;
    }
    
    public function getHvacEffic($defID)
    {
        switch ($defID) {
            case 247: return 115;
            case 248: return 77;
            case 249: return 104;
            case 250: return 65;
            case 360: return 0.0000001;
            case 251: // Other HVAC System calculations, coming to and from the future
                      break;
        }
        return 0;
    }
    
    
    protected function getAllReportCalcs()
    {
        $this->loadTotFlwrSqFt();
        $this->chkScoreCalcs();
        $this->prepPrintEfficLgt();
        $this->chkUnprintableSubScores();
        $this->v["sessData"] = $this->sessData->dataSets;
        $this->v["psid"] = $this->sessData->dataSets["PowerScore"][0]->getKey();
        $this->v["hasRefresh"] = (($GLOBALS["SL"]->REQ->has('refresh')) ? '&refresh=1' : '');
        $this->v["filtClimate"] = (($GLOBALS["SL"]->REQ->has('climate') 
            && intVal($GLOBALS["SL"]->REQ->get('climate')) == 1) ? 1 : 0);
        $this->v["filtFarm"] = (($GLOBALS["SL"]->REQ->has('farm')) 
            ? intVal($GLOBALS["SL"]->REQ->get('farm')) : 0);
        $GLOBALS["SL"]->loadStates();
        return true;
    }
    
    protected function getSimilarStats($ps = NULL)
    {
        if (!$ps && isset($this->sessData->dataSets["PowerScore"]) 
            && sizeof($this->sessData->dataSets["PowerScore"]) > 0) {
            $ps = $this->sessData->dataSets["PowerScore"][0];
        }
        $chk = RIIPSRankings::where('PsRnkPSID', $ps->PsID)
            ->where('PsRnkFilters', '&fltFarm=' . $ps->PsCharacterize)
            ->first();
        if (!$chk || !isset($chk->PsRnkPSID)) {
            $GLOBALS["SL"]->pageJAVA .= 'setTimeout("document.getElementById(\'hidFrameID\').src=\'/calculated/u-' 
                . $ps->PsID . '?refresh=1&fltFarm=' 
                . $ps->PsCharacterize . '\'", 12000); ';
        }
        return $chk;
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
    
    protected function prepFeedbackSkipLnk()
    {
        $this->v["psOwner"] = ((session()->has('PowerScoreOwner')) ? session()->get('PowerScoreOwner') 
            : ((isset($this->sessData->dataSets["PsFeedback"]) && isset($this->sessData->dataSets["PsFeedback"][0]) 
                && isset($this->sessData->dataSets["PsFeedback"][0]->PsfPsID)) 
                ? $this->sessData->dataSets["PsFeedback"][0]->PsfPsID : -3));
        return true;
    }
    
    protected function loadCupScoreIDs()
    {
        $this->v["cultClassicIds"] = $this->v["emeraldIds"] = [];
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic'))
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) $this->v["cultClassicIds"][] = $c->PsCupPSID;
        }
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Emerald Cup Regenerative Award'))
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) $this->v["emeraldIds"][] = $c->PsCupPSID;
        }
        return true;
    }
    
    protected function getCultClassicReport()
    {
        $this->v["farms"] = [];
        $chk = RIICompetitors::where('CmptYear', '=', date("Y"))
            ->where('CmptCompetition', '=', $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic'))
            ->orderBy('CmptName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $farm) {
                $this->v["farms"][$i] = [ "name" => $farm->CmptName, "ps" => [], "srch" => [] ];
                $chk2 = DB::table('RII_PowerScore')
                    ->leftJoin('RII_PSRankings', function ($join) {
                        $join->on('RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                            ->where('RII_PSRankings.PsRnkFilters', '');
                    })
                    ->where('RII_PowerScore.PsName', 'LIKE', $farm->CmptName)
                    ->whereIn('RII_PowerScore.PsStatus', [$this->v["defCmplt"], 364])
                    ->orderBy('RII_PowerScore.PsID', 'desc')
                    ->get();
                if ($chk2->isNotEmpty()) {
                    foreach ($chk2 as $j => $ps) {
                        if ($j == 0) $this->v["farms"][$i]["ps"] = $ps;
                    }
                } else {
                    $chk2 = RIIPowerScore::where('PsName', 'LIKE', $farm->CmptName)
                        ->where('PsStatus', 'LIKE', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
                        ->orderBy('PsID', 'desc')
                        ->get();
                    if ($chk2->isNotEmpty()) {
                        foreach ($chk2 as $j => $ps) {
                            if ($j == 0) $this->v["farms"][$i]["ps"] = $ps;
                        }
                    } else {
                        $srchs = $GLOBALS["SL"]->parseSearchWords($farm->CmptName);
                        if (sizeof($srchs) > 0) {
                            foreach ($srchs as $srch) {
                                $chk2 = RIIPowerScore::where('PsName', 'LIKE', '%' . $srch . '%')
                                    ->get();
                                if ($chk2->isNotEmpty()) {
                                    foreach ($chk2 as $j => $ps) {
                                        if (isset($ps->PsName) && trim($ps->PsName) != '' 
                                            && !isset($this->v["farms"][$i]["srch"][$ps->PsID])) {
                                            $this->v["farms"][$i]["srch"][$ps->PsID] = $ps->PsName;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->v["farmTots"] = [ 0, 0 ];
        if (sizeof($this->v["farms"]) > 0) {
            foreach ($this->v["farms"] as $i => $f) {
                if (isset($this->v["farms"][$i]["ps"]) && isset($this->v["farms"][$i]["ps"]->PsStatus)) {
                    if (in_array($this->v["farms"][$i]["ps"]->PsStatus, [$this->v["defCmplt"], 364])) {
                        $this->v["farmTots"][1]++;
                    } else {
                        $this->v["farmTots"][0]++;
                    }
                }
            }
        }
//echo '<pre>'; print_r($this->v["farms"]); echo '</pre>';

        //$chk = RIIPowerScore::get();
        //$this->v["entryFarmNames"] = $this->listSimilarNames($chk);
        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.744-cult-classic-report-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'CultClassic-PowerScoreReport-' . date("Y-m-d") . '.xls');
        } else {
            if ($GLOBALS["SL"]->REQ->has('refresh')) {
                $this->v["reportExtras"] = '<script type="text/javascript"> ';
                if ($this->v["farms"] && sizeof($this->v["farms"]) > 0) {
                    foreach ($this->v["farms"] as $i => $farm) {
                        if (isset($farm["ps"]) && isset($farm["ps"]->PsID)) {
                            $this->v["reportExtras"] .= 'setTimeout("document.getElementById(\'hidFrameID\').src=\''
                                . '/calculated/u-' . $farm["ps"]->PsID . '?refresh=1\'", ' . ($i*5000) . '); ';
                        }
                    }
                }
                $this->v["reportExtras"] .= ' </script>';
            }
            return view('vendor.cannabisscore.nodes.744-cult-classic-report', $this->v)->render();
        }
    }
    
    protected function listSimilarNames($chk)
    {
        $ret = '';
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                if (isset($ps->PsName) && trim($ps->PsName) != '') {
                    $ret .= ', <a href="/calculated/u-' . $ps->PsID . '" target="_blank">' . $ps->PsName . '</a>';
                }
            }
        }
        return $ret;
    }
    
    protected function getEmailsList()
    {
        $this->v["sendResults"] = '';
        $this->v["emailList"] = [];
        $this->v["scoreLists"] = [ 'all' => [], 'blw' => [], 'avg' => [], 'abv' => [], 'inc' => [] ];
        $this->v["wchLst"] = 'all';
        if ($GLOBALS["SL"]->REQ->has('wchLst') 
            && in_array($GLOBALS["SL"]->REQ->wchLst, ['all', 'blw', 'avg', 'abv', 'inc'])) {
            $this->v["wchLst"] = $GLOBALS["SL"]->REQ->wchLst;
        }
        $this->v["wchEma"] = 0;
        if ($GLOBALS["SL"]->REQ->has('wchEma') && intVal($GLOBALS["SL"]->REQ->wchEma) > 0) {
            $this->v["wchEma"] = $GLOBALS["SL"]->REQ->wchEma;
        }
        if ($GLOBALS["SL"]->REQ->has('yesSend') && intVal($GLOBALS["SL"]->REQ->yesSend) == 1
            && $GLOBALS["SL"]->REQ->has('scores') && sizeof($GLOBALS["SL"]->REQ->scores) > 0
            && $this->v["wchEma"] > 0) {
            $chk = RIIPowerScore::whereIn('PsID', $GLOBALS["SL"]->REQ->scores)
                ->get();
            if ($chk->isNotEmpty()) {
                $ajax = '';
                foreach ($chk as $i => $ps) {
                    $this->v["sendResults"] .= '<div>Sending #' . $ps->PsID . ' ' . $ps->PsEmail . ' (' . $ps->PsName 
                        . ')<br /><div id="emaAjax' . $ps->PsID . '">' . $GLOBALS["SL"]->sysOpts["spinner-code"] 
                        . '</div></div>';
                    $ajax .= 'setTimeout(function() { 
                        var name = encodeURIComponent(document.getElementById(\'replyNameID\').value);
                        var url = "/ajadm/send-email?e=' . $this->v["wchEma"] . '&t=1&c=' . $ps->PsID 
                            . '&r="+document.getElementById(\'replyToID\').value+"&rn="+name+"";
                        $("#emaAjax' . $ps->PsID . '").load(url); }, ' . ($i*2000) . ');';
                }
                $this->v["sendResults"] .= $GLOBALS["SL"]->opnAjax() . $ajax . $GLOBALS["SL"]->clsAjax();
            }
        }
        $chk = RIIPowerScore::where(function ($query) {
                $query->whereNull('PsEfficOverSimilar')
                      ->orWhere('PsEfficOverSimilar', 'LIKE', 0);
            })
            ->where('PsEmail', 'NOT LIKE', '')
            ->where('PsEmail', 'NOT LIKE', 'NWPCC@NWPCC.com')
            ->where('PsEfficFacility', '>', 0)
            ->where('PsEfficProduction', '>', 0)
            ->where('PsEfficLighting', '>', 0)
            ->where('PsEfficHvac', '>', 0)
            ->get();
        if ($chk->isNotEmpty()) {
            $this->v["sendResults"] .= '<h3>Calculating...</h3>';
            foreach ($chk as $i => $ps) {
                $this->v["sendResults"] .= '<iframe id="calcEmaPs' . $ps->PsID . '" src="" class="fL"
                    style="height: 260px; width: 130px;"></iframe>';
                $GLOBALS["SL"]->pageJAVA .= 'setTimeout("document.getElementById(\'calcEmaPs' . $ps->PsID 
                    . '\').src=\'/calculated/u-' . $ps->PsID . '?fltFarm=' . $ps->PsCharacterize . '&refresh=1\'", ' 
                    . ($i*2000) . '); ';
            }
            $this->v["sendResults"] .= '<div class="fC"></div><hr>Once all the above frames have loaded, then these '
                . 'records have been recalculated and updated.<hr>';
        }
        $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
            ->orderBy('PsEmail', 'asc')
            ->orderBy('PsID', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $row) {
                if (isset($row->PsEmail) && trim($row->PsEmail) != '') {
                    if (!isset($this->v["emailList"][$row->PsState])) $this->v["emailList"][$row->PsState] = [];
                    $found = false;
                    if (sizeof($this->v["scoreLists"]["all"]) > 0) {
                        foreach ($this->v["scoreLists"]["all"] as $i => $infChk) {
                            if (strtolower($infChk["email"]) == strtolower($row->PsEmail)) $found = true;
                        }
                    }
                    if (!$found) {
                        if (!in_array(trim($row->PsEmail), $this->v["emailList"][$row->PsState])) {
                            $this->v["emailList"][$row->PsState][] = trim($row->PsEmail);
                        }
                        $infArr = [
                            "id"    => $row->PsID,
                            "email" => $row->PsEmail, 
                            "farm"  => $row->PsName, 
                            "score" => $row->PsEfficOverSimilar,
                            "sent"  => false
                            ];
                        $this->v["scoreLists"]["all"][] = $infArr;
                        if ($row->PsStatus != $this->v["defCmplt"] || !isset($row->PsEfficFacility) 
                            || !isset($row->PsEfficProduction) || !isset($row->PsEfficHvac) 
                            || !isset($row->PsEfficLighting) || $row->PsEfficFacility <= 0 
                            || $row->PsEfficProduction <= 0 || $row->PsEfficHvac <= 0 || $row->PsEfficLighting <= 0) {
                            $this->v["scoreLists"]["inc"][] = $infArr;
                        } elseif ($row->PsEfficOverSimilar >= 200/3) {
                            $this->v["scoreLists"]["abv"][] = $infArr;
                        } elseif ($row->PsEfficOverSimilar >= 100/3) {
                            $this->v["scoreLists"]["avg"][] = $infArr;
                        } else {
                            $this->v["scoreLists"]["blw"][] = $infArr;
                        }
                        // check past sent list
                    }
                }
            }
        }
        $GLOBALS["SL"]->pageAJAX .= view('vendor.cannabisscore.nodes.637-email-list-ajax', $this->v)->render();
        return view('vendor.cannabisscore.nodes.637-email-list', $this->v)->render();
    }
    
    protected function getProccessUploads()
    {
        $this->v["uploaders"] = RIIPowerScore::where('PsUploadEnergyBills', 'LIKE', 1)
            //->where('PsStatus', 'LIKE', $this->v["defCmplt"])
            ->orderBy('PsID', 'desc')
            ->get();
        $this->v["upMonths"] = [];
        if ($this->v["uploaders"] && sizeof($this->v["uploaders"]) > 0) {
            foreach ($this->v["uploaders"] as $i => $ps) {
                $this->v["upMonths"][$ps->PsID] = [];
                $chk = RIIPSMonthly::where('PsMonthPSID', 'LIKE', $ps->PsID)
                    ->get();
                if ($chk && sizeof($chk) > 0) {
                    foreach ($chk as $mon) $this->v["upMonths"][$ps->PsID][$mon->PsMonthMonth] = $mon;
                }
            }
            if ($GLOBALS["SL"]->REQ->has("sub")) {
                foreach ($this->v["uploaders"] as $i => $ps) {
                    if ($GLOBALS["SL"]->REQ->has("kwh" . $ps->PsID)) {
                        $newKwh = $GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID);
                        if (!$GLOBALS["SL"]->REQ->has("kwh" . $ps->PsID . "a") || 
                            $GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID . "a") != $newKwh) {
                            $this->v["uploaders"][$i]->PsKWH = $newKwh;
                            $this->v["uploaders"][$i]->save();
                        }
                        for ($mon = 1; $mon <= 12; $mon++) {
                            if ($GLOBALS["SL"]->REQ->has("kwh" . $ps->PsID . "m" . $mon) 
                                && $GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID . "m" . $mon)) {
                                $kWh = intVal($GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID . "m" . $mon));
                                if (!isset($this->v["upMonths"][$ps->PsID][$mon])) {
                                    $this->v["upMonths"][$ps->PsID][$mon] = new RIIPSMonthly;
                                    $this->v["upMonths"][$ps->PsID][$mon]->PsMonthPSID = $ps->PsID;
                                    $this->v["upMonths"][$ps->PsID][$mon]->PsMonthMonth = $mon;
                                }
                                $this->v["upMonths"][$ps->PsID][$mon]->PsMonthKWH1 = intVal($kWh);
                                $this->v["upMonths"][$ps->PsID][$mon]->save();
                            }
                        }
                    }
                    if ($GLOBALS["SL"]->REQ->has("status" . $ps->PsID)) {
                        $newStatus = intVal($GLOBALS["SL"]->REQ->get("status" . $ps->PsID));
                        if ($newStatus > 0 && $this->v["uploaders"][$i]->PsStatus != $newStatus) {
                            $this->v["uploaders"][$i]->PsStatus = $newStatus;
                            $this->v["uploaders"][$i]->save();
                            $this->logAdd('session-stuff', 'Admin Changing PowerScore#' . $ps->PsID 
                                . ' Status To ' . $newStatus . ' <i>(RII.getProccessUploads)</i>');
                        }
                    }
                }
            }
        }
        $this->getProccessUploadsBadLnks();
        return view('vendor.cannabisscore.nodes.742-process-uploads', $this->v)->render();
    }
    
    protected function getProccessUploadsBadLnks()
    {
        $this->v["log1"] = '';
        $this->v["uploads"] = $this->v["uploadInfo"] = [];
        if (isset($this->v["uploaders"]) && $this->v["uploaders"]->isNotEmpty()) {
            foreach ($this->v["uploaders"] as $i => $ps) {
                $this->v["uploads"][$i] = SLUploads::where('UpTreeID', '=', 1)
                    ->where('UpCoreID', '=', $ps->getKey())
                    ->orderBy('UpID', 'asc')
                    ->get();
                if ($this->v["uploads"][$i] && sizeof($this->v["uploads"][$i]) > 0) {
                    $this->v["uploadInfo"][$i] = [
                        "fld"  => '../storage/app/up/evidence/' . str_replace('-', '/', substr($ps->created_at, 0, 10)),
                        "fold" => $this->getUploadFolder(69, $ps, 'PowerScore'),
                        "ups"  => []
                        ];
                    foreach ($this->v["uploads"][$i] as $j => $up) {
                        if (trim($up->UpStoredFile) != '') {
                            $baseFilen = $up->UpStoredFile . '.' . $GLOBALS["SL"]->getFileExt($up->UpUploadFile);
                            $this->v["uploadInfo"][$i]["ups"][$j] = [
                                "full" => $this->v["uploadInfo"][$i]["fold"] . $baseFilen,
                                "subf" => []
                                ];
                            //$this->v["uploadInfo"][$i]["full"] 
                            //    = $GLOBALS["SL"]->searchDeeperDirs($this->v["uploadInfo"][$i]["full"]);
                            if (!file_exists($this->v["uploadInfo"][$i]["ups"][$j]["full"])) {
                                $this->v["uploadInfo"][$i]["ups"][$j]["subf"] 
                                    = $GLOBALS["SL"]->findDirFile($this->v["uploadInfo"][$i]["fld"], $baseFilen);
                                $this->v["log1"] .= '<h3>' . $ps->PsID . '</h3> Not Found: ' 
                                    . $this->v["uploadInfo"][$i]["ups"][$j]["full"] . '<br />';
                                if (sizeof($this->v["uploadInfo"][$i]["ups"][$j]["subf"]) > 0) {
                                    foreach ($this->v["uploadInfo"][$i]["ups"][$j]["subf"] as $k => $fld) {
                                        $this->v["log1"] .= ', ' . $fld;
                                    }
                                    $ps->PsUniqueStr = $this->v["uploadInfo"][$i]["ups"][$j]["subf"][
                                        sizeof($this->v["uploadInfo"][$i]["ups"][$j]["subf"])-1];
                                    $ps->save();
                                }
                                $this->v["log1"] .= '<br />new uniqueStr: ' . $ps->PsUniqueStr . '<br /><br />';
                                $this->logAdd('session-stuff', 'Manually Restoring PowerScore#' . $ps->PsID 
                                    . ' UniqueStr ' . $ps->PsUniqueStr . ' <i>(RII.getProccessUploadsBadLnks)</i>');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    protected function getProccessUploadsAjax(Request $request)
    {
        $ret = '';
        if ($request->has('p')) {
            $ps = RIIPowerScore::find($request->get('p'));
            if ($ps && isset($ps->PsID) && $this->v["isAdmin"]) {
                $this->loadTree(1);
                $this->loadAllSessData('PowerScore', $ps->getKey());
                $ups = $this->getUploads(69, $this->v["isAdmin"]);
                if (sizeof($ups) > 0) {
                    foreach ($ups as $up) $ret .= $up;
                    $ret .= '<style> #psUpload' . $ps->PsID . ' { display: table-row; } </style>';
                }
            }
        }
        if ($request->has('last')) {
            $ret .= '<style> #animLoadingUploads { display: none; } </style>';
        }
        return $ret;
    }
    
    protected function getTroubleshoot()
    {
        $this->v["lgtChk"] = $this->v["lgtAvg"] = $this->v["hvcChk"] = $this->v["hvcAvg"] = [ 0, 0, 0, 0 ];
        $this->v["allScoreIDs"] = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
            ->where('PsCharacterize', 144)
            ->select('PsID', 'PsName', 'PsEfficLighting', 'PsEfficHvac')
            ->orderBy('PsID', 'desc')
            ->get();
        if ($this->v["allScoreIDs"]->isNotEmpty()) {
            foreach ($this->v["allScoreIDs"] as $i => $ps) {
                $this->v["hvcChk"][$i] = [ $ps->PsEfficHvac, 0, 0 ];
                $this->v["lgtChk"][$i] = [ $ps, [], 0, 0, 0, 0, 0, 0, 0, 0 ];
                $this->v["lgtChk"][$i][1] = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->select('PsAreaType', 'PsAreaSize', 'PsAreaTotalLightWatts', 'PsAreaHvacType')
                    ->get();
                if ($this->v["lgtChk"][$i][1]->isNotEmpty()) {
                    $areaTot = 0;
                    foreach ($this->v["lgtChk"][$i][1] as $area) {
                        if (isset($area->PsAreaSize)) $areaTot += $area->PsAreaSize;
                    }
                    foreach ($this->v["lgtChk"][$i][1] as $area) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->PsAreaType == $defID && $typ != 'Dry') {
                                if (isset($area->PsAreaSize) && isset($area->PsAreaTotalLightWatts) 
                                    && intVal($area->PsAreaSize) > 0 && intVal($area->PsAreaTotalLightWatts) > 0) {
                                    $this->v["lgtChk"][$i][2] += $area->PsAreaSize;
                                    $this->v["lgtChk"][$i][3] 
                                        += $area->PsAreaTotalLightWatts*$this->getTypeHours($typ)*365;
                                    $this->v["lgtChk"][$i][6] += $area->PsAreaTotalLightWatts;
                                    $this->v["lgtChk"][$i][8] += ($area->PsAreaTotalLightWatts/$area->PsAreaSize);
                                    $this->v["lgtChk"][$i][9]++;
                                }
                            }
                        }
                        $effic = $this->getHvacEffic($area->PsAreaHvacType);
                        if (isset($area->PsAreaSize) && $area->PsAreaSize > 0 && $effic > 0) {
                            $this->v["hvcChk"][$i][1] += ($area->PsAreaSize*$effic);
                            $this->v["hvcChk"][$i][2] += $effic*($area->PsAreaSize/$areaTot);
                        }
                    }
                    if ($this->v["lgtChk"][$i][2] > 0) {
                        $this->v["lgtChk"][$i][4] = $this->v["lgtChk"][$i][3]/(1000*$this->v["lgtChk"][$i][2]);
                        $this->v["lgtChk"][$i][5] = $this->v["lgtChk"][$i][6]/$this->v["lgtChk"][$i][2];
                    }
                }
                if ($this->v["lgtChk"][$i][9] > 0) {
                    $this->v["lgtChk"][$i][7] = $this->v["lgtChk"][$i][8]/$this->v["lgtChk"][$i][9];
                }
                $this->v["lgtAvg"][0] += $ps->PsEfficLighting;
                $this->v["lgtAvg"][1] += $this->v["lgtChk"][$i][4];
                $this->v["lgtAvg"][2] += $this->v["lgtChk"][$i][5];
                $this->v["lgtAvg"][3] += $this->v["lgtChk"][$i][7];
                $this->v["hvcAvg"][0] += $this->v["hvcChk"][$i][0];
                $this->v["hvcAvg"][1] += $this->v["hvcChk"][$i][1];
                $this->v["hvcAvg"][2] += $this->v["hvcChk"][$i][2];
            }
            $this->v["lgtAvg"][0] = $this->v["lgtAvg"][0]/sizeof($this->v["allScoreIDs"]);
            $this->v["lgtAvg"][1] = $this->v["lgtAvg"][1]/sizeof($this->v["allScoreIDs"]);
            $this->v["lgtAvg"][2] = $this->v["lgtAvg"][2]/sizeof($this->v["allScoreIDs"]);
            $this->v["lgtAvg"][3] = $this->v["lgtAvg"][3]/sizeof($this->v["allScoreIDs"]);
            $this->v["hvcAvg"][0] = $this->v["hvcAvg"][0]/sizeof($this->v["allScoreIDs"]);
            $this->v["hvcAvg"][1] = $this->v["hvcAvg"][1]/sizeof($this->v["allScoreIDs"]);
            $this->v["hvcAvg"][2] = $this->v["hvcAvg"][2]/sizeof($this->v["allScoreIDs"]);
        }
        
        /*
        $this->v["logOne"] = '';
        $this->v["subTblsPowerScore"] = [
            'PSAreas'       => ['PsAreaID',   'PsAreaPSID'   ],
            'PSFarm'        => ['PsFrmID',    'PsFrmPSID'    ],
            'PSForCup'      => ['PsCupID',    'PsCupPSID'    ],
            'PSLicenses'    => ['PsLicID',    'PsLicPSID'    ],
            'PSMonthly'     => ['PsMonthID',  'PsMonthPSID'  ],
            'PSOtherPower'  => ['PsOthPwrID', 'PsOthPwrPSID' ],
            'PSRankings'    => ['PsRnkID',    'PsRnkPSID'    ],
            'PSRenewables'  => ['PsRnwID',    'PsRnwPSID'    ],
            'PSUtiliLinks'  => ['PsUtLnkID',  'PsUtLnkPSID'  ]
            ];
        $this->v["subTblsPSAreas"] = [
            'PSAreasBlds'   => ['PsArBldID',  'PsArBldAreaID'],
            'PSLightTypes'  => ['PsLgTypID',  'PsLgTypAreaID']
            ];
        $this->v["subTblsPSAreasBlds"] = [
            'PSAreasConstr' => ['PsArCnsID',  'PsArCnsBldID' ]
            ];
        $baks = [ '_221', '_310', '_417', '_521' ];
        foreach ($baks as $b => $bak) {
            $qman = "SELECT r2.* FROM `RII" . $bak . "_PowerScore` r2 JOIN "
                . "`RII_PowerScore` r ON r2.`PsID` LIKE r.`PsID` WHERE r2.`PsZipCode` IS NOT NULL "
                . "AND r2.`PsZipCode` NOT LIKE '' AND (r.`PsZipCode` IS NULL OR r.`PsZipCode` LIKE '')";
            $this->v["logOne"] .= '<div class="fPerc66">' . $qman . '</div>';
            $this->v["chk1"] = DB::select( DB::raw( $qman ) );
            $this->v["chks2"] = [];
            if ($this->v["chk1"]->isNotEmpty()) {
                foreach ($this->v["chk1"] as $i => $ps) {
                    $this->v["logOne"] .= $GLOBALS["SL"]->copyTblRecFromRow('PowerScore', $ps);
                    $this->logAdd('session-stuff', 'Manually Restoring PowerScore#' . $ps->PsID 
                        . ' <i>(RII.getTroubleshoot)</i>');
                    $this->v["chks2"][$i] = [];
                    foreach ($this->v["subTblsPowerScore"] as $tbl => $keys) {
                        $qman = "SELECT * FROM `RII" . $bak . "_" . $tbl . "` WHERE `" . $keys[1] . "` LIKE '" 
                            . $ps->PsID . "'";
                        $chk1 = DB::select( DB::raw( $qman ) );
                        //echo '<br />' . $qman . '<pre>'; print_r($chk1); echo '</pre>';
                        if ($chk1->isNotEmpty() && isset($this->v["subTbls" . $tbl . ""]) 
                            && sizeof($this->v["subTbls" . $tbl . ""]) > 0) {
                            foreach ($chk1 as $j => $row1) {
                                $this->v["logOne"] .= $GLOBALS["SL"]->copyTblRecFromRow($tbl, $row1);
                                $this->v["chks2"][$i][$tbl][] = $row1;
                                foreach ($this->v["subTbls" . $tbl . ""] as $tbl2 => $keys2) {
                                    $qman = "SELECT * FROM `RII" . $bak . "_" . $tbl2 . "` WHERE `" . $keys2[1] 
                                        . "` LIKE '" . $row1->{ $keys[0] } . "'";
                                    $chk2 = DB::select( DB::raw( $qman ) );
                                    //echo '<br />' . $qman . '<pre>'; print_r($chk2); echo '</pre>';
                                    if ($chk2->isNotEmpty()) {
                                        foreach ($chk2 as $k => $row2) {
                                            $this->v["logOne"] .= $GLOBALS["SL"]->copyTblRecFromRow($tbl2, $row2);
                                            $this->v["chks2"][$i][$tbl2][] = $row2;
                                            if (isset($this->v["subTbls" . $tbl2 . ""]) 
                                                && sizeof($this->v["subTbls" . $tbl2 . ""]) > 0) {
                                                foreach ($this->v["subTbls" . $tbl2 . ""] as $tbl3 => $keys3) {
                                                    $qman = "SELECT * FROM `RII" . $bak . "_" . $tbl3 . "` WHERE `" 
                                                        . $keys3[1] . "` LIKE '" . $row2->{ $keys2[0] } . "'";
                                                    $chk3 = DB::select( DB::raw( $qman ) );
                                                    //echo '<br />' . $qman . '<pre>'; print_r($chk3); echo '</pre>';
                                                    if ($chk3->isNotEmpty()) {
                                                        foreach ($chk3 as $l => $row3) {
                                                            $this->v["logOne"] 
                                                                .= $GLOBALS["SL"]->copyTblRecFromRow($tbl3, $row3);
                                                            $this->v["chks2"][$i][$tbl3][] = $row3;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        */
        
        /*
        $ret = '';
        foreach ($baks as $b => $bak) {
            $qman = "SELECT r2.* FROM `RII" . $bak . "_PowerScore` r2 WHERE r2.`PsStatus` LIKE " . $this->v["defCmplt"]
                . " AND r2.`PsID` NOT IN (SELECT r.`PsID` FROM `RII_PowerScore` r WHERE r.`PsStatus` LIKE " 
                . $this->v["defCmplt"] . ")";
            $this->v["chk3"] = DB::select( DB::raw( $qman ) );
            $this->v["chks4"] = [];
            if ($this->v["chk3"]->isNotEmpty()) {
                foreach ($this->v["chk3"] as $i => $ps) {
                     $ret .= '<h3>' . $ps->PsID . '</h3><pre>' . print_r($ps) . '</pre><br />';
                }
            }
        }
        */
        if ($GLOBALS["SL"]->REQ->has('import')) $this->runImport();
        if ($GLOBALS["SL"]->REQ->has('refresh')) {
            return $this->calcAllScoreRanks();
        } else {
            return view('vendor.cannabisscore.nodes.740-trouble-shooting', $this->v)->render();
        }
    }
    
    protected function runImport()
    {
        $this->v["importResult"] = '';
    
        /*
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/cultivation-classic-import-data.csv';
        if (file_exists($file)) {
            $rawData = $colNames = [];
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $l) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if ($i > 0) {
                        echo '<pre>'; print_r($row); echo '</pre>';
                        $score = new RIIPowerScore;
                        $score->PsName            = $row[0];
                        $score->PsZipCode         = $row[1];
                        if (isset($row[2]) && trim($row[2]) != '') $score->PsGrams             = $row[2];
                        if (isset($row[3]) && trim($row[3]) != '') $score->PsTotalSize       = $row[3];
                        if (isset($row[5]) && trim($row[5]) != '') $score->PsEfficFacility   = $row[5];
                        if (isset($row[6]) && trim($row[6]) != '') $score->PsEfficProduction = $row[6];
                        $score->save();
                    }
                }
            }
        }
        */
        
        /*
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/cultivation_classic_2016-import.csv';
        if (file_exists($file)) {
            $rawData = $colNames = [];
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $l) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if ($i == 0) $colNames = $row;
                    elseif (isset($row[0])) {
                        if (!isset($rawData[$row[0]])) $rawData[$row[0]] = [];
                        $rawData[$row[0]][] = $row;
                    }
                }
            }
        }
        //echo '<pre>'; print_r($colNames); print_r($rawData); echo '</pre>';
        foreach ($rawData as $farm => $farmRows) {
            echo '<br /><br /><br /><table border=1 >';
            foreach ($colNames as $i => $name) {
                if ($i < 35) {
                    echo '<tr><th>' . $name . '</th>';
                    foreach ($farmRows as $j => $row) {
                        echo '<td>' . ((isset($row[$i])) ? $row[$i] : '') . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
        exit;
        */
        
        /*
        // Import Utility Zip Codes (Ran One-Time)
        // taken from https://openei.org/datasets/dataset/u-s-electric-utility-companies-and-rates-look-up-by-zipcode-feb-2011/resource/3f00482e-8ea0-4b48-8243-a212b6322e74
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/noniouzipcodes2011.csv';
        if (file_exists($file)) {
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                echo 'line count: ' . sizeof($lines) . '<br />';
                foreach ($lines as $i => $l) {
                if ($i > 16000) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if (substr($row[2], 0, 1) == '"' && isset($row[9])) {
                        $row[2] = substr($row[2], 1) . substr($row[3], 0, strlen($row[3])-1);
                        $row[3] = $row[4]; $row[4] = $row[5]; $row[5] = $row[6]; 
                        $row[6] = $row[7]; $row[7] = $row[8]; $row[8] = $row[9];
                    }
                    $ut = RIIPSUtilities::where('PsUtName', trim($row[2]))
                        ->first();
                    if (!$ut || !isset($ut->PsUtID)) {
                        $ut = new RIIPSUtilities;
                        $ut->PsUtName = trim($row[2]);
                        $ut->PsUtType = $GLOBALS["SL"]->def->getID('Utility Company Type', 'Non-Investor Owned Utilities');
                        $ut->save();
                    }
                    $utzip = RIIPSUtiliZips::where('PsUtZpUtilID', $ut->PsUtID)
                        ->where('PsUtZpZipCode', trim($row[0]))
                        ->first();
                    if (!$utzip || !isset($utzip->PsUtZpID)) {
                        $utzip = new RIIPSUtiliZips;
                        $utzip->PsUtZpUtilID = $ut->PsUtID;
                        $utzip->PsUtZpZipCode = trim($row[0]);
                        $utzip->save();
                    } else {
                        //echo 'already found ' . $row[0] . ', ' . $row[2] . '??<br />';
                    }
                }
                }
            }
        }
        */
        return true;
    }
    
    protected function runNwpccImport()
    {
        $ret = '';
        if ($GLOBALS["SL"]->REQ->has('import')) {
            $this->v["nwpcc"] = [];
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-A.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        if ($i > 1) {
                            $row = $GLOBALS["SL"]->mexplode(';', $l);
                            $id = intVal($row[0]);
                            $this->v["nwpcc"][$id] = [];
                            $this->v["nwpcc"][$id]["PowerScore"] = new RIIPowerScore;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsName = 'NWPCC #' . $row[0];
                            $this->v["nwpcc"][$id]["PowerScore"]->PsEmail = 'NWPCC@NWPCC.com';
                            $this->v["nwpcc"][$id]["PowerScore"]->PsStatus = 242;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsPrivacy = 361;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsUserID = 0;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsSubmissionProgress = 44;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsUniqueStr 
                                = $this->getRandStr('PowerScore', 'PsUniqueStr', 20);
                            $this->v["nwpcc"][$id]["PowerScore"]->PsIsMobile = 0;
                            $this->v["nwpcc"][$id]["PowerScore"]->PsIPaddy = '--import--';
                            $this->v["nwpcc"][$id]["PowerScore"]->PsZipCode = $row[1];
                            $this->v["nwpcc"][$id]["PowerScore"]->PsKWH = 0;
                            if (trim($row[4]) != '' || (trim($row[3]) != '' && trim($row[4]) != '') 
                                || (trim($row[3]) != '' && trim($row[5]) != '')
                                || (trim($row[4]) != '' && trim($row[5]) != '')) {
                                $this->v["nwpcc"][$id]["PowerScore"]->PsCharacterize = 145;
                            } elseif (trim($row[3]) != '') {
                                $this->v["nwpcc"][$id]["PowerScore"]->PsCharacterize = 143;
                            } elseif (trim($row[5]) != '') {
                                $this->v["nwpcc"][$id]["PowerScore"]->PsCharacterize = 144;
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->PsTotalSize = intVal($row[8]);
                            $this->v["nwpcc"][$id]["PowerScore"]->PsHavestsPerYear 
                                = (isset($row[30]) && (intVal($row[30]) > 0) ? intVal($row[30]) : 1);
                            if (isset($row[31]) && intVal($row[31]) > 0) {
                                $this->v["nwpcc"][$id]["PowerScore"]->PsGrams 
                                    = $this->v["nwpcc"][$id]["PowerScore"]->PsHavestsPerYear
                                        *$this->cnvrtLbs2Grm(intVal($row[31]));
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->save();
                            $this->v["nwpcc"][$id]["PSForCup"] = new RIIPSForCup;
                            $this->v["nwpcc"][$id]["PSForCup"]->PsCupCupID = 369;
                            $this->v["nwpcc"][$id]["PSForCup"]->save();
                            if ($row[2] == 'Recreational') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPSLicenses;
                                $this->v["nwpcc"][$id]["PSLicenses"]->PsLicLicense = 142;
                                $this->v["nwpcc"][$id]["PSLicenses"]->save();
                            } elseif ($row[2] == 'Medical') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPSLicenses;
                                $this->v["nwpcc"][$id]["PSLicenses"]->PsLicLicense = 141;
                                $this->v["nwpcc"][$id]["PSLicenses"]->save();
                            }
                            $this->runNwpccImportInitAreas($id);
                            //$this->v["nwpcc"][$id]["PSAreas"][1]->PsAreaSize = $row[5];
                            $totSize = intVal($row[7]);
                            $this->v["nwpcc"][$id]["PSAreas"][0]->update([ 'PsAreaSize' => $totSize*(10/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][1]->update([ 'PsAreaSize' => $totSize*(10/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][2]->update([ 'PsAreaSize' => $totSize*(68/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][3]->update([ 'PsAreaSize' => $totSize*(245/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][4]->update([ 'PsAreaSize' => $totSize*(23/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][3]->update([ 'PsAreaLgtDep' 
                                => (($row[9] == 'TRUE') ? 1 : 0) ]);
//echo 'id: ' . $id . '<pre>'; print_r($this->v["nwpcc"][$id]["PSAreas"]); echo '</pre>';
                            if (in_array($row[25], ['PGE', 'Portland General'])) $row[25] = 'Portland General Electric';
                            if (trim($row[25]) != '') {
                                $chk = RIIPSUtilities::where('PsUtName', 'LIKE', $row[25])
                                    ->first();
                                if ($chk) {
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"] = new RIIPSUtiliLinks;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->PsUtLnkUtilityID = $chk->PsUtID;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->save();
                                } else {
                                    $this->v["nwpcc"][$id]["PowerScore"]->update([ 'PsSourceUtilityOther' => $row[25]]);
                                }
                            }
                            if (intVal($row[27]) > 0 || intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPSRenewables;
                                $this->v["nwpcc"][$id]["PSRenewables"]->PsRnwRenewable = 153;
                                $this->v["nwpcc"][$id]["PSRenewables"]->save();
                            }
                            if (intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPSRenewables;
                                $this->v["nwpcc"][$id]["PSRenewables"]->PsRnwRenewable = 154;
                                $this->v["nwpcc"][$id]["PSRenewables"]->save();
                            }
                        } 
                    }
                }
            }
            
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-B.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(';', $l);
                        if ($i > 1) {
                            $id = intVal($row[0]);
                            $area = 3;
                            if ($row[1] == 'Greenhouse' && in_array($id, [61, 69])) $area = 2;
                            elseif ($row[1] == 'Vegetative Room') $area = 2;
                            elseif ($row[1] == 'Clone Room') $area = 1;
                            elseif ($row[1] == 'Drying Room') $area = 4;
                            if (!isset($this->v["nwpcc"][$id]["PSLightTypes"])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"] = [];
                            }
                            if (!isset($this->v["nwpcc"][$id]["PSLightTypes"][$area])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area] = [];
                            }
                            $lgtInd = sizeof($this->v["nwpcc"][$id]["PSLightTypes"][$area]);
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd] = new RIIPSLightTypes;
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypAreaID 
                                = $this->v["nwpcc"][$id]["PSAreas"][$area]->PsAreaID;
                            if (isset($row[5]) && intVal($row[5]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypHours = intVal($row[5]);
                            }
                            if (in_array(trim($row[2]), ['Linear Fluorescent T5', 'Compact Fluorescent'])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 165;
                            } elseif (trim($row[2]) == 'High Pressure Sodium Double-Ended') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 168;
                            } elseif (trim($row[2]) == 'High Pressure Sodium') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 169;
                            } elseif (in_array(trim($row[2]), ['Metal Halide Ceramic', 'Metal Halide', 
                                'High Intensity Discharge'])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 171;
                            } elseif (trim($row[2]) == 'LED') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 203;
                            } elseif (trim($row[2]) == 'LED') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypLight = 203;
                            }
                            if (intVal($row[3]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypCount = $row[3];
                            }
                            if (intVal($row[4]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypWattage = $row[4];
                            }
                            if (intVal($row[5]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->PsLgTypHours = $row[5];
                            }
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->save();
                            if (intVal($row[8]) > 0) {
                                $this->v["nwpcc"][$id]["PowerScore"]->PsKWH += intVal($row[8]);
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->save();
                        }
                    }
                }
            }
            
            $hvacCool = $hvacDehum = [];
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-C.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(',', $l);
                        if ($i > 0 && isset($row[1])) $hvacCool[intVal($row[0])] = $row[1];
                    }
                }
            }
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-D.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(',', $l);
                        if ($i > 0 && isset($row[1])) $hvacDehum[intVal($row[0])] = $row[1];
                    }
                }
            }
            foreach ($this->v["nwpcc"] as $id => $recs) {
                $hvac = 247; // System A, traditional
                if (isset($hvacDehum[$id]) && $hvacDehum[$id] == 'Standalone (High-Performance)') {
                    $hvac = 248; // System B, high-performance
                } elseif (isset($hvacDehum[$id]) && $hvacDehum[$id] == 'HVAC Integrated') {
                    if (isset($hvacCool[$id]) && $hvacCool[$id] == 'Air Cooled Chiller') {
                        $hvac = 356; // System E
                    } else {
                        $hvac = 250; // System D
                    }
                }
                foreach ($this->v["nwpcc"][$id]["PSAreas"] as $area => $row) {
                    $this->v["nwpcc"][$id]["PSAreas"][$area]->PsAreaHvacType = $hvac;
                    $this->v["nwpcc"][$id]["PSAreas"][$area]->save();
                }
            }
            
        }
        $this->chkScoreFilters();
        $this->v["psFilter"] = '<a href="?showEmpty=1"><i class="fa fa-toggle-off" aria-hidden="true"></i> '
            . 'Show Empties</a>';
        $this->v["allscores"] = RIIPowerScore::where('PsName', 'LIKE', 'NWPCC%')
            ->where('PsEfficFacility', '>', 0)
            ->where('PsEfficProduction', '>', 0)
            ->where('PsEfficLighting', '>', 0)
            ->where('PsEfficHvac', '>', 0)
            ->orderBy($this->v['sort'][0], $this->v['sort'][1])
            ->get();
        if ($GLOBALS["SL"]->REQ->has('showEmpty')) {
            $this->v['allscores'] = RIIPowerScore::where('PsName', 'LIKE', 'NWPCC%')
                ->orderBy($this->v['sort'][0], $this->v['sort'][1])
                ->get();
            $this->v["psFilter"] = '<a href="?"><i class="fa fa-toggle-on" aria-hidden="true"></i> Hide Empties</a>';
        } elseif ($this->v['allscores']->isNotEmpty()) {
            foreach ($this->v['allscores'] as $i => $score) {
                $this->v['allscores'][$i]->update([ 'PsStatus' => 243 ]);
            }
        }
            
        $this->getAllscoresAvgFlds();
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view('vendor.cannabisscore.nodes.170-all-powerscores-excel', $this->v)->render();
            $exportFile = 'NWPCC Import Into PowerScore';
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        $this->v["nID"] = 808;
        $GLOBALS["SL"]->loadStates();
        $this->loadCupScoreIDs();
        $this->getAllscoresRefreshes();
        //$this->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->v)->render();
        $ret .= view('vendor.cannabisscore.nodes.170-all-powerscores', $this->v)->render();

        //$this->v["importResult"] .= '<pre>' . $xml . '</pre>';
        return $ret;
    }
    
    protected function runNwpccImportInitAreas($id)
    {
        if (!isset($this->v["nwpcc"][$id]["PSAreas"])) {
            $this->v["nwpcc"][$id]["PSAreas"] = [];
            $this->v["nwpcc"][$id]["PSAreas"][0] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][0]->PsAreaPSID = $this->v["nwpcc"][$id]["PowerScore"]->PsID;
            $this->v["nwpcc"][$id]["PSAreas"][0]->PsAreaType = 237;
            $this->v["nwpcc"][$id]["PSAreas"][0]->PsAreaHasStage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][0]->save();
            $this->v["nwpcc"][$id]["PSAreas"][1] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][1]->PsAreaPSID = $this->v["nwpcc"][$id]["PowerScore"]->PsID;
            $this->v["nwpcc"][$id]["PSAreas"][1]->PsAreaType = 160;
            $this->v["nwpcc"][$id]["PSAreas"][1]->PsAreaHasStage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][1]->save();
            $this->v["nwpcc"][$id]["PSAreas"][2] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][2]->PsAreaPSID = $this->v["nwpcc"][$id]["PowerScore"]->PsID;
            $this->v["nwpcc"][$id]["PSAreas"][2]->PsAreaType = 161;
            $this->v["nwpcc"][$id]["PSAreas"][2]->PsAreaHasStage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][2]->save();
            $this->v["nwpcc"][$id]["PSAreas"][3] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][3]->PsAreaPSID = $this->v["nwpcc"][$id]["PowerScore"]->PsID;
            $this->v["nwpcc"][$id]["PSAreas"][3]->PsAreaType = 162;
            $this->v["nwpcc"][$id]["PSAreas"][3]->PsAreaHasStage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][3]->save();
            $this->v["nwpcc"][$id]["PSAreas"][4] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][4]->PsAreaPSID = $this->v["nwpcc"][$id]["PowerScore"]->PsID;
            $this->v["nwpcc"][$id]["PSAreas"][4]->PsAreaType = 163;
            $this->v["nwpcc"][$id]["PSAreas"][4]->PsAreaHasStage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][4]->save();
        }
        return true;
    }
    
    protected function printReportBlds($nID)
    {
        $deet = '';
        $blds = $this->sessData->getBranchChildRows('PSAreasBlds');
        if (sizeof($blds) > 0) {
            foreach ($blds as $i => $bld) {
                $deet .= (($i > 0) ? ', ' : '') 
                    . $GLOBALS["SL"]->def->getVal('PowerScore Building Types', $bld->PsArBldType);
                if (isset($bld->PsArBldTypeOther) && trim($bld->PsArBldTypeOther) != '') {
                    $deet .= ': ' . $bld->PsArBldTypeOther;
                }
                $cnsts = $this->sessData->dataWhere('PSAreasConstr', 'PsArCnsBldID', $bld->getKey());
                if ($cnsts) {
                    $deet .= ' (';
                    foreach ($cnsts as $j => $cnst) {
                        $deet .= (($j > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->def->getVal('PowerScore Building Construction', $cnst->PsArCnsType);
                        if (isset($cnst->PsArCnsTypeOther) && trim($cnst->PsArCnsTypeOther) != '') {
                            $deet .= ': ' . $cnst->PsArCnsTypeOther;
                        }
                    }
                    $deet .= ')';
                }
            }
        }
        return [ 'Building Types', $deet ];
    }

    protected function printReportLgts($nID)
    {
        $deet = '';
        $lgts = $this->sessData->getBranchChildRows('PSLightTypes');
        if (sizeof($lgts) > 0) {
            foreach ($lgts as $i => $lgt) {
                $deet .= (($i > 0) ? ', ' : '') 
                    . $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $lgt->PsLgTypLight)
                    . ((isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0) ? ' ' . $lgt->PsLgTypCount . ' x ' 
                        . ((isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) 
                            ? $lgt->PsLgTypWattage . 'W' : '') : '');
                if ((isset($lgt->PsLgTypHours) && intVal($lgt->PsLgTypHours) > 0)
                    || (isset($lgt->PsLgTypMake) && trim($lgt->PsLgTypMake) != '')
                    || (isset($lgt->PsLgTypModel) && trim($lgt->PsLgTypModel) != '')) {
                    $deet .= '<div class="pL20">' . ((isset($lgt->PsLgTypHours) && intVal($lgt->PsLgTypHours) > 0) 
                        ? ' ' . $lgt->PsLgTypHours . ' hours' : '')
                        . ((isset($lgt->PsLgTypMake) && trim($lgt->PsLgTypMake) != '') ? ' ' . $lgt->PsLgTypMake : '')
                        . ((isset($lgt->PsLgTypModel) && trim($lgt->PsLgTypModel) != '') ? ' ' . $lgt->PsLgTypModel :'')
                        . '</div>';
                }
            }
        }
        return [ 'Light Types', $deet ];
    }
    
    public function chkCoreRecEmpty($coreID = -3, $coreRec = NULL)
    {
        if ($this->treeID == 1) {
            if ($coreID <= 0) $coreID = $this->coreID;
            if (!$coreRec && $coreID > 0) $coreRec = RIIPowerScore::find($coreID);
            if (!$coreRec) return false;
            if (!isset($coreRec->PsSubmissionProgress) || intVal($coreRec->PsSubmissionProgress) <= 0) return true;
            if (!isset($coreRec->PsZipCode) || trim($coreRec->PsZipCode) == '') return true;
        }
        return false;
    }
    
    public function filterAllPowerScoresPublic()
    {
        $eval = "whereIn('PsStatus', [" . (($this->v["fltCmpl"] == 0) ? (($this->v["isAdmin"]) ? "242, 243, 364" : 243)
            : $this->v["fltCmpl"]) . "])";
        $psidLghts = $psidHvac = $psidRenew = $psidCups = [];
        if ($this->v["fltLght"][1] > 0) {
            eval("\$chk = DB::table('RII_PSAreas')->join('RII_PSLightTypes', function (\$join) {
                    \$join->on('RII_PSAreas.PsAreaID', '=', 'RII_PSLightTypes.PsLgTypAreaID')
                        ->where('RII_PSLightTypes.PsLgTypLight', " . $this->v["fltLght"][1] . ");
                })"
                . (($this->v["fltLght"][0] > 0) ? "->where('PsAreaType', " . $this->v["fltLght"][0] . ")" : "")
                . "->select('RII_PSAreas.PsAreaPSID')
                ->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsAreaPSID, $psidLghts)) $psidLghts[] = $ps->PsAreaPSID;
                }
            }
        }
        if ($this->v["fltHvac"][1] > 0) {
            eval("\$chk = " . $GLOBALS["SL"]->modelPath('PSAreas') . "::where('PsAreaHvacType', " 
                . $this->v["fltHvac"][1] . ")" . (($this->v["fltHvac"][0] > 0) ? "->where('PsAreaType', " 
                . $this->v["fltHvac"][0] . ")" : "") . "->select('PsAreaPSID')->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsAreaPSID, $psidHvac)) $psidHvac[] = $ps->PsAreaPSID;
                }
            }
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            $chk = RIIPSRenewables::whereIn('PsRnwRenewable', $this->v["fltRenew"])
                ->select('PsRnwPSID')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsRnwPSID, $psidRenew)) $psidRenew[] = $ps->PsRnwPSID;
                }
            }
        }
        if ($this->v["fltCup"] > 0) {
            $chk = RIIPSForCup::where('PsCupCupID', $this->v["fltCup"])
                ->select('PsCupPSID')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsCupPSID, $psidCups)) $psidCups[] = $ps->PsCupPSID;
                }
            }
        }
        if ($this->v["fltState"] != '') {
            $GLOBALS["SL"]->loadStates();
            $eval .= "->whereIn('PsState', [ '" . implode("', '", 
                $GLOBALS["SL"]->states->getStateWhereIn($this->v["fltState"])) . "' ])";
        }
        if ($this->v["fltClimate"] != '') {
            if ($this->v["fltClimate"] == 'US') $eval .= "->where('PsAshrae', 'NOT LIKE', 'Canada')";
            else $eval .= "->where('PsAshrae', '" . $this->v["fltClimate"] . "')";
        }
        if ($this->v["fltFarm"] > 0) $eval .= "->where('PsCharacterize', " . $this->v["fltFarm"] . ")";
        if ($this->v["fltLght"][1] > 0) $eval .= "->whereIn('PsID', [" . implode(', ', $psidLghts) . "])";
        if ($this->v["fltHvac"][1] > 0) $eval .= "->whereIn('PsID', [" . implode(', ', $psidHvac) . "])";
        if ($this->v["fltPerp"] > 0) $eval .= "->where('PsHarvestBatch', 1)";
        if ($this->v["fltPump"] > 0) $eval .= "->where('PsHasWaterPump', 1)";
        if ($this->v["fltWtrh"] > 0) $eval .= "->where('PsHeatWater', 1)";
        if ($this->v["fltManu"] > 0) $eval .= "->where('PsControls', 1)";
        if ($this->v["fltAuto"] > 0) $eval .= "->where('PsControlsAuto', 1)";
        if ($this->v["fltVert"] > 0) $eval .= "->where('PsVerticalStack', 1)";
        if (sizeof($this->v["fltRenew"]) > 0) $eval .= "->whereIn('PsID', [" . implode(', ', $psidRenew) . "])";
        if ($this->v["fltCup"] > 0) $eval .= "->whereIn('PsID', [" . implode(', ', $psidCups) . "])";
        return $eval;
    }
    
    public function getAllPowerScoresPublic()
    {
        if ($GLOBALS["SL"]->REQ->has('random') && intVal($GLOBALS["SL"]->REQ->get('random')) == 1) {
            $randScore = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
                ->where('PsEfficFacility', '>', 0)
                ->where('PsEfficProduction', '>', 0)
                ->where('PsEfficLighting', '>', 0)
                ->where('PsEfficHvac', '>', 0)
                ->inRandomOrder()
                ->first();
            if ($randScore && isset($randScore->PsID)) {
                return '<script type="text/javascript"> setTimeout("window.location=\'/calculated/u-' 
                    . $randScore->PsID . '\'", 1); </script><br /><br /><center>'
                    . $GLOBALS["SL"]->sysOpts["spinner-code"] . '</center>';
            }
        }
        $this->chkScoreFilters();
        $eval = "\$this->v['allscores'] = " . $GLOBALS["SL"]->modelPath('PowerScore') . "::" 
            . $this->filterAllPowerScoresPublic() . "->where('PsEfficFacility', '>', 0)"
                . "->where('PsEfficProduction', '>', 0)->where('PsEfficLighting', '>', 0)"
                . "->where('PsEfficHvac', '>', 0)->orderBy(\$this->v['sort'][0], \$this->v['sort'][1])->get();";
        eval($eval);
        $this->getAllscoresAvgFlds();
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $this->v["showFarmNames"] = $GLOBALS["SL"]->REQ->has('farmNames');
            $innerTable = view('vendor.cannabisscore.nodes.170-all-powerscores-excel', $this->v)->render();
            $exportFile = 'Compare All';
            if ($this->v["fltFarm"] == 0) $exportFile .= ' Farms';
            else $exportFile .= ' ' . $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $this->v["fltFarm"]);
            if ($this->v["fltClimate"] != '') $exportFile .= ' Climate Zone ' . $this->v["fltClimate"];
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        $this->getAllscoresRefreshes();
        $this->v["nID"] = 170;
        $GLOBALS["SL"]->loadStates();
        $this->loadCupScoreIDs();
        
        $this->v["allranks"] = [];
        if ($this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $s) {
                $this->v["allranks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', '')
                    ->first();
            }
        }
        
        $this->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->v)->render();
        return view('vendor.cannabisscore.nodes.170-all-powerscores', $this->v)->render();
    }
    
    public function getAllscoresAvgFlds()
    {
        $this->v["avgFlds"] = ['PsEfficOverall', 'PsEfficFacility', 'PsEfficProduction', 'PsEfficLighting', 
            'PsEfficHvac', 'PsGrams', 'PsKWH', 'PsTotalSize'];
        $this->v["psAvg"] = new RIIPowerScore;
        foreach ($this->v["avgFlds"] as $fld) $this->v["psAvg"]->{ $fld } = 0;
        if ($this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $i => $ps) {
                foreach ($this->v["avgFlds"] as $fld) $this->v["psAvg"]->{ $fld } += (1*$ps->{ $fld });
            }
            foreach ($this->v["avgFlds"] as $fld) {
                $this->v["psAvg"]->{ $fld } = $this->v["psAvg"]->{ $fld }/sizeof($this->v["allscores"]);
            }
        }
        return $this->v["psAvg"];
    }
    
    public function getAllscoresRefreshes()
    {
        $this->v["reportExtras"] = '';
        if ($GLOBALS["SL"]->REQ->has('refresh')) {
            $this->v["reportExtras"] .= '<script type="text/javascript"> ';
            if ($this->v["allscores"] && sizeof($this->v["allscores"]) > 0) {
                foreach ($this->v["allscores"] as $i => $ps) {
                    $this->v["reportExtras"] .= 'setTimeout("document.getElementById(\'hidFrameID\').src=\''
                        . '/calculated/u-' . $ps->PsID . '?refresh=1\'", ' . ((sizeof($this->v["allscores"])+$i)*10000)
                        . '); setTimeout("document.getElementById(\'hidFrameID\').src=\'' . '/calculated/u-' 
                        . $ps->PsID . '?refresh=1&fltFarm=' . $ps->PsCharacterize . '\'", ' . ($i*10000) . '); ';
                }
            }
            $this->v["reportExtras"] .= ' </script>';
        }
        return true;
    }
    
    public function getAllPowerScoreAvgsPublic()
    {
        $this->v["allscores"] = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
            ->get();
        $this->calcAllPowerScoreAvgs();
        if ($this->v["isExcel"]) {
            $innerTable = view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'PowerScore_Averages-' . date("Y-m-d") . '.xls');
        }
        return view('vendor.cannabisscore.nodes.773-powerscore-avgs', $this->v)->render();
    }
    
    protected function getPowerScoreFinalReport()
    {
        $this->v["allscores"] = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
            ->get();
        $this->calcAllPowerScoreAvgs();
        //$GLOBALS["SL"]->x["needsCharts"] = true;
        //return view('vendor.cannabisscore.nodes.797-powerscore-report-tbls-ORIG', $this->v)->render();
        return view('vendor.cannabisscore.nodes.797-powerscore-report-tbls', $this->v)->render();
    }
    
    protected function calcAllPowerScoreAvgs()
    {
        $fltFarms = [ [144, 145, 143], ['Indoor', 'Greenhouse/Mixed', 'Outdoor'] ];
        $fltAreasGrow = [ [162, 161, 160, 237], ['Flowering', 'Vegetating', 'Cloning', 'Mothers'] ];
        $fltAreasAll = [ [162, 161, 160, 237, 163], ['Flowering', 'Vegetating', 'Cloning', 'Mothers', 'Drying'] ];
        $fltHvac = [ [247, 248, 249, 250, 356, 357, 251, 360], 
            ['System A', 'System B', 'System C', 'System D', 'System E', 'System F', 'Other System', 'None'] ];
        $this->v["statMisc"] = new SurvLoopStat;
        $this->v["statMisc"]->addFilt('farm', 'Farm Type', $fltFarms[0], $fltFarms[1]);  // a
        $this->v["statMisc"]->addDataType('g', 'Grams', 'g');                       // a
        $this->v["statMisc"]->addDataType('kWh', 'Facility Kilowatt Hours', 'kWh'); // b
        foreach ($this->v["psTechs"] as $fld => $name) $this->v["statMisc"]->addDataType($fld, $name);
        foreach ($this->v["psContact"] as $fld => $name) $this->v["statMisc"]->addDataType($fld, $name);
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $renew) {
            $this->v["statMisc"]->addDataType('rnw' . $renew->DefID, $renew->DefValue);
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore License Types') as $lic) {
            $this->v["statMisc"]->addDataType('lic' . $lic->DefID, $lic->DefValue);
        }
        $this->v["statMisc"]->loadMap();
        
        $this->v["statSqft"] = new SurvLoopStat;
        $this->v["statSqft"]->addFilt('farm', 'Farm Type', $fltFarms[0], $fltFarms[1]);          // a
        $this->v["statSqft"]->addFilt('area', 'Growth Stage', $fltAreasAll[0], $fltAreasAll[1]); // b
        $this->v["statSqft"]->addDataType('sqft', 'Square Feet', 'sqft');                        // a
        $this->v["statSqft"]->loadMap();
        
        $this->v["statLgts"] = new SurvLoopStat;
        $this->v["statLgts"]->addFilt('farm', 'Farm Type', $fltFarms[0], $fltFarms[1]);            // a
        $this->v["statLgts"]->addFilt('area', 'Growth Stage', $fltAreasGrow[0], $fltAreasGrow[1]); // b
        $lgts = [ [], [] ];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Light Types') as $l) {
            $lgts[0][] = $l->DefID;
            $lgts[1][] = str_replace('single-ended ', '1x', str_replace('double-ended ', '2x', $l->DefValue));
        }
        $lgts[0][] = 2;
        $lgts[1][] = 'No Lights';
        $this->v["statLgts"]->addFilt('lgty', 'Lights Types', $lgts[0], $lgts[1]);      // c
        $this->v["statLgts"]->addDataType('sqft', 'Square Feet', 'sqft');               // a
        $this->v["statLgts"]->addDataType('sun', 'Sunlight');                           // b
        $this->v["statLgts"]->addDataType('dep', 'Light deprivation');                  // c
        $this->v["statLgts"]->addDataType('arf', 'Artificial Light');                   // d
        $this->v["statLgts"]->addDataType('kWh', 'kWh', 'kWh');                         // e
        $this->v["statLgts"]->addDataType('lgtfx', 'Fixtures');                         // f
        $this->v["statLgts"]->loadMap();
        
        $this->v["statHvac"] = new SurvLoopStat;
        $this->v["statHvac"]->addFilt('farm', 'Farm Type', $fltFarms[0], $fltFarms[1]);            // a
        $this->v["statHvac"]->addFilt('area', 'Growth Stage', $fltAreasGrow[0], $fltAreasGrow[1]); // b
        $this->v["statHvac"]->addFilt('hvac', 'HVAC System', $fltHvac[0], $fltHvac[1]);            // c
        $this->v["statHvac"]->addDataType('kWh/sqft', 'kWh/sqft', 'kWh/sqft');                     // a
        $this->v["statHvac"]->addDataType('sqft', 'Square foot', 'sqft');                          // b
        $this->v["statHvac"]->loadMap();

        $this->v["statScor"] = new SurvLoopStat;
        $scoreRowLabs = [
            [ 'Facility Score',   'kWh/SqFt' ], 
            [ 'Production Score', 'g/kWh'    ], 
            [ 'Lighting Score',   'kWh/SqFt' ], 
            [ 'HVAC Score',       'kWh/SqFt' ]
            ];
        $this->v["statScor"]->addDataType('over', 'Overall Score', '%', $scoreRowLabs);
        $this->v["statScor"]->addTag('farm', 'Farm Type', $fltFarms[0], $fltFarms[1]);
        $this->v["statScor"]->addTag('cups', 'Competitions', $GLOBALS["SL"]->def->getSet('PowerScore Competitions'));
        $tmp = [];
        foreach ($lgts[1] as $i => $l) $tmp[] = 'Flower ' . $l;
        $this->v["statScor"]->addTag('flw-lgty', 'Flower Lights Types', $lgts[0], $tmp);
        $tmp = [];
        foreach ($lgts[1] as $i => $l) $tmp[] = 'Veg ' . $l;
        $this->v["statScor"]->addTag('veg-lgty', 'Veg Lights Types', $lgts[0], $tmp);
        $tmp = [];
        foreach ($lgts[1] as $i => $l) $tmp[] = 'Clone ' . $l;
        $this->v["statScor"]->addTag('cln-lgty', 'Clone Lights Types', $lgts[0], $tmp);
        $tmp = [];
        foreach ($fltHvac[1] as $i => $h) $tmp[] = 'Flower HVAC ' . $h;
        $this->v["statScor"]->addTag('flw-hvac', 'HVAC System', $fltHvac[0], $tmp);
        $tmp = [];
        foreach ($fltHvac[1] as $i => $h) $tmp[] = 'Veg HVAC ' . $h;
        $this->v["statScor"]->addTag('veg-hvac', 'HVAC System', $fltHvac[0], $tmp);
        $tmp = [];
        foreach ($fltHvac[1] as $i => $h) $tmp[] = 'Clone HVAC ' . $h;
        $this->v["statScor"]->addTag('cln-hvac', 'HVAC System', $fltHvac[0], $tmp);
        $tmp = [ [], [] ];
        foreach ($this->v["psTechs"] as $fld => $name) {
            $tmp[0][] = $fld;
            $tmp[1][] = $name;
        }
        $this->v["statScor"]->addTag('tech', 'Techniques', $tmp[0], $tmp[1]);
        $this->v["statScor"]->addTag('powr', 'Power Sources', 
            $GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources'));
        $this->v["statScor"]->loadMap();
        
        $this->v["statMore"] = [];
        $this->v["statMore"]["scrHID"] = [ 0, 0, 0, 0, 0, 0, 0 ]; // Overall, Fac, Prod, Lght, HVAC, count, Sqft/Fixture
        $this->v["statMore"]["scrLED"] = [ 0, 0, 0, 0, 0, 0 ];
        $this->v["statMore"]["scrLHR"] = [ 0, 0, 0, 0, 0 ];
        
        if ($this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $cnt => $ps) {
                
                $psRow = [$ps->PsEfficFacility, $ps->PsEfficProduction, $ps->PsEfficLighting, $ps->PsEfficHvac];
                $psTags = [];
                $psTags[] = ['farm', $ps->PsCharacterize];
                $chk = RIIPSForCup::where('PsCupPSID', $ps->PsID)
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $c) $psTags[] = ['cups', $c->PsCupCupID];
                }
                foreach ($this->v["psTechs"] as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) $psTags[] = ['tech', $fld];
                }
                
                $this->v["statMisc"]->resetRecFilt();
                $this->v["statMisc"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                $this->v["statMisc"]->addRecDat('g', $ps->PsGrams, $ps->PsID);
                $this->v["statMisc"]->addRecDat('kWh', $ps->PsKWH, $ps->PsID);
                foreach ($this->v["psTechs"] as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->PsID);
                    }
                }
                foreach ($this->v["psContact"] as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->PsID);
                    }
                }
                $chk = RIIPSRenewables::where('PsRnwPSID', $ps->PsID)->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $renew) {
                        $this->v["statMisc"]->addRecDat('rnw' . $renew->PsRnwRenewable, 1, $ps->PsID);
                        $psTags[] = ['powr', $renew->PsRnwRenewable];
                    }
                }
                $chk = RIIPSLicenses::where('PsLicPSID', $ps->PsID)->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $lic) {
                        $this->v["statMisc"]->addRecDat('lic' . $lic->PsLicLicense, 1, $ps->PsID);
                    }
                }
                
                $this->v["statSqft"]->resetRecFilt();
                $this->v["statSqft"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                
                $this->v["statLgts"]->resetRecFilt();
                $this->v["statLgts"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                
                $this->v["statHvac"]->resetRecFilt();
                $this->v["statHvac"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                
                $kwh = 0;
                $sqft = [ 0 => 0, 162 => 0, 161 => 0, 160 => 0, 237 => 0, 163 => 0 ];
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        if (isset($area->PsAreaType) && intVal($area->PsAreaHasStage) == 1) {
                            $aID = $area->PsAreaID;
                            $aTyp = '';
                            if ($area->PsAreaType == $this->v["areaTypes"]["Clone"]) $aTyp = 'cln';
                            elseif ($area->PsAreaType == $this->v["areaTypes"]["Veg"]) $aTyp = 'veg';
                            elseif ($area->PsAreaType == $this->v["areaTypes"]["Flower"]) $aTyp = 'flw';
                            if ($aTyp != '' && intVal($area->PsAreaHvacType) > 0) {
                                $psTags[] = [ $aTyp . '-hvac', $area->PsAreaHvacType ];
                            }
                            
                            $this->v["statSqft"]->addRecFilt('area', $area->PsAreaType, $ps->PsID);
                            $this->v["statSqft"]->addRecDat('sqft', intVal($area->PsAreaSize), $ps->PsID);
                            $this->v["statSqft"]->delRecFilt('area');
                            
                            $hvac = ((!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0) 
                                ? 360 : $area->PsAreaHvacType);
                            $this->v["statHvac"]->addRecFilt('area', $area->PsAreaType, $ps->PsID);
                            $this->v["statHvac"]->addRecFilt('hvac', $area->PsAreaHvacType, $aID);
                            $this->v["statHvac"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                            $this->v["statHvac"]->addRecDat('kWh/sqft', $this->getHvacEffic($hvac), $aID);
                            $this->v["statHvac"]->delRecFilt('hvac');
                            $this->v["statHvac"]->delRecFilt('area');
                            
                            $this->v["statLgts"]->addRecFilt('area', $area->PsAreaType, $aID);
                            $this->v["statLgts"]->addRecDat('sun', intVal($area->PsAreaLgtSun), $aID);
                            $this->v["statLgts"]->addRecDat('dep', intVal($area->PsAreaLgtDep), $aID);
                            $this->v["statLgts"]->addRecDat('arf', intVal($area->PsAreaLgtArtif), $aID);
                            $w = 0;
                            if (isset($area->PsAreaTotalLightWatts) && $area->PsAreaTotalLightWatts > 0) {
                                $w = ($area->PsAreaTotalLightWatts*$this->getTypeHours($area->PsAreaType)*365)/1000;
                            }
                            $this->v["statLgts"]->addRecDat('kWh', $w, $aID);
                            $foundLights = $foundHID = $foundLED = false;
                            $fixtureCnt = 0;
                            $lgts = RIIPSLightTypes::where('PsLgTypAreaID', $area->getKey())
                                ->get();
                            if ($lgts->isNotEmpty()) {
                                foreach ($lgts as $lgt) {
                                    if (isset($lgt->PsLgTypLight) && intVal($lgt->PsLgTypLight) > 0
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0) {
                                        $this->v["statLgts"]->addRecFilt('lgty', $lgt->PsLgTypLight, $aID);
                                        $this->v["statLgts"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                                        $this->v["statLgts"]->addRecDat('lgtfx', $lgt->PsLgTypCount, $lgt->PsLgTypID);
                                        $foundLights = true;
                                        if ($aTyp != '') $psTags[] = [ $aTyp . '-lgty', $lgt->PsLgTypLight ];
                                        if (in_array($lgt->PsLgTypLight, [168, 169, 170, 171])) $foundHID = true;
                                        if (in_array($lgt->PsLgTypLight, [165, 203])) $foundLED = true;
                                        $fixtureCnt += $lgt->PsLgTypCount;
                                    }
                                }
                            }
                            if (!$foundLights) {
                                $this->v["statLgts"]->addRecFilt('lgty', 2, $aID); // no lights status
                                $this->v["statLgts"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                                $this->v["statLgts"]->addRecDat('lgtfx', 0, $aID);
                                $this->v["statLgts"]->delRecFilt('lgty');
                                if ($aTyp != '') $psTags[] = [ $aTyp . '-lgty', 2 ];
                            }
                            $this->v["statLgts"]->delRecFilt('area');
                            
                            if ($aTyp == 'flw' && $foundHID) {
                                $this->v["statMore"]["scrHID"][0] += $ps->PsEfficOverall;
                                $this->v["statMore"]["scrHID"][1] += $ps->PsEfficFacility;
                                $this->v["statMore"]["scrHID"][2] += $ps->PsEfficProduction;
                                $this->v["statMore"]["scrHID"][3] += $ps->PsEfficLighting;
                                $this->v["statMore"]["scrHID"][4] += $ps->PsEfficHvac;
                                $this->v["statMore"]["scrHID"][5]++;
                                if ($fixtureCnt > 0) {
                                    $this->v["statMore"]["scrHID"][6] += $area->PsAreaSize/$fixtureCnt;
                                }
                            }
                            if ($aTyp == 'flw' && $foundLED) {
                                $this->v["statMore"]["scrLED"][0] += $ps->PsEfficOverall;
                                $this->v["statMore"]["scrLED"][1] += $ps->PsEfficFacility;
                                $this->v["statMore"]["scrLED"][2] += $ps->PsEfficProduction;
                                $this->v["statMore"]["scrLED"][3] += $ps->PsEfficLighting;
                                $this->v["statMore"]["scrLED"][4] += $ps->PsEfficHvac;
                                $this->v["statMore"]["scrLED"][5]++;
                            }
                        }
                    }
                }
                $this->v["statScor"]->addRecDat('over', $ps->PsEfficOverall, $ps->PsID, $psRow, $psTags);
            }
            
            $this->v["statMisc"]->resetRecFilt();
            $this->v["statMisc"]->calcStats(); 
            $this->v["statSqft"]->resetRecFilt();
            $this->v["statSqft"]->calcStats(); 
            $this->v["statLgts"]->resetRecFilt();
            $this->v["statLgts"]->calcStats();
            $this->v["statHvac"]->resetRecFilt();
            $this->v["statHvac"]->calcStats();
            $this->v["statScor"]->calcStats();
            $this->v["statLgts"]->tblFltRowsCalcDiv('area', 'farm', 'kWh', 'sqft', 'kWh/sqft');
            //$this->v["statLgts"]->calcStats();
            
            $this->v["statMore"]["hvacTotPrc"] = $this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][3]
                                                /$this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][0];
            $this->v["statMore"]["hvacTotKwh"] = $this->v["statMore"]["hvacTotPrc"]
                                                *$this->v["statMisc"]->getDatTot('kWh');
            $this->v["statMore"]["othrTotKwh"] = $this->v["statMisc"]->getDatTot('kWh')
                                                -$this->v["statLgts"]->getDatTot('kWh')
                                                -$this->v["statMore"]["hvacTotKwh"];
            $this->v["statMore"]["othrTotPrc"] = $this->v["statMore"]["othrTotKwh"]
                                                /$this->v["statMisc"]->getDatTot('kWh');
            for ($i = 0; $i < 5; $i++) {
                foreach (["scrLED", "scrHID"] as $lyt) {
                    $this->v["statMore"][$lyt][$i] = $this->v["statMore"][$lyt][$i]/$this->v["statMore"][$lyt][5];
                }
                if (in_array($i, [0, 2])) { // higher is better
                    $this->v["statMore"]["scrLHR"][$i] = ($this->v["statMore"]["scrLED"][$i]
                        -$this->v["statMore"]["scrHID"][$i])/$this->v["statMore"]["scrHID"][$i];
                } else { // lower is better
                    $this->v["statMore"]["scrLHR"][$i] = ($this->v["statMore"]["scrHID"][$i]
                        -$this->v["statMore"]["scrLED"][$i])/$this->v["statMore"]["scrHID"][$i];
                }
            }
            $this->v["statMore"]["flwrPercHID"] = ($this->v["statLgts"]->getDatCnt('b162-c168')
                +$this->v["statLgts"]->getDatCnt('b162-c169')+$this->v["statLgts"]->getDatCnt('b162-c170')
                +$this->v["statLgts"]->getDatCnt('b162-c171'))/$this->v["statLgts"]->getDatCnt('b162');
            $this->v["statMore"]["sqftFxtHID"] = $this->v["statMore"]["scrHID"][6]/$this->v["statMore"]["scrHID"][5];
            
        }
        return true;
    }
    
    
    protected function allAvgsEmpty()
    {
        return [ "tot" => 0, "ovr" => [ 0, 0 ], 
            "fac" => [ 0, 0 ], "pro" => [ 0, 0 ], "lgt" => [ 0, 0 ], "hvc" => [ 0, 0 ],
            "area" => [ 162 => [ 0, 0 ], 161 => [ 0, 0 ], 160 => [ 0, 0 ], 237 => [ 0, 0 ], 163 => [ 0, 0 ] ]
            ];
    }
    
    protected function allKeyDataAreasEmpty()
    {
        $ret = [];
        foreach (['areas', 'sqfts', 'sqratio', 'lgtkWh', 'kWh', 'g'] as $k) {
            $ret[$k] = [ 0 => 0, 162 => 0, 161 => 0, 160 => 0, 237 => 0, 163 => 0 ];
        }
        $ret2 = [];
        foreach ([ 0, 144, 145, 143 ] as $k) $ret2[$k] = $ret;
        return $ret2;
    }
    
    protected function allTechEmpty()
    {
        $ret = [ 141 => 0, 142 => 0 ]; // medical, recreational
        foreach ($this->v["psTechs"] as $fld => $name) $ret[$fld] = 0;
        foreach ($this->v["psContact"] as $fld => $name) $ret[$fld] = 0;
        
        return $ret;
    }
    
    protected function reportPowerScoreFeedback()
    {
        $this->v["feedback"] = [];
        $chk = DB::table('RII_PsFeedback')
            ->join('RII_PowerScore', 'RII_PsFeedback.PsfPsID', '=', 'RII_PowerScore.PsID')
            ->orderBy('RII_PsFeedback.created_at', 'desc')
            ->select('RII_PowerScore.PsName', 'RII_PowerScore.PsEfficOverall', 'RII_PsFeedback.*')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $row) {
                if ((isset($row->PsfFeedback1) && trim($row->PsfFeedback1) != '')
                    || (isset($row->PsfFeedback2) && trim($row->PsfFeedback2) != '')
                    || (isset($row->PsfFeedback3) && trim($row->PsfFeedback3) != '')
                    || (isset($row->PsfFeedback4) && trim($row->PsfFeedback4) != '')
                    || (isset($row->PsfFeedback5) && trim($row->PsfFeedback5) != '')) {
                    $this->v["feedback"][] = $row;
                }
            }
        }
        return view('vendor.cannabisscore.nodes.777-powerscore-feedback', $this->v)->render();
    }
    
    protected function checkBadRecs()
    {
        $this->v["goodies"] = [776, 788, 878, 881, 884, 899, 901, 914, 915, 920, 922, 929, 930, 932, 934, 947, 958, 993,
            1140, 1235, 1364, 1365, 1390, 1396, 1427, 1449, 1477, 1492, 1498, 1503, 1505, 1506, 1605, 1634, 1696, 1714,
            1771, 1829, 1840, 1869, 1879, 1894, 1917, 1919, 1955, 1959, 1978, 1986, 1993, 2033, 2040, 2056, 2170];
        $this->v["allscores"] = $added = [];
        $chk = DB::table('RII_PowerScore')
            ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
            ->where('RII_PSRankings.PsRnkFilters', '')
            ->where('RII_PowerScore.PsStatus', 'LIKE', $this->v["defCmplt"])
            ->orderBy('RII_PowerScore.PsID', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ps) {
                if (!in_array($ps->PsID, $added) && !in_array($ps->PsID, $this->v["goodies"])) {
                    $this->v["allscores"][] = $ps;
                    $added[] = $ps->PsID;
                }
            }
        }
        return view('vendor.cannabisscore.nodes.775-powerscore-publishing', $this->v)->render();
    }
    
    
    protected function adminSearchResults()
    {
        $searches = [];
        if ($GLOBALS["SL"]->REQ->has('s') && trim($GLOBALS["SL"]->REQ->get('s')) != '') {
            $searches = $GLOBALS["SL"]->parseSearchWords($GLOBALS["SL"]->REQ->get('s'));
        }
        if (sizeof($searches) > 0) {
            foreach ($searches as $s) {
                $rows = DB::table('RII_PowerScore')
                    ->join('RII_PSRankings', function ($join) {
                        $join->on('RII_PowerScore.PsID', '=', 'RII_PSRankings.PsRnkPSID')
                            ->where('RII_PSRankings.PsRnkFilters', '');
                    })
                    ->where('RII_PowerScore.PsName', 'LIKE', '%' . $s . '%')
                    ->orWhere('RII_PowerScore.PsZipCode', 'LIKE', '%' . $s . '%')
                    ->orWhere('RII_PowerScore.PsCounty', 'LIKE', '%' . $s . '%')
                    ->orWhere('RII_PowerScore.PsEmail', 'LIKE', '%' . $s . '%')
                    ->orderBy('RII_PowerScore.PsName', 'asc')
                    ->get();
                $GLOBALS["SL"]->addSrchResults('name', $rows, 'PsID');
            }
        }
        $GLOBALS["SL"]->getDumpSrchResultIDs($searches, 1);
        if (sizeof($searches) > 0) {
            foreach ($searches as $s) {
                $rows = DB::table('RII_PowerScore')
                    ->join('RII_PSRankings', function ($join) {
                        $join->on('RII_PowerScore.PsID', '=', 'RII_PSRankings.PsRnkPSID')
                            ->where('RII_PSRankings.PsRnkFilters', '');
                    })
                    ->whereIn('RII_PowerScore.PsID', $GLOBALS["SL"]->x["srchResDump"])
                    ->orderBy('RII_PowerScore.PsName', 'asc')
                    ->get();
                $GLOBALS["SL"]->addSrchResults('dump', $rows, 'PsID');
            }
        }
        return view('vendor.cannabisscore.nodes.786-admin-search-results', $this->v)->render();
    }
    
    protected function getTableRecLabelCustom($tbl, $rec = [], $ind = -3)
    {
        if ($tbl == 'PowerScore' && isset($rec->PsName)) {
            return $rec->PsName;
        }
        return '';
    }
    
    protected function slimLgtType($defValue = '')
    {
        return str_replace('double-ended ', '2x', str_replace('single-ended ', '1x', $defValue));
    }
    
    
    protected function tmpDebug($str = '')
    {
        $tmp = ' - tmpDebug - ' . $str;
        $chk = RIIPSAreas::where('PsAreaPSID', 169)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $row) $tmp .= ', ' . $row->getKey();
        }
        echo $tmp . '<br />';
        return true;
    }
    
}