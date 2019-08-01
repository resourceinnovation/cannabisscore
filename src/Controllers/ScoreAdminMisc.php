<?php
/**
  * ScoreAdminMisc is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which run secondary system functions
  * which only staff have access to.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSMonthly;
use App\Models\RIIPSCommunications;
use App\Models\RIIManufacturers;
use App\Models\RIILightModels;
use App\Models\SLUploads;
use SurvLoop\Controllers\Stats\SurvTrends;
use CannabisScore\Controllers\ScoreCalcs;

class ScoreAdminMisc extends ScoreCalcs
{
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
                                        += $area->PsAreaTotalLightWatts*$GLOBALS["CUST"]->getTypeHours($typ)*365;
                                    $this->v["lgtChk"][$i][6] += $area->PsAreaTotalLightWatts;
                                    $this->v["lgtChk"][$i][8] += ($area->PsAreaTotalLightWatts/$area->PsAreaSize);
                                    $this->v["lgtChk"][$i][9]++;
                                }
                            }
                        }
                        $effic = $GLOBALS["CUST"]->getHvacEffic($area->PsAreaHvacType);
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
        } elseif ($GLOBALS["SL"]->REQ->has('recalc')) {
            return $this->recalc2AllSubScores();
        } else {
            return view('vendor.cannabisscore.nodes.740-trouble-shooting', $this->v)->render();
        }
    }
    
    protected function getEmailsList()
    {
        $this->v["sendResults"] = '';
        $this->v["emailList"] = [ 'Newsletter' => [] ];
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
        $chk = RIIPowerScore::where('PsEmail', 'NOT LIKE', '')
            ->orderBy('PsEmail', 'asc')
            ->orderBy('PsID', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $row) {
                if (isset($row->PsEmail) && trim($row->PsEmail) != '') {
                    if (!isset($this->v["emailList"][$row->PsState])) {
                        $this->v["emailList"][$row->PsState] = [];
                    }
                    if (isset($row->PsNewsletter) && intVal($row->PsNewsletter) == 1 
                        && !in_array(trim($row->PsEmail), $this->v["emailList"]["Newsletter"])) {
                        $this->v["emailList"]["Newsletter"][] = trim($row->PsEmail);
                    }

                    $found = false;
                    if (sizeof($this->v["scoreLists"]["all"]) > 0) {
                        foreach ($this->v["scoreLists"]["all"] as $i => $infChk) {
                            if (strtolower($infChk["email"]) == strtolower($row->PsEmail)) {
                                $found = true;
                            }
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
    
    protected function getProccessUploadsAjax()
    {
        $ret = '';
        if ($GLOBALS["SL"]->REQ->has('p')) {
            $ps = RIIPowerScore::find($GLOBALS["SL"]->REQ->get('p'));
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
        if ($GLOBALS["SL"]->REQ->has('last')) {
            $ret .= '<style> #animLoadingUploads { display: none; } </style>';
        }
        return $ret;
    }
    
    protected function reportInSurveyFeedback()
    {
        $this->v["nID"] = 838;
        $this->v["feedbackPages"] = ['', '', '', '', '', '', '', '', ''];
        $this->v["feedbackPName"] = ['Your Farm', 'Your Growing Environments', 'Your Lighting', 'Your HVAC', 
            'Your Annual Totals', 'Other Techniques & Energy', 'Contact & Options', '', ''];
        $this->v["feedbackscores"] = RIIPowerScore::select('PsFeedback1', 'PsFeedback2', 'PsFeedback3', 'PsFeedback4',
            'PsFeedback5', 'PsFeedback6', 'PsFeedback7', 'PsFeedback8', 'PsID', 'PsStatus', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        if ($this->v["feedbackscores"]->isNotEmpty()) {
            foreach ($this->v["feedbackscores"] as $ps) {
                for ($page = 1; $page < 9; $page++) {
                    if (isset($ps->{ 'PsFeedback' . $page }) && trim($ps->{ 'PsFeedback' . $page }) != '') {
                        $this->v["feedbackPages"][($page-1)] .= strip_tags($ps->{ 'PsFeedback' . $page }) 
                            . ' <a href="/calculated/read-' . $ps->PsID 
                            . '" target="_blank" class="slGrey mL10 fPerc66">' . date("n/j/y", strtotime($ps->created_at)) . ' '
                            . $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->PsStatus) . ' #' 
                            . $ps->PsID . '</a><br /><br />';
                    }
                }
            }
        }
        return view('vendor.cannabisscore.nodes.838-in-survey-feedback', $this->v)->render();
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
        $this->initSearcher();
        $this->searcher->v["allscores"] = $added = [];
        $chk = DB::table('RII_PowerScore')
            ->leftJoin('RII_PSRankings', 'RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
            ->where('RII_PSRankings.PsRnkFilters', '')
            ->where('RII_PowerScore.PsStatus', 'LIKE', $this->v["defCmplt"])
            ->orderBy('RII_PowerScore.PsID', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ps) {
                if (!in_array($ps->PsID, $added) && !in_array($ps->PsID, $this->v["goodies"])) {
                    $this->searcher->v["allscores"][] = $ps;
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
    
    protected function printDashSessGraph()
    {
        $this->v["isDash"] = true;
        $grapher = new SurvTrends('' . rand(1000000, 10000000) . '');
        $grapher->addDataLineType('complete', 'Complete', '', '#8DC63F', '#8DC63F');
        $grapher->addDataLineType('archived', 'Archived', '', '#726659', '#726659');
        $grapher->addDataLineType('incomplete', 'Incomplete', '', '#F07B3A', '#F07B3A');
        $recentAttempts = RIIPowerScore::whereNotNull('PsZipCode')
            ->where('PsZipCode', 'NOT LIKE', '')
            ->where('created_at', '>=', $grapher->getPastStartDate() . ' 00:00:00')
            ->select('PsStatus', 'created_at')
            ->get();
        if ($recentAttempts->isNotEmpty()) {
            foreach ($recentAttempts as $i => $rec) {
                switch ($rec->PsStatus) {
                    case $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'):
                        $grapher->addDayTally('incomplete', $rec->created_at);
                        break;
                    case $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'):
                        $grapher->addDayTally('complete', $rec->created_at);
                        break;
                    case $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived'):
                        $grapher->addDayTally('archived', $rec->created_at);
                        break;
                }
            }
        }
        return '<h5 class="slBlueDark">Recent PowerScore Submission Attempts</h5>'
            . $grapher->printDailyGraph();
    }
    
    protected function printAdminPsComms()
    {
        $comms = $adms = [];
        if (isset($this->sessData->dataSets["PSCommunications"])) {
            $comms = $this->sessData->dataSets["PSCommunications"];
            if (sizeof($comms) > 0) {
                foreach ($comms as $com) {
                    $adms[$com->PsComUser] = $this->printUserLnk($com->PsComUser);
                }
            }
        }
        return view('vendor.cannabisscore.nodes.845-admin-communications-log', [
            "nID"   => 845,
            "ps"    => $this->coreID,
            "comms" => $comms,
            "adms"  => $adms
        ])->render();
    }
    
    protected function admCommsForm(Request $request)
    {
        $this->survLoopInit($request);
        if (!$this->v["isAdmin"]) {
            return ':-/';
        }
        $this->v["ps"] = 0;
        if ($request->has('ps') && intVal($request->ps) > 0) {
            $this->v["ps"] = intVal($request->ps);
            if ($request->has('logCommFld') && trim($request->logCommFld) != '') {
                $com = new RIIPSCommunications;
                $com->PsComPSID = $this->v["ps"];
                $com->PsComUser = $this->v["uID"];
                $com->PsComDescription = trim($request->logCommFld);
                $com->save();
                return $this->redir('/calculated/read-' . $this->v["ps"], true);
            }
            return view('vendor.cannabisscore.nodes.845-admin-communications-log-form', $this->v)->render();
        }
        return '';
    }
    
    protected function printMgmtManufacturers($nID = -3)
    {
        return view('vendor.cannabisscore.nodes.914-manage-manufacturers', [
            "manus" => RIIManufacturers::orderBy('ManuName', 'asc')->get()            
            ])->render();
    }
    
    protected function addManufacturers($nID = -3)
    {
        if ($GLOBALS["SL"]->REQ->has('addManu') && trim($GLOBALS["SL"]->REQ->get('addManu')) != '') {
            $lines = $GLOBALS["SL"]->mexplode("\n", $GLOBALS["SL"]->REQ->get('addManu'));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($line != '') {
                        $chk = RIIManufacturers::where('ManuName', 'LIKE', $line)
                            ->first();
                        if (!$chk || !isset($chk->ManuID)) {
                            $chk = new RIIManufacturers;
                            $chk->ManuName = $line;
                            $chk->save();
                        }
                    }
                }
            }
        }
        return true;
    }
    
    protected function printMgmtLightModels($nID = -3)
    {
        $this->loadManufactIDs();
        $this->v["models"] = $this->getAllLightModels();
        return view('vendor.cannabisscore.nodes.917-manage-lighting-models', $this->v)->render();
    }
    
    protected function addLightModels($nID = -3)
    {
        if ($GLOBALS["SL"]->REQ->has('addModels') && trim($GLOBALS["SL"]->REQ->get('addModels')) != '') {
            $lines = $GLOBALS["SL"]->mexplode("\n", $GLOBALS["SL"]->REQ->get('addModels'));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($line != '') {
                        $cols = $GLOBALS["SL"]->mexplode("\t", $line);
                        if (sizeof($cols) == 3) {
                            foreach ($cols as $i => $col) {
                                $cols[$i] = trim($col);
                            }
                            $manu = RIIManufacturers::where('ManuName', 'LIKE', $cols[0])
                                ->first();
                            if (!$manu || !isset($manu->ManuName)) {
                                $manu = new RIIManufacturers;
                                $manu->ManuName = $cols[0];
                                $manu->save();
                            }
                            $chk = RIILightModels::where('LgtModManuID', 'LIKE', $manu->ManuID)
                                ->where('LgtModName', 'LIKE', $cols[1])
                                ->first();
                            if (!$chk || !isset($chk->LgtModID)) {
                                $chk = new RIILightModels;
                                $chk->LgtModManuID = $manu->ManuID;
                                $chk->LgtModName = $cols[1];
                            }
                            $chk->LgtModTech = $cols[2];
                            $chk->save();
                        }
                    }
                }
            }
        }
        return true;
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