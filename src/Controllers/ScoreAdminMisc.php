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
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsMonthly;
use App\Models\RIIPsCommunications;
use App\Models\RIIManufacturers;
use App\Models\RIILightModels;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use App\Models\SLUsersRoles;
use App\Models\SLUploads;
use App\Models\User;
use SurvLoop\Controllers\Stats\SurvTrends;
use CannabisScore\Controllers\ScorePrintReport;

class ScoreAdminMisc extends ScorePrintReport
{
    protected function getTroubleshoot()
    {

        // Artificial Lighting was being overwritten by false positives during calculations
        $this->v["artifErrs"] = '';
        if ($GLOBALS["SL"]->REQ->has('artifChk')) {
            $this->v["artifChk"] = $this->v["artifRecent"] = [];
            $chk = RIIPsAreas::select('ps_area_psid', 'ps_area_id', 'ps_area_type',
                    'ps_area_lgt_artif', 'ps_area_lgt_sun', 'ps_area_lgt_dep')
                ->get();
            foreach ($chk as $area) {
                $psid = $area->ps_area_psid;
                if (!isset($this->v["artifChk"][$psid])) {
                    $this->v["artifChk"][$psid] = [];
                    $this->v["artifChk"][$psid] = [];
                }
                $this->v["artifChk"][$psid][$area->ps_area_id] = [
                    "id"    => $area->ps_area_id,
                    "type"  => $area->ps_area_type,
                    "artif" => intVal($area->ps_area_lgt_artif),
                    "sun"   => intVal($area->ps_area_lgt_sun),
                    "dep"   => intVal($area->ps_area_lgt_dep)
                ];
            }
            $qman = "SELECT `rii_ps_areas`.`ps_area_psid`, `rii_ps_areas`.`ps_area_id`, 
                `sl_node_saves`.`node_save_node`, `sl_node_saves`.`node_save_new_val`
                FROM `sl_node_saves` JOIN `rii_ps_areas` 
                ON `rii_ps_areas`.`ps_area_id`=`sl_node_saves`.`node_save_loop_item_id` 
                WHERE `sl_node_saves`.`node_save_node` IN (882, 881, 880);";
            $chk = DB::select( DB::raw( $qman ) );
            if ($chk) {
                foreach ($chk as $i => $save) {
                    $psid = $save->ps_area_psid;
                    if (isset($this->v["artifChk"][$psid])) {
                        if (!isset($this->v["artifRecent"][$psid])) {
                            $this->v["artifRecent"][$psid] = $this->v["artifChk"][$psid];
                        }
                        $areaID = $save->ps_area_id;
                        $val = 0;
                        if (isset($save->node_save_new_val) 
                            && intVal($save->node_save_new_val) == 1) {
                            $val = 1;
                        }
                        if ($save->node_save_node == 882) {
                            $this->v["artifRecent"][$psid][$areaID]["artifOG"] = $val;
                        }
                        if ($save->node_save_node == 880) {
                            $this->v["artifRecent"][$psid][$areaID]["sunOG"] = $val;
                        }
                        if ($save->node_save_node == 881) {
                            $this->v["artifRecent"][$psid][$areaID]["depOG"] = $val;
                        }
                    }
                }
            }
            if (sizeof($this->v["artifRecent"]) > 0) {
                foreach ($this->v["artifRecent"] as $psid => $artif) {
                    foreach ($artif as $areaID => $area) {
                        foreach (['artif', 'sun', 'dep'] as $type) {
                            if (isset($area[$type . "OG"]) 
                                && isset($area[$type]) 
                                && $area[$type . "OG"] != $area[$type]) {
                                $this->v["artifErrs"] .= '<b>' . $psid . '</b>: ' . $area["id"] 
                                    . ', ' . $area["type"] . ', <b>' . $area[$type . "OG"] 
                                    . '</b> -} <span style="color: red;">' 
                                    . $area[$type] . '</span><br />';
                                RIIPsAreas::find($area["id"])
                                    ->update([ 'ps_area_lgt_' . $type => $area[$type . "OG"] ]);
                            }
                        }
                    }
                }
            }
        }


        /*
        $this->v["logOne"] = '';
        $this->v["subTblsPowerScore"] = [
            'PSAreas'       => ['ps_area_id',   'ps_area_psid'   ],
            'PSFarm'        => ['PsFrmID',    'PsFrmPSID'    ],
            'PSForCup'      => ['PsCupID',    'ps_cup_psid'    ],
            'PSLicenses'    => ['PsLicID',    'ps_lic_psid'    ],
            'PSMonthly'     => ['PsMonthID',  'ps_month_psid'  ],
            'PSOtherPower'  => ['PsOthPwrID', 'PsOthPwrPSID' ],
            'PSRankings'    => ['ps_rnk_id',    'ps_rnk_psid'    ],
            'PSRenewables'  => ['PsRnwID',    'ps_rnw_psid'    ],
            'PSUtiliLinks'  => ['PsUtLnkID',  'PsUtLnkPSID'  ]
            ];
        $this->v["subTblsPSAreas"] = [
            'PSAreasBlds'   => ['PsArBldID',  'ps_ar_bld_area_id'],
            'PSLightTypes'  => ['PsLgTypID',  'ps_lg_typ_area_id']
            ];
        $this->v["subTblsPSAreasBlds"] = [
            'PSAreasConstr' => ['PsArCnsID',  'PsArCnsBldID' ]
            ];
        $baks = [ '_221', '_310', '_417', '_521' ];
        foreach ($baks as $b => $bak) {
            $qman = "SELECT r2.* FROM `RII" . $bak . "_PowerScore` r2 JOIN "
                . "`rii_powerscore` r ON r2.`ps_id` LIKE r.`PsID` WHERE r2.`PsZipCode` IS NOT NULL "
                . "AND r2.`PsZipCode` NOT LIKE '' AND (r.`PsZipCode` IS NULL OR r.`PsZipCode` LIKE '')";
            $this->v["logOne"] .= '<div class="fPerc66">' . $qman . '</div>';
            $this->v["chk1"] = DB::select( DB::raw( $qman ) );
            $this->v["chks2"] = [];
            if ($this->v["chk1"]->isNotEmpty()) {
                foreach ($this->v["chk1"] as $i => $ps) {
                    $this->v["logOne"] .= $GLOBALS["SL"]->copyTblRecFromRow('PowerScore', $ps);
                    $this->logAdd('session-stuff', 'Manually Restoring PowerScore#' . $ps->ps_id 
                        . ' <i>(RII.getTroubleshoot)</i>');
                    $this->v["chks2"][$i] = [];
                    foreach ($this->v["subTblsPowerScore"] as $tbl => $keys) {
                        $qman = "SELECT * FROM `RII" . $bak . "_" . $tbl . "` WHERE `" . $keys[1] . "` LIKE '" 
                            . $ps->ps_id . "'";
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
        
        if ($GLOBALS["SL"]->REQ->has('import')) {
            $this->runImport();
        }
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
        $this->v["emailList"] = [
            'Newsletter' => [] 
        ];
        $wchLsts = [ 'all', 'blw', 'avg', 'abv', 'inc' ];
        $this->v["scoreLists"] = [];
        foreach ($wchLsts as $lst) {
            $this->v["scoreLists"][$lst] = [];
        }
        $this->v["wchLst"] = 'all';
        if ($GLOBALS["SL"]->REQ->has('wchLst') 
            && in_array($GLOBALS["SL"]->REQ->wchLst, $wchLsts)) {
            $this->v["wchLst"] = $GLOBALS["SL"]->REQ->wchLst;
        }
        $this->v["wchEma"] = 0;
        if ($GLOBALS["SL"]->REQ->has('wchEma') 
            && intVal($GLOBALS["SL"]->REQ->wchEma) > 0) {
            $this->v["wchEma"] = $GLOBALS["SL"]->REQ->wchEma;
        }
        if ($GLOBALS["SL"]->REQ->has('yesSend') 
            && intVal($GLOBALS["SL"]->REQ->yesSend) == 1
            && $GLOBALS["SL"]->REQ->has('scores') 
            && sizeof($GLOBALS["SL"]->REQ->scores) > 0
            && $this->v["wchEma"] > 0) {
            $chk = RIIPowerscore::whereIn('ps_id', $GLOBALS["SL"]->REQ->scores)
                ->get();
            if ($chk->isNotEmpty()) {
                $ajax = '';
                foreach ($chk as $i => $ps) {
                    $this->v["sendResults"] .= '<div>Sending #' . $ps->ps_id . ' ' . $ps->ps_email 
                        . ' (' . $ps->ps_name . ')<br /><div id="emaAjax' . $ps->ps_id . '">' 
                        . $GLOBALS["SL"]->sysOpts["spinner-code"] . '</div></div>';
                    $ajax .= 'setTimeout(function() { 
                        var name = encodeURIComponent(document.getElementById(\'replyNameID\').value);
                        var url = "/ajadm/send-email?e=' . $this->v["wchEma"] 
                            . '&t=1&c=' . $ps->ps_id 
                            . '&r="+document.getElementById(\'replyToID\').value+"&rn="+name+"";
                        $("#emaAjax' . $ps->ps_id . '").load(url); }, ' . ($i*2000) . ');';
                }
                $this->v["sendResults"] .= $GLOBALS["SL"]->opnAjax() . $ajax . $GLOBALS["SL"]->clsAjax();
            }
        }
        $chk = RIIPowerscore::where('ps_email', 'NOT LIKE', '')
            ->orderBy('ps_email', 'asc')
            ->orderBy('ps_id', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $row) {
                if (isset($row->ps_email) && trim($row->ps_email) != '') {
                    if (!isset($this->v["emailList"][$row->ps_state])) {
                        $this->v["emailList"][$row->ps_state] = [];
                    }
                    if (isset($row->ps_newsletter) && intVal($row->ps_newsletter) == 1 
                        && !in_array(trim($row->ps_email), $this->v["emailList"]["Newsletter"])) {
                        $this->v["emailList"]["Newsletter"][] = trim($row->ps_email);
                    }

                    $found = false;
                    if (sizeof($this->v["scoreLists"]["all"]) > 0) {
                        foreach ($this->v["scoreLists"]["all"] as $i => $infChk) {
                            if (strtolower($infChk["email"]) == strtolower($row->ps_email)) {
                                $found = true;
                            }
                        }
                    }
                    if (!$found) {
                        if (!in_array(trim($row->ps_email), $this->v["emailList"][$row->ps_state])) {
                            $this->v["emailList"][$row->ps_state][] = trim($row->ps_email);
                        }
                        $infArr = [
                            "id"    => $row->ps_id,
                            "email" => $row->ps_email, 
                            "farm"  => $row->ps_name, 
                            "score" => $row->ps_effic_over_similar,
                            "sent"  => false
                        ];
                        $this->v["scoreLists"]["all"][] = $infArr;
                        if ($row->ps_status != $this->v["defCmplt"] 
                            || !isset($row->ps_effic_facility) 
                            || !isset($row->ps_effic_production) 
                            || !isset($row->ps_effic_hvac) 
                            || !isset($row->ps_effic_lighting) 
                            || $row->ps_effic_facility <= 0 
                            || $row->ps_effic_production <= 0 
                            || $row->ps_effic_hvac <= 0 
                            || $row->ps_effic_lighting <= 0) {
                            $this->v["scoreLists"]["inc"][] = $infArr;
                        } elseif ($row->ps_effic_over_similar >= 200/3) {
                            $this->v["scoreLists"]["abv"][] = $infArr;
                        } elseif ($row->ps_effic_over_similar >= 100/3) {
                            $this->v["scoreLists"]["avg"][] = $infArr;
                        } else {
                            $this->v["scoreLists"]["blw"][] = $infArr;
                        }
                        // check past sent list
                    }
                }
            }
        }
        $GLOBALS["SL"]->pageAJAX .= view(
            'vendor.cannabisscore.nodes.637-email-list-ajax', 
            $this->v
        )->render();
        return view('vendor.cannabisscore.nodes.637-email-list', $this->v)->render();
    }
    
    protected function getProccessUploads()
    {
        $this->v["uploaders"] = RIIPowerscore::where('ps_upload_energy_bills', 'LIKE', 1)
            //->where('ps_status', 'LIKE', $this->v["defCmplt"])
            ->orderBy('ps_id', 'desc')
            ->get();
        $this->v["upMonths"] = [];
        if ($this->v["uploaders"] && sizeof($this->v["uploaders"]) > 0) {
            foreach ($this->v["uploaders"] as $i => $ps) {
                $this->v["upMonths"][$ps->ps_id] = [];
                $chk = RIIPsMonthly::where('ps_month_psid', 'LIKE', $ps->ps_id)
                    ->get();
                if ($chk && sizeof($chk) > 0) {
                    foreach ($chk as $mon) {
                        $this->v["upMonths"][$ps->ps_id][$mon->ps_month_month] = $mon;
                    }
                }
            }
            if ($GLOBALS["SL"]->REQ->has("sub")) {
                foreach ($this->v["uploaders"] as $i => $ps) {
                    if ($GLOBALS["SL"]->REQ->has("kwh" . $ps->ps_id)) {
                        $newKwh = $GLOBALS["SL"]->REQ->get("kwh" . $ps->ps_id);
                        if (!$GLOBALS["SL"]->REQ->has("kwh" . $ps->ps_id . "a") 
                            || $GLOBALS["SL"]->REQ->get("kwh" . $ps->ps_id . "a") != $newKwh) {
                            $this->v["uploaders"][$i]->ps_kwh = $newKwh;
                            $this->v["uploaders"][$i]->save();
                        }
                        for ($mon = 1; $mon <= 12; $mon++) {
                            if ($GLOBALS["SL"]->REQ->has("kwh" . $ps->ps_id . "m" . $mon) 
                                && $GLOBALS["SL"]->REQ->get("kwh" . $ps->ps_id . "m" . $mon)) {
                                $kWh = intVal($GLOBALS["SL"]->REQ->get("kwh" . $ps->ps_id . "m" . $mon));
                                if (!isset($this->v["upMonths"][$ps->ps_id][$mon])) {
                                    $this->v["upMonths"][$ps->ps_id][$mon] = new RIIPsMonthly;
                                    $this->v["upMonths"][$ps->ps_id][$mon]->ps_month_psid = $ps->ps_id;
                                    $this->v["upMonths"][$ps->ps_id][$mon]->ps_month_month = $mon;
                                }
                                $this->v["upMonths"][$ps->ps_id][$mon]->ps_month_kwh1 = intVal($kWh);
                                $this->v["upMonths"][$ps->ps_id][$mon]->save();
                            }
                        }
                    }
                    if ($GLOBALS["SL"]->REQ->has("status" . $ps->ps_id)) {
                        $newStatus = intVal($GLOBALS["SL"]->REQ->get("status" . $ps->ps_id));
                        if ($newStatus > 0 && $this->v["uploaders"][$i]->ps_status != $newStatus) {
                            $this->v["uploaders"][$i]->ps_status = $newStatus;
                            $this->v["uploaders"][$i]->save();
                            $this->logAdd('session-stuff', 'Admin Changing PowerScore#' . $ps->ps_id 
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
                $this->v["uploads"][$i] = SLUploads::where('up_tree_id', '=', 1)
                    ->where('up_core_id', '=', $ps->getKey())
                    ->orderBy('up_id', 'asc')
                    ->get();
                if ($this->v["uploads"][$i] && sizeof($this->v["uploads"][$i]) > 0) {
                    $fld = '../storage/app/up/evidence/' 
                        . str_replace('-', '/', substr($ps->created_at, 0, 10));
                    $this->v["uploadInfo"][$i] = [
                        "fld"  => $fld,
                        "fold" => $this->getUploadFolder(69, $ps, 'powerscore'),
                        "ups"  => []
                    ];
                    foreach ($this->v["uploads"][$i] as $j => $up) {
                        if (trim($up->up_stored_file) != '') {
                            $baseFilen = $up->up_stored_file . '.' 
                                . $GLOBALS["SL"]->getFileExt($up->up_upload_file);
                            $this->v["uploadInfo"][$i]["ups"][$j] = [
                                "full" => $this->v["uploadInfo"][$i]["fold"] . $baseFilen,
                                "subf" => []
                            ];
                            //$this->v["uploadInfo"][$i]["full"] 
                            //    = $GLOBALS["SL"]->searchDeeperDirs($this->v["uploadInfo"][$i]["full"]);
                            if (!file_exists($this->v["uploadInfo"][$i]["ups"][$j]["full"])) {
                                $this->v["uploadInfo"][$i]["ups"][$j]["subf"] = $GLOBALS["SL"]->findDirFile(
                                    $this->v["uploadInfo"][$i]["fld"], 
                                    $baseFilen
                                );
                                $this->v["log1"] .= '<h3>' . $ps->ps_id . '</h3> Not Found: ' 
                                    . $this->v["uploadInfo"][$i]["ups"][$j]["full"] . '<br />';
                                if (sizeof($this->v["uploadInfo"][$i]["ups"][$j]["subf"]) > 0) {
                                    foreach ($this->v["uploadInfo"][$i]["ups"][$j]["subf"] as $k => $fld) {
                                        $this->v["log1"] .= ', ' . $fld;
                                    }
                                    $ind = sizeof($this->v["uploadInfo"][$i]["ups"][$j]["subf"])-1;
                                    $ps->ps_unique_str = $this->v["uploadInfo"][$i]["ups"][$j]["subf"][$ind];
                                    $ps->save();
                                }
                                $this->v["log1"] .= '<br />new uniqueStr: ' . $ps->ps_unique_str . '<br /><br />';
                                $logDesc = 'Manually Restoring PowerScore#' . $ps->ps_id . ' UniqueStr '
                                    . $ps->ps_unique_str . ' <i>(RII.getProccessUploadsBadLnks)</i>';
                                $this->logAdd('session-stuff', $logDesc);
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
            $ps = RIIPowerscore::find($GLOBALS["SL"]->REQ->get('p'));
            if ($ps && isset($ps->ps_id) && $this->v["isAdmin"]) {
                $this->loadTree(1);
                $this->loadAllSessData('powerscore', $ps->getKey());
                $ups = $this->getUploads(69, $this->v["isAdmin"]);
                if (sizeof($ups) > 0) {
                    foreach ($ups as $up) {
                        $ret .= $up;
                    }
                    $ret .= '<style> #psUpload' . $ps->ps_id . ' { display: table-row; } </style>';
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
        $this->v["feedbackPName"] = [
            'Your Farm', 
            'Your Growing Environments', 
            'Your Lighting', 
            'Your HVAC', 
            'Your Annual Totals', 
            'Other Techniques & Energy', 
            'Contact & Options', 
            '', 
            ''
        ];
        $this->v["feedbackscores"] = RIIPowerscore::select('ps_feedback1', 'ps_feedback2', 
            'ps_feedback3', 'ps_feedback4', 'ps_feedback5', 'ps_feedback6', 'ps_feedback7', 
            'ps_feedback8', 'ps_id', 'ps_status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        if ($this->v["feedbackscores"]->isNotEmpty()) {
            foreach ($this->v["feedbackscores"] as $ps) {
                for ($page = 1; $page < 9; $page++) {
                    if (isset($ps->{ 'ps_feedback' . $page }) && trim($ps->{ 'ps_feedback' . $page }) != '') {
                        $this->v["feedbackPages"][($page-1)] .= strip_tags($ps->{ 'ps_feedback' . $page }) 
                            . ' <a href="/calculated/read-' . $ps->ps_id 
                            . '" target="_blank" class="slGrey mL10 fPerc66">' 
                            . date("n/j/y", strtotime($ps->created_at)) . ' '
                            . $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->ps_status) 
                            . ' #' . $ps->ps_id . '</a><br /><br />';
                    }
                }
            }
        }
        return view('vendor.cannabisscore.nodes.838-in-survey-feedback', $this->v)->render();
    }
    
    protected function reportPowerScoreFeedback()
    {
        $this->v["feedback"] = [];
        $chk = DB::table('rii_ps_feedback')
            ->join('rii_powerscore', 'rii_ps_feedback.psf_psid', '=', 'rii_powerscore.ps_id')
            ->orderBy('rii_ps_feedback.created_at', 'desc')
            ->select('rii_powerscore.ps_name', 'rii_powerscore.ps_effic_overall', 'rii_ps_feedback.*')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $row) {
                if ((isset($row->psf_feedback1) && trim($row->psf_feedback1) != '')
                    || (isset($row->psf_feedback2) && trim($row->psf_feedback2) != '')
                    || (isset($row->psf_feedback3) && trim($row->psf_feedback3) != '')
                    || (isset($row->psf_feedback4) && trim($row->psf_feedback4) != '')
                    || (isset($row->psf_feedback5) && trim($row->psf_feedback5) != '')) {
                    $this->v["feedback"][] = $row;
                }
            }
        }
        return view('vendor.cannabisscore.nodes.777-powerscore-feedback', $this->v)->render();
    }
    
    protected function checkBadRecs()
    {
        $this->v["goodies"] = [
            776, 788, 878, 881, 884, 899, 901, 914, 915, 920, 922, 929, 930,  932, 934, 
            947, 958, 993,1140, 1235, 1364, 1365, 1390, 1396, 1427, 1449, 1477, 1492, 
            1498, 1503, 1505, 1506, 1605, 1634, 1696, 1714, 1771, 1829, 1840, 1869, 1879, 
            1894, 1917, 1919, 1955, 1959, 1978, 1986, 1993, 2033, 2040, 2056, 2170
        ];
        $this->initSearcher();
        $this->searcher->v["allscores"] = $added = [];
        $chk = DB::table('rii_powerscore')
            ->leftJoin('rii_ps_rankings', 'rii_ps_rankings.ps_rnk_psid', '=', 'rii_powerscore.ps_id')
            ->where('rii_ps_rankings.ps_rnk_filters', '')
            ->where('rii_powerscore.ps_status', 'LIKE', $this->v["defCmplt"])
            ->orderBy('rii_powerscore.ps_id', 'desc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ps) {
                if (!in_array($ps->ps_id, $added) && !in_array($ps->ps_id, $this->v["goodies"])) {
                    $this->searcher->v["allscores"][] = $ps;
                    $added[] = $ps->ps_id;
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
                $rows = DB::table('rii_powerscore')
                    ->join('rii_ps_rankings', function ($join) {
                        $join->on('rii_powerscore.ps_id', '=', 'rii_ps_rankings.ps_rnk_psid')
                            ->where('rii_ps_rankings.ps_rnk_filters', '');
                    })
                    ->where('rii_powerscore.ps_name', 'LIKE', '%' . $s . '%')
                    ->orWhere('rii_powerscore.ps_zip_code', 'LIKE', '%' . $s . '%')
                    ->orWhere('rii_powerscore.ps_county', 'LIKE', '%' . $s . '%')
                    ->orWhere('rii_powerscore.ps_email', 'LIKE', '%' . $s . '%')
                    ->orderBy('rii_powerscore.ps_name', 'asc')
                    ->get();
                $GLOBALS["SL"]->addSrchResults('name', $rows, 'ps_id');
            }
        }
        $GLOBALS["SL"]->getDumpSrchResultIDs($searches, 1);
        if (sizeof($searches) > 0) {
            foreach ($searches as $s) {
                $rows = DB::table('rii_powerscore')
                    ->join('rii_ps_rankings', function ($join) {
                        $join->on('rii_powerscore.ps_id', '=', 'rii_ps_rankings.ps_rnk_psid')
                            ->where('rii_ps_rankings.ps_rnk_filters', '');
                    })
                    ->whereIn('rii_powerscore.ps_id', $GLOBALS["SL"]->x["srchResDump"])
                    ->orderBy('rii_powerscore.ps_name', 'asc')
                    ->get();
                $GLOBALS["SL"]->addSrchResults('dump', $rows, 'ps_id');
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
        $cutoffDate = $grapher->getPastStartDate() . ' 00:00:00';
        $recentAttempts = RIIPowerscore::whereNotNull('ps_zip_code')
            ->where('ps_zip_code', 'NOT LIKE', '')
            ->where('created_at', '>=', $cutoffDate)
            ->select('ps_status', 'created_at')
            ->get();
        if ($recentAttempts->isNotEmpty()) {
            foreach ($recentAttempts as $i => $rec) {
                if ($rec->ps_status == $this->statusIncomplete) {
                    $grapher->addDayTally('incomplete', $rec->created_at);
                } elseif ($rec->ps_status == $this->statusComplete) {
                    $grapher->addDayTally('complete', $rec->created_at);
                } elseif ($rec->ps_status == $this->statusArchive) {
                    $grapher->addDayTally('archived', $rec->created_at);
                }
            }
        }
        return '<h5 class="slBlueDark">Recent PowerScore Submission Attempts</h5>'
            . $grapher->printDailyGraph();
    }
    
    protected function printAdminPsComms()
    {
        $comms = $adms = [];
        if (isset($this->sessData->dataSets["ps_communications"])) {
            $comms = $this->sessData->dataSets["ps_communications"];
            if (sizeof($comms) > 0) {
                foreach ($comms as $com) {
                    $adms[$com->ps_com_user] = $this
                        ->printUserLnk($com->ps_com_user);
                }
            }
        }
        return view(
            'vendor.cannabisscore.nodes.845-admin-communications-log', 
            [
                "nID"   => 845,
                "ps"    => $this->coreID,
                "comms" => $comms,
                "adms"  => $adms
            ]
        )->render();
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
            if ($request->has('logCommFld') 
                && trim($request->logCommFld) != '') {
                $com = new RIIPsCommunications;
                $com->ps_com_psid = $this->v["ps"];
                $com->ps_com_user = $this->v["uID"];
                $com->ps_com_description = trim($request->logCommFld);
                $com->save();
                $redir = '/calculated/read-' . $this->v["ps"];
                return $this->redir($redir, true);
            }
            return view(
                'vendor.cannabisscore.nodes.845-admin-communications-log-form', 
                $this->v
            )->render();
        }
        return '';
    }
    
    /**
     * Print admin overview tools to manage lighting manufacturers.
     *
     * @param int $nID
     * @return string
     */
    protected function printMgmtManufacturers($nID = -3)
    {
        $this->v["manus"] = RIIManufacturers::orderBy('ManuName', 'asc')
            ->get();
        $this->addManufacturers($nID);
        $this->cntManufacturerAdoption();
        $this->loadPartnerUsers();
        return view(
            'vendor.cannabisscore.nodes.914-manage-manufacturers', 
            $this->v
        )->render();
    }
    
    /**
     * Update manufacturer counts for how many rooms 
     * in the official data set use their lights.
     *
     * @return boolean
     */
    protected function cntManufacturerAdoption()
    {
        $this->v["errorNotes"] = '';
        if ($this->v["manus"]->isNotEmpty()) {
            foreach ($this->v["manus"] as $m => $manu) {
                $this->v["manus"][$m]->manu_cnt_flower 
                    = $this->v["manus"][$m]->manu_cnt_veg
                    = $this->v["manus"][$m]->manu_cnt_clone
                    = $this->v["manus"][$m]->manu_cnt_mother = 0;
            }
            $areaIDs = $areaIDsDone = [];
            $chk = RIIPsAreas::whereIn('ps_area_psid', function($query){
                    $query->select('ps_id')
                    ->from(with(new RIIPowerscore)->getTable())
                    ->where('ps_status', 243)
                    ->where('ps_effic_lighting_status', 243);
                })
                ->select('ps_area_id')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $area) {
                    $areaIDs[] = $area->ps_area_id;
                }
            }
            $lgts = DB::table('rii_ps_light_types')
                ->join('rii_ps_areas', 'rii_ps_areas.ps_area_id', 
                    '=', 'rii_ps_light_types.ps_lg_typ_area_id')
                ->where('rii_ps_light_types.ps_lg_typ_make', 'NOT LIKE', '')
                ->whereNotNull('rii_ps_light_types.ps_lg_typ_make')
                ->whereIn('rii_ps_light_types.ps_lg_typ_area_id', $areaIDs)
                ->select(
                    'rii_ps_light_types.ps_lg_typ_make', 
                    'rii_ps_areas.ps_area_type', 
                    'rii_ps_areas.ps_area_psid'
                )
                ->get();
            foreach ($this->v["manus"] as $m => $manu) {
                if ($lgts->isNotEmpty()) {
                    $this->cntManufacturerAdoptionOne($m, $lgts);
                }
                $this->v["manus"][$m]->save();
            }
        }
        return true;
    }
    
    /**
     * Load all partner users' info, and extended info.
     *
     * @return boolean
     */
    protected function loadPartnerUsers()
    {
        $this->v["partners"] = [];
        $chk = User::whereIn('id', function($query){
                $query->select('role_user_uid')
                ->from(with(new SLUsersRoles)->getTable())
                ->where('role_user_rid', 368); // partner def ID
            })
            ->select('id', 'name', 'email')
            ->orderBy('name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ind => $usr) {
                $this->v["partners"][$ind] = new ScoreUserInfo;
                $this->v["partners"][$ind]->loadUser($usr->id, $usr);
            }
        }
        $this->loadManufactIDs();
        return true;
    }
    
    /**
     * Count how many rooms in the official 
     * use this manufacturer's lights.
     *
     * @param  int $m
     * @param  array $lgts
     * @return boolean
     */
    protected function cntManufacturerAdoptionOne($m, $lgts)
    {
        $found = [
            'Flower' => [],
            'Veg'    => [],
            'Clone'  => [],
            'Mother' => []
        ];
        foreach($lgts as $lgt) {
            $nick = $this->getStageNick($lgt->ps_area_type);
            $pos = stripos(
                ' ' . $lgt->ps_lg_typ_make . ' ', 
                ' ' . $this->v["manus"][$m]->manu_name . ' '
            );
            if ($pos !== false
                && !in_array($lgt->ps_area_psid, $found[$nick])) {
                $found[$nick][] = $lgt->ps_area_psid;
            }
        }
        $stages = ['Flower', 'Veg', 'Clone', 'Mother'];
        foreach ($stages as $nick) {
            $this->v["manus"][$m]->{ 'manu_cnt_' . $nick } += sizeof($found[$nick]);
            $this->v["manus"][$m]->{ 'manu_ids_' . $nick } = ',' . implode(',', $found[$nick]) . ',';
        }
        return true;
    }
    
    /**
     * Admins can add manufacturers to the database.
     *
     * @param  int $nID
     * @return boolean
     */
    protected function addManufacturers($nID = -3)
    {
        if ($GLOBALS["SL"]->REQ->has('addManu') 
            && trim($GLOBALS["SL"]->REQ->get('addManu')) != '') {
            $lines = $GLOBALS["SL"]->mexplode("\n", $GLOBALS["SL"]->REQ->get('addManu'));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($line != '') {
                        $chk = RIIManufacturers::where('manu_name', 'LIKE', $line)
                            ->first();
                        if (!$chk || !isset($chk->manu_id)) {
                            $chk = new RIIManufacturers;
                            $chk->manu_name = $line;
                            $chk->save();
                        }
                    }
                }
            }
        } elseif ($GLOBALS["SL"]->REQ->has('partnerUser')
            && intVal($GLOBALS["SL"]->REQ->get('partnerUser')) > 0
            && $GLOBALS["SL"]->REQ->has('partnerManu') 
            && intVal($GLOBALS["SL"]->REQ->get('partnerManu')) > 0) {
            $usr = intVal($GLOBALS["SL"]->REQ->partnerUser);
            $manu = intVal($GLOBALS["SL"]->REQ->partnerManu);
            $chk = SLUsersRoles::where('role_user_uid', $usr)
                ->where('role_user_rid', 368) // partner def ID
                ->first();
            if ($chk) {
                $usrInfo = RIIUserInfo::where('usr_user_id', $usr)
                    ->first();
                if (!$usrInfo || !isset($usrInfo->usr_user_id)) {
                    $usrInfo = new RIIUserInfo;
                    $usrInfo->usr_user_id = $usr;
                }
                if ($GLOBALS["SL"]->REQ->has('partnerCompanyName')) {
                    $usrInfo->usr_company_name = trim($GLOBALS["SL"]->REQ->partnerCompanyName);
                }
                $usrInfo->save();
            }
            $chk = RIIUserManufacturers::where('usr_man_user_id', $usr)
                ->where('usr_man_manu_id', $manu)
                ->first();
            if (!$chk || !isset($chk->usr_man_manu_id)) {
                $chk = new RIIUserManufacturers;
                $chk->usr_man_user_id = $usr;
                $chk->usr_man_manu_id = $manu;
                $chk->save();
            }
        }
        return true;
    }
    
    /**
     * List lighting models by manufacturer.
     *
     * @param  int $nID
     * @return boolean
     */
    protected function printMgmtLightModels($nID = -3)
    {
        $this->loadManufactIDs();
        $this->v["models"] = $this->getAllLightModels();
        return view(
            'vendor.cannabisscore.nodes.917-manage-lighting-models', 
            $this->v
        )->render();
    }
    
    /**
     * Admins can add lighting models to the database.
     *
     * @param  int $nID
     * @return boolean
     */
    protected function addLightModels($nID = -3)
    {
        if ($GLOBALS["SL"]->REQ->has('addModels') 
            && trim($GLOBALS["SL"]->REQ->get('addModels')) != '') {
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
                            $manu = RIIManufacturers::where('manu_name', 'LIKE', $cols[0])
                                ->first();
                            if (!$manu || !isset($manu->manu_name)) {
                                $manu = new RIIManufacturers;
                                $manu->manu_name = $cols[0];
                                $manu->save();
                            }
                            $chk = RIILightModels::where('lgt_mod_manu_id', 'LIKE', $manu->manu_id)
                                ->where('lgt_mod_name', 'LIKE', $cols[1])
                                ->first();
                            if (!$chk || !isset($chk->lgt_mod_id)) {
                                $chk = new RIILightModels;
                                $chk->lgt_mod_manu_id = $manu->manu_id;
                                $chk->lgt_mod_name = $cols[1];
                            }
                            $chk->lgt_mod_tech = $cols[2];
                            $chk->save();
                        }
                    }
                }
            }
        }
        return true;
    }
    
    public function printPartnerProfileDashHead($nID)
    {
        $title = 'Partner Members Dashboard';
        $company = $this->getPartnerCompany();
        if (trim($company) != '') {
            $title = $company . ' Dashboard';
        }
        $ret = '<div class="pL15 pR15"><h2>' . $title . '</h2>';
        if (isset($this->v["usrInfo"])) {
            if (isset($this->v["usrInfo"]->manufacturers)
                && sizeof($this->v["usrInfo"]->manufacturers) > 0) {
                foreach ($this->v["usrInfo"]->manufacturers as $manu) {
                    $ret .= '<div class="row mT10">
                            <div class="col-6">
                                <a href="/dash/competitive-performance?manu=' 
                                    . urlencode($manu->manu_name) . '" '
                                    . 'class="btn btn-xl btn-primary btn-block">' 
                                    . $manu->manu_name . ' Competitive Performance</a>
                            </div><div class="col-6 pT10">
                                <p>Compare the competitive advantage of 
                                growers who use <b>' . $manu->manu_name 
                                . '</b> during at least one growth stage.</p>
                            </div>
                        </div>';
                }
            } else {
                $ret .= '<div class="row mT10">
                        <div class="col-6">
                            <a href="/dash/competitive-performance" '
                                . 'class="btn btn-xl btn-primary btn-block"'
                                . '>Competitive Performance Report</a>
                        </div><div class="col-6 pT10">
                            <p>Compare the competitive advantage of 
                            growers who use a partner\'s equipment during at least one growth stage. (Demo)</p>
                        </div>
                    </div>';
            }
            if ($company != '') {
                $ret .= '<p>&nbsp;</p><div class="row mT10">
                    <div class="col-6">
                        <a href="/dash/partner-compare-powerscores" 
                            class="btn btn-xl btn-primary btn-block"
                            >' . $company . ' Individual Scores</a>
                    </div><div class="col-6 pT10">
                        <p>List <b>all of your</b> individual PowerScores with sub-score
                        averages, plus dozens of different filter options.</p>
                    </div>
                </div>';
            }
        }
        return $ret . '</div>';
    }
    
    protected function tmpDebug($str = '')
    {
        $tmp = ' - tmpDebug - ' . $str;
        $chk = RIIPsAreas::where('ps_area_psid', 169)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $row) {
                $tmp .= ', ' . $row->getKey();
            }
        }
        echo $tmp . '<br />';
        return true;
    }
}