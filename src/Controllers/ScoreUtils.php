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
        return '<p>&nbsp;</p><h4>You Have ' . (($cnt == 1) ? 'An Unfinished PowerScore' : 'Unfinished PowerScores')
            . '</h4>';
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
    
    protected function loadAreaLgtTypes()
    {
        $this->v["fltATs"] = [];
        if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
            foreach ($this->sessData->dataSets["PSAreas"] as $area) {
                if (isset($area->PsAreaHasStage) && intVal($area->PsAreaHasStage) == 1
                    && $area->PsAreaType != $this->v["areaTypes"]["Dry"]) {
                    $this->v["fltATs"][$area->PsAreaType] = [ "hvc" => 0, "lgt" => 0 ];
                    if (isset($area->PsAreaHvacType)) {
                        $this->v["fltATs"][$area->PsAreaType]["hvac"] = $area->PsAreaHvacType;
                    }
                    $lgts = $this->getAreaLights($area->PsAreaID);
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
                                            && $typ == 'Clone') 
                                        || ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
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
                            $curr = $typ;
                            if (isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)) {
                                switch ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc) {
                                    case $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones'):
                                        if (in_array($typ, ['Mother', 'Clone'])) $curr = 'Mother & Clones';
                                        break;
                                    case $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room'):
                                        if (in_array($typ, ['Mother', 'Veg'])) $curr = 'Mother & Veg';
                                        break;
                                }
                            }
                            $perc = $area->PsAreaCalcSize/$this->sessData->dataSets["PowerScore"][0]->PsTotalCanopySize;
                            $this->v["printEfficLgt"][] = [
                                "typ" => $typ,
                                "eng" => '( (' . $curr . ' <nobr>' . number_format($area->PsAreaCalcWatts) 
                                    . ' W</nobr> / <nobr>' . number_format($area->PsAreaCalcSize) 
                                    . ' sq ft )</nobr> <nobr>x ' . round(100*($perc)) . '% grow area</nobr>',
                                "lgt" => $curr . ': ' . $lightBreakdown[$typ],
                                "num" => '<nobr>' . $curr . ' ' 
                                    . $GLOBALS["SL"]->sigFigs($area->PsAreaLightingEffic*$perc, 3) . ' W / sq ft</nobr>'
                                ];
                        }
                    }
                }
            }
        }
        return $this->v["printEfficLgt"];
    }
    
    protected function getLoopItemLabelCustom($loop, $itemRow = [], $itemInd = -3)
    {
        if (in_array($loop, ['Growth Stages', 'Harvest Stages'])) {
            switch (intVal($itemRow->PsAreaType)) {
                case 237: return 'Mother Plants';     break;
                case 160: return 'Clone Plants';      break;
                case 161: return 'Vegetating Plants'; break;
                case 162: return 'Flowering Plants';  break;
                case 163: return 'Drying / Curing';   break;
            }
        } elseif ($loop == 'Types of Light Fixtures') {
            if ($itemRow && isset($itemRow->PsLgTypAreaID)) {
                $lgtDesc = '<b>' . $this->getAreaIdTypeName($itemRow->PsLgTypAreaID) . ':</b> ';
                if (isset($itemRow->PsLgTypCount) && trim($itemRow->PsLgTypCount) != '') {
                    $lgtDesc .= number_format($itemRow->PsLgTypCount) . ' fixtures, ';
                }
                if (isset($itemRow->PsLgTypWattage) && trim($itemRow->PsLgTypWattage) != '') {
                    $lgtDesc .= number_format($itemRow->PsLgTypWattage) . 'W each';
                }
                $lgtDesc .= '</h3>';
                if (isset($itemRow->PsLgTypLight) && intVal($itemRow->PsLgTypLight) > 0) {
                    $lgtDesc .= $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $itemRow->PsLgTypLight);
                    if ((isset($itemRow->PsLgTypMake) && trim($itemRow->PsLgTypMake) != '') 
                        || (isset($itemRow->PsLgTypModel) && trim($itemRow->PsLgTypModel) != '')) {
                        $lgtDesc .= ', ';
                    }
                }
                if (isset($itemRow->PsLgTypMake) && trim($itemRow->PsLgTypMake) != '') {
                    $lgtDesc .= $itemRow->PsLgTypMake;
                    if (isset($itemRow->PsLgTypModel) && trim($itemRow->PsLgTypModel) != '') {
                        $lgtDesc .= ', ';
                    }
                }
                if (isset($itemRow->PsLgTypModel) && trim($itemRow->PsLgTypModel) != '') {
                    $lgtDesc .= $itemRow->PsLgTypModel;
                }
                $lgtDesc .= '<h3 class="disNon">';
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
                    if ($type == $typ && $area->PsAreaType == $defID) {
                        return $area->{ $fldName };
                    }
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
                $new->save();
                $this->sessData->dataSets["PSMonthly"][] = $new;
            }
        }
        return RIIPSMonthly::where('PsMonthPSID', $this->coreID)
            ->orderBy('PsMonthMonth', 'asc')
            ->get();
    }
    
    protected function getAreaLights($areaID = -3, $areaType = '')
    {
        $ret = [];
        if ($areaID <= 0 && trim($areaType) != '') {
            $areaID = $this->getAreaFld($areaType, 'PsAreaID');
        }
        if ($areaID <= 0) {
            return [];
        }
        return $this->sessData->getRowIDsByFldVal('PSLightTypes', [ 'PsLgTypAreaID' => $areaID ], true);
    }
    
    public function printProfileExtraBtns()
    {
        if (isset($this->v["profileUser"]) && isset($this->v["profileUser"]->id) 
            && $this->v["profileUser"]->id == $this->v["uID"] && $this->isUserPastCultClassic($this->v["uID"])) {
            return '<a href="/start/calculator?new=1&cups=230&time=232" class="btn btn-lg btn-primary btn-block">'
                . 'Start A Fresh PowerScore for the Cultivation Classic</a><br /><br />'
                . '<a href="/start/calculator?new=1" class="btn btn-lg btn-primary btn-block">'
                . 'Start A Fresh PowerScore</a>';
        }
        return '';
    }
    
    protected function isUserPastCultClassic($uID)
    {
        $chk = DB::table('RII_PowerScore')
            ->join('RII_PSForCup', 'RII_PSForCup.PsCupPSID', '=', 'RII_PowerScore.PsID')
            ->where('RII_PowerScore.PsUserID', $uID)
            ->where('RII_PSForCup.PsCupCupID', 
                $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic'))
            ->get();
        return $chk->isNotEmpty();
    }
    
    protected function prepFeedbackSkipLnk()
    {
        $this->v["psOwner"] = ((session()->has('PowerScoreOwner')) ? session()->get('PowerScoreOwner') 
            : ((isset($this->sessData->dataSets["PsFeedback"]) && isset($this->sessData->dataSets["PsFeedback"][0]) 
                && isset($this->sessData->dataSets["PsFeedback"][0]->PsfPsID)) 
                ? $this->sessData->dataSets["PsFeedback"][0]->PsfPsID : -3));
        return true;
    }
    
    protected function listSimilarNames($chk)
    {
        $ret = '';
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                if (isset($ps->PsName) && trim($ps->PsName) != '') {
                    $ret .= ', <a href="/calculated/read-' . $ps->PsID . '" target="_blank">' . $ps->PsName . '</a>';
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
            $this->v["sessDataCopySkips"] = ['PSMonthly', 'PSRankings'];
        }
        return $this->v["sessDataCopySkips"];
    }
    
    protected function deepCopySetsClean($cid)
    {
        if (isset($this->sessData->dataSets["PSUtilities"]) && sizeof($this->sessData->dataSets["PSUtilities"]) > 0) {
            foreach ($this->sessData->dataSets["PSUtilities"] as $i => $util) {
                if (isset($util->PsUtLnkUtilityID)) {
                    unset($util->PsUtLnkUtilityID);
                }
            }
        }
        return true;
    }
    
    protected function deepCopyFinalize($cid)
    {
        $this->sessData->dataSets["PowerScore"][0]->update([
            'PsStatus'              => $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'),
            'PsGrams'               => 0,
            'PsKWH'                 => 0,
            'PsEfficOverall'        => 0,
            'PsEfficOverSimilar'    => 0,
            'PsEfficFacility'       => 0,
            'PsEfficProduction'     => 0,
            'PsEfficLighting'       => 0,
            'PsEfficHvac'           => 0,
            'PsEfficLightingMother' => 0,
            'PsEfficLightingClone'  => 0,
            'PsEfficLightingVeg'    => 0,
            'PsEfficLightingFlower' => 0
            ]);
        return true;
    }
    
}