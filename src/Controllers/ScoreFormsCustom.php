<?php
/**
  * ScoreFormsCustom is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains functions used to override survey nodes.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPsGrowingRooms;
use CannabisScore\Controllers\ScoreCondsCustom;

class ScoreFormsCustom extends ScoreCondsCustom
{
    
    protected function postZipCode($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('n' . $nID . 'fld') 
            && trim($GLOBALS["SL"]->REQ->get('n' . $nID . 'fld')) != '') {
            $this->sessData->updateZipInfo(
                $GLOBALS["SL"]->REQ->get('n' . $nID . 'fld'), 
                'powerscore', 
                'ps_state', 
                'ps_county', 
                'ps_ashrae', 
                'ps_country'
            );
        }
        return true;
    }
    
    protected function postRoomCnt($nID)
    {
        $roomCnt = $currCnt = 0;
        if ($GLOBALS["SL"]->REQ->has('n' . $nID . 'fld') 
            && intVal($GLOBALS["SL"]->REQ->get('n' . $nID . 'fld')) > 0) {
            $roomCnt = intVal($GLOBALS["SL"]->REQ->get('n' . $nID . 'fld'));
        }
        if (isset($this->sessData->dataSets["ps_growing_rooms"])) {
            $currCnt = sizeof($this->sessData->dataSets["ps_growing_rooms"]);
        }
        if ($roomCnt > $currCnt) {
            for ($i = 0; $i < ($roomCnt-$currCnt); $i++) {
                $newRoom = new RIIPsGrowingRooms;
                $newRoom->ps_room_psid = $this->coreID;
                $newRoom->save();
                $this->sessData->dataSets["ps_growing_rooms"][] = $newRoom;
            }
        } elseif ($roomCnt < $currCnt) {
            for ($i = ($currCnt-1); $i >= $roomCnt; $i--) {
                $this->sessData->dataSets["ps_growing_rooms"][$i]->delete();
            }
        }
        $this->sessData->refreshDataSets();
        return false;
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
        $min = 4;
        $max = 81;
        if (isset($area->ps_area_lgt_sun) && intVal($area->ps_area_lgt_sun) == 1) {

        }
        if ($fixCnt == 0) {
            $ret = 'No lights were added for your <b>' . strtolower($typ) . ' stage</b>.';
        /*
        } elseif ($area->ps_area_sq_ft_per_fix2 < $min) {
            $ret = 'You only listed ' . $fixCnt . ' lighting fixture' 
                . (($fixCnt == 1) ? '' : 's') . ' in your ' . strtolower($typ) . ' stage. '
                . round($area->ps_area_sq_ft_per_fix2) . ' square feet per fixture is very low. ';
        } elseif ($area->ps_area_sq_ft_per_fix2 > $max) {
            $ret = 'You listed ' . $fixCnt . ' lighting fixtures in your ' 
                . strtolower($typ) . ' stage. ' . round($area->ps_area_sq_ft_per_fix2)
                . ' square feet per fixture is very high. ';
        */
        }
        return $ret;
    }




    // MA-specific functions

    protected function maMonthTblLoad()
    {
        if (!isset($this->v["psMonths"])) {
            $this->v["psMonths"] = [];
            if (isset($this->sessData->dataSets["ps_monthly"])
                && sizeof($this->sessData->dataSets["ps_monthly"]) > 0) {
                foreach ($this->sessData->dataSets["ps_monthly"] as $mon) {
                    if (isset($mon->ps_month_month) && intVal($mon->ps_month_month) > 0) {
                        $this->v["psMonths"][$mon->ps_month_month] = $mon;
                    }
                }
            }
        }
        return $this->v["psMonths"];
    }

    protected function maMonthTblElectric($nID)
    {
        $this->maMonthTblLoad();
        return view(
            'vendor.cannabisscore.nodes.1103-ma-month-table-electric', 
            [
                "nID"      => $nID,
                "months"   => $this->v["psMonths"],
                "dataSets" => $this->sessData->dataSets,
                "nKWH"     => 1106,
                "nKW"      => 1107
            ]
        )->render();
    }

    protected function maMonthTblDelivered($nID)
    {
        $this->maMonthTblLoad();
        return view(
            'vendor.cannabisscore.nodes.1121-ma-month-table-delivered', 
            [
                "nID"      => $nID,
                "months"   => $this->v["psMonths"],
                "dataSets" => $this->sessData->dataSets,
                "nKWH"     => 1122,
                "nKW"      => 1123
            ]
        )->render();
    }

    protected function maMonthTblWater($nID)
    {
        $this->maMonthTblLoad();
        return view(
            'vendor.cannabisscore.nodes.1122-ma-month-table-water', 
            [
                "nID"      => $nID,
                "months"   => $this->v["psMonths"],
                "dataSets" => $this->sessData->dataSets,
                "nKWH"     => 1124
            ]
        )->render();
    }

    protected function maMonthTblRenew($nID)
    {
        $this->maMonthTblLoad();
        return view(
            'vendor.cannabisscore.nodes.1123-ma-month-table-renew', 
            [
                "nID"      => $nID,
                "months"   => $this->v["psMonths"],
                "dataSets" => $this->sessData->dataSets,
                "nKWH"     => 1125
            ]
        )->render();
    }

}
