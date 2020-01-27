<?php
/**
  * ScoreUtils is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of simplest PowerScore-specific utilities functions like
  * looking up common definition lists, translating single PowerScores, and prepping various proccesses.
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
use App\Models\RIIPSRenewables;
use App\Models\RIIPSMonthly;
use App\Models\RIIPSForCup;
use App\Models\SLZips;
use CannabisScore\Controllers\ScoreVars;

class ScoreUtils extends ScorePowerUtilities
{   
    
    public function multiRecordCheckIntro($cnt = 1)
    {
        return '<p>&nbsp;</p><h4>You Have ' 
            . (($cnt == 1) ? 'An Unfinished PowerScore' : 'Unfinished PowerScores')
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
        if ($GLOBALS["SL"]->REQ->has('cups') && trim($GLOBALS["SL"]->REQ->get('cups')) != '') {
            $cupsIn = $GLOBALS["SL"]->mexplode(',', urldecode($GLOBALS["SL"]->REQ->get('cups')));
            $cupList = $GLOBALS["SL"]->def->getSet('PowerScore Competitions');
            if (sizeof($cupList) > 0) {
                foreach ($cupList as $c) {
                    if (in_array($c->def_id, $cupsIn)) {
                        $chk = RIIPSForCup::where('ps_cup_psid', $this->coreID)
                            ->where('ps_cup_cup_id', $c->def_id)
                            ->first();
                        if (!$chk || !isset($chk->ps_cup_cup_id)) {
                            $chk = new RIIPSForCup;
                            $chk->ps_cup_psid  = $this->coreID;
                            $chk->ps_cup_cup_id = $c->def_id;
                            $chk->save();
                        }
                    } else {
                        RIIPSForCup::where('ps_cup_psid', $this->coreID)
                            ->where('ps_cup_cup_id', $c->def_id)
                            ->delete();
                    }
                }
            }
        }
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
    
    protected function prepPrintEfficLgt()
    {
        $this->loadTotFlwrSqFt();
        $this->v["printEfficLgt"] = $sqft = $watt = $lightBreakdown = [];
        $this->v["printEfficHvac"] = $wattHvac = $hvacBreakdown = [];
        $this->v["printEfficWtr"] = $gal = $waterBreakdown = [];
        $ps = $this->sessData->dataSets["powerscore"][0];
        $mothClone = $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones');
        $mothVeg = $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room');
        if (isset($this->sessData->dataSets["ps_areas"])) {
            foreach ($this->sessData->dataSets["ps_areas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $sqft[$typ] = $area->ps_area_size;
                        $watt[$typ] = $area->ps_area_total_light_watts;
                        $wattHvac[$typ] = $GLOBALS["CUST"]->getHvacEffic($area->ps_area_hvac_type);
                        $gal[$typ] = $area->ps_area_total_light_watts;
                    }
                }
            }

            foreach ($this->sessData->dataSets["ps_areas"] as $i => $area) {
                foreach ($this->v["areaTypes"] as $typ => $defID) {
                    if ($area->ps_area_type == $defID && $typ != 'Dry') {
                        $lightBreakdown[$typ] = $hvacBreakdown[$typ] = $waterBreakdown[$typ] = '';
                        if (isset($area->ps_area_lighting_effic) 
                            && $area->ps_area_lighting_effic > 0) {
                            //  (Clone watts x # of lights x 24 hrs) / Clone sq ft)
                            if (isset($this->sessData->dataSets["ps_light_types"]) 
                                && sizeof($this->sessData->dataSets["ps_light_types"]) > 0) {
                                foreach ($this->sessData->dataSets["ps_light_types"] as $lgt) {
                                    $areaIDs = [ $area->getKey() ];
                                    if (isset($ps->ps_mother_loc)
                                        && (($ps->ps_mother_loc == $mothClone && $typ == 'Clone') 
                                        || ($ps->ps_mother_loc == $mothVeg && $typ == 'Veg'))) {
                                        $areaIDs[] = $this->getAreaFld('Mother', 'ps_area_id');
                                    }
                                    if (in_array($lgt->ps_lg_typ_area_id, $areaIDs) 
                                        && isset($lgt->ps_lg_typ_count) 
                                        && intVal($lgt->ps_lg_typ_count) > 0 
                                        && isset($lgt->ps_lg_typ_wattage) 
                                        && intVal($lgt->ps_lg_typ_wattage) > 0) {
                                        if ($lightBreakdown[$typ] != '') {
                                            $lightBreakdown[$typ] .= ' + ';
                                        }
                                        $lightBreakdown[$typ] .= '<nobr>( ' 
                                            . number_format($lgt->ps_lg_typ_count) . ' fixtures x '
                                            . number_format($lgt->ps_lg_typ_wattage) . ' W )</nobr>';
                                    }
                                }
                            }
                            if (strpos($lightBreakdown[$typ], '+') === false) {
                                $lightBreakdown[$typ] = str_replace('(', '', 
                                    str_replace(')', '', $lightBreakdown[$typ]));
                            }
                            $curr = $typ;
                            if (isset($ps->ps_mother_loc)) {
                                switch ($ps->ps_mother_loc) {
                                    case $mothClone:
                                        if (in_array($typ, ['Mother', 'Clone'])) {
                                            $curr = 'Mother & Clones';
                                        }
                                        break;
                                    case $mothVeg:
                                        if (in_array($typ, ['Mother', 'Veg'])) {
                                            $curr = 'Mother & Veg';
                                        }
                                        break;
                                }
                            }
                            $perc = $area->ps_area_calc_size/$ps->ps_total_canopy_size;
                            $eng = '( (' . $curr . ' <nobr>' . number_format($area->ps_area_calc_watts) 
                                . ' W</nobr> / <nobr>' . number_format($area->ps_area_calc_size) 
                                . ' sq ft )</nobr> <nobr>x ' . round(100*($perc)) . '% grow area</nobr>';
                            $num = '<nobr>' . $curr . ' ' 
                                . $GLOBALS["SL"]->sigFigs($area->ps_area_lighting_effic*$perc, 3) 
                                . ' W / sq ft</nobr>';
                            $this->v["printEfficLgt"][] = [
                                "typ" => $typ,
                                "eng" => $eng,
                                "lgt" => $curr . ': ' . $lightBreakdown[$typ],
                                "num" => $num
                            ];
                            $eng = '( (' . $curr . ' <nobr>' 
                                . $GLOBALS["CUST"]->getHvacEffic($area->ps_area_hvac_type) 
                                . ' kWh / sq ft</nobr> )</nobr> <nobr>x ' 
                                . round(100*($perc)) . '% grow area</nobr>';
                            $num = '<nobr>' . $curr . ' ' 
                                . $GLOBALS["SL"]->sigFigs($area->ps_area_hvac_effic*$perc, 3) 
                                . ' kWh / sq ft</nobr>';
                            $this->v["printEfficHvac"][] = [
                                "typ" => $typ,
                                "eng" => $eng,
                                "num" => $num
                            ];
                            $eng = '( (' . $curr . ' <nobr>' . $area->ps_area_gallons 
                                . ' Gallons</nobr> / <nobr>' 
                                . number_format($area->ps_area_calc_size) 
                                . ' sq ft )</nobr> <nobr>x ' . round(100*($perc)) 
                                . '% grow area</nobr>';
                            $num = '<nobr>' . $curr . ' ' 
                                . $GLOBALS["SL"]->sigFigs($area->ps_area_water_effic*$perc, 3) 
                                . ' Gallons / sq ft</nobr>';
                            $this->v["printEfficWtr"][] = [
                                "typ" => $typ,
                                "eng" => $eng,
                                "num" => $num
                            ];
                        }
                        
                    }
                }
            }
        }
        return $this->v["printEfficLgt"];
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
            switch (intVal($itemRow->ps_area_type)) {
                case 237: return 'Mother Plants';     break;
                case 160: return 'Clone Plants';      break;
                case 161: return 'Vegetating Plants'; break;
                case 162: return 'Flowering Plants';  break;
                case 163: return 'Drying / Curing';   break;
            }
        } elseif ($loop == 'Types of Light Fixtures') {
            if ($itemRow && isset($itemRow->ps_lg_typ_area_id)) {
                $lgtDesc = '<b>' . $this->getAreaIdTypeName($itemRow->ps_lg_typ_area_id) 
                    . ':</b> ';
                if (isset($itemRow->ps_lg_typ_count) 
                    && trim($itemRow->ps_lg_typ_count) != '') {
                    $lgtDesc .= number_format($itemRow->ps_lg_typ_count) 
                        . ' fixtures, ';
                }
                if (isset($itemRow->ps_lg_typ_wattage) 
                    && trim($itemRow->ps_lg_typ_wattage) != '') {
                    $lgtDesc .= number_format($itemRow->ps_lg_typ_wattage) 
                        . 'W each';
                }
                $lgtDesc .= '</h4>';
                if (isset($itemRow->ps_lg_typ_light)
                    && intVal($itemRow->ps_lg_typ_light) > 0) {
                    $lgtDesc .= $GLOBALS["SL"]->def->getVal(
                        'PowerScore Light Types', 
                        $itemRow->ps_lg_typ_light
                    );
                    if ((isset($itemRow->ps_lg_typ_make) 
                            && trim($itemRow->ps_lg_typ_make) != '') 
                        || (isset($itemRow->ps_lg_typ_model) 
                            && trim($itemRow->ps_lg_typ_model) != '')) {
                        $lgtDesc .= ', ';
                    }
                }
                if (isset($itemRow->ps_lg_typ_make) 
                    && trim($itemRow->ps_lg_typ_make) != '') {
                    $lgtDesc .= $itemRow->ps_lg_typ_make;
                    if (isset($itemRow->ps_lg_typ_model) 
                        && trim($itemRow->ps_lg_typ_model) != '') {
                        $lgtDesc .= ', ';
                    }
                }
                if (isset($itemRow->ps_lg_typ_model) 
                    && trim($itemRow->ps_lg_typ_model) != '') {
                    $lgtDesc .= $itemRow->ps_lg_typ_model;
                }
                $lgtDesc .= '<h4 class="disNon">';
                return $lgtDesc;
            }
        }
        return '';
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
        if ($area && isset($area->{ $fldName }) && trim($area->{ $fldName }) != '') {
            return $area->{ $fldName };
        }
        return null;
    }
    
    protected function loadTotFlwrSqFt()
    {
        $this->v["totFlwrSqFt"] = $this->getAreaFld('Flower', 'ps_area_size');
        return $this->v["totFlwrSqFt"];
    }
    
    protected function sortMonths()
    {
        if (!isset($this->sessData->dataSets["ps_monthly"]) 
            || sizeof($this->sessData->dataSets["ps_monthly"]) == 0) {
            $this->sessData->dataSets["ps_monthly"] = [];
            for ($m = 1; $m <= 12; $m++) {
                $new = new RIIPSMonthly;
                $new->ps_month_psid  = $this->coreID;
                $new->ps_month_month = $m;
                $new->save();
                $this->sessData->dataSets["ps_monthly"][] = $new;
            }
        }
        return RIIPSMonthly::where('ps_month_psid', $this->coreID)
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
    
    public function printPartnerProfileDashBtn($nID)
    {
        return '<a href="/dashboard" class="btn btn-xl btn-primary btn-block">'
            . $this->getPartnerCompany() . ' Dashboard</a>';
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
        $ccDef = $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic');
        $chk = DB::table('rii_powerscore')
            ->join('rii_ps_for_cup', 'rii_ps_for_cup.ps_cup_psid', '=', 'rii_powerscore.ps_id')
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
            && isset($this->sessData->dataSets["ps_feedback"][0]->psf_ps_id)) {
            $this->v["psOwner"] = $this->sessData->dataSets["ps_feedback"][0]->psf_ps_id;
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
        $blds = $this->sessData->getBranchChildRows('PSAreasBlds');
        if (sizeof($blds) > 0) {
            foreach ($blds as $i => $bld) {
                $deet .= (($i > 0) ? ', ' : '') 
                    . $GLOBALS["SL"]->def->getVal(
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
                    $deet .= ' (';
                    foreach ($cnsts as $j => $cnst) {
                        $deet .= (($j > 0) ? ', ' : '') 
                            . $GLOBALS["SL"]->def->getVal(
                                'PowerScore Building Construction', 
                                $cnst->ps_ar_cns_type
                            );
                        if (isset($cnst->ps_ar_cns_type_other) 
                            && trim($cnst->ps_ar_cns_type_other) != '') {
                            $deet .= ': ' . $cnst->ps_ar_cns_type_other;
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
        $lgts = $this->sessData->getBranchChildRows('ps_light_types');
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
        if ($tbl == 'powerscore' && isset($rec->ps_name)) {
            return $rec->ps_name;
        }
        return '';
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
        $chk = RIIPSAreas::where('ps_area_psid', 169)
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