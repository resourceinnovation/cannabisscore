<?php
/**
  * ScoreReportAvgs generates the entire PowerScore Averages Report, using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSForCup;
use App\Models\RIIPSLicenses;
use SurvLoop\Controllers\Stats\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\CannabisScoreSearcher;
use CannabisScore\Controllers\ScoreReportStats;

class ScoreReportAvgs extends ScoreReportStats
{
    public function getAllPowerScoreAvgsPublic()
    {
        $this->calcAllPowerScoreAvgs();
        $this->v["psTechs"] = $GLOBALS["CUST"]->psTechs();
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'PowerScore_Averages-' . date("Y-m-d") . '.xls');
        }
        return view('vendor.cannabisscore.nodes.773-powerscore-avgs', $this->v)->render();
    }
    
    public function getMorePowerStats()
    {
        $this->initSearcher();
        $this->prepStatFilts();
        $this->calcMorePowerStats();
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'PowerScore_Averages-' . date("Y-m-d") . '.xls');
        }
        return view('vendor.cannabisscore.nodes.859-report-more-stats', $this->v)->render();
    }
    
    public function getPowerScoreFinalReport()
    {
        $this->calcAllPowerScoreAvgs();
        $this->v["allscores"] = $this->searcher->v["allscores"];
        $this->calcMorePowerStats();
        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view('vendor.cannabisscore.nodes.797-powerscore-report-tbls', $this->v)->render();
    }
    
    protected function calcAllPowerScoreAvgs()
    {
        $this->initSearcher();
        $this->prepStatFilts();
        if ($this->searcher->v["allscores"]->isEmpty()) {
            return false;
        }
        $psTags = [];
        foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
            $psTags[$ps->PsID] = $this->loadPsTags($ps, RIIPSAreas::where('PsAreaPSID', $ps->PsID)->get());
        }
        $this->v["scoreSets"] = [
            ['farm',     'PowerScore Averages by Type of Farm'],
            ['cups',     'PowerScore Averages by Competition / Data Set'],
            ['flw-lgty', 'Indoor PowerScore Averages by Type of Flowering Lights'],
            ['veg-lgty', 'Indoor PowerScore Averages by Type of Veg Lights'],
            ['cln-lgty', 'Indoor PowerScore Averages by Type of Cloning/Mother Lights'],
            ['tech',     'PowerScore Averages by Technique'],
            ['pow1',     'PowerScore Averages by Other Power Sources'],
            ['pow2',     'PowerScore Averages by Other Power Sources']
            ];
        foreach ($this->v["scoreSets"] as $i => $set) {
            $tmp = new ScoreStats([$set[0]]);
            $tmp->loadMap();
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                if (!in_array($i, [2, 3, 4]) || $ps->PsCharacterize == 144) {
                    $tmp->applyScoreFilts($ps, 0, $psTags[$ps->PsID]);
                    $tmp->addScoreData($ps);
                    $tmp->resetRecFilt();
                }
            }
            $tmp->calcStats();
            if (in_array($i, [0, 1])) {
                $this->v["scoreSets"][$i][2] = $tmp->printScoreAvgsTbl($set[0]);
            } else {
                $this->v["scoreSets"][$i][2] = $tmp->printScoreAvgsTbl2();
            }
            unset($tmp);
        }
        return true;
    }
    
    protected function calcMorePowerStats()
    {
        $this->v["statMisc"] = new SurvStatsGraph;
        $this->v["statMisc"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statMisc"]->addDataType('g', 'Grams', 'g');                           // stat var a
        $this->v["statMisc"]->addDataType('kWh', 'Facility Kilowatt Hours', 'kWh');     // stat var b
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
            $this->v["statMisc"]->addDataType($fld, $name);
        }
        foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) {
            $this->v["statMisc"]->addDataType($fld, $name);
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $renew) {
            $this->v["statMisc"]->addDataType('rnw' . $renew->DefID, $renew->DefValue);
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore License Types') as $lic) {
            $this->v["statMisc"]->addDataType('lic' . $lic->DefID, $lic->DefValue);
        }
        $this->v["statMisc"]->loadMap();
        
        $this->v["statSqft"] = new SurvStatsGraph;
        $this->v["statSqft"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statSqft"]->addFilt('area', 'Growth Stage', $this->v["sfAreasAll"][0], $this->v["sfAreasAll"][1]); // stat filter b
        $this->v["statSqft"]->addDataType('sqft', 'Square Feet', 'sqft');                        // stat var a
        $this->v["statSqft"]->loadMap();
        
        $this->v["statLgts"] = new SurvStatsGraph;
        $this->v["statLgts"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statLgts"]->addFilt('area', 'Growth Stage', $this->v["sfAreasGrow"][0], $this->v["sfAreasGrow"][1]); // stat filter b
        
        $this->v["statLgts"]->addFilt('lgty', 'Lights Types', $this->lgts[0], $this->lgts[1]); // stat filter c
        $this->v["statLgts"]->addDataType('sqft', 'Square Feet', 'sqft'); // stat var a
        $this->v["statLgts"]->addDataType('sun', 'Sunlight');             // stat var b
        $this->v["statLgts"]->addDataType('dep', 'Light deprivation');    // stat var c
        $this->v["statLgts"]->addDataType('arf', 'Artificial Light');     // stat var d
        $this->v["statLgts"]->addDataType('kWh', 'kWh', 'kWh');           // stat var e
        $this->v["statLgts"]->addDataType('lgtfx', 'Fixtures');           // stat var f
        $this->v["statLgts"]->addDataType('W', 'W', 'W');                 // stat var g
        $this->v["statLgts"]->loadMap();
        
        $this->v["statLarf"] = new SurvStatsGraph;
        $this->v["statLarf"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statLarf"]->addFilt('area', 'Growth Stage', $this->v["sfAreasGrow"][0], $this->v["sfAreasGrow"][1]); // stat filter b
        $this->v["statLarf"]->addDataType('sqft', 'Square Feet', 'sqft');               // stat var a
        $this->v["statLarf"]->addDataType('W', 'W', 'W');                               // stat var b
        
        $this->v["statHvac"] = new SurvStatsGraph;
        $this->v["statHvac"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statHvac"]->addFilt('area', 'Growth Stage', $this->v["sfAreasGrow"][0], $this->v["sfAreasGrow"][1]); // stat filter b
        $this->v["statHvac"]->addFilt('hvac', 'HVAC System', $this->v["sfHvac"][0], $this->v["sfHvac"][1]); // stat filter c
        $this->v["statHvac"]->addDataType('kWh/sqft', 'kWh/sqft', 'kWh/sqft');                     // stat var a
        $this->v["statHvac"]->addDataType('sqft', 'Square foot', 'sqft');                          // stat var b
        $this->v["statHvac"]->loadMap();

        $this->v["statMore"] = [];
        $this->v["statMore"]["scrHID"] = [ 0, 0, 0, 0, 0, 0, 0 ]; // Overall, Fac, Prod, Lght, HVAC, count, Sqft/Fixture
        $this->v["statMore"]["scrLED"] = [ 0, 0, 0, 0, 0, 0 ];
        $this->v["statMore"]["scrLHR"] = [ 0, 0, 0, 0, 0 ];
        
        $this->v["enrgys"] = [
            "cmpl"  => [ 0 => 0 ],
            "extra" => [ 0 => 0, 1 => [] ],
            "data"  => [],
            "pie"   => []
            ];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->v["enrgys"]["cmpl"][$type->DefID] = [ 0 => 0 ];
            $this->v["enrgys"]["extra"][$type->DefID] = [ 0 => 0 ];
            $this->v["enrgys"]["data"][$type->DefID] = [];
            $this->v["enrgys"]["pie"][$type->DefID] = [];
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew) {
                $this->v["enrgys"]["cmpl"][$type->DefID][$renew->DefID] = 0;
                $this->v["enrgys"]["extra"][$type->DefID][$renew->DefID] = 0;
            }
        }
        
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $this->v["statMisc"]->resetRecFilt();
                $this->v["statMisc"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                $this->v["statMisc"]->addRecDat('g', $ps->PsGrams, $ps->PsID);
                $this->v["statMisc"]->addRecDat('kWh', $ps->PsKWH, $ps->PsID);
                foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->PsID);
                    }
                }
                foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->PsID);
                    }
                }
                $chk = RIIPSRenewables::where('PsRnwPSID', $ps->PsID)->get();
                if ($chk->isNotEmpty()) {
                    $this->v["enrgys"]["cmpl"][0]++;
                    $this->v["enrgys"]["cmpl"][$ps->PsCharacterize][0]++;
                    foreach ($chk as $renew) {
                        $this->v["statMisc"]->addRecDat('rnw' . $renew->PsRnwRenewable, 1, $ps->PsID);
                        $this->v["enrgys"]["cmpl"][$ps->PsCharacterize][$renew->PsRnwRenewable]++;
                    }
                }
                $chk = RIIPSLicenses::where('PsLicPSID', $ps->PsID)->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $lic) {
                        $this->v["statMisc"]->addRecDat('lic' . $lic->PsLicLicense, 1, $ps->PsID);
                    }
                }
                
                $this->v["statSqft"]->resetRecFilt();
                $this->v["statSqft"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                
                $this->v["statLgts"]->resetRecFilt();
                $this->v["statLgts"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                $hasArtifLgt = false;
                $areas = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        if (isset($area->PsAreaType) && $area->PsAreaType == $this->v["areaTypes"]["Flower"]
                            && intVal($area->PsAreaHasStage) == 1 && intVal($area->PsAreaLgtArtif) == 1) {
                            $hasArtifLgt = true;
                        }
                    }
                }
                if ($hasArtifLgt) {
                    $this->v["statLarf"]->resetRecFilt();
                    $this->v["statLarf"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                }
                
                $this->v["statHvac"]->resetRecFilt();
                $this->v["statHvac"]->addRecFilt('farm', $ps->PsCharacterize, $ps->PsID);
                
                $kwh = 0;
                $sqft = [ 0 => 0, 162 => 0, 161 => 0, 160 => 0, 237 => 0, 163 => 0 ];
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        if (isset($area->PsAreaType) && intVal($area->PsAreaHasStage) == 1) {
                            $aID = $area->PsAreaID;
                            $aTyp = $this->getAType($area);
                            $this->v["statSqft"]->addRecFilt('area', $area->PsAreaType, $ps->PsID);
                            $this->v["statSqft"]->addRecDat('sqft', intVal($area->PsAreaSize), $ps->PsID);
                            $this->v["statSqft"]->delRecFilt('area');
                            
                            $hvac = 360;
                            if (isset($area->PsAreaHvacType) && intVal($area->PsAreaHvacType) > 0) {
                                $hvac = $area->PsAreaHvacType;
                            }
                            $this->v["statHvac"]->addRecFilt('area', $area->PsAreaType, $ps->PsID);
                            $this->v["statHvac"]->addRecFilt('hvac', $area->PsAreaHvacType, $aID);
                            $this->v["statHvac"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                            $this->v["statHvac"]->addRecDat('kWh/sqft', $GLOBALS["CUST"]->getHvacEffic($hvac), $aID);
                            $this->v["statHvac"]->delRecFilt('hvac');
                            $this->v["statHvac"]->delRecFilt('area');
                            
                            $this->v["statLgts"]->addRecFilt('area', $area->PsAreaType, $aID);
                            $this->v["statLgts"]->addRecDat('sun', intVal($area->PsAreaLgtSun), $aID);
                            $this->v["statLgts"]->addRecDat('dep', intVal($area->PsAreaLgtDep), $aID);
                            $this->v["statLgts"]->addRecDat('arf', intVal($area->PsAreaLgtArtif), $aID);
                            $w = 0;
                            if (isset($area->PsAreaTotalLightWatts) && $area->PsAreaTotalLightWatts > 0) {
                                $w = ($area->PsAreaTotalLightWatts
                                    *$GLOBALS["CUST"]->getTypeHours($area->PsAreaType)*365)/1000;
                                $this->v["statLgts"]->addRecDat('W', $area->PsAreaTotalLightWatts, $aID);
                                if ($hasArtifLgt) {
                                    $this->v["statLarf"]->addRecFilt('area', $area->PsAreaType, $aID);
                                    $this->v["statLarf"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                                    $this->v["statLarf"]->addRecDat('W', $area->PsAreaTotalLightWatts, $aID);
                                }
                            }
                            $this->v["statLgts"]->addRecDat('kWh', $w, $aID);
                            $foundLights = $foundHID = $foundLED = false;
                            $fixtureCnt = 0;
                            $lgts = RIIPSLightTypes::where('PsLgTypAreaID', $area->getKey())
                                ->get();
                            if ($lgts->isNotEmpty()) {
                                foreach ($lgts as $lgt) {
                                    if (isset($lgt->PsLgTypLight) && intVal($lgt->PsLgTypLight) > 0
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0) {
                                        $this->v["statLgts"]->addRecFilt('lgty', $lgt->PsLgTypLight, $aID);
                                        $this->v["statLgts"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                                        $this->v["statLgts"]->addRecDat('lgtfx', $lgt->PsLgTypCount, $lgt->PsLgTypID);
                                        $foundLights = true;
                                        if (in_array($lgt->PsLgTypLight, [168, 169, 170, 171])) $foundHID = true;
                                        if (in_array($lgt->PsLgTypLight, [165, 203])) $foundLED = true;
                                        $fixtureCnt += $lgt->PsLgTypCount;
                                    }
                                }
                            }
                            if (!$foundLights) {
                                $this->v["statLgts"]->addRecFilt('lgty', 2, $aID); // no lights status
                                $this->v["statLgts"]->addRecDat('sqft', intVal($area->PsAreaSize), $aID);
                                $this->v["statLgts"]->addRecDat('lgtfx', 0, $aID);
                                $this->v["statLgts"]->delRecFilt('lgty');
                            }
                            $this->v["statLgts"]->delRecFilt('area');
                            
                            if ($aTyp == 'flw' && $foundHID) {
                                $this->v["statMore"]["scrHID"][0] += $ps->PsEfficOverall;
                                $this->v["statMore"]["scrHID"][1] += $ps->PsEfficFacility;
                                $this->v["statMore"]["scrHID"][2] += $ps->PsEfficProduction;
                                $this->v["statMore"]["scrHID"][3] += $ps->PsEfficLighting;
                                $this->v["statMore"]["scrHID"][4] += $ps->PsEfficHvac;
                                $this->v["statMore"]["scrHID"][5]++;
                                if ($fixtureCnt > 0) {
                                    $this->v["statMore"]["scrHID"][6] += $area->PsAreaSize/$fixtureCnt;
                                }
                            }
                            if ($aTyp == 'flw' && $foundLED) {
                                $this->v["statMore"]["scrLED"][0] += $ps->PsEfficOverall;
                                $this->v["statMore"]["scrLED"][1] += $ps->PsEfficFacility;
                                $this->v["statMore"]["scrLED"][2] += $ps->PsEfficProduction;
                                $this->v["statMore"]["scrLED"][3] += $ps->PsEfficLighting;
                                $this->v["statMore"]["scrLED"][4] += $ps->PsEfficHvac;
                                $this->v["statMore"]["scrLED"][5]++;
                            }
                        }
                    }
                }
                
            }
            $this->v["statMisc"]->resetRecFilt();
            $this->v["statMisc"]->calcStats(); 
            $this->v["statSqft"]->resetRecFilt();
            $this->v["statSqft"]->calcStats(); 
            $this->v["statLgts"]->resetRecFilt();
            $this->v["statLgts"]->calcStats();
            $this->v["statLarf"]->resetRecFilt();
            $this->v["statLarf"]->calcStats();
            $this->v["statHvac"]->resetRecFilt();
            $this->v["statHvac"]->calcStats();
            /*
            $this->v["statLgts"]->addNewDataCalc('kWh', 'sqft', '/');
            $this->v["statLgts"]->addNewDataCalc('W', 'sqft', '/');
            $this->v["statLgts"]->calcStats();
            */
            
            $this->v["statMore"]["hvacTotPrc"] = 0;
            /*
            if (isset($this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][3])
                && isset($this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][0])
                && $this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][0] != 0) {
                $this->v["statMore"]["hvacTotPrc"] = $this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][3]
                                                    /$this->v["statScor"]->tagTot["a"]["1"]["avg"]["row"][0];
            }
            */
            $this->v["statMore"]["hvacTotKwh"] = $this->v["statMore"]["hvacTotPrc"]
                                                *$this->v["statMisc"]->getDatTot('kWh');
            $this->v["statMore"]["othrTotKwh"] = $this->v["statMisc"]->getDatTot('kWh')
                                                -$this->v["statLgts"]->getDatTot('kWh')
                                                -$this->v["statMore"]["hvacTotKwh"];
            $this->v["statMore"]["othrTotPrc"] = $this->v["statMore"]["othrTotKwh"]
                                                /$this->v["statMisc"]->getDatTot('kWh');
            for ($i = 0; $i < 5; $i++) {
                foreach (["scrLED", "scrHID"] as $lyt) {
                    $this->v["statMore"][$lyt][$i] = $this->v["statMore"][$lyt][$i]/$this->v["statMore"][$lyt][5];
                }
                if (in_array($i, [0, 2])) { // higher is better
                    $this->v["statMore"]["scrLHR"][$i] = ($this->v["statMore"]["scrLED"][$i]
                        -$this->v["statMore"]["scrHID"][$i])/$this->v["statMore"]["scrHID"][$i];
                } else { // lower is better
                    $this->v["statMore"]["scrLHR"][$i] = ($this->v["statMore"]["scrHID"][$i]
                        -$this->v["statMore"]["scrLED"][$i])/$this->v["statMore"]["scrHID"][$i];
                }
            }
            $this->v["statMore"]["flwrPercHID"] = ($this->v["statLgts"]->getDatCnt('b162-c168')
                +$this->v["statLgts"]->getDatCnt('b162-c169')+$this->v["statLgts"]->getDatCnt('b162-c170')
                +$this->v["statLgts"]->getDatCnt('b162-c171'))/$this->v["statLgts"]->getDatCnt('b162');
            $this->v["statMore"]["sqftFxtHID"] = $this->v["statMore"]["scrHID"][6]/$this->v["statMore"]["scrHID"][5];
            
        }
        
        $chk = RIIPSRenewables::whereIn('PsRnwPSID', [1427, 1447, 1503, 1628, 1648, 1669, 1681, 1690, 1725, 1756, 
                2101, 878, 881, 884, 914, 922, 929, 934])
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $renew) {
                if (!in_array($renew->PsRnwPSID, $this->v["enrgys"]["extra"][1])) {
                    $this->v["enrgys"]["extra"][1][] = $renew->PsRnwPSID;
                    $this->v["enrgys"]["extra"][0]++;
                    $this->v["enrgys"]["extra"][143][0]++;
                }
                $this->v["enrgys"]["extra"][143][$renew->PsRnwRenewable]++;
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew) {
                $this->v["enrgys"]["data"][$type->DefID][] = [
                    $this->v["enrgys"]["cmpl"][$type->DefID][$renew->DefID],
                    $renew->DefValue,
                    "'" . $GLOBALS["SL"]->printColorFadeHex(($j*0.1), 
                        $GLOBALS["SL"]->getCssColor('color-main-on'), $GLOBALS["SL"]->getCssColor('color-main-bg')) . "'"
                    ];
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->v["enrgys"]["pie"][$type->DefID] 
                = ''; //$this->v["statScor"]->pieView($this->v["enrgys"]["data"][$type->DefID]);
        }
        $this->v["enrgys"]["data"][143143] = $this->v["enrgys"]["pie"][143143] = [];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew) {
            $color1 = $GLOBALS["SL"]->getCssColor('color-main-on');
            $color2 = $GLOBALS["SL"]->getCssColor('color-main-bg');
            $this->v["enrgys"]["data"][143143][] = [
                $this->v["enrgys"]["extra"][143][$renew->DefID],
                $renew->DefValue,
                "'" . $GLOBALS["SL"]->printColorFadeHex(($j*0.1), $color1, $color2) . "'"
                ];
        }
        $this->v["enrgys"]["pie"][143143] = ''; // $this->v["statScor"]->pieView($this->v["enrgys"]["data"][143143]);
        return true;
    }
    
    protected function getAType($area)
    {
        $aTyp = '';
        if ($area->PsAreaType == $this->v["areaTypes"]["Clone"]) {
            $aTyp = 'cln';
        } elseif ($area->PsAreaType == $this->v["areaTypes"]["Veg"]) {
            $aTyp = 'veg';
        } elseif ($area->PsAreaType == $this->v["areaTypes"]["Flower"]) {
            $aTyp = 'flw';
        }
        return $aTyp;
    }
    
    protected function loadPsTags($ps, $areas)
    {
        $psTags = [];
        $psTags[] = ['farm', $ps->PsCharacterize];
        $chk = RIIPSForCup::where('PsCupPSID', $ps->PsID)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) {
                $psTags[] = ['cups', $c->PsCupCupID];
            }
        }
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
            if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                $psTags[] = ['tech', $fld];
            }
        }
        $chk = RIIPSRenewables::where('PsRnwPSID', $ps->PsID)->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $renew) {
                $psTags[] = ['powr', $renew->PsRnwRenewable];
            }
        }
        $foundLights = $foundHID = $foundLED = false;
        if ($areas->isNotEmpty()) {
            foreach ($areas as $area) {
                if (isset($area->PsAreaType) && intVal($area->PsAreaHasStage) == 1) {
                    $aTyp = $this->getAType($area);
                    if ($aTyp != '') {
                        if (intVal($area->PsAreaHvacType) > 0) {
                            $psTags[] = [ $aTyp . '-hvac', $area->PsAreaHvacType ];
                        }
                        $lgts = RIIPSLightTypes::where('PsLgTypAreaID', $area->getKey())
                            ->get();
                        if ($lgts->isNotEmpty()) {
                            foreach ($lgts as $lgt) {
                                if (isset($lgt->PsLgTypLight) && intVal($lgt->PsLgTypLight) > 0
                                    && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0) {
                                    $foundLights = true;
                                    if (in_array($lgt->PsLgTypLight, [168, 169, 170, 171])) {
                                        $foundHID = true;
                                    }
                                    if (in_array($lgt->PsLgTypLight, [165, 203])) {
                                        $foundLED = true;
                                    }
                                    $psTags[] = [ $aTyp . '-lgty', $lgt->PsLgTypLight ];
                                }
                            }
                        }
                        if (!$foundLights) {
                            $psTags[] = [ $aTyp . '-lgty', 2 ];
                        }
                    }
                }
            }
        }
        return $psTags;
    }
    
}

class ScoreReportSet
{
    public $setAbbr    = '';
    public $setTitle   = '';
    public $setTable   = '';
    public $indoorOnly = false;
    
    public function __construct($abbr = '', $title = '', $indoor = false)
    {
        $this->setAbbr    = $abbr;
        $this->setTitle   = $title;
        $this->indoorOnly = $indoor;
    }
    
}