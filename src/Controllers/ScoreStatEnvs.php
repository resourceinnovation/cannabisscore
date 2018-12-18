<?php
/**
  * ScoreStatEnvs is an extension of the SurvStatsGraph which creates an Stats instance to analyze
  * growing environments uses for all types of farms.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPSAreasBlds;
use SurvLoop\Controllers\SurvStatsTbl;
use SurvLoop\Controllers\SurvStatsGraph;

class ScoreStatEnvs extends SurvStatsGraph
{
    public $cmpl    = [];
    public $blds    = [];
    public $envPies = [];
    public $envIDs  = [];
    
    public function initEnvs()
    {
        $this->cmpl    = [ 0 => 0 ];
        $this->blds    = [];
        $this->envPies = [];
        $this->envIDs  = [ 0 => [] ];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->cmpl[$type->DefID]    = [ 0 => 0 ];
            $this->blds[$type->DefID]    = [];
            $this->envPies[$type->DefID] = [];
            $this->envIDs[$type->DefID]  = [ 0 => [] ];
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
                $this->envIDs[$type->DefID][$bld->DefID] = [];
            }
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                if ($area->DefValue != 'Dry') {
                    $this->cmpl[$type->DefID][$area->DefID]    = [ 0 => 0 ];
                    $this->blds[$type->DefID][$area->DefID]    = [];
                    $this->envPies[$type->DefID][$area->DefID] = [];
                    $this->envIDs[$type->DefID][$area->DefID]  = [];
                    foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
                        $this->cmpl[$type->DefID][$area->DefID][$bld->DefID] = 0;
                    }
                }
            }
        }
        return true;
    }
    
    public function addDataEnvs($ps, $areaType, $areaID)
    {
        $this->addRecFilt('area', $areaType, $areaID);
        $blds = RIIPSAreasBlds::where('PsArBldAreaID', $areaID)
            ->get();
        if ($blds->isNotEmpty() && isset($this->cmpl[$ps->PsCharacterize][$areaType])) {
            if (!in_array($ps->PsID, $this->envIDs[0])) {
                $this->envIDs[0][] = $ps->PsID;
            }
            if (!in_array($ps->PsID, $this->envIDs[$ps->PsCharacterize][0])) {
                $this->envIDs[$ps->PsCharacterize][0][] = $ps->PsID;
            }
            if (!in_array($ps->PsID, $this->envIDs[$ps->PsCharacterize][$areaType])) {
                $this->envIDs[$ps->PsCharacterize][$areaType][] = $ps->PsID;
            }
            foreach ($blds as $bld) {
                if (!in_array($ps->PsID, $this->envIDs[$ps->PsCharacterize][$bld->PsArBldType])) {
                    $this->envIDs[$ps->PsCharacterize][$bld->PsArBldType][] = $ps->PsID;
                }
                $this->addRecDat('bld' . $bld->PsArBldType, 1, $areaID);
                $this->cmpl[$ps->PsCharacterize][$areaType][$bld->PsArBldType]++;
            }
        }
        $this->delRecFilt('area');
        return true;
    }
    
    public function calcBlds()
    {
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->cmpl[$type->DefID][0] = sizeof($this->envIDs[$type->DefID][0]);
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                $this->cmpl[$type->DefID][$area->DefID][0] = sizeof($this->envIDs[$type->DefID][$area->DefID]);
                foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $j => $bld) {
                    $color1 = $GLOBALS["SL"]->getCssColor('color-main-on');
                    $color2 = $GLOBALS["SL"]->getCssColor('color-main-bg');
                    $this->blds[$type->DefID][$area->DefID][] = [
                        $this->cmpl[$type->DefID][$area->DefID][$bld->DefID],
                        str_replace('Commercial/Warehouse', 'Commercial', 
                            str_replace('House/Garage', 'House', $bld->DefValue)),
                        "'" . $GLOBALS["SL"]->printColorFadeHex(($j*0.16), $color1, $color2) . "'"
                        ];
                }
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                $this->envPies[$type->DefID][$area->DefID] 
                    = $this->pieView($this->blds[$type->DefID][$area->DefID]);
            }
        }
        return true;
    }
    
    
    public function tblHeaderRow($fltAbbr, $lnk = '', $tots = true)
    {
        $farm = intVal(str_replace('a', '', $this->applyCurrFilt('1')));
//echo '<h2>tblHeaderRow(' . $this->applyCurrFilt('1') . '</h2><pre>'; print_r($this->filts); echo '</pre>'; exit;
        $ret = '<tr><th>&nbsp;</th><th class="brdRgt">Total<sub class="slGrey">' 
            . $this->cmpl[$farm][0] . '</sub></th>';
        $fLet = $this->fAbr($fltAbbr);
        if ($fLet != '' && isset($this->filts[$fLet]) && sizeof($this->filts[$fLet]["val"]) > 0) {
            foreach ($this->filts[$fLet]["val"] as $i => $val) {
                $ret .= '<th>' . $this->filts[$fLet]["vlu"][$i] . '<sub class="slGrey">' 
                    . $this->cmpl[$farm][$val][0] . '</sub></th>';
            }
        }
        return $ret . '</tr>';
    }
    
    public function tblPercHasDat($fltCol, $datTypes = [])
    {
        $farm = intVal(str_replace('a', '', $this->applyCurrFilt('1')));
        $this->tblOut = [];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
            $row = [ $bld->DefValue, round(100*sizeof($this->envIDs[$farm][$bld->DefID])/$this->cmpl[$farm][0]) ];
            if ($row[1] > 0) {
                $row[1] = $row[1] . '% <sub class="slGrey">' . number_format(sizeof($this->envIDs[$farm][$bld->DefID]))
                    . '</sub>';
            }
            foreach ($this->filts["b"]["val"] as $i => $val) {
                $cell = round(100*$this->cmpl[$farm][$val][$bld->DefID]/$this->cmpl[$farm][$val][0]);
                if ($cell > 0) {
                    $cell .= '% <sub class="slGrey">' . number_format($this->cmpl[$farm][$val][$bld->DefID])
                        . '</sub>';
                }
                $row[] = $cell;
            }
            $this->tblOut[] = $row;
        }
        return view('vendor.survloop.inc-stat-tbl-percs', [ "tblOut" => $this->tblOut ])->render();
    }
    
    
}