<?php
/**
  * ScoreStats is an extension of the SurvStatsGraph which creates an Stats instance to analyze PowerScores.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use Auth;
use App\Models\RIIPSAreas;
use SurvLoop\Controllers\Stats\SurvStatsTbl;
use SurvLoop\Controllers\Stats\SurvStatTh;
use SurvLoop\Controllers\Stats\SurvStatsGraph;

class ScoreStats extends SurvStatsGraph
{
    public $sfFarms = [];
    public $sfSizes = [];
    public $sfAuto  = [];
    public $sfVert  = [];
    public $sfHvacs = [];
    public $sfCups  = [];
    public $sfLgts  = [];
    
    function __construct($filts = ['farm'])
    {
        $this->v["psComplete"] = 243;
        $lgtTypes = [
            'flw-lgty', 
            'veg-lgty', 
            'cln-lgty', 
            'mth-lgty'
        ];
        $baseUrl = '/dash/compare-powerscores';
        /* if (Auth::user() && Auth::user()->hasRole('partner')) {
            $baseUrl = '/dash/partner-compare-powerscores';
        } */
        $this->sfFarms = [
            [
                144, 
                145, 
                143
            ], [
                '<a href="' . $baseUrl . '?fltFarm=144"'
                    . ' target="_blank">Indoor',
                '<a href="' . $baseUrl . '?fltFarm=145"'
                    . ' target="_blank">Greenhouse/Mixed</a>',
                '<a href="' . $baseUrl . '?fltFarm=143"'
                    . ' target="_blank">Outdoor</a>'
            ]
        ];
        $this->sfSizes = [
            [
                375, 
                376, 
                431, 
                377, 
                378
            ], [
                '<a href="' . $baseUrl . '?fltFarm=144&fltSize=375"'
                    . ' target="_blank">&lt;5,000 sf</a>',
                '<a href="' . $baseUrl . '?fltFarm=144&fltSize=376"'
                    . ' target="_blank">5,000-10,000 sf</a>',
                '<a href="' . $baseUrl . '?fltFarm=144&fltSize=431"'
                    . ' target="_blank">10,000-30,000 sf</a>',
                '<a href="' . $baseUrl . '?fltFarm=144&fltSize=377"'
                    . ' target="_blank">30,000-50,000 sf</a>',
                '<a href="' . $baseUrl . '?fltFarm=144&fltSize=378"'
                    . ' target="_blank">50,000+ sf</a>'
            ]
        ];
        $this->sfAuto = [
            [
                1, 
                2, 
                3
            ], [
                'Automatic Controls', 
                'Manual Controls',
                '<a href="' . $baseUrl . '?fltFarm=144&fltAuto=1&fltManu=1"'
                    . ' target="_blank">Using Both</a>'
            ]
        ];
        $this->sfVert = [
            [
                0, 
                1
            ], [
                'Without Vertical Stacking',
                '<a href="' . $baseUrl . '?fltFarm=144&fltVert=1"'
                    . ' target="_blank">With Vertical Stacking</a>'
            ]
        ];
        $this->sfHvacs = [
            [
                247, 
                248, 
                249, 
                250, 
                356, 
                357, 
                251, 
                360
            ], [
                'System A', 
                'System B', 
                'System C', 
                'System D', 
                'System E', 
                'System F', 
                'Other System', 
                'None'
            ]
        ];
        $this->sfCups = [
            [  
                230, 
                231, 
                369
            ], [
                '<a href="' . $baseUrl . '?fltCup=230" '
                    . 'target="_blank">Cultivation Classic</a>',
                '<a href="' . $baseUrl . '?fltCup=231" '
                    . 'target="_blank">Emerald Cup Regenerative Award</a>',
                '<a href="' . $baseUrl . '?fltCup=369" '
                    . 'target="_blank">NWPCC</a>'
            ]
        ];
        $this->sfLgts = [];

        $this->v["filts"] = $filts;
        $this->constructScoreBasicData();
        if (sizeof($filts) > 0) {
            foreach ($filts as $f) {
                if ($f == 'farm') {
                    $this->addFilt( // stat filter 'a'
                        'farm', 
                        'Farm Type', 
                        $this->sfFarms[0], 
                        $this->sfFarms[1]
                    );
                } elseif ($f == 'vert') {
                    $this->addFilt(
                        'vert', 
                        'Vertical Stacking', 
                        $this->sfVert[0], 
                        $this->sfVert[1]
                    );
                } elseif ($f == 'size') {
                    $this->addFilt(
                        'size', 
                        'Flowering Square Feet', 
                        $this->sfSizes[0], 
                        $this->sfSizes[1]
                    );
                } elseif ($f == 'auto') {
                    $this->addFilt(
                        'auto', 
                        'Automatic Controls', 
                        $this->sfAuto[0], 
                        $this->sfAuto[1]
                    );
                } elseif ($f == 'hvac') {
                    $this->addFilt(
                        'hvac', 
                        'HVAC System Type', 
                        $this->sfHvacs[0], 
                        $this->sfHvacs[1]
                    );
                } elseif ($f == 'cups') {
                    $this->addFilt(
                        'cups', 
                        'Data Sets', 
                        $this->sfCups[0], 
                        $this->sfCups[1]
                    );
                } elseif (in_array($f, $lgtTypes)) {
                    if ($f == 'flw-lgty') {
                        $this->loadLgts(162);
                    } elseif ($f == 'veg-lgty') {
                        $this->loadLgts(161);
                    } elseif ($f == 'cln-lgty') {
                        $this->loadLgts(160);
                    } elseif ($f == 'mth-lgty') {
                        $this->loadLgts(237);
                    }
                    foreach ($this->sfLgts as $val => $name) {
                        $this->addFilt(
                            'lgt' . $val, 
                            $name, 
                            [0, 1], 
                            ['No', 'Yes']
                        );
                    }
                } elseif ($f == 'tech') {
                    foreach ($GLOBALS["CUST"]->psTechs(true) 
                        as $fld => $name) {
                        if ($fld != 'PsVerticalStack') {
                            $this->addFilt(
                                $fld, 
                                $name, 
                                [0, 1], 
                                ['No', 'Yes']
                            );
                        }
                    }
                } elseif (in_array($f, ['pow1', 'pow2'])) {
                    $set = $GLOBALS["SL"]->def
                        ->getSet('PowerScore Onsite Power Sources');
                    foreach ($set as $def) {
                        if (($f == 'pow1' && in_array($def->DefID, 
                                [149, 159, 151, 150, 158])) 
                            || ($f != 'pow1' && !in_array($def->DefID, 
                                [149, 159, 151, 150, 158]))) {
                            $lab = '<a href="/dash/compare-powerscores?fltRenew=' 
                                . $def->DefID . '" target="_blank">' 
                                . $def->DefValue . '</a>';
                            $this->addFilt(
                                'powr' . $def->DefID, 
                                $lab, 
                                [0, 1], 
                                ['No', 'Yes']
                            );
                        }
                    }
                }
            }
        }
        return true;
    }
    
    public function constructScoreBasicData()
    {
        $this->addDataType( // stat var 'a'
            'fac',  
            '<nobr>Facility <sup class="slBlueDark">kWh/SqFt</sup></nobr>'
        );
        $this->addDataType( // stat var 'b'
            'pro',  
            '<nobr>Production <sup class="slBlueDark">g/kWh</sup></nobr>'
        );
        $this->addDataType( // stat var 'c'
            'hvc',  
            '<nobr>HVAC <sup class="slBlueDark">kWh/SqFt</sup></nobr>'
        );
        $this->addDataType( // stat var 'd'
            'lgt',  
            '<nobr>Lighting <sup class="slBlueDark">W/SqFt</sup></nobr>'
        );
        $this->addDataType(
            'lgtM', 
            '<nobr><i class="fa fa-level-up fa-rotate-90 slGrey mL5 mR5"'
            . ' aria-hidden="true"></i>'
            . 'Mother <sup class="slBlueDark">W/SqFt</sub>'
        );
        $this->addDataType(
            'lgtC', 
            '<nobr><i class="fa fa-level-up fa-rotate-90 slGrey mL5 mR5"'
            . ' aria-hidden="true"></i>'
            . 'Clone <sup class="slBlueDark">W/SqFt</sup></nobr>'
        );
        $this->addDataType(
            'lgtV', 
            '<nobr><i class="fa fa-level-up fa-rotate-90 slGrey mL5 mR5"'
            . ' aria-hidden="true"></i>'
            . 'Veg <sup class="slBlueDark">W/SqFt</sup></nobr>'
        );
        $this->addDataType(
            'lgtF', 
            '<nobr><i class="fa fa-level-up fa-rotate-90 slGrey mL5 mR5"'
            . ' aria-hidden="true"></i>'
            . 'Flower <sup class="slBlueDark">W/SqFt</sup></nobr>'
        );
        return true;
    }
    
    public function applyScoreFilts($ps = null, $size = 0, $psTags = [])
    {
        if (sizeof($this->filts) > 0) {
            foreach ($this->filts as $let => $filt) {
                if ($filt["abr"] == 'farm') {
                    $this->addRecFilt(
                        'farm', 
                        $ps->PsCharacterize, 
                        $ps->PsID
                    );
                } elseif ($filt["abr"] == 'size') {
                    if ($size > 0) {
                        $sizeDef = 0;
                        if ($size < 5000) {
                            $sizeDef = 375;
                        } elseif ($size >= 5000 && $size < 10000) {
                            $sizeDef = 376;
                        } elseif ($size >= 10000 && $size < 30000) {
                            $sizeDef = 431;
                        } elseif ($size >= 30000 && $size < 50000) {
                            $sizeDef = 377;
                        } elseif ($size >= 50000) {
                            $sizeDef = 378;
                        }
                        $this->addRecFilt('size', $sizeDef, $ps->PsID);
                    }
                } elseif ($filt["abr"] == 'auto') {
                    if (isset($ps->PsControls) 
                        && intVal($ps->PsControls) == 1 
                        && isset($ps->PsControlsAuto) 
                        && intVal($ps->PsControlsAuto) == 1) {
                        $this->addRecFilt('auto', 3, $ps->PsID);
                    } elseif (isset($ps->PsControlsAuto) 
                        && intVal($ps->PsControlsAuto) == 1) {
                        $this->addRecFilt('auto', 1, $ps->PsID);
                    } elseif (isset($ps->PsControls) 
                        && intVal($ps->PsControls) == 1) {
                        $this->addRecFilt('auto', 2, $ps->PsID);
                    }
                } elseif ($filt["abr"] == 'vert') {
                    if (isset($ps->PsVerticalStack)) {
                        $this->addRecFilt(
                            'vert', 
                            intVal($ps->PsVerticalStack), 
                            $ps->PsID
                        );
                    } else {
                        $this->addRecFilt(
                            'vert', 
                            0, 
                            $ps->PsID
                        );
                    }
                } elseif ($filt["abr"] == 'cups') {
                    if (sizeof($psTags) > 0) {
                        foreach ($psTags as $tag) {
                            if ($tag[0] == 'cups') {
                                $this->addRecFilt(
                                    'cups', 
                                    $tag[1], 
                                    $ps->PsID
                                );
                            }
                        }
                    }
                } else {
                    foreach ($GLOBALS["CUST"]->psTechs() 
                        as $fld => $name) {
                        if ($filt["abr"] == $fld 
                            && isset($ps->{ $fld })) {
                            $this->addRecFilt(
                                $fld, 
                                intVal($ps->{ $fld }), 
                                $ps->PsID
                            );
                        }
                    }
                    $set = $GLOBALS["SL"]->def
                        ->getSet('PowerScore Onsite Power Sources');
                    foreach ($set as $def) {
                        if ($filt["abr"] == 'powr' . $def->DefID 
                            && sizeof($psTags) > 0) {
                            foreach ($psTags as $tag) {
                                if ($tag[0] == 'powr') {
                                    $this->addRecFilt(
                                        'powr' . $tag[1], 
                                        1, 
                                        $ps->PsID
                                    );
                                }
                            }
                        }
                    }
                    foreach ($this->sfLgts as $val => $name) {
                        if ($filt["abr"] == 'lgt' . $val 
                            && sizeof($psTags) > 0) {
                            foreach ($psTags as $tag) {
                                if ($tag[0] == $this->v["filts"][0]) {
                                    $this->addRecFilt(
                                        'lgt' . $tag[1], 
                                        1, 
                                        $ps->PsID
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    public function addScoreData($ps = null, $area = null)
    {
        if ($ps && isset($ps->PsID)) {
            $dataPoints = [
                ['fac', 'Facility'],
                ['pro', 'Production'],
                ['hvc', 'Hvac'],
                ['lgt', 'Lighting']
            ];
            foreach ($dataPoints as $type) {
                if (isset($ps->{ 'PsEffic' . $type[1] . 'Status' })
                    && intVal($ps->{ 'PsEffic' . $type[1] . 'Status' }) 
                        == $this->v["psComplete"]) {
                    $this->addRecDat(
                        $type[0], 
                        $ps->{ 'PsEffic' . $type[1] }, 
                        $ps->PsID
                    );
                }
            }
            if (isset($ps->PsEfficLightingStatus)
                && intVal($ps->PsEfficLightingStatus) 
                    == $this->v["psComplete"]) {
                foreach (['Mother', 'Clone', 'Veg', 'Flower'] as $type) {
                    $area = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                        ->where('PsAreaType', 
                            $GLOBALS["CUST"]->getAreaTypeFromNick($type))
                        ->first();
                    if ($area && isset($area->PsAreaSize) 
                        && $area->PsAreaSize > 0) {
                        if (isset($area->PsAreaLightingEffic) 
                            && $area->PsAreaLightingEffic > 0) {
                            $this->addRecDat('lgt' . substr($type, 0, 1), 
                                $area->PsAreaLightingEffic, $ps->PsID);
                        }
                    }
                }
            }
        }
        return true;
    }
    
    public function printScoreAvgsTblPrep($fltAbbr = '', $lnk = '')
    {
        $fLet = $this->fAbr($fltAbbr);
        $tbl = new SurvStatsTbl('', [0, 3], [1]);
        if ($fLet != '' && !isset($this->filts[$fLet])) {
            echo 'error in printScoreAvgsTblPrep(' . $fltAbbr;
            exit;
        }
        foreach ($this->filts[$fLet]["val"] as $v => $val) {
            $lab = $this->filts[$fLet]["vlu"][$v];
            if (trim($lnk) != '') {
                $lab = '<a href="' 
                    . str_replace('[[val]]', $val, $lnk) 
                    . '" target="_blank">' . $lab 
                    . '</a>';
            }
            $tbl->addHeaderCol(
                $lab, 
                $this->getDatCnt($fLet . $val)
            );
        }
        foreach ($this->datMap as $dLet => $dat) {
            $tbl->addRowStart($dat["lab"]);
            $tbl->addRowCell(
                $this->getDatLetAvg($dLet), 
                $this->getDatCntForDatLet('1', $dLet)
            );
            foreach ($this->filts[$fLet]["val"] as $v => $val) {
                $cnt = $this->getDatCntForDatLet($fLet . $val, $dLet);
                $avg = $this->getDatLetAvg($dLet, $fLet . $val);
                $tbl->addRowCell($avg, $cnt);
            }
        }
        $tbl->rows[0][1] = new SurvStatTh(
            'Averages', 
            $this->getDatCntForDatLet('1', 'a')
        );
        return $tbl;
    }
    
    public function printScoreAvgsTbl($fltAbbr = '', $lnk = '')
    {
        return view(
            'vendor.cannabisscore.inc-score-avgs-report-table', 
            [
                "tbl" => $this->printScoreAvgsTblPrep($fltAbbr, $lnk)
            ]
        )->render();
    }
    
    public function printScoreAvgsExcel($fltAbbr = '', $lnk = '')
    {
        return view(
            'vendor.cannabisscore.inc-score-avgs-report-excel', 
            [
                "tbl" => $this->printScoreAvgsTblPrep($fltAbbr, $lnk)
            ]
        )->render();
    }
    
    public function printScoreAvgsTbl2Prep($lnk = '', $only = [])
    {
        $tbl = new SurvStatsTbl('', [0, 3], [1]);
        foreach ($this->filts as $fLet => $filt) {
            if (sizeof($only) == 0 
                || in_array($fLet, $only)) {
                $tbl->addHeaderCol(
                    $filt["lab"], 
                    $this->getDatCnt($fLet . '1')
                );
            }
        }
        foreach ($this->datMap as $dLet => $dat) {
            $tbl->addRowStart($dat["lab"]);
            $tbl->addRowCell(
                $this->getDatLetAvg($dLet), 
                $this->getDatCntForDatLet('1', $dLet)
            );
            foreach ($this->filts as $fLet => $filt) {
                if (sizeof($only) == 0 
                    || in_array($fLet, $only)) {
                    $cnt = $this->getDatCntForDatLet(
                        $fLet . '1', 
                        $dLet
                    );
                    $avg = $this->getDatLetAvg(
                        $dLet, 
                        $fLet . '1'
                    );
                    $tbl->addRowCell($avg, $cnt);
                }
            }
        }
        $tbl->rows[0][1] = new SurvStatTh(
            'Averages', 
            $this->getDatCntForDatLet('1', 'a')
        );
        return $tbl;
    }
    
    public function printScoreAvgsTbl2($lnk = '', $only = [])
    {
        return view(
            'vendor.cannabisscore.inc-score-avgs-report-table', 
            [
                "tbl" => $this->printScoreAvgsTbl2Prep($lnk, $only)
            ]
        )->render();
    }
    
    public function printScoreAvgsExcel2($lnk = '', $only = [])
    {
        return view(
            'vendor.cannabisscore.inc-score-avgs-report-excel', 
            [
                "tbl" => $this->printScoreAvgsTbl2Prep($lnk, $only)
            ]
        )->render();
    }
    
    protected function loadLgts($area)
    {
        $base = '<a href="/dash/compare-powerscores'
            . '?fltFarm=144&fltLght=' . $area;
        $end = '" target="_blank">';
        $this->sfLgts = [
            168 => $base . '-168' . $end . 'HID (double-ended HPS)</a>',
            169 => $base . '-169' . $end . 'HID (single-ended HPS)</a>',
            170 => $base . '-170' . $end . 'HID (double-ended MH)</a>',
            171 => $base . '-171' . $end . 'HID (single-ended MH)</a>',
            164 => $base . '-164' . $end . 'CMH</a>',
            165 => $base . '-165' . $end . 'Fluorescent</a>',
            203 => $base . '-203' . $end . 'LED</a>',
            2   => 'No Lights'
        ];
        return true;
    }
    
}