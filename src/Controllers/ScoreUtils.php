<?php
/**
  * ScoreUtils is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of simplest PowerScore-specific utilities functions like
  * looking up common definition lists, translating single PowerScores, and prepping various proccesses.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsRenewables;
use App\Models\RIIPsMonthly;
use App\Models\RIIPsForCup;
use App\Models\RIIComplianceMa;
use App\Models\RIIComplianceMaMonths;
use App\Models\RIIComplianceMaFuels;
use App\Models\RIIComplianceMaRenewables;
use App\Models\RIIPsOnsiteFuels;
use App\Models\SLZips;
use CannabisScore\Controllers\ScorePowerUtilities;

class ScoreUtils extends ScorePowerUtilities
{   
    
    public function multiRecordCheckIntro($cnt = 1)
    {
        return '<p>&nbsp;</p><h4>You Have ' 
            . (($cnt == 1) 
                ? 'An Unfinished PowerScore' 
                : 'Unfinished PowerScores')
            . '</h4>';
    }
    
    public function multiRecordCheckRowTitle($coreRecord)
    {
        return 'PowerScore #' . $coreRecord[1]->getKey();
    }
    
    public function multiRecordCheckRowSummary($coreRecord)
    {
        return '<div class="mT5 mB5 slGrey">Last Edited: ' 
            . date('n/j/y, g:ia', strtotime($coreRecord[1]->updated_at)) 
            . '<br />Percent Complete: ' 
            . $this->rawOrderPercent($coreRecord[1]->ps_submission_progress) . '%</div>';
    }
    
    protected function checkScore()
    {
    	if (isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
    		if (isset($ps->ps_zip_code)) {
    			$this->sessData->updateZipInfo(
                    $ps->ps_zip_code, 
    				'powerscore', 
                    'ps_state', 
                    'ps_county', 
                    'ps_ashrae', 
                    'ps_country'
                );
            }
		}
		return true;
	}
    
    protected function firstPageChecks()
    {
        if (!isset($this->sessData->dataSets["powerscore"]) 
            || !isset($this->sessData->dataSets["powerscore"][0])) {
            return false;
        }
        if ($GLOBALS["SL"]->REQ->has('time') 
            && trim($GLOBALS["SL"]->REQ->get('time')) != '') {
            $this->sessData->dataSets["powerscore"][0]
                ->ps_time_type = intVal($GLOBALS["SL"]->REQ->get('time'));
            $this->sessData->dataSets["powerscore"][0]->save();
        } elseif (!isset($this->sessData->dataSets["powerscore"][0]->ps_time_type)
            || intVal($this->sessData->dataSets["powerscore"][0]->ps_time_type) <= 0) {
            $this->sessData->dataSets["powerscore"][0]->ps_time_type 
                = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
            $this->sessData->dataSets["powerscore"][0]->save();
        }
        if ($GLOBALS["SL"]->REQ->has('go') 
            && trim($GLOBALS["SL"]->REQ->get('go')) == 'pro') {
            $this->sessData->dataSets["powerscore"][0]->ps_is_pro = 1;
            $this->sessData->dataSets["powerscore"][0]->save();
        } elseif (!isset($this->sessData->dataSets["powerscore"][0]->ps_is_pro)) {
            $this->sessData->dataSets["powerscore"][0]->ps_is_pro = 0;
            $this->sessData->dataSets["powerscore"][0]->save();
        }
        $this->firstPageChecksCups();
        $this->firstPageChecksCopyMa();
        return true;
    }

    /**
     * Initializing extra things for special admin pages.
     *
     * @param  Illuminate\Http\Request  $request
     * @return boolean
     */
    protected function constructorExtra()
    {
        $this->runPowerScoreChecks();
        return true;
    }
    
    /**
     * Run any validation and cleanup needed specific to PowerScore.
     *
     * @return boolean
     */
    protected function runPowerScoreChecks()
    {
        if (!isset($this->v["uID"]) 
            || $this->v["uID"] <= 0 
            || !isset($this->v["user"])
            || !$this->v["user"]->hasRole('administrator|staff')) {
            return false;
        }
        if (!session()->has('powerChks') 
                || !session()->get('powerChks') 
                || $GLOBALS["SL"]->REQ->has('refresh')) {
            $cutoff = mktime(date("H"), date("i"), date("s"), 
                date("n"), date("j")-14, date("Y"));
            $cutoff = date("Y-m-d H:i:s", $cutoff);
            $this->clearEmptyPowerScores($cutoff);
            $this->clearEmptyComply($cutoff);
            $this->clearLostSessionHelpers();
            session()->put('powerChks', true);
        }
        return true;
    }
    
    /**
     * Clean out old PowerScores.
     *
     * @return boolean
     */
    protected function clearEmptyPowerScores($cutoff)
    {
        DB::select(DB::raw(
            "DELETE FROM `rii_powerscore` 
            WHERE `created_at` < '" . $cutoff . "'
                AND `ps_status` LIKE '" . $this->statusIncomplete . "'
                AND (`ps_user_id` IS NULL OR `ps_user_id` <= 0)
                AND (`ps_zip_code` IS NULL OR `ps_zip_code` LIKE '')
            LIMIT 2000"
        ));
        DB::select(DB::raw(
            "DELETE FROM `sl_sess` 
            WHERE `sl_sess`.`sess_tree` = 1
                AND `sl_sess`.`sess_core_id` NOT IN 
                    (SELECT `rii_powerscore`.`ps_id` FROM `rii_powerscore`)"
        ));
        DB::select(DB::raw(
            "DELETE FROM `rii_ps_monthly` 
            WHERE `rii_ps_monthly`.`ps_month_psid` NOT IN 
                (SELECT `rii_powerscore`.`ps_id` FROM `rii_powerscore`)"
        ));
        return true;
    }
    
    /**
     * Clean out old compliance records.
     *
     * @return boolean
     */
    protected function clearEmptyComply($cutoff)
    {
        DB::select(DB::raw(
            "DELETE FROM `rii_compliance_ma` 
            WHERE `created_at` < '" . $cutoff . "'
                AND (`com_ma_user_id` IS NULL OR `com_ma_user_id` <= 0)
                AND (`com_ma_postal_code` IS NULL OR `com_ma_postal_code` LIKE '')
            LIMIT 2000"
        ));
        DB::select(DB::raw(
            "DELETE FROM `sl_sess` 
            WHERE `sl_sess`.`sess_tree` = 71
                AND `sl_sess`.`sess_core_id` NOT IN 
                    (SELECT `rii_compliance_ma`.`com_ma_id` FROM `rii_compliance_ma`)"
        ));
        DB::select(DB::raw(
            "DELETE FROM `rii_compliance_ma_months` 
            WHERE `rii_compliance_ma_months`.`com_ma_month_com_ma_id` NOT IN 
                (SELECT `rii_compliance_ma`.`com_ma_id` FROM `rii_compliance_ma`)"
        ));
        return true;
    }
    
    protected function firstPageChecksCups()
    {
        if ($GLOBALS["SL"]->REQ->has('cups') 
            && trim($GLOBALS["SL"]->REQ->get('cups')) != '') {
            $cupsIn = urldecode($GLOBALS["SL"]->REQ->get('cups'));
            $cupsIn = $GLOBALS["SL"]->mexplode(',', $cupsIn);
            $cupList = $GLOBALS["SL"]->def->getSet('PowerScore Competitions');
            if (sizeof($cupList) > 0) {
                foreach ($cupList as $c) {
                    if (in_array($c->def_id, $cupsIn)) {
                        $chk = RIIPsForCup::where('ps_cup_psid', $this->coreID)
                            ->where('ps_cup_cup_id', $c->def_id)
                            ->first();
                        if (!$chk || !isset($chk->ps_cup_cup_id)) {
                            $chk = new RIIPsForCup;
                            $chk->ps_cup_psid  = $this->coreID;
                            $chk->ps_cup_cup_id = $c->def_id;
                            $chk->save();
                        }
                    } else {
                        RIIPsForCup::where('ps_cup_psid', $this->coreID)
                            ->where('ps_cup_cup_id', $c->def_id)
                            ->delete();
                    }
                }
            }
        }
        return true;
    }
    
    protected function firstPageChecksCopyMa()
    {
        if ($GLOBALS["SL"]->REQ->has('cpyMa') 
            && trim($GLOBALS["SL"]->REQ->get('cpyMa')) != '') {
            $ps = $this->sessData->dataSets["powerscore"][0];
            $psOn = $this->sessData->dataSets["ps_onsite"][0];
            $ps->ps_is_pro = 1;
            $ps->ps_time_type 
                = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
            $copyMa = trim($GLOBALS["SL"]->REQ->get('cpyMa'));
            $copyMa = $GLOBALS["SL"]->mexplode('-', $copyMa);
            if (sizeof($copyMa) == 2) {
                $ma = RIIComplianceMa::where('com_ma_id', $copyMa[0])
                    ->where('com_ma_unique_str', $copyMa[1])
                    ->first();
                if ($ma && isset($ma->com_ma_id)) {
                    $this->firstPageChecksCopyMaCore($ps, $ma);
                    $idFld = 'com_ma_month_com_ma_id';
                    $maMonths = RIIComplianceMaMonths::where($idFld, $ma->com_ma_id)
                        ->get();
                    if ($maMonths && sizeof($maMonths) > 0) {
                        $psMonths = $this->sortMonths();
                        foreach ($maMonths as $maMon) {
                            foreach ($psMonths as $psMon) {
                                if ($psMon->ps_month_month == $maMon->com_ma_month_month) {
                                    $this->firstPageChecksCopyMaMonth($psMon, $maMon);
                                    if (isset($maMon->com_ma_month_renew_kwh)
                                        && intVal($maMon->com_ma_month_renew_kwh) > 0) {
                                        $ps->ps_kwh_include_renewables = 1;
                                    }
                                    if (isset($maMon->com_ma_month_water)
                                        && intVal($maMon->com_ma_month_water) > 0) {
                                        $psOn->ps_on_water_by_months = 1;
                                    }
                                }
                            }
                        }
                    }
                    $chk = RIIPsOnsiteFuels::where('ps_fuel_ps_id', $ma->com_ma_ps_id)
                        ->get();
                    $maFuels = RIIComplianceMaFuels::where('com_ma_fuel_com_ma_id', $ma->com_ma_id)
                        ->get();
                    if ($maFuels && sizeof($maFuels) > 0) {
                        foreach ($maFuels as $maFuel) {
                            $found = false;
                            if ($chk && sizeof($chk) > 0) {
                                foreach ($chk as $psFuel) {
                                    if (isset($psFuel->ps_fuel_type)
                                        && $psFuel->ps_fuel_type == $maFuel->com_ma_fuel_type) {
                                        $found = true;
                                    }
                                }
                            }
                            if (!$found) {
                                $psFuel = new RIIPsOnsiteFuels;
                                $psFuel->ps_fuel_ps_id = $ps->ps_id;
                                $psFuel->ps_fuel_type = $maFuel->com_ma_fuel_type;
                                $psFuel->save();
                            }
                        }
                    }

                    $chk = RIIPsRenewables::where('ps_rnw_psid', $ma->com_ma_ps_id)
                        ->get();
                    $renews = RIIComplianceMaRenewables::where('com_ma_rnw_com_ma_id', $ma->com_ma_id)
                        ->get();
                    if ($renews && sizeof($renews) > 0) {
                        foreach ($renews as $renew) {
                            $found = false;
                            if ($chk && sizeof($chk) > 0) {
                                foreach ($chk as $psRenew) {
                                    if (isset($psRenew->ps_rnw_renewable)
                                        && $psRenew->ps_rnw_renewable == $renew->com_ma_rnw_renewable) {
                                        $found = true;
                                    }
                                }
                            }
                            if (!$found) {
                                $psRenew = new RIIPsRenewables;
                                $psRenew->ps_rnw_psid = $ps->ps_id;
                                $psRenew->ps_rnw_renewable = $renew->com_ma_rnw_renewable;
                                $psRenew->save();
                            }
                        }
                    }
                }
            }
            $ps->save();
            $psOn->save();
            $this->sessData->dataSets["powerscore"][0] = $ps;
        }
        return true;
    }
    
    protected function firstPageChecksCopyMaCore(&$ps, &$ma)
    {
        $ma->com_ma_ps_id          = $ps->ps_id;
        $ma->save();
        $ps->ps_com_ma_id          = $ma->com_ma_id;
        $ps->ps_year               = $ma->com_ma_year;
        $ps->ps_start_month        = $ma->com_ma_start_month;
        $ps->ps_name               = $ma->com_ma_name;
        $ps->ps_zip_code           = $ma->com_ma_postal_code;
        $ps->ps_flower_weight_type = $ma->com_ma_flower_weight_type;
        $ps->ps_grams              = $ma->com_ma_grams;
        $ps->ps_kwh                = $ma->com_ma_tot_kwh;
        $ps->ps_tot_kw_peak        = $ma->ps_tot_kw_peak;
        $ps->ps_no_natural_gas     = $ma->com_ma_no_natural_gas;
        $ps->ps_unit_natural_gas   = $ma->com_ma_unit_natural_gas;
        $ps->ps_unit_wood          = $ma->com_ma_unit_wood;
        $ps->ps_unit_generator     = $ma->com_ma_unit_generator;
        if (isset($ma->com_ma_no_renewable_electricity)) {
            if (intVal($ma->com_ma_no_renewable_electricity) == 1) {
                $ps->ps_source_renew = 0;
            } else {
                $ps->ps_source_renew = 1;
            }
        }
        $ps->save();
        return true;
    }
    
    protected function firstPageChecksCopyMaMonth($psMon, $maMon)
    {
        $psMon->ps_month_kwh1          = $maMon->com_ma_month_kwh;
        $psMon->ps_month_kw            = $maMon->com_ma_month_kw;
        $psMon->ps_month_kwh_renewable = $maMon->com_ma_month_renew_kwh;
        $psMon->ps_month_natural_gas   = $maMon->com_ma_month_natural_gas_therms;
        $psMon->ps_month_generator     = $maMon->com_ma_month_diesel_gallons;
        $psMon->ps_month_biofuel_wood  = $maMon->com_ma_month_biofuel_wood_tons;
        $psMon->ps_month_propane       = $maMon->com_ma_month_propane;
        $psMon->ps_month_fuel_oil      = $maMon->com_ma_month_fuel_oil;
        $psMon->ps_month_water         = $maMon->com_ma_month_water;
        $psMon->save();
        return true;
    }
    
    protected function loadAreaLgtTypes()
    {
        $this->v["fltATs"] = [];
        if (isset($this->sessData->dataSets["ps_areas"]) 
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && $area->ps_area_type != $this->v["areaTypes"]["Dry"]) {
                    $this->v["fltATs"][$area->ps_area_type] = [
                        "hvc" => 0, 
                        "lgt" => 0
                    ];
                    if (isset($area->ps_area_hvac_type)) {
                        $this->v["fltATs"][$area->ps_area_type]["hvac"] = $area->ps_area_hvac_type;
                    }
                    $lgts = $this->getAreaLights($area->ps_area_id);
                    if ($lgts && sizeof($lgts) > 0) {
                        
                        //sessData->
                    }
                }
            }
        }
        return true;
    }
    
    public function hasRooms()
    {
        $this->v["hasRooms"] = (isset($this->sessData->dataSets["ps_growing_rooms"]) 
            && sizeof($this->sessData->dataSets["ps_growing_rooms"]) > 0);
        return $this->v["hasRooms"];
    }
    
    protected function getRoomIDs()
    {
        if (!isset($this->v["roomIDs"])) {
            $this->v["roomIDs"] = [];
            if ($this->hasRooms()) {
                foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
                    $this->v["roomIDs"][] = $room->ps_room_id;
                }
            }
        }
        return $this->v["roomIDs"];
    }
    
    protected function getLookupRoomIDs()
    {
        if (!isset($this->v["roomsByID"])) {
            $this->v["roomsByID"] = [];
            if ($this->hasRooms()) {
                foreach ($this->sessData->dataSets["ps_growing_rooms"] as $room) {
                    $this->v["roomsByID"][$room->ps_room_id] = $room;
                }
            }
        }
        return $this->v["roomsByID"];
    }
    
    protected function loadRoomIndsByStage()
    {
        if (!isset($this->v["stageRooms"])) {
            $this->v["stageRooms"] = [];
            foreach ($this->v["areaTypes"] as $typ => $defID) {
                $this->v["stageRooms"][$typ] = [];
            }
            if ($this->hasRooms()) {
                foreach ($this->sessData->dataSets["ps_growing_rooms"] as $roomInd => $room) {
                    $areas = $this->sessData->getChildRows(
                        'ps_growing_rooms', 
                        $room->ps_room_id, 
                        'ps_link_room_area'
                    );
                    foreach ($areas as $areaLnk) {
                        $area = $this->sessData->getRowById(
                            'ps_areas', 
                            $areaLnk->ps_lnk_rm_ar_area_id
                        );
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->ps_area_type == $defID) {
                                $this->v["stageRooms"][$typ][] = $roomInd;
                            }
                        }
                    }
                }
            }
        }
        return $this->v["stageRooms"];
    }
    
    protected function getRoomIndLightHours($roomInd)
    {
        $this->loadRoomIndsByStage();
        if (isset($this->v["stageRooms"]["Flower"]) 
            && in_array($roomInd, $this->v["stageRooms"]["Flower"])) {
            return 12;
        }
        return 18;
    }
    
    protected function loadRoomLights()
    {
        if (!isset($this->v["roomLights"])) {
            $this->v["roomLights"] = [];
            if (isset($this->sessData->dataSets["ps_light_types"]) 
                && sizeof($this->sessData->dataSets["ps_light_types"]) > 0) {
                foreach ($this->sessData->dataSets["ps_light_types"] as $lgt) {
                    if (isset($lgt->ps_lg_typ_room_id) 
                        && intVal($lgt->ps_lg_typ_room_id) > 0) {
                        if (!isset($this->v["roomLights"][$lgt->ps_lg_typ_room_id])) {
                            $this->v["roomLights"][$lgt->ps_lg_typ_room_id] = [];
                        }
                        $ind = 0;
                        if (isset($lgt->ps_lg_typ_room_ord) 
                            && intVal($lgt->ps_lg_typ_room_ord) >= 0) {
                            $ind = intVal($lgt->ps_lg_typ_room_ord);
                        }
                        $this->v["roomLights"][$lgt->ps_lg_typ_room_id][$ind] = $lgt;
                    }
                }
            }
        }
        return $this->v["roomLights"];
    }
    
    protected function customLabels($nIDtxt = '', $str = '')
    {
        // Temporary for 3.0 mock-ups
        if (strpos($str, '100 sf of Vegetation space') !== false) {
            $area = $this->sessData->getDataBranchRow('ps_areas');
            $areaLab = $this->getLoopItemLabelCustom('Growth Stages', $area);
            $areaLab = str_replace(' Plants', '', $areaLab);
            $areaSq = number_format($area->ps_area_size);
            $swap = $areaSq . ' sf of <span class="txtInfo">' . $areaLab . ' space</span>';
            $str = str_replace('100 sf of Vegetation space', $swap, $str);
        } elseif ($nIDtxt == '1088res1') {
            $str = str_replace('HVAC System 0', 'HVAC System A', $str);
        } elseif ($nIDtxt == '1088res2') {
            $str = str_replace('HVAC System 0', 'HVAC System B', $str);
        } elseif ($nIDtxt == '1088res3') {
            $str = str_replace('HVAC System 0', 'HVAC System C', $str);
        } elseif ($nIDtxt == '1088res4') {
            $str = str_replace('HVAC System 0', 'HVAC System D', $str);
        } elseif ($nIDtxt == '1088res5') {
            $str = str_replace('HVAC System 0', 'HVAC System E', $str);
        } elseif ($nIDtxt == '1088res6') {
            $str = str_replace('HVAC System 0', 'HVAC System F', $str);
        } elseif ($nIDtxt == '1088res7') {
            $str = str_replace('HVAC System 0', 'HVAC System G', $str);
        } elseif ($nIDtxt == '1088res8') {
            $str = str_replace('HVAC System 0', 'Other HVAC System', $str);
        }
        return $str;
    }
    
    protected function customCleanLabel($str = '', $nIDtxt = '')
    {
        if ($this->treeID == 1) {
            if (isset($this->sessData->dataSets["powerscore"]) 
                && isset($this->sessData->dataSets["powerscore"][0]->ps_time_type)
                && $this->sessData->dataSets["powerscore"][0]->ps_time_type 
                    == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                $str = str_replace('Does your', 'Will your', 
                    str_replace('does your', 'will your', $str));
                $str = str_replace('do you ', 'will you ', $str);
            }
        }
        return $str; 
    }
    
    protected function getLoopItemLabelCustom($loop, $itemRow = [], $itemInd = -3)
    {
        if (in_array($loop, [ 'Growth Stages', 'Harvest Stages', 'Four Growth Stages' ])) {
            return $this->getStageName($itemRow, $itemInd);
        } elseif (in_array($loop, ['Room Nicknames', 'Room Environments'])) {
            return $this->getRoomName($itemRow, $itemInd);
        } elseif ($loop == 'Types of Room Light Fixtures') {
            return $this->getRoomLightDesc($loop, $itemRow, $itemInd);
        } elseif ($loop == 'Types of Light Fixtures') {
            return $this->getLightDesc($loop, $itemRow, $itemInd, true);
        }
        return '';
    }
    
    private function getStageName($itemRow = [], $itemInd = 0)
    {
        switch (intVal($itemRow->ps_area_type)) {
            case 237: return 'Mother Plants';          break; // to be phased out
            case 160: return 'Clone or Mother Plants'; break;
            case 161: return 'Vegetating Plants';      break;
            case 162: return 'Flowering Plants';       break;
            case 163: return 'Drying / Curing';        break;
        }
        return '';
    }
    
    protected function getRoomName($itemRow = [], $itemInd = 0)
    {
        if ($itemInd < 0) {
            $itemInd = 0;
        }
        $ret = 'Room #' . (1+$itemInd);
        if ($itemRow 
            && isset($itemRow->ps_room_name) 
            && trim($itemRow->ps_room_name) != '') {
            return trim($itemRow->ps_room_name) . ' (' . $ret . ')';
        }
//echo '<br /><br /><br />getRoomName(' . $itemInd . ' - ' . $ret . '<br />';
        return $ret;
    }
    
    private function getLightDesc($loop, $itemRow = [], $itemInd = 0, $headers = false)
    {
        $ret = '';
        if ($itemRow && isset($itemRow->ps_lg_typ_id)) {
            $hasKeyDeets = 0;
            if (isset($itemRow->ps_lg_typ_area_id) 
                && intVal($itemRow->ps_lg_typ_area_id) > 0) {
                $ret .= '<b>' . $this->getAreaIdTypeName($itemRow->ps_lg_typ_area_id)
                    . ':</b> ';
            } else {
                $ret .= 'Light Type #' . (1+$itemInd) . ': ';
            }
            if (isset($itemRow->ps_lg_typ_count) 
                && trim($itemRow->ps_lg_typ_count) != '') {
                $ret .= number_format($itemRow->ps_lg_typ_count) . ' fixtures, ';
                $hasKeyDeets++;
            }
            if (isset($itemRow->ps_lg_typ_wattage) 
                && trim($itemRow->ps_lg_typ_wattage) != '') {
                $ret .= number_format($itemRow->ps_lg_typ_wattage) . 'W each';
                $hasKeyDeets++;
            }
            if ($headers) {
                $ret .= '</h4>';
            } elseif ($hasKeyDeets > 0) {
                $ret .= ', ';
            }
            if (isset($itemRow->ps_lg_typ_light) 
                && intVal($itemRow->ps_lg_typ_light) > 0) {
                $ret .= $GLOBALS["SL"]->def->getVal(
                    'PowerScore Light Types', 
                    $itemRow->ps_lg_typ_light
                );
                if ((isset($itemRow->ps_lg_typ_make) 
                        && trim($itemRow->ps_lg_typ_make) != '') 
                    || (isset($itemRow->ps_lg_typ_model) 
                        && trim($itemRow->ps_lg_typ_model) != '')) {
                    $ret .= ', ';
                }
            }
            if (isset($itemRow->ps_lg_typ_make) 
                && trim($itemRow->ps_lg_typ_make) != '') {
                $ret .= $itemRow->ps_lg_typ_make;
                if (isset($itemRow->ps_lg_typ_model) 
                    && trim($itemRow->ps_lg_typ_model) != '') {
                    $ret .= ', ';
                }
            }
            if (isset($itemRow->ps_lg_typ_model) 
                && trim($itemRow->ps_lg_typ_model) != '') {
                $ret .= $itemRow->ps_lg_typ_model;
            }
            if ($headers) {
                $ret .= '<h4 class="disNon">';
            }
            return $ret;
        }
    }
    
    private function getRoomLightDesc($loop, $itemRow = [], $itemInd = 0)
    {
        $ret = '';
        if (isset($itemRow->ps_lg_typ_room_id) 
            && intVal($itemRow->ps_lg_typ_room_id) > 0) {
            $roomID = intVal($itemRow->ps_lg_typ_room_id);
            $roomRow = $this->sessData->getRowById('ps_growing_rooms', $roomID);
            $roomInd = $this->sessData->getRowInd('ps_growing_rooms', $roomID);
            $ret = $this->getRoomName($roomRow, $roomInd);
            $lgtDesc = trim($this->getLightDesc($loop, $itemRow, $itemInd));
            if ($lgtDesc != '') {
                $ret .= ': ' . $lgtDesc;
            }
            if (!isset($itemRow->ps_lg_typ_count) 
                || intVal($itemRow->ps_lg_typ_count) <= 0
                || !isset($itemRow->ps_lg_typ_wattage) 
                || intVal($itemRow->ps_lg_typ_wattage) <= 0) {
                $ret .= ' <nobr><span class="red">To Do</span></nobr>';
            }
        }
        return $ret;
    }

    protected function getRenewableDefs()
    {
        $defSet = 'PowerScore Onsite Power Sources';
        return [
            $GLOBALS["SL"]->def->getID($defSet, 'Solar PV'),
            $GLOBALS["SL"]->def->getID($defSet, 'Wind'),
            $GLOBALS["SL"]->def->getID($defSet, 'Other renewable'),
            $GLOBALS["SL"]->def->getID($defSet, 'Geothermal'),
            $GLOBALS["SL"]->def->getID($defSet, 'Direct use geothermal'),
            $GLOBALS["SL"]->def->getID($defSet, 'Pelton wheel')
        ];
    }
    
    protected function nIDgetRenew($nID)
    {
        $defSet = 'PowerScore Renewables';
        switch ($nID) {
            case 59:
            case 78: return $GLOBALS["SL"]->def->getID($defSet, 'Solar PV');
            case 80: return $GLOBALS["SL"]->def->getID($defSet, 'Wind');
            case 61: return $GLOBALS["SL"]->def->getID($defSet, 'Biomass');
            case 60: return $GLOBALS["SL"]->def->getID($defSet, 'Geothermal');
            case 81: return $GLOBALS["SL"]->def->getID($defSet, 'Pelton Wheel');
        }
        return -3;
    }

    protected function getLookupLgtNicknames()
    {
        $this->getLookupAreaIDs();
        if (!isset($this->v["lgtNicknames"])) {
            $this->v["lgtNicknames"] 
                = $this->v["lgtHours"] 
                = $this->v["lgtTotKwh"] 
                = [];
            if (!isset($this->sessData->dataSets["ps_light_types"])) {
                return $this->v["lgtNicknames"];
            }
            foreach ($this->sessData->dataSets["ps_light_types"] as $l => $lgt) {
                $this->v["lgtNicknames"][$lgt->ps_lg_typ_id] = 'Light #' . (1+$l);
                $this->v["lgtTotKwh"][$lgt->ps_lg_typ_id] = 0;
                if (isset($lgt->ps_lg_typ_area_id) 
                    && intVal($lgt->ps_lg_typ_area_id) > 0
                    && isset($this->v["areasByID"][$lgt->ps_lg_typ_area_id])
                    && isset($this->v["areasByID"][$lgt->ps_lg_typ_area_id]->ps_area_type)) {
                    $areaType = $this->v["areasByID"][$lgt->ps_lg_typ_area_id]->ps_area_type;
                    $areaAbbr = $this->getAreaAbbr($areaType);
                    $this->v["lgtNicknames"][$lgt->ps_lg_typ_id] .= ' ' . $areaAbbr;
                    $this->v["lgtHours"][$lgt->ps_lg_typ_id] = 18;
                    if ($areaAbbr == 'Flower') {
                        $this->v["lgtHours"][$lgt->ps_lg_typ_id] = 12;
                    }
                    if (isset($lgt->ps_lg_typ_count) 
                        && intVal($lgt->ps_lg_typ_count) > 0
                        && isset($lgt->ps_lg_typ_wattage)
                        && intVal($lgt->ps_lg_typ_wattage) > 0) {
                        $this->v["lgtTotKwh"][$lgt->ps_lg_typ_id] = ($lgt->ps_lg_typ_count
                            *$lgt->ps_lg_typ_wattage*$this->v["lgtHours"][$lgt->ps_lg_typ_id])
                            /1000;
                    }
                } elseif ($this->hasRooms()
                    && isset($lgt->ps_lg_typ_room_id) 
                    && intVal($lgt->ps_lg_typ_room_id) > 0) {
                    foreach ($this->sessData->dataSets["ps_growing_rooms"] as $r => $room) {
                        if ($room->ps_room_id == $lgt->ps_lg_typ_room_id) {
                            $this->v["lgtHours"][$lgt->ps_lg_typ_id] = $this->getRoomIndLightHours($r);
                            $this->v["lgtNicknames"][$lgt->ps_lg_typ_id] .= ', ' 
                                . str_replace(' (', ', ', str_replace(')', '', 
                                    $this->getRoomName($room, $r)));
                            if (isset($lgt->ps_lg_typ_wattage)) {
                                $watts = intVal($lgt->ps_lg_typ_wattage);
                                $watts *= $this->v["lgtHours"][$lgt->ps_lg_typ_id]/1000; // now kWh
                                if (isset($lgt->ps_lg_typ_count)) {
                                    $watts *= intVal($lgt->ps_lg_typ_count); // light type's total kWh
                                    $this->v["lgtTotKwh"][$lgt->ps_lg_typ_id] += $watts;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->v["lgtNicknames"];
    }

    protected function getLookupAreaIDs()
    {
        if (!isset($this->v["areasByID"])) {
            $this->v["areasByID"] = [];
            if (isset($this->sessData->dataSets["ps_areas"])) {
                foreach ($this->sessData->dataSets["ps_areas"] as $area) {
                    $this->v["areasByID"][$area->ps_area_id] = $area;
                }
            }
        }
        return $this->v["areasByID"];
    }
    
    protected function getArea($type = 'Mother') 
    {
        if (isset($this->sessData->dataSets["ps_areas"]) 
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $i => $area) {
                if (isset($area->ps_area_type) 
                    && $area->ps_area_type == $this->v["areaTypes"][$type]) {
                    return $area;
                }
            }
        }
        return [];
    }
    
    protected function sortAreas() 
    {
        $this->v["psAreas"] = [];
        if (isset($this->sessData->dataSets["ps_areas"]) 
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $type => $defID) {
                    if (isset($area->ps_area_type) 
                        && $area->ps_area_type == $this->v["areaTypes"][$type]) {
                        $this->v["psAreas"][$type] = $area;
                    }
                }
            }
        }
        return true;
    }
    
    protected function getAreaFld($type, $fldName)
    {
        if (isset($this->sessData->dataSets["ps_areas"]) 
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            foreach ($this->sessData->dataSets["ps_areas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($type == $typ && $area->ps_area_type == $defID) {
                        return $area->{ $fldName };
                    }
                }
            }
        }
        return false;
    }
    
    protected function getCurrAreaFld($fldName)
    {
        $area = $this->sessData->getDataBranchRow('areas');
        if ($area && isset($area->{ $fldName }) 
            && trim($area->{ $fldName }) != '') {
            return $area->{ $fldName };
        }
        return null;
    }
    
    protected function loadTotFlwrSqFt()
    {
        $this->v["totFlwrSqFt"] = 0;
        /*
        if (isset($this->sessData->dataSets["powerscore"])
            && isset($this->sessData->dataSets["powerscore"][0])
            && isset($this->sessData->dataSets["powerscore"][0]->ps_flower_canopy_size)
            && $this->sessData->dataSets["powerscore"][0]->ps_flower_canopy_size > 0) {
            if (!$GLOBALS["SL"]->REQ->has('refresh') 
                && !$GLOBALS["SL"]->REQ->has('recalc')) {
                $this->v["totFlwrSqFt"] = $this->sessData
                    ->dataSets["powerscore"][0]->ps_flower_canopy_size;
                return $this->v["totFlwrSqFt"];
            }
        }
        */

        if ($this->hasRooms()) { // post-3.0
            $this->loadRoomIndsByStage();
            foreach (['Flower', 'Veg', 'Clone'] as $typ) {
                if ($this->v["totFlwrSqFt"] == 0) {
                    if (isset($this->v["stageRooms"][$typ]) 
                        && sizeof($this->v["stageRooms"][$typ]) > 0) {
                        foreach ($this->v["stageRooms"][$typ] as $roomInd) {
                            if (isset($this->sessData->dataSets["ps_growing_rooms"][$roomInd])) {
                                $room = $this->sessData->dataSets["ps_growing_rooms"][$roomInd];
                                if (isset($room->ps_room_canopy_sqft)) {
                                    $this->v["totFlwrSqFt"] += intVal($room->ps_room_canopy_sqft);
                                }
                            }
                        }
                    }
                }
            }
        } else { // pre-3.0 method
            $this->v["totFlwrSqFt"] = $this->getAreaFld('Flower', 'ps_area_size');
            if (intVal($this->v["totFlwrSqFt"]) == 0) {
                $veg = intVal($this->getAreaFld('Veg', 'ps_area_size'));
                if ($veg > 0) {
                    $this->v["totFlwrSqFt"] = $veg;
                } else {
                    $clone = intVal($this->getAreaFld('Clone', 'ps_area_size'));
                    if ($clone > 0) {
                        $this->v["totFlwrSqFt"] = $clone;
                    }
                }
            }
        }
        $this->sessData->dataSets["powerscore"][0]
            ->ps_flower_canopy_size = $this->v["totFlwrSqFt"];
        $this->sessData->dataSets["powerscore"][0]->save();
        return $this->v["totFlwrSqFt"];
    }
    
    protected function sortMonths()
    {
        if (!isset($this->sessData->dataSets["ps_monthly"]) 
            || sizeof($this->sessData->dataSets["ps_monthly"]) == 0) {
            $this->sessData->dataSets["ps_monthly"] = [];
            for ($m = 1; $m <= 12; $m++) {
                $new = new RIIPsMonthly;
                $new->ps_month_psid  = $this->coreID;
                $new->ps_month_month = $m;
                $new->save();
                $this->sessData->dataSets["ps_monthly"][] = $new;
            }
        }
        return RIIPsMonthly::where('ps_month_psid', $this->coreID)
            ->orderBy('ps_month_month', 'asc')
            ->get();
    }
    
    protected function getAreaLights($areaID = -3, $areaType = '')
    {
        $ret = [];
        if ($areaID <= 0 && trim($areaType) != '') {
            $areaID = $this->getAreaFld($areaType, 'ps_area_id');
        }
        if ($areaID <= 0) {
            return [];
        }
        $areaArr = [ 'ps_lg_typ_area_id' => $areaID ];
        return $this->sessData->getRowIDsByFldVal('ps_light_types', $areaArr, true);
    }
    
    public function printProfileExtraBtns()
    {
        if (isset($this->v["profileUser"]) && isset($this->v["uID"])
            && isset($this->v["profileUser"]->id)
            && $this->v["profileUser"]->id == $this->v["uID"] 
            && $this->isUserPastCultClassic($this->v["uID"])) {
            return '<a href="/start/calculator?new=1&cups=230&time=232" '
                . 'class="btn btn-lg btn-primary btn-block">'
                . 'Start A Fresh PowerScore for '
                . 'the Cultivation Classic</a><br /><br />'
                . '<a href="/start/calculator?new=1" '
                . 'class="btn btn-lg btn-primary btn-block">'
                . 'Start A Fresh PowerScore</a>';
        }
        return '';
    }
    
    protected function isUserPastCultClassic($uID)
    {
        $ccDef = $GLOBALS["SL"]->def->getID(
            'PowerScore Competitions', 
            'Cultivation Classic'
        );
        $chk = DB::table('rii_powerscore')
            ->join('rii_ps_for_cup', 'rii_ps_for_cup.ps_cup_psid', 
                '=', 'rii_powerscore.ps_id')
            ->where('rii_powerscore.ps_user_id', $uID)
            ->where('rii_ps_for_cup.ps_cup_cup_id', $ccDef)
            ->get();
        return $chk->isNotEmpty();
    }
    
    protected function prepFeedbackSkipLnk()
    {
        $this->v["psOwner"] = -3;
        if (session()->has('PowerScoreOwner')) {
            $this->v["psOwner"] = session()->get('PowerScoreOwner');
        } elseif (isset($this->sessData->dataSets["ps_feedback"]) 
            && isset($this->sessData->dataSets["ps_feedback"][0])
            && isset($this->sessData->dataSets["ps_feedback"][0]->psf_psid)) {
            $this->v["psOwner"] = $this->sessData->dataSets["ps_feedback"][0]->psf_psid;
        }
        return true;
    }
    
    protected function listSimilarNames($chk)
    {
        $ret = '';
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                if (isset($ps->ps_name) && trim($ps->ps_name) != '') {
                    $ret .= ', <a href="/calculated/read-' . $ps->ps_id 
                        . '" target="_blank">' . $ps->ps_name . '</a>';
                }
            }
        }
        return $ret;
    }
    
    protected function printReportBlds($nID)
    {
        $deet = '';
        $blds = $this->sessData->getBranchChildRows('ps_areas_blds');
        if (sizeof($blds) > 0) {
            foreach ($blds as $i => $bld) {
                if ($i > 0) {
                    $deet .= ', ';
                }
                $deet .= $GLOBALS["SL"]->def->getVal(
                    'PowerScore Building Types', 
                    $bld->ps_ar_bld_type
                );
                if (isset($bld->ps_ar_bld_type_other) 
                    && trim($bld->ps_ar_bld_type_other) != '') {
                    $deet .= ': ' . $bld->ps_ar_bld_type_other;
                }
                $cnsts = $this->sessData->dataWhere(
                    'ps_areas_constr', 
                    'ps_ar_cns_bld_id', 
                    $bld->getKey()
                );
                if ($cnsts) {
                    $deetCnst = '';
                    foreach ($cnsts as $j => $cnst) {
                        $deetCnst .= (($j > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->def->getVal(
                                'PowerScore Building Construction', 
                                $cnst->ps_ar_cns_type
                            );
                        if (isset($cnst->ps_ar_cns_type_other) 
                            && trim($cnst->ps_ar_cns_type_other) != '') {
                            $deetCnst .= ': ' . $cnst->ps_ar_cns_type_other;
                        }
                    }
                    if (trim($deetCnst) != '') {
                        $deet .= ' (' . $deetCnst . ')';
                    }
                }
            }
        }
        return [ 'Building Types', $deet ];
    }

    protected function printReportLgts($nID)
    {
        $deet = '';
        $lgts = [];
        if ($nID == 1360) {
            $roomID = $this->sessData->getLatestDataBranchID();
            $fldVals = [ "ps_lg_typ_room_id" => $roomID ];
            $lgts = $this->sessData->getRowIDsByFldVal('ps_light_types', $fldVals, true);
        } else {
            $lgts = $this->sessData->getBranchChildRows('ps_light_types');
        }
        if (sizeof($lgts) > 0) {
            foreach ($lgts as $i => $lgt) {
                $lgtType = $GLOBALS["SL"]->def->getVal(
                    'PowerScore Light Types', 
                    $lgt->ps_lg_typ_light
                );
                $deet .= (($i > 0) ? ', ' : '') . $lgtType;
                if (isset($lgt->ps_lg_typ_count) && intVal($lgt->ps_lg_typ_count) > 0) {
                    $deet .= ' ' . $lgt->ps_lg_typ_count . ' x ';
                    if (isset($lgt->ps_lg_typ_wattage) && intVal($lgt->ps_lg_typ_wattage) > 0) {
                        $deet .= $lgt->ps_lg_typ_wattage . 'W';
                    }
                }
                if ((isset($lgt->ps_lg_typ_hours) && intVal($lgt->ps_lg_typ_hours) > 0)
                    || (isset($lgt->ps_lg_typ_make) && trim($lgt->ps_lg_typ_make) != '')
                    || (isset($lgt->ps_lg_typ_model) && trim($lgt->ps_lg_typ_model) != '')) {
                    $deet .= '<div class="pL20">';
                    if (isset($lgt->ps_lg_typ_hours) && intVal($lgt->ps_lg_typ_hours) > 0) {
                        $deet .= ' ' . $lgt->ps_lg_typ_hours . ' hours';
                    }
                    if (isset($lgt->ps_lg_typ_make) && trim($lgt->ps_lg_typ_make) != '') {
                        $deet .= ' ' . $lgt->ps_lg_typ_make;
                    }
                    if (isset($lgt->ps_lg_typ_model) && trim($lgt->ps_lg_typ_model) != '') {
                        $deet .= ' ' . $lgt->ps_lg_typ_model;
                    }
                    $deet .= '</div>';
                }
            }
        }
        return [ 'Light Types', $deet ];
    }
    
    protected function chkUnprintableSubScores()
    {
        $this->v["noprints"] = '';
        $ps = $this->sessData->dataSets["powerscore"][0];
        $noprints = [];
        if (!isset($ps->ps_effic_facility) 
            || !$ps->ps_effic_facility 
            || $ps->ps_effic_facility == 0) {
            $noprints[] = 'facility';
        }
        if (!isset($ps->ps_effic_production) 
            || !$ps->ps_effic_production 
            || $ps->ps_effic_production == 0) {
            $noprints[] = 'production';
        }
        if (!isset($ps->ps_effic_hvac) 
            || !$ps->ps_effic_hvac 
            || $ps->ps_effic_hvac == 0) {
            $noprints[] = 'HVAC';
        }
        if (!isset($ps->ps_effic_lighting) 
            || !$ps->ps_effic_lighting 
            || $ps->ps_effic_lighting == 0) {
            $noprints[] = 'lighting';
        }
        if (!isset($ps->ps_effic_water) 
            || !$ps->ps_effic_water 
            || $ps->ps_effic_water == 0) {
            $noprints[] = 'water';
        }
        if (!isset($ps->ps_effic_waste) 
            || !$ps->ps_effic_waste 
            || $ps->ps_effic_waste == 0) {
            $noprints[] = 'waste';
        }
        if (sizeof($noprints) > 0) {
            foreach ($noprints as $i => $no) {
                if ($i == 0) {
                    $this->v["noprints"] .= $no;
                } else {
                    if (sizeof($noprints) == 2) {
                        $this->v["noprints"] .= ' and ' . $no;
                    } else {
                        $this->v["noprints"] .= ', '
                            . (($i == (sizeof($noprints)-1)) ? 'and ' : '') . $no;
                    }
                }
            }
        }
        return $this->v["noprints"];
    }
    
    protected function getTableRecLabelCustom($tbl, $rec = [], $ind = -3)
    {
        $ret = '';
        if ($tbl == 'powerscore' && isset($rec->ps_name)) {
            return $rec->ps_name;
        }
        return $ret;
    }
    
    protected function slimLgtType($defValue = '')
    {
        return str_replace('double-ended ', '2x', 
            str_replace('single-ended ', '1x', $defValue)
        );
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
    
    protected function deepCopyCoreSkips($cid)
    {
        $this->v["sessDataCopySkips"] = [];
        if ($this->treeID == 1) {
            $this->v["sessDataCopySkips"] = [ 'ps_monthly', 'ps_rankings' ];
        }
        return $this->v["sessDataCopySkips"];
    }
    
    protected function deepCopySetsClean($cid)
    {
        if (isset($this->sessData->dataSets["ps_utilities"]) 
            && sizeof($this->sessData->dataSets["ps_utilities"]) > 0) {
            foreach ($this->sessData->dataSets["ps_utilities"] as $i => $util) {
                if (isset($util->ps_ut_lnk_utility_id)) {
                    unset($util->ps_ut_lnk_utility_id);
                }
            }
        }
        return true;
    }
    
    protected function deepCopyFinalize($cid)
    {
        $this->sessData->dataSets["powerscore"][0]->update([
            'ps_status'             => $this->statusIncomplete,
            'ps_grams'              => 0,
            'ps_kwh'                => 0,
            'ps_effic_overall'      => 0,
            'ps_effic_over_similar' => 0,
            'ps_effic_facility'     => 0,
            'ps_effic_production'   => 0,
            'ps_effic_lighting'     => 0,
            'ps_effic_hvac'         => 0,
            'ps_effic_carbon'       => 0,
            'ps_effic_water'        => 0,
            'ps_effic_waste'        => 0
        ]);
        return true;
    }
    
}