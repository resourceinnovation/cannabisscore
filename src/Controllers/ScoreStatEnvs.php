<?php
/**
  * ScoreStatEnvs is an extension of the SurvStatsGraph which creates an Stats instance to analyze
  * growing environments uses for all types of farms.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPsAreasBlds;
use SurvLoop\Controllers\Stats\SurvStatsTbl;
use SurvLoop\Controllers\Stats\SurvStatsGraph;

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
            $this->cmpl[$type->def_id]    = [ 0 => 0 ];
            $this->blds[$type->def_id]    = [];
            $this->envPies[$type->def_id] = [];
            $this->envIDs[$type->def_id]  = [ 0 => [] ];
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
                $this->envIDs[$type->def_id][$bld->def_id] = [];
            }
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                if ($area->def_value != 'Dry') {
                    $this->cmpl[$type->def_id][$area->def_id]    = [ 0 => 0 ];
                    $this->blds[$type->def_id][$area->def_id]    = [];
                    $this->envPies[$type->def_id][$area->def_id] = [];
                    $this->envIDs[$type->def_id][$area->def_id]  = [];
                    foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $bld) {
                        $this->cmpl[$type->def_id][$area->def_id][$bld->def_id] = 0;
                    }
                }
            }
        }
        return true;
    }
    
    public function addDataEnvs($ps, $areaType, $areaID)
    {
        $this->addRecFilt('area', $areaType, $areaID);
        $farmType = $ps->ps_characterize;
        $blds = RIIPsAreasBlds::where('ps_ar_bld_area_id', $areaID)
            ->get();
        if ($blds->isNotEmpty() && isset($this->cmpl[$farmType][$areaType])) {
            if (!in_array($ps->ps_id, $this->envIDs[0])) {
                $this->envIDs[0][] = $ps->ps_id;
            }
            if (!in_array($ps->ps_id, $this->envIDs[$farmType][0])) {
                $this->envIDs[$farmType][0][] = $ps->ps_id;
            }
            if (!in_array($ps->ps_id, $this->envIDs[$farmType][$areaType])) {
                $this->envIDs[$farmType][$areaType][] = $ps->ps_id;
            }
            foreach ($blds as $bld) {
                $type = $bld->ps_ar_bld_type;
                if (!in_array($ps->ps_id, $this->envIDs[$farmType][$type])) {
                    $this->envIDs[$farmType][$type][] = $ps->ps_id;
                }
                $this->addRecDat('bld' . $bld->ps_ar_bld_type, 1, $areaID);
                $this->cmpl[$farmType][$areaType][$type]++;
            }
        }
        $this->delRecFilt('area');
        return true;
    }
    
    public function calcBlds()
    {
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->cmpl[$type->def_id][0] = sizeof($this->envIDs[$type->def_id][0]);
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                $this->cmpl[$type->def_id][$area->def_id][0] 
                    = sizeof($this->envIDs[$type->def_id][$area->def_id]);
                foreach ($GLOBALS["SL"]->def->getSet('PowerScore Building Types') as $j => $bld) {
                    $color1 = $GLOBALS["SL"]->getCssColor('color-main-on');
                    $color2 = $GLOBALS["SL"]->getCssColor('color-main-bg');
                    $bldType = str_replace('Commercial/Warehouse', 'Commercial', 
                        str_replace('House/Garage', 'House', $bld->def_value));
                    $this->blds[$type->def_id][$area->def_id][] = [
                        $this->cmpl[$type->def_id][$area->def_id][$bld->def_id],
                        $bldType,
                        "'" . $GLOBALS["SL"]->printColorFadeHex(($j*0.16), $color1, $color2) . "'"
                    ];
                }
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $area) {
                $this->envPies[$type->def_id][$area->def_id] 
                    = $this->pieView($this->blds[$type->def_id][$area->def_id]);
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
            if ($this->cmpl[$farm][0] > 0) {
                $perc = round(100*sizeof($this->envIDs[$farm][$bld->def_id])/$this->cmpl[$farm][0]);
                $row = [ $bld->def_value, $perc ];
                if ($row[1] > 0) {
                    $row[1] = $row[1] . '% <sub class="slGrey">' 
                        . number_format(sizeof($this->envIDs[$farm][$bld->def_id])) . '</sub>';
                }
                foreach ($this->filts["b"]["val"] as $i => $val) {
                    $cell = '-';
                    if ($this->cmpl[$farm][$val][0] > 0) {
                        $cell = round(100*$this->cmpl[$farm][$val][$bld->def_id]
                            /$this->cmpl[$farm][$val][0]);
                        if ($cell > 0) {
                            $cell .= '% <sub class="slGrey">' 
                                . number_format($this->cmpl[$farm][$val][$bld->def_id]) . '</sub>';
                        }
                    }
                    $row[] = $cell;
                }
                $this->tblOut[] = $row;
            }
        }
        return view(
            'vendor.survloop.reports.inc-stat-tbl-percs', 
            [ "tblOut" => $this->tblOut ]
        )->render();
    }
    
    
}