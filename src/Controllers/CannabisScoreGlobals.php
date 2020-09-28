<?php
/**
  * CannabisScoreGlobals allows the attachment of custom variables and processes
  * in Survloop's main Globals class.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use App\Models\RIIManufacturers;

class CannabisScoreGlobals
{
    public function getAreaTypeFromNick($nick)
    {
        $defSet = 'PowerScore Growth Stages';
        switch ($nick) {
            case 'Mother': 
                return $GLOBALS["SL"]->def->getID($defSet, 'Mother Plants');
            case 'Clone':
                return $GLOBALS["SL"]->def->getID($defSet, 'Clone or Mother Plants');
            case 'Veg':
                return $GLOBALS["SL"]->def->getID($defSet, 'Vegetating Plants');
            case 'Flower':
                return $GLOBALS["SL"]->def->getID($defSet, 'Flowering Plants');
            case 'Dry':
                return $GLOBALS["SL"]->def->getID($defSet, 'Drying/Curing');
        }
        return '';
    }
    
    public function getTypeHours($typ)
    {
        return (($typ == 'Veg') 
            ? 18 
            : (($typ == 'Flower') ? 12 : 24));
    }
    
    public function getHvacEffic($defID)
    {
        switch ($defID) {
            case 247:
            case 510:
                return 115; // System A
            case 248:
            case 511:
                return 77;  // System B
            case 249:
            case 512:
                return 104; // System C
            case 250:
            case 513:
                return 65;  // System D
            case 356:
            case 514:
                return 90;  // System E (average of A-D)
            case 357:
            case 515: // 3.0 System F: Chilled Water System with Enhanced Dehumid
            case 516: // 3.0 System G: Chilled Water Systems with Integrated Dehumid
            case 517: // 3.0 System H: Greenhouse HVAC Systems
                return 90;  // System F
            case 251:
            case 518:
                return 90;  // Other System
            case 360:
            case 519:
                return 0.0000001; // None
        }
        return 0;
    }
    
    public function psTechs($lnks = false)
    {
        if (!$lnks) {
            return [
                'ps_harvest_batch'  => 'Perpetual Harvesting',
                'ps_has_water_pump' => 'Water Pumps',
                'ps_heat_water'     => 'Mechanical Water Heating',
                'ps_controls'       => 'Manual Environmental Controls',
                'ps_controls_auto'  => 'Automatic Environmental Controls',
                'ps_vertical_stack' => 'Vertical Stacking'
            ];
        }
        return [
            'ps_harvest_batch'
                => '<a href="/dash/compare-powerscores?fltPerp=1" '
                    . 'target="_blank">Perpetual Harvesting</a>',
            'ps_has_water_pump'
                => '<a href="/dash/compare-powerscores?fltPump=1" '
                    . 'target="_blank">Water Pumps</a>',
            'ps_heat_water'
                => '<a href="/dash/compare-powerscores?fltWtrh=1" '
                    . 'target="_blank">Mechanical Water Heating</a>',
            'ps_controls'
                => '<a href="/dash/compare-powerscores?fltManu=1" '
                    . 'target="_blank">Manual Environmental Controls</a>',
            'ps_controls_auto'
                => '<a href="/dash/compare-powerscores?fltAuto=1" '
                    . 'target="_blank">Automatic Environmental Controls</a>',
            'ps_vertical_stack'
                => '<a href="/dash/compare-powerscores?fltVert=1" '
                    . 'target="_blank">Vertical Stacking</a>'
            ];
    }
    
    public function psContact()
    {
        return [
            'ps_consider_upgrade' => 'Considering Upgrade Next 12 Months',
            'ps_incentive_wants'  => 'Wants Utility Incentives',
            'ps_incentive_used'   => 'Has Used Utility Incentives',
            'ps_newsletter'       => 'RII Newsletter'
        ];
    }
    
    public function chkScoreFiltCombs()
    {
        $this->v["fltComb"] = [
            'fltFarm' => [ 0, 143, 144, 145 ],
            'fltClimate' => [
                '', '1A', '2A', '2B', '3A', '3B', '3C', '4A', 
                '4B', '4C', '5A', '5B', '6A', '6B', '7A', '7B'
            ],
            'fltLgtArt' => [ 
                [0, 0],
                [237, 0], [237, 1],
                [160, 0], [160, 1],
                [161, 0], [161, 1],
                [162, 0], [162, 1],
                [163, 0], [163, 1]
            ],
            'fltLgtDep' => [ 
                [0, 0],
                [237, 0], [237, 1],
                [160, 0], [160, 1],
                [161, 0], [161, 1],
                [162, 0], [162, 1],
                [163, 0], [163, 1]
            ],
            'fltLgtSun' => [ 
                [0, 0],
                [237, 0], [237, 1],
                [160, 0], [160, 1],
                [161, 0], [161, 1],
                [162, 0], [162, 1],
                [163, 0], [163, 1]
            ],
            'fltLght' => [
                [0, 0],
                [237, 0], [237, 168], [237, 169], [237, 170], 
                [237, 171], [237, 164], [237, 165], [237, 203], 
                [160, 0], [160, 168], [160, 169], [160, 170], 
                [160, 171], [160, 164], [160, 165], [160, 203], 
                [161, 0], [161, 168], [161, 169], [161, 170], 
                [161, 171], [161, 164], [161, 165], [161, 203], 
                [162, 0], [162, 168], [162, 169], [162, 170], 
                [162, 171], [162, 164], [162, 165], [162, 203], 
                [163, 0], [163, 168], [163, 169], [163, 170], 
                [163, 171], [163, 164], [163, 165], [163, 203] 
            ],
            'fltHvac' => [ 
                [0, 0],
                [237, 247], [237, 248], [237, 249], [237, 250], 
                [237, 356], [237, 357], [237, 251], [237, 360], 
                [160, 247], [160, 248], [160, 249], [160, 250], 
                [160, 356], [160, 357], [160, 251], [160, 360], 
                [161, 247], [161, 248], [161, 249], [161, 250], 
                [161, 356], [161, 357], [161, 251], [161, 360], 
                [162, 247], [162, 248], [162, 249], [162, 250], 
                [162, 356], [162, 357], [162, 251], [162, 360], 
                [163, 247], [163, 248], [163, 249], [163, 250], 
                [163, 356], [163, 357], [163, 251], [163, 360]
            ],
            'fltRenew' => [
                [], [149], [159], [151], [150], [158], 
                [153], [154], [155], [156], [157] 
            ],
            'fltSize' => [ 0, 375, 376, 431, 377, 378 ],
            'fltPerp' => [ 0, 1 ],
            'fltWtrh' => [ 0, 1 ],
            'fltManu' => [ 0, 1 ],
            'fltAuto' => [ 0, 1 ],
            'fltVert' => [ 0, 1 ]
        ];
        return true;
    }
    
    public function allAvgsEmpty()
    {
        return [
            "tot" => 0,
            "ovr" => [ 0, 0 ], 
            "fac" => [ 0, 0 ], 
            "pro" => [ 0, 0 ], 
            "wtr" => [ 0, 0 ],
            "wst" => [ 0, 0 ],
            "hvc" => [ 0, 0 ],
            "lgt" => [ 0, 0 ], 
            "lgtAr" => [
                162 => [ 0, 0 ], 
                161 => [ 0, 0 ], 
                160 => [ 0, 0 ], 
                237 => [ 0, 0 ], 
                163 => [ 0, 0 ] 
            ],
            "area"  => [
                162 => [ 0, 0 ], 
                161 => [ 0, 0 ], 
                160 => [ 0, 0 ], 
                237 => [ 0, 0 ], 
                163 => [ 0, 0 ] 
            ]
        ];
    }
    
    public function allKeyDataAreasEmpty()
    {
        $ret = [];
        $keys = [
            'areas', 
            'sqfts', 
            'sqratio', 
            'lgtkWh', 
            'kWh', 
            'g'
        ];
        foreach ($keys as $k) {
            $ret[$k] = [
                0   => 0, 
                162 => 0, 
                161 => 0, 
                160 => 0, 
                237 => 0, 
                163 => 0 
            ];
        }
        $ret2 = [];
        foreach ([ 0, 144, 145, 143 ] as $k) {
            $ret2[$k] = $ret;
        }
        return $ret2;
    }
    
    
    public function getSizeDefRange($defID)
    {
        $defSet = 'Indoor Size Groups';
        if ($defID == $GLOBALS["SL"]->def->getID($defSet, '<5,000 sf')) {
            return [0, 5000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID($defSet, '5,000-10,000 sf')) {
            return [5000, 10000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID($defSet, '10,000-30,000 sf')) {
            return [10000, 30000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID($defSet, '30,000-50,000 sf')) {
            return [30000, 50000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID($defSet, '50,000+ sf')) {
            return [ 50000, 1000000000 ];
        }
        return [ 0, 1000000000 ];
    }
    
    public function getSizeDefID($size)
    {
        $defSet = 'Indoor Size Groups';
        $size = intVal($size);
        if ($size < 5000) {
            return $GLOBALS["SL"]->def->getID($defSet, '<5,000 sf');
        } elseif ($size >= 5000 && $size < 10000) {
            return $GLOBALS["SL"]->def->getID($defSet, '5,000-10,000 sf');
        } elseif ($size >= 10000 && $size < 30000) {
            return $GLOBALS["SL"]->def->getID($defSet, '10,000-30,000 sf');
        } elseif ($size >= 30000 && $size < 50000) {
            return $GLOBALS["SL"]->def->getID($defSet, '30,000-50,000 sf');
        } else {
            return $GLOBALS["SL"]->def->getID($defSet, '50,000+ sf');
        }
        return 0;
    }

    public function loadManufactIDs()
    {
        if (!isset($this->v["manufacts"])) {
            $this->v["manufacts"] = [];
            $chk = RIIManufacturers::get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $manu) {
                    $this->v["manufacts"][$manu->manu_id] = $manu->manu_name;
                }
            }
        }
        asort($this->v["manufacts"]);
        return $this->v["manufacts"];
    }
    
    
    
}