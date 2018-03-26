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
use App\Models\RIIPSMonthly;
use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPsFeedback;
use App\Models\SLZipAshrae;
use App\Models\SLZips;
use App\Models\SLSess;
use App\Models\SLNodeSavesPage;
use App\Models\SLUploads;

use CannabisScore\Controllers\CannabisScoreReport;

use SurvLoop\Controllers\SurvFormTree;

class CannabisScore extends SurvFormTree
{
    
    public $classExtension         = 'CannabisScore';
    public $treeID                 = 1;
    
    // Shortcuts...
    public $farmTypes              = [];
    
    
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function initExtra(Request $request)
    {
        // Establishing Main Navigation Organization, with Node ID# and Section Titles
        $this->majorSections = [];
        
        // Shortcuts...
        $this->farmTypes = [
            'Indoor'     => $GLOBALS["SL"]->getDefID('PowerScore Farm Types', 'Indoor'),
            'Outdoor'    => $GLOBALS["SL"]->getDefID('PowerScore Farm Types', 'Outdoor'),
            'Greenhouse' => $GLOBALS["SL"]->getDefID('PowerScore Farm Types', 'Greenhouse'),
            'Multiple'   => $GLOBALS["SL"]->getDefID('PowerScore Farm Types', 'Multiple Environments')
            ];
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->getDefID('PowerScore Growth Stages', 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->getDefID('PowerScore Growth Stages', 'Clone Plants'),
            'Veg'    => $GLOBALS["SL"]->getDefID('PowerScore Growth Stages', 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->getDefID('PowerScore Growth Stages', 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->getDefID('PowerScore Growth Stages', 'Drying/Curing')
            ];
        $this->v["defCmplt"] = $GLOBALS["SL"]->getDefID('PowerScore Status', 'Complete');
        
        //$GLOBALS["SL"]->addTopNavItem('Calculate PowerScore', '/start/calculator');
        return true;
    }
    
    public function getAllPublicCoreIDs($coreTbl = '')
    {
        if (trim($coreTbl) == '') $coreTbl = $GLOBALS["SL"]->coreTbl;
        $this->allPublicCoreIDs = $list = [];
        if ($coreTbl == 'PowerScore') {
            $list = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if ($list && sizeof($list) > 0) {
            foreach ($list as $l) $this->allPublicCoreIDs[] = $l->getKey();
        }
        return $this->allPublicCoreIDs;
    }
    
    
        
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function loadExtra()
    {
        if (!session()->has('PowerScoreChecks') || $GLOBALS["SL"]->REQ->has('refresh')) {
            
            $chk = RIIPowerScore::where('PsSubmissionProgress', 'LIKE', '147') // redirection page
                ->where('PsStatus', '=', $GLOBALS["SL"]->getDefID('PowerScore Status', 'Incomplete'))
                ->update([ 'PsStatus' => $this->v["defCmplt"] ]);
            
            $chk = RIIPowerScore::where('PsZipCode', 'NOT LIKE', '')
                ->whereNull('PsAshrae')
                ->get();
            if ($chk && sizeof($chk) > 0) {
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
            if ($chk && sizeof($chk) > 0) {
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
            if ($chk && sizeof($chk) > 0) {
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
            if ($chkSess && sizeof($chkSess) > 0) {
                foreach ($chkSess as $j => $sess) {
                    $saveChk = SLNodeSavesPage::where('PageSaveSession', $sess->getKey())
                        ->get();
                    $saveTot = (($saveChk) ? sizeof($saveChk) : 0);
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
                            $ut->PsUtType = $GLOBALS["SL"]->getDefID('Utility Company Type', 'Non-Investor Owned Utilities');
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
            session()->put('PowerScoreChecks', true);
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
                = $GLOBALS["SL"]->getDefID('PowerScore Submission Type', 'Past');
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        if ($GLOBALS["SL"]->REQ->has('cups') && trim($GLOBALS["SL"]->REQ->get('cups')) != '') {
            $cupsIn = $GLOBALS["SL"]->mexplode(',', urldecode($GLOBALS["SL"]->REQ->get('cups')));
            $cupList = $GLOBALS["SL"]->getDefSet('PowerScore Competitions');
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
        if ($nID == 46) {
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
            $this->getAllReportCalcs();
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
        } elseif (in_array($nID, [714, 525, 718, 720, 722, 724, 734, 736, 738])) {
            $ret .= $this->printAdmReportList($nID);
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
        } elseif ($nID == 773) {
            $ret .= $this->getAllPowerScoreAvgsPublic();
        } elseif ($nID == 775) {
            $ret .= $this->checkBadRecs();
        } elseif ($nID == 777) {
            $ret .= $this->reportPowerScoreFeedback();
        }
        return $ret;
    }
    
    protected function customResponses($nID, $curr)
    {
        if ($nID == 57) {
            $curr->clearResponses();
            if (isset($this->sessData->dataSets["PowerScore"]) 
                && isset($this->sessData->dataSets["PowerScore"][0]->PsZipCode)) {
                $utIDs = RIIPSUtiliZips::where('PsUtZpZipCode', $this->sessData->dataSets["PowerScore"][0]->PsZipCode)
                    ->get();
                if ($utIDs && sizeof($utIDs) > 0) {
                    $ids = [];
                    foreach ($utIDs as $u) $ids[] = $u->PsUtZpUtilID;
                    $uts = RIIPSUtilities::whereIn('PsUtID', $ids)
                        ->get(); // will be upgrade to check for farm's zip code
                    if ($uts && sizeof($uts) > 0) {
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
        if (sizeof($tmpSubTier) == 0) $tmpSubTier = $this->loadNodeSubTier($nID);
        list($tbl, $fld) = $this->allNodes[$nID]->getTblFld();
        
        if ($nID == 47) {
            if ($GLOBALS["SL"]->REQ->has('n47fld') && trim($GLOBALS["SL"]->REQ->get('n47fld')) != '') {
                $this->sessData->updateZipInfo($GLOBALS["SL"]->REQ->get('n47fld'), 
                    'PowerScore', 'PsState', 'PsCounty', 'PsAshrae');
            }
        } elseif ($nID == 70) { // dump monthly energy notes
            $currMonth = (($GLOBALS["SL"]->REQ->has('elecMonth')) ? intVal($GLOBALS["SL"]->REQ->elecMonth) : 1);
            $powerMonths = $this->sortMonths();
            foreach ($powerMonths as $i => $row) {
                $row->PsMonthMonth = $currMonth;
                $f = 'elec' . (1+$i);
                $row->PsMonthKWH1  = (($GLOBALS["SL"]->REQ->has($f . 'a')) ? $GLOBALS["SL"]->REQ->get($f . 'a') : null);
                $row->PsMonthNotes = (($GLOBALS["SL"]->REQ->has($f . 'd')) ? $GLOBALS["SL"]->REQ->get($f . 'd') : null);
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
            if (sizeof($area) > 0 && (!isset($area->PsAreaHvacType) || intVal($area->PsAreaHvacType) == 0)
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
            $watts = [];
            if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
                foreach ($this->sessData->dataSets["PSAreas"] as $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
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
//echo '<pre>'; print_r($watts); echo '</pre>';
                if (isset($watts["Mother"]) && intVal($watts["Mother"]) > 0 
                    && isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)) {
                    if ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->getDefID('PowerScore Mother Location', 'With Clones')) {
                        $watts["Clone"] += $watts["Mother"];
                    } elseif ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->getDefID('PowerScore Mother Location', 'In Veg Room')) {
                        $watts["Veg"] += $watts["Mother"];
                    }
                }
                foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
                            $this->sessData->dataSets["PSAreas"][$i]->PsAreaTotalLightWatts = $watts[$typ];
                            $this->sessData->dataSets["PSAreas"][$i]->PsAreaLightingEffic = 0;
                            if ($watts[$typ] > 0 && isset($area->PsAreaSize) && intVal($area->PsAreaSize) > 0) {
                                $hours = $this->getTypeHours($typ);
                                $this->sessData->dataSets["PSAreas"][$i]->PsAreaLightingEffic
                                    = ($watts[$typ]*$hours)/$area->PsAreaSize;
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
        if (!$this->sessData->dataSets["PowerScore"][0]->PsEfficFacility 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility == 0) {
            $noprints[] = 'facility';
        }
        if (!$this->sessData->dataSets["PowerScore"][0]->PsEfficProduction 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction == 0) {
            $noprints[] = 'production';
        }
        if (!$this->sessData->dataSets["PowerScore"][0]->PsEfficHvac 
            || $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac == 0) {
            $noprints[] = 'HVAC';
        }
        if (!$this->sessData->dataSets["PowerScore"][0]->PsEfficLighting 
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
    
    protected function chkScoreFilters()
    {
        $this->v["eff"] = (($GLOBALS["SL"]->REQ->has('eff')) 
            ? trim($GLOBALS["SL"]->REQ->get('eff')) : 'Overall');
        $this->v["filtClimate"] = (($GLOBALS["SL"]->REQ->has('climate')) 
            ? intVal($GLOBALS["SL"]->REQ->get('climate')) : 0);
        $this->v["filtFarm"] = (($GLOBALS["SL"]->REQ->has('farm')) 
            ? intVal($GLOBALS["SL"]->REQ->get('farm')) : 0);
        $this->v["psid"] = (($GLOBALS["SL"]->REQ->has('ps')) 
            ? intVal($GLOBALS["SL"]->REQ->get('ps')) : 0);
        $this->v["powerscore"] = RIIPowerScore::find($this->v["psid"]);
        $this->v["fltFarm"] = (($GLOBALS["SL"]->REQ->has('fltFarm')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltFarm')) : 0);
        $this->v["fltClimate"] = (($GLOBALS["SL"]->REQ->has('fltClimate')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltClimate')) : '');
        $this->v["sort"] = [ 'PsEfficOverall', 'desc' ];
        if ($GLOBALS["SL"]->REQ->has('srt') && trim($GLOBALS["SL"]->REQ->get('srt')) != '') {
            $this->v["sort"][0] = $GLOBALS["SL"]->REQ->get('srt');
            if ($GLOBALS["SL"]->REQ->has('srta') && in_array(trim($GLOBALS["SL"]->REQ->get('srta')), ['asc', 'desc'])) {
                $this->v["sort"][1] = $GLOBALS["SL"]->REQ->get('srta');
            }
        }
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
            $updateCutoff = mktime(date("H"), date("i"), date("s"), date("n"), date("j")-3, date("Y"));
            //$coreRec
            $this->v["ranksCache"] = RIIPSRankings::where('PsRnkPSID', $this->v["powerscore"]->PsID)
                ->where('PsRnkFilterByClimate', $this->v["filtClimate"])
                ->where('PsRnkFarmType', $this->v["filtFarm"])
                ->first();
            if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->PsRnkID)) {
                $this->v["ranksCache"] = new RIIPSRankings;
                $this->v["ranksCache"]->PsRnkPSID            = $this->v["powerscore"]->PsID;
                $this->v["ranksCache"]->PsRnkFilterByClimate = $this->v["filtClimate"];
                $this->v["ranksCache"]->PsRnkFarmType        = $this->v["filtFarm"];
            } elseif (strtotime($this->v["ranksCache"]->updated_at) < $updateCutoff || $request->has('refresh')) {
                $this->v["ranksCache"]->PsRnkOverall = $this->v["ranksCache"]->PsRnkFacility 
                    = $this->v["ranksCache"]->PsRnkProduction = $this->v["ranksCache"]->PsRnkHVAC 
                    = $this->v["ranksCache"]->PsRnkLighting = 0;
            }
            if (!isset($this->v["ranksCache"]->PsRnkOverall) || $this->v["ranksCache"]->PsRnkOverall == 0
                || $this->v["ranksCache"]->PsRnkOverall === null) {
                $this->v["ranksCache"]->PsRnkOverall = $overAvgCnt = 0;
                $this->v["efficPercs"] = [ // current PowerScore is "better" than X others, and "worse" than Y others
                    "Facility"   => [ "better" => 0, "worse" => 0, "perc" => 0 ], 
                    "Production" => [ "better" => 0, "worse" => 0, "perc" => 0 ], 
                    "HVAC"       => [ "better" => 0, "worse" => 0, "perc" => 0 ], 
                    "Lighting"   => [ "better" => 0, "worse" => 0, "perc" => 0 ]
                    ];
                $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') 
                    . "::where('PsStatus', \$this->v['defCmplt'])->where('PsTimeType', "
                    . $GLOBALS["SL"]->getDefID('PowerScore Submission Type', 'Past') . ")->where('PsID', 'NOT LIKE', "
                    . $this->coreID . ")";
                if ($this->v["filtClimate"] == 1) $eval .= "->where('PsAshrae', \$this->v['powerscore']->PsAshrae)";
                if ($this->v["filtFarm"] > 0) $eval .= "->where('PsCharacterize', \$this->v['filtFarm'])";
                $eval .= "->select('PsID', 'PsEfficOverall', 'PsEfficFacility', 'PsEfficProduction', 'PsEfficHvac', "
                    . "'PsEfficLighting')->get();";
                eval($eval);
                $psRnkTotCnt = [];
                if ($allscores && sizeof($allscores) > 0) {
                    foreach ($allscores as $s) {
                        if (isset($this->v["powerscore"]->PsEfficFacility) 
                            && $this->v["powerscore"]->PsEfficFacility > 0
                            && isset($s->PsEfficFacility) && $s->PsEfficFacility > 0) { // kWh/sqft
                            $sort = (($this->v["powerscore"]->PsEfficFacility <= $s->PsEfficFacility) 
                                ? "better" : "worse");
                            $this->v["efficPercs"]["Facility"][$sort]++;
                            if (!in_array($s->PsID, $psRnkTotCnt)) $psRnkTotCnt[] = $s->PsID;
                        }
                        if (isset($this->v["powerscore"]->PsEfficProduction) 
                            && $this->v["powerscore"]->PsEfficProduction > 0
                            && isset($s->PsEfficProduction) && $s->PsEfficProduction > 0) { // grams/kWh
                            $sort = (($this->v["powerscore"]->PsEfficProduction >= $s->PsEfficProduction) 
                                ? "better" : "worse");
                            $this->v["efficPercs"]["Production"][$sort]++;
                            if (!in_array($s->PsID, $psRnkTotCnt)) $psRnkTotCnt[] = $s->PsID;
                        }
                        if (isset($this->v["powerscore"]->PsEfficHvac) 
                            && $this->v["powerscore"]->PsEfficHvac > 0
                            && isset($s->PsEfficHvac) && $s->PsEfficHvac > 0) { // kWh/sqft
                            $sort = (($this->v["powerscore"]->PsEfficHvac <= $s->PsEfficHvac) 
                                ? "better" : "worse");
                            $this->v["efficPercs"]["HVAC"][$sort]++;
                            if (!in_array($s->PsID, $psRnkTotCnt)) $psRnkTotCnt[] = $s->PsID;
                        }
                        if (isset($this->v["powerscore"]->PsEfficLighting) 
                            && $this->v["powerscore"]->PsEfficLighting > 0
                            && isset($s->PsEfficLighting) && $s->PsEfficLighting > 0) { // kWh/sqft
                            $sort = (($this->v["powerscore"]->PsEfficLighting <= $s->PsEfficLighting) 
                                ? "better" : "worse");
                            $this->v["efficPercs"]["Lighting"][$sort]++;
                            if (!in_array($s->PsID, $psRnkTotCnt)) $psRnkTotCnt[] = $s->PsID;
                        }
                    }
                }
                $this->v["ranksCache"]->PsRnkTotCnt = sizeof($psRnkTotCnt);
                foreach ($this->v["efficPercs"] as $type => $percs) {
                    $this->v["ranksCache"]->{ 'PsRnk' . $type . 'Cnt' } = 1+$percs["better"]+$percs["worse"];
                    $this->v["efficPercs"][$type]["perc"] 
                        = 100*($percs["better"]/$this->v["ranksCache"]->{ 'PsRnk' . $type . 'Cnt' });
                    //if (isset($this->v["powerscore"]->{ 'PsEffic' . $type }) && (1*$this->v["powerscore"]->{ 'PsEffic' . $type }) > 0) {
                    $this->v["ranksCache"]->{ 'PsRnk' . $type } = $this->v["efficPercs"][$type]["perc"];
                    if ($this->v["efficPercs"][$type]["perc"] > 0) {
                        $this->v["ranksCache"]->PsRnkOverall += $this->v["efficPercs"][$type]["perc"];
                        $overAvgCnt++;
                    }
                }
                if ($overAvgCnt > 0 && $this->v["ranksCache"]->PsRnkOverall > 0) {
                    $this->v["ranksCache"]->PsRnkOverall = $this->v["ranksCache"]->PsRnkOverall/$overAvgCnt;
                }
                if ($this->v["filtClimate"] == 0 && $this->v["filtFarm"] == 0) {
                    $this->v["powerscore"]->PsEfficOverall = $this->v["ranksCache"]->PsRnkOverall;
                    $this->v["powerscore"]->save();
                }
            }
            $this->v["ranksCache"]->save();
            $this->v["currGuage"]    = round($this->v["ranksCache"]->{ 'PsRnk' . $this->v["eff"] });
            $this->v["currGuageTot"] = $this->v["ranksCache"]->{ 'PsRnk' . $this->v["eff"] . 'Cnt' };
            if ($this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkProductionCnt
                && $this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkHVACCnt
                && $this->v["ranksCache"]->PsRnkFacilityCnt == $this->v["ranksCache"]->PsRnkLightingCnt) {
                $this->v["currGuageTot"] = -3;
            }
            return view('vendor.cannabisscore.nodes.490-report-calculations-ajax-graphs', $this->v)->render();
        }
        return '';
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
        $this->v["printEfficLgt"] = [];
        if (isset($this->sessData->dataSets["PSAreas"])) {
            foreach ($this->sessData->dataSets["PSAreas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->PsAreaType == $defID && $typ != 'Dry') {
                        if (isset($area->PsAreaLightingEffic) && $area->PsAreaLightingEffic > 0) {
                            //  (Clone watts x # of lights x 24 hrs) / Clone sq ft)
                            $lightBreakdown = '';
                            if (isset($this->sessData->dataSets["PSLightTypes"]) 
                                && sizeof($this->sessData->dataSets["PSLightTypes"]) > 0) {
                                foreach ($this->sessData->dataSets["PSLightTypes"] as $lgt) {
                                    if ($lgt->PsLgTypAreaID == $area->getKey() 
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0 
                                        && isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) {
                                        if ($lightBreakdown != '') $lightBreakdown .= ' + ';
                                        $lightBreakdown .= '<nobr>( ' . number_format($lgt->PsLgTypCount) 
                                            . ' fixtures x ' . number_format($lgt->PsLgTypWattage) . ' watts )</nobr>';
                                    }
                                }
                            }
                            if (strpos($lightBreakdown, '+') === false) {
                                $lightBreakdown = str_replace('(', '', str_replace(')', '', $lightBreakdown));
                            }
                            $this->v["printEfficLgt"][] = [
                                "eng" => '( (' . $typ . ' ' 
                                    . number_format($this->getAreaFld($typ, 'PsAreaTotalLightWatts')) 
                                    . ' watts x ' . $this->getTypeHours($typ) . ' hours) / '
                                    . number_format($this->getAreaFld($typ, 'PsAreaSize')) . ' sq ft )',
                                "lgt" => $typ . ': ' . $lightBreakdown,
                                "num" => $typ . ' ' . $GLOBALS["SL"]->sigFigs($area->PsAreaLightingEffic, 3) 
                                    . ' Wh / sq ft'
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
                == $GLOBALS["SL"]->getDefID('PowerScore Privacy Options', 'Private')) {
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
            case 78: return $GLOBALS["SL"]->getDefID('PowerScore Renewables', 'Solar PV');
            case 80: return $GLOBALS["SL"]->getDefID('PowerScore Renewables', 'Wind');
            case 61: return $GLOBALS["SL"]->getDefID('PowerScore Renewables', 'Biomass');
            case 60: return $GLOBALS["SL"]->getDefID('PowerScore Renewables', 'Geothermal');
            case 81: return $GLOBALS["SL"]->getDefID('PowerScore Renewables', 'Pelton Wheel');
        }
        return -3;
    }
    
    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        $dynamos = [
            '[{ PowerScore }]',
            '[{ PowerScore kWh/gram }]',
            '[{ PowerScore Percentile }]',
            '[{ PowerScore Total Submissions }]',
            '[{ Zip Code }]'
        ];
        foreach ($dynamos as $dy) {
            if (strpos($emailBody, $dy) !== false) {
                $swap = $dy;
                $dyCore = str_replace('[{ ', '', str_replace(' }]', '', $dy));
                switch ($dy) {
                    /*
                    case '[{ PowerScore }]': 
                        $swap = $GLOBALS["SL"]->sigFigs($this->sessData->dataSets["PowerScoreBasic"][0]->PsbScore, 3);
                        break;
                    case '[{ PowerScore kWh/gram }]': // from lbs/kWh to kWh/g
                        $swap = $this->cnvrtLbs2Grm($this->sessData->dataSets["PowerScoreBasic"][0]->PsbScore);
                        $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        break;
                    case '[{ PowerScore Percentile }]': 
                        $swap = $GLOBALS["SL"]->sigFigs($this->sessData->dataSets["PowerScoreBasic"][0]->PsbPercentile, 
                            1) . '%';
                        break;
                    case '[{ PowerScore Total Submissions }]': 
                        $chk = RIIPowerScoreBasic::where('PsbEmail', 'NOT LIKE', '')
                            ->get();
                        $swap = (($chk) ? sizeof($chk) : 0);
                        break;
                    case '[{ Zip Code }]': 
                        if (isset($this->sessData->dataSets["PowerScoreBasic"]) 
                            && isset($this->sessData->dataSets["PowerScoreBasic"][0]->PsbZipCode)) {
                            $swap = $this->sessData->dataSets["PowerScoreBasic"][0]->PsbZipCode;
                        }
                        break;
                    */
                }
                $emailBody = str_replace($dy, $swap, $emailBody);
            }
        }
        return $emailBody;
    }
    
    
    public function loadUtils()
    {
        $this->v["powerUtils"] = $this->v["powerUtilsInd"] = [];
        $chk = RIIPSUtilities::orderBy('PsUtName', 'asc')->get();
        if ($chk && sizeof($chk) > 0) {
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
        if ($chk && sizeof($chk) > 0) {
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
                if ($chk && sizeof($chk) > 0) {
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
    
    protected function recordIsIncomplete($coreTbl, $coreID, $coreRec = [])
    {
        if ($coreID > 0) {
            if (!isset($coreRec->PsID)) $coreRec = RIIPowerScore::find($coreID);
//echo 'recordIsIncomplete(' . $coreTbl . ', ' . $coreID . ', status#' . $coreRec->PsStatus . '<br />';
            return (!isset($coreRec->PsStatus) 
                || $coreRec->PsStatus == $GLOBALS["SL"]->getDefID('PowerScore Status', 'Incomplete'));
        }
        return false;
    }
    
    public function multiRecordCheckIntro($cnt = 1)
    {
        return '<a id="hidivBtnUnfinished" class="btn btn-lg btn-default w100 hidivBtn" href="javascript:;">You Have '
            . (($cnt == 1) ? 'An Unfinished PowerScore' : 'Unfinished PowerScores') . '</a>';
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
    
    protected function printAdmReportList($nID)
    {
        $this->v["nID"] = $nID;
        $this->v["cultClassicIds"] = $this->v["emeraldIds"] = [];
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->getDefID('PowerScore Competitions', 'Cultivation Classic'))
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $c) $this->v["cultClassicIds"][] = $c->PsCupPSID;
        }
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->getDefID('PowerScore Competitions', 'Emerald Cup Regenerative Award'))
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $c) $this->v["emeraldIds"][] = $c->PsCupPSID;
        }
        $this->v["filtTitle"] = 'Complete';
        $status = [$this->v["defCmplt"]];
        if (in_array($nID, [720, 722, 724])) {
            $status = [$GLOBALS["SL"]->getDefID('PowerScore Status', 'Incomplete')];
            $this->v["filtTitle"] = 'Incomplete';
        } elseif (in_array($nID, [734, 736, 738])) {
            $status[] = $GLOBALS["SL"]->getDefID('PowerScore Status', 'Incomplete');
        }
        $this->v["filtSort"] = ['RII_PowerScore.PsID', 'desc'];
        if ($GLOBALS["SL"]->REQ->has('sort')) {
            switch (trim($GLOBALS["SL"]->REQ->get('sort'))) {
                case 'date':  $this->v["filtSort"] = ['RII_PowerScore.PsID',   'desc']; break;
                case '-date': $this->v["filtSort"] = ['RII_PowerScore.PsID',   'asc' ]; break;
                case 'name':  $this->v["filtSort"] = ['RII_PowerScore.PsName', 'asc' ]; break;
            }
        }
        $this->v["cupScores"] = [];
        if (in_array($nID, [714, 720, 738])) {
            $this->v["filtTitle"] = 'All ' . $this->v["filtTitle"];
            $this->v["cupScores"] = DB::table('RII_PowerScore')
                ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                ->where('RII_PSRankings.PsRnkFilterByClimate', 0)
                ->where('RII_PSRankings.PsRnkFarmType', 0)
                ->whereIn('RII_PowerScore.PsStatus', $status)
                ->orderBy($this->v["filtSort"][0], $this->v["filtSort"][1])
                ->get();
        } elseif (in_array($nID, [525, 722, 736])) {
            $this->v["filtTitle"] .= ' Cultivation Classic';
            $this->v["cupScores"] = DB::table('RII_PowerScore')
                ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                ->where('RII_PSRankings.PsRnkFilterByClimate', 0)
                ->where('RII_PSRankings.PsRnkFarmType', 0)
                ->whereIn('RII_PowerScore.PsStatus', $status)
                ->whereIn('RII_PowerScore.PsID', $this->v["cultClassicIds"])
                ->orderBy($this->v["filtSort"][0], $this->v["filtSort"][1])
                ->get();
        } elseif (in_array($nID, [718, 724, 734])) {
            $this->v["filtTitle"] .= ' Emerald Cup';
            $this->v["cupScores"] = DB::table('RII_PowerScore')
                ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                ->where('RII_PSRankings.PsRnkFilterByClimate', 0)
                ->where('RII_PSRankings.PsRnkFarmType', 0)
                ->whereIn('RII_PowerScore.PsStatus', $status)
                ->whereIn('RII_PowerScore.PsID', $this->v["emeraldIds"])
                ->orderBy($this->v["filtSort"][0], $this->v["filtSort"][1])
                ->get();
        }
        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.525-emerald-cup-submissions-excel', $this->v)->render();
            $exportFile = str_replace(' ', '_', $this->v["filtTitle"]) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        } else {
            return view('vendor.cannabisscore.nodes.525-emerald-cup-submissions', $this->v)->render();
        }
    }
    
    protected function getCultClassicReport()
    {
        $this->v["farms"] = [];
        $chk = RIICompetitors::where('CmptYear', '=', date("Y"))
            ->where('CmptCompetition', '=', $GLOBALS["SL"]->getDefID('PowerScore Competitions', 'Cultivation Classic'))
            ->orderBy('CmptName', 'asc')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $i => $farm) {
                $this->v["farms"][$i] = [ "name" => $farm->CmptName, "ps" => [], "srch" => [] ];
                $chk2 = DB::table('RII_PowerScore')
                    ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                    ->where('RII_PSRankings.PsRnkFilterByClimate', 0)
                    ->where('RII_PSRankings.PsRnkFarmType', 0)
                    ->where('RII_PowerScore.PsName', 'LIKE', $farm->CmptName)
                    ->where('RII_PowerScore.PsStatus', 'LIKE', $this->v["defCmplt"])
                    ->orderBy('RII_PowerScore.PsID', 'desc')
                    ->get();
                if ($chk2 && sizeof($chk2) > 0) {
                    foreach ($chk2 as $j => $ps) {
                        if ($j == 0) $this->v["farms"][$i]["ps"] = $ps;
                    }
                } else {
                    $chk2 = RIIPowerScore::where('PsName', 'LIKE', $farm->CmptName)
                        ->where('PsStatus', 'LIKE', $GLOBALS["SL"]->getDefID('PowerScore Status', 'Incomplete'))
                        ->orderBy('PsID', 'desc')
                        ->get();
                    if ($chk2 && sizeof($chk2) > 0) {
                        foreach ($chk2 as $j => $ps) {
                            if ($j == 0) $this->v["farms"][$i]["ps"] = $ps;
                        }
                    } else {
                        $srchs = $GLOBALS["SL"]->parseSearchWords($farm->CmptName);
                        if (sizeof($srchs) > 0) {
                            foreach ($srchs as $srch) {
                                $chk2 = RIIPowerScore::where('PsName', 'LIKE', '%' . $srch . '%')
                                    ->get();
                                if ($chk2 && sizeof($chk2) > 0) {
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
                    if ($this->v["farms"][$i]["ps"]->PsStatus == $this->v["defCmplt"]) {
                        $this->v["farmTots"][1]++;
                    } else {
                        $this->v["farmTots"][0]++;
                    }
                }
            }
        }
        //$chk = RIIPowerScore::get();
        //$this->v["entryFarmNames"] = $this->listSimilarNames($chk);
        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.744-cult-classic-report-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'CultClassic-PowerScoreReport-' . date("Y-m-d") . '.xls');
        } else {
            return view('vendor.cannabisscore.nodes.744-cult-classic-report', $this->v)->render();
        }
    }
    
    protected function listSimilarNames($chk)
    {
        $ret = '';
        if ($chk && sizeof($chk) > 0) {
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
        $emailList = [];
        $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
            ->orderBy('PsEmail', 'asc')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $row) {
                if (isset($row->PsEmail) && trim($row->PsEmail) != '') {
                    if (!isset($emailList[$row->PsState])) $emailList[$row->PsState] = [];
                    if (!in_array(trim($row->PsEmail), $emailList[$row->PsState])) {
                        $emailList[$row->PsState][] = trim($row->PsEmail);
                    }
                }
            }
        }
        $ret = '<br /><h1>Export Emails</h1>';
        foreach ($emailList as $state => $stateList) {
            $ret .= '<h2>' . $state . '</h2><textarea class="w100" style="height: 200px;">' 
                . implode(', ', $stateList) . '</textarea>';
        }
        return $ret;
    }
    
    protected function getProccessUploads()
    {
        $this->v["uploaders"] = RIIPowerScore::where('PsUploadEnergyBills', 'LIKE', 1)
            //->where('PsStatus', 'LIKE', $this->v["defCmplt"])
            ->orderBy('PsID', 'desc')
            ->get();
        if ($GLOBALS["SL"]->REQ->has("sub") && $this->v["uploaders"] && sizeof($this->v["uploaders"]) > 0) {
            foreach ($this->v["uploaders"] as $i => $ps) {
                if ($GLOBALS["SL"]->REQ->has("kwh" . $ps->PsID)) {
                    $newKwh = $GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID);
                    if (!$GLOBALS["SL"]->REQ->has("kwh" . $ps->PsID . "a") || 
                        $GLOBALS["SL"]->REQ->get("kwh" . $ps->PsID . "a") != $newKwh) {
                        $this->v["uploaders"][$i]->PsKWH = $newKwh;
                        $this->v["uploaders"][$i]->save();
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
        $this->getProccessUploadsBadLnks();
        return view('vendor.cannabisscore.nodes.742-process-uploads', $this->v)->render();
    }
    
    protected function getProccessUploadsBadLnks()
    {
        $this->v["log1"] = '';
        $this->v["uploads"] = $this->v["uploadInfo"] = [];
        if ($this->v["uploaders"] && sizeof($this->v["uploaders"]) > 0) {
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
                }
            }
        }
        return $ret;
    }
    
    protected function getTroubleshoot()
    {
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
        $baks = [ '_221' ];
        foreach ($baks as $b => $bak) {
            $qman = "SELECT r2.* FROM `RII" . $bak . "_PowerScore` r2 JOIN "
                . "`RII_PowerScore` r ON r2.`PsID` LIKE r.`PsID` WHERE r2.`PsZipCode` IS NOT NULL "
                . "AND r2.`PsZipCode` NOT LIKE '' AND (r.`PsZipCode` IS NULL OR r.`PsZipCode` LIKE '')";
            $this->v["chk1"] = DB::select( DB::raw( $qman ) );
            $this->v["chks2"] = [];
            if ($this->v["chk1"] && sizeof($this->v["chk1"]) > 0) {
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
                        if ($chk1 && sizeof($chk1) > 0 && isset($this->v["subTbls" . $tbl . ""]) 
                            && sizeof($this->v["subTbls" . $tbl . ""]) > 0) {
                            foreach ($chk1 as $j => $row1) {
                                $this->v["logOne"] .= $GLOBALS["SL"]->copyTblRecFromRow($tbl, $row1);
                                $this->v["chks2"][$i][$tbl][] = $row1;
                                foreach ($this->v["subTbls" . $tbl . ""] as $tbl2 => $keys2) {
                                    $qman = "SELECT * FROM `RII" . $bak . "_" . $tbl2 . "` WHERE `" . $keys2[1] 
                                        . "` LIKE '" . $row1->{ $keys[0] } . "'";
                                    $chk2 = DB::select( DB::raw( $qman ) );
                                    //echo '<br />' . $qman . '<pre>'; print_r($chk2); echo '</pre>';
                                    if ($chk2 && sizeof($chk2) > 0) {
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
                                                    if ($chk3 && sizeof($chk3) > 0) {
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
        return view('vendor.cannabisscore.nodes.740-trouble-shooting', $this->v)->render();
    }
    
    protected function printReportBlds($nID)
    {
        $deet = '';
        $blds = $this->sessData->getBranchChildRows('PSAreasBlds');
        if ($blds && sizeof($blds) > 0) {
            foreach ($blds as $i => $bld) {
                $deet .= (($i > 0) ? ', ' : '') 
                    . $GLOBALS["SL"]->getDefValue('PowerScore Building Types', $bld->PsArBldType);
                if (isset($bld->PsArBldTypeOther) && trim($bld->PsArBldTypeOther) != '') {
                    $deet .= ': ' . $bld->PsArBldTypeOther;
                }
                $cnsts = $this->sessData->dataWhere('PSAreasConstr', 'PsArCnsBldID', $bld->getKey());
                if ($cnsts && sizeof($cnsts) > 0) {
                    $deet .= ' (';
                    foreach ($cnsts as $j => $cnst) {
                        $deet .= (($j > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->getDefValue('PowerScore Building Construction', $cnst->PsArCnsType);
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
        if ($lgts && sizeof($lgts) > 0) {
            foreach ($lgts as $i => $lgt) {
                $deet .= (($i > 0) ? ', ' : '') 
                    . $GLOBALS["SL"]->getDefValue('PowerScore Light Types', $lgt->PsLgTypLight)
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
    
    public function chkCoreRecEmpty($coreID = -3, $coreRec = [])
    {
        if ($this->treeID == 1) {
            if ($coreID <= 0) $coreID = $this->coreID;
            if (sizeof($coreRec) == 0 && $coreID > 0) $coreRec = RIIPowerScore::find($coreID);
            if (!isset($coreRec->PsSubmissionProgress) || intVal($coreRec->PsSubmissionProgress) <= 0) return true;
            if (!isset($coreRec->PsZipCode) || trim($coreRec->PsZipCode) == '') return true;
        }
        return false;
    }
    
    
    public function getAllPowerScoresPublic()
    {
        $this->chkScoreFilters();
        $eval = "\$this->v['allscores'] = " . $GLOBALS["SL"]->modelPath('PowerScore') 
            . "::where('PsStatus', " . $this->v["defCmplt"] . ")";
        if ($this->v["fltClimate"] != '') $eval .= "->where('PsAshrae', \$this->v['fltClimate'])";
        if ($this->v["fltFarm"] > 0) $eval .= "->where('PsCharacterize', \$this->v['fltFarm'])";
        $eval .= "->orderBy(\$this->v['sort'][0], \$this->v['sort'][1])->get();";
        eval($eval);
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view('vendor.cannabisscore.nodes.170-all-powerscores-excel', $this->v)->render();
            $exportFile = 'Compare All';
            if ($this->v["fltFarm"] == 0) $exportFile .= ' Farms';
            else $exportFile .= ' ' . $GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $this->v["fltFarm"]);
            if ($this->v["fltClimate"] != '') $exportFile .= ' Climate Zone ' . $this->v["fltClimate"];
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        return view('vendor.cannabisscore.nodes.170-all-powerscores', $this->v)->render();
    }
    
    
    public function getAllPowerScoreAvgsPublic()
    {
        $this->v["allscores"] = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
            ->get();
        $this->calcAllPowerScoreAvgs();
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'PowerScore_Averages-' . date("Y-m-d") . '.xls');
        }
        return view('vendor.cannabisscore.nodes.773-powerscore-avgs', $this->v)->render();
    }
    
    
    protected function calcAllPowerScoreAvgs()
    {
        $this->v["allAvgs"] = [
            "all"    => $this->allAvgsEmpty(),
            "states" => [],
            "zones"  => [],
            "types"  => [ 143 => $this->allAvgsEmpty(), 144 => $this->allAvgsEmpty(), 145 => $this->allAvgsEmpty(), 
                223 => $this->allAvgsEmpty() ],
            "cups"   => [ 230 => $this->allAvgsEmpty(), 231 => $this->allAvgsEmpty() ]
            ];
        if ($this->v["allscores"] && sizeof($this->v["allscores"]) > 0) {
            foreach ($this->v["allscores"] as $ps) {
                $cups = [];
                $chk = RIIPSForCup::where('PsCupPSID', $ps->PsID)
                    ->get();
                if ($chk && sizeof($chk) > 0) {
                    foreach ($chk as $c) $cups[] = $c->PsCupCupID;
                }
                $this->v["allAvgs"]["all"]["tot"]++;
                if (isset($ps->PsCharacterize) && isset($this->v["allAvgs"]["types"][$ps->PsCharacterize])) {
                    $this->v["allAvgs"]["types"][$ps->PsCharacterize]["tot"]++;
                }
                if (isset($ps->PsState) && trim($ps->PsState) != '') {
                    if (!isset($this->v["allAvgs"]["states"][$ps->PsState])) {
                        $this->v["allAvgs"]["states"][$ps->PsState] = $this->allAvgsEmpty();
                    }
                    $this->v["allAvgs"]["states"][$ps->PsState]["tot"]++;
                }
                if (isset($ps->PsAshrae) && trim($ps->PsAshrae) != '') {
                    if (!isset($this->v["allAvgs"]["zones"][$ps->PsAshrae])) {
                        $this->v["allAvgs"]["zones"][$ps->PsAshrae] = $this->allAvgsEmpty();
                    }
                    $this->v["allAvgs"]["zones"][$ps->PsAshrae]["tot"]++;
                }
                if (sizeof($cups) > 0) {
                    foreach ($cups as $c) $this->v["allAvgs"]["cups"][$c]["tot"]++;
                }
                $subscores = [
                    [ 'PsEfficFacility',   'fac' ],
                    [ 'PsEfficProduction', 'pro' ],
                    [ 'PsEfficLighting',   'lgt' ],
                    [ 'PsEfficHvac',       'hvc' ],
                    ];
                foreach ($subscores as $sub) {
                    if (isset($ps->{ $sub[0] }) && $ps->{ $sub[0] } > 0) {
                        $this->v["allAvgs"]["all"][$sub[1]][0]++;
                        $this->v["allAvgs"]["all"][$sub[1]][1] += $ps->{ $sub[0] };
                        if (isset($ps->PsCharacterize) && isset($this->v["allAvgs"]["types"][$ps->PsCharacterize])) {
                            $this->v["allAvgs"]["types"][$ps->PsCharacterize][$sub[1]][0]++;
                            $this->v["allAvgs"]["types"][$ps->PsCharacterize][$sub[1]][1] += $ps->{ $sub[0] };
                        }
                        if (isset($ps->PsState) && trim($ps->PsState) != '') {
                            $this->v["allAvgs"]["states"][$ps->PsState][$sub[1]][0]++;
                            $this->v["allAvgs"]["states"][$ps->PsState][$sub[1]][1] += $ps->{ $sub[0] };
                        }
                        if (isset($ps->PsAshrae) && trim($ps->PsAshrae) != '') {
                            if (!isset($this->v["allAvgs"]["zones"][$ps->PsAshrae])) {
                                $this->v["allAvgs"]["zones"][$ps->PsAshrae] = $this->allAvgsEmpty();
                            }
                            $this->v["allAvgs"]["zones"][$ps->PsAshrae][$sub[1]][0]++;
                            $this->v["allAvgs"]["zones"][$ps->PsAshrae][$sub[1]][1] += $ps->{ $sub[0] };
                        }
                        if (sizeof($cups) > 0) {
                            foreach ($cups as $c) {
                                $this->v["allAvgs"]["cups"][$c][$sub[1]][0]++;
                                $this->v["allAvgs"]["cups"][$c][$sub[1]][1] += $ps->{ $sub[0] };
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    
    protected function allAvgsEmpty()
    {
        return [ "tot" => 0, "fac" => [ 0, 0 ], "pro" => [ 0, 0 ], "lgt" => [ 0, 0 ], "hvc" => [ 0, 0 ] ];
    }
    
    protected function reportPowerScoreFeedback()
    {
        $this->v["feedback"] = [];
        $chk = DB::table('RII_PsFeedback')
            ->join('RII_PowerScore', 'RII_PsFeedback.PsfPsID', '=', 'RII_PowerScore.PsID')
            ->orderBy('RII_PsFeedback.created_at', 'desc')
            ->select('RII_PowerScore.PsName', 'RII_PowerScore.PsEfficOverall', 'RII_PsFeedback.*')
            ->get();
        if ($chk && sizeof($chk) > 0) {
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
        $this->v["allscores"] = [];
        $chk = DB::table('RII_PowerScore')
            ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
            ->where('RII_PSRankings.PsRnkFilterByClimate', 0)
            ->where('RII_PSRankings.PsRnkFarmType', 0)
            ->where('RII_PowerScore.PsStatus', 'LIKE', $this->v["defCmplt"])
            ->orderBy('RII_PowerScore.PsID', 'desc')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $ps) {
                if (!in_array($ps->PsID, $this->v["goodies"])) $this->v["allscores"][] = $ps;
            }
        }
        return view('vendor.cannabisscore.nodes.775-powerscore-publishing', $this->v)->render();
    }
    
    
    protected function tmpDebug($str = '')
    {
        $tmp = ' - tmpDebug - ' . $str;
        $chk = RIIPSAreas::where('PsAreaPSID', 169)->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $i => $row) $tmp .= ', ' . $row->getKey();
        }
        echo $tmp . '<br />';
        return true;
    }
    
}