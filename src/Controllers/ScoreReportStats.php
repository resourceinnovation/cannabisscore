<?php
/**
  * ScoreReportStats starts off a report with info on a few basic filters with ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use SurvLoop\Controllers\Stats\SurvStatsGraph;
use CannabisScore\Controllers\ScoreLookups;
use CannabisScore\Controllers\ScoreStats;

class ScoreReportStats
{
    public $v        = [];
    public $searcher = null;
    public $lgts     = [];
    
    protected function prepStatFilts()
    {
        $lookups = new ScoreLookups;
        foreach ($lookups->v as $var => $val) {
            $this->v[$var] = $val;
        }
        $this->v["psComplete"] = 243;
            //[242, 243, 364],
            //['Incomplete', 'Complete', 'Archived']
        $this->v["sfFarms"] = [
            [144, 145, 143],
            ['Indoor', 'Greenhouse/Mixed', 'Outdoor']
        ];
        $this->v["sfAreasGrow"] = [
            [162, 161, 160, 237],
            ['Flowering', 'Vegetating', 'Cloning', 'Mothers']
        ];
        $this->v["sfAreasAll"] = [
            [162, 161, 160, 237, 163],
            ['Flowering', 'Vegetating', 'Cloning', 'Mothers', 'Drying']
        ];
        $this->v["sfAreasAlt"] = [
            [162, 161, 160, 163],
            ['Flowering', 'Vegetating', 'Cloning/Mothers', 'Drying']
        ];
        $this->v["sfHvac"] = [
            [247, 248, 249, 250, 356, 357, 251, 360], 
            ['System A', 'System B', 'System C', 'System D', 'System E', 'System F', 'Other System', 'None']
        ];
        $this->v["scoreRowLabs"] = [
            [ 'Facility Score',   'kBtu/SqFt' ], 
            [ 'Production Score', 'g/kBtu'    ], 
            [ 'Lighting Score',   'W/SqFt'   ], 
            [ 'HVAC Score',       'kWh/SqFt' ]
        ];
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone & Mother Plants'),
            'Veg'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing')
        ];
        $this->v["sfLgts"] = [ [], [] ];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Light Types') as $l) {
            $this->v["sfLgts"][0][] = $l->def_id;
            $this->v["sfLgts"][1][] = str_replace('single-ended ', '1x', 
                str_replace('double-ended ', '2x', $l->def_value));
        }
        $this->v["sfLgts"][0][] = 2;
        $this->v["sfLgts"][1][] = 'No Lights';
        return true;
    }
    
    protected function motherToClone($type)
    {
        return (($type == $this->v["areaTypes"]["Mother"]) 
            ? $this->v["areaTypes"]["Clone"] : $type);
    }
    
    protected function getFlowerSize($areas = null)
    {
        $flowerSize = 0;
        if ($areas && $areas->isNotEmpty()) {
            foreach ($areas as $a) {
                if ($a->ps_area_type == $this->v["areaTypes"]["Flower"]) {
                    $flowerSize = $a->ps_area_size;
                }
            }
        }
        return $flowerSize;
    }
    
    protected function initSearcher()
    {
        $this->searcher = new CannabisScoreSearcher;
        $this->searcher->getSearchFilts(1);
        $this->searcher->loadAllScoresPublic();
        return true;
    }
    
    protected function initClimateFilts()
    {
        $GLOBALS["SL"]->loadStates();
        $this->initSearcher();
        $this->v["fltStateClim"] = ((isset($this->searcher->searchFilts["fltStateClim"]))
            ? trim($this->searcher->searchFilts["fltStateClim"]) : '');
        $this->prepStatFilts();
        return true;
    }
    
}