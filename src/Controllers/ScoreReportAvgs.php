<?php
/**
  * ScoreReportAvgs generates the entire PowerScore Averages Report, using ScoreStats and SurvStats.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIPsRenewables;
use App\Models\RIIPsForCup;
use App\Models\RIIPsLicenses;
use RockHopSoft\Survloop\Controllers\Stats\SurvStatsGraph;
use ResourceInnovation\CannabisScore\Controllers\ScoreStats;
use ResourceInnovation\CannabisScore\Controllers\CannabisScoreSearcher;
use ResourceInnovation\CannabisScore\Controllers\ScoreReportStats;

class ScoreReportAvgs extends ScoreReportStats
{
    public function getAllPowerScoreAvgsPublic($nID)
    {
        $this->v["nID"] = $nID;
        $this->calcAllPowerScoreAvgs();
        $this->v["psTechs"] = $GLOBALS["CUST"]->psTechs();
        if ($GLOBALS["SL"]->REQ->has('excel') 
            && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.773-powerscore-avgs-excel', 
                $this->v
            )->render();
            $filename = 'PowerScore_Averages' 
                . ((trim($this->v["fltStateClim"]) != '') 
                    ? '-' . str_replace(' ', '_', $this->v["fltStateClim"]) 
                    : '')
                . '-' . date("ymd") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $filename);
            exit;
        }
        return view(
            'vendor.cannabisscore.nodes.773-powerscore-avgs', 
            $this->v
        )->render();
    }
    
    public function getMorePowerStats()
    {
        $this->initClimateFilts();
        $this->calcMorePowerStats();
        /*
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.170-avg-powerscores-innertable', 
                $this->v
            )->render();
            $GLOBALS["SL"]->exportExcelOldSchool(
                $innerTable, 
                'PowerScore_Averages-' . date("Y-m-d") . '.xls'
            );
        }
        */
        return view(
            'vendor.cannabisscore.nodes.859-report-more-stats', 
            $this->v
        )->render();
    }
    
    public function getPowerScoreFinalReport()
    {
        $this->calcAllPowerScoreAvgs();
        $this->v["allscores"] = $this->searcher->v["allscores"];
        $this->calcMorePowerStats();
        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view(
            'vendor.cannabisscore.nodes.797-powerscore-report-tbls', 
            $this->v
        )->render();
    }
    
    protected function calcAllPowerScoreAvgs()
    {
        $this->initClimateFilts();
        if (isset($GLOBALS["SL"]->x["usrInfo"])
            && isset($GLOBALS['SL']->x['usrInfo']->companies[0]->manus)
            && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->manus) > 0
            && isset($GLOBALS['SL']->x['usrInfo']->companies[0]->manus[0]->id)
            && intVal($GLOBALS['SL']->x['usrInfo']->companies[0]->manus[0]->id) > 0) {
            $this->searcher->v["fltPartner"] = 0;
            $this->searcher->v["fltManuLgt"] = intVal(
                $GLOBALS['SL']->x['usrInfo']->companies[0]->manus[0]->id
            );
        }
        $this->searcher->loadAllScoresPublic();
        $this->v["scoreSets"] = [
            ['farm',     'PowerScore Averages by Type of Farm'],
            //['cups',     'PowerScore Averages by Competition / Data Set'],
            ['flw-lgty', 'Indoor PowerScore Averages by Type of Flowering Lights'],
            ['veg-lgty', 'Indoor PowerScore Averages by Type of Veg Lights'],
            ['cln-lgty', 'Indoor PowerScore Averages by Type of Cloning/Mother Lights'],
            ['tech',     'PowerScore Averages by Technique'],
            ['pow1',     'PowerScore Averages by Other Power Sources'],
            ['pow2',     'PowerScore Averages by Other Power Sources'],
            ['pow3',     'PowerScore Averages by Other Power Sources']
        ];
//echo 'calcAllPowerScoreAvgs()<pre>'; print_r($this->searcher->v); echo '</pre>'; exit;
        if ($this->searcher->v["allscores"]->isEmpty()) {
            return false;
        }
        $psTags = [];
        foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
            $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                ->get();
            $psTags[$ps->ps_id] = $this->loadPsTags($ps, $areas);
        }
        foreach ($this->v["scoreSets"] as $i => $set) {
            $tmp = new ScoreStats([$set[0]]);
            $tmp->loadMap();
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                if (!in_array($i, [1, 2, 3]) || $ps->ps_characterize == 144) {
                    $tmp->applyScoreFilts($ps, 0, $psTags[$ps->ps_id]);
                    $tmp->addScoreData($ps, null, $this->searcher->v["fltCmpl"]);
                    $tmp->resetRecFilt();
                }
            }
            $tmp->calcStats();
            if (in_array($i, [0])) { // , 1
                $this->v["scoreSets"][$i][2] = $tmp->printScoreAvgsTbl($set[0]);
            } else {
                $this->v["scoreSets"][$i][2] = $tmp->printScoreAvgsTbl2();
            }
            //$this->v["scoreSets"][$i][3] = $tmp;
            unset($tmp);
        }
        return true;
    }
    
    protected function calcMorePowerStats()
    {
        $this->v["statMisc"] = new SurvStatsGraph;
        $this->v["statMisc"]->addFilt(
            'farm', 
            'Farm Type', 
            $this->v["sfFarms"][0], 
            $this->v["sfFarms"][1]
        ); // a
        $this->v["statMisc"]->addDataType('g', 'Grams', 'g');                       // stat var a
        $this->v["statMisc"]->addDataType('kWh', 'Facility Kilowatt Hours', 'kWh'); // stat var b
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
            $this->v["statMisc"]->addDataType($fld, $name);
        }
        foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) {
            $this->v["statMisc"]->addDataType($fld, $name);
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $renew) {
            $this->v["statMisc"]->addDataType('rnw' . $renew->def_id, $renew->def_value);
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore License Types') as $lic) {
            $this->v["statMisc"]->addDataType('lic' . $lic->def_id, $lic->def_value);
        }
        $this->v["statMisc"]->loadMap();
        
        $this->v["statSqft"] = new SurvStatsGraph;
        $this->v["statSqft"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statSqft"]->addFilt('area', 'Growth Stage', $this->v["sfAreasAll"][0], $this->v["sfAreasAll"][1]); // stat filter b
        $this->v["statSqft"]->addDataType('sqft', 'Square Feet', 'sqft'); // stat var a
        $this->v["statSqft"]->loadMap();
        
        $this->v["statLgts"] = new SurvStatsGraph;
        $this->v["statLgts"]->addFilt('farm', 'Farm Type', $this->v["sfFarms"][0], $this->v["sfFarms"][1]); // a
        $this->v["statLgts"]->addFilt('area', 'Growth Stage', $this->v["sfAreasGrow"][0], $this->v["sfAreasGrow"][1]); // stat filter b
        $this->v["statLgts"]->addFilt('lgty', 'Lights Types', $this->v["sfLgts"][0], $this->v["sfLgts"][1]); // stat filter c
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
        $this->v["statHvac"]->addDataType('kBtu/sqft', 'kBtu/sqft', 'kBtu/sqft');                     // stat var a
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
            $this->v["enrgys"]["cmpl"][$type->def_id] = [ 0 => 0 ];
            $this->v["enrgys"]["extra"][$type->def_id] = [ 0 => 0 ];
            $this->v["enrgys"]["data"][$type->def_id] = [];
            $this->v["enrgys"]["pie"][$type->def_id] = [];
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew) {
                $this->v["enrgys"]["cmpl"][$type->def_id][$renew->def_id] = 0;
                $this->v["enrgys"]["extra"][$type->def_id][$renew->def_id] = 0;
            }
        }
        
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $cnt => $ps) {
                $char = $ps->ps_characterize;
                $this->v["statMisc"]->resetRecFilt();
                $this->v["statMisc"]->addRecFilt('farm', $char, $ps->ps_id);
                $this->v["statMisc"]->addRecDat('g', $ps->ps_grams, $ps->ps_id);
                $this->v["statMisc"]->addRecDat('kWh', $ps->ps_kwh_tot_calc, $ps->ps_id);
                foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->ps_id);
                    }
                }
                foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) {
                    if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                        $this->v["statMisc"]->addRecDat($fld, 1, $ps->ps_id);
                    }
                }
                $chk = RIIPsRenewables::where('ps_rnw_psid', $ps->ps_id)->get();
                if ($chk->isNotEmpty()) {
                    $this->v["enrgys"]["cmpl"][0]++;
                    $this->v["enrgys"]["cmpl"][$char][0]++;
                    foreach ($chk as $renew) {
                        $type = $renew->ps_rnw_renewable;
                        $this->v["statMisc"]->addRecDat('rnw' . $type, 1, $ps->ps_id);
                        if (isset($this->v["enrgys"]["cmpl"][$char])
                            && isset($this->v["enrgys"]["cmpl"][$char][$type])) {
                            $this->v["enrgys"]["cmpl"][$char][$type]++;
                        }
                    }
                }
                $chk = RIIPsLicenses::where('ps_lic_psid', $ps->ps_id)->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $lic) {
                        $type = 'lic' . $lic->ps_lic_license;
                        $this->v["statMisc"]->addRecDat($type, 1, $ps->ps_id);
                    }
                }
                
                $this->v["statSqft"]->resetRecFilt();
                $this->v["statSqft"]->addRecFilt('farm', $char, $ps->ps_id);
                
                $this->v["statLgts"]->resetRecFilt();
                $this->v["statLgts"]->addRecFilt('farm', $char, $ps->ps_id);
                $hasArtifLgt = false;
                $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        if (isset($area->ps_area_type) 
                            && $area->ps_area_type == $this->v["areaTypes"]["Flower"]
                            && intVal($area->ps_area_has_stage) == 1 
                            && intVal($area->ps_area_lgt_artif) == 1) {
                            $hasArtifLgt = true;
                        }
                    }
                }
                if ($hasArtifLgt) {
                    $this->v["statLarf"]->resetRecFilt();
                    $this->v["statLarf"]->addRecFilt('farm', $char, $ps->ps_id);
                }
                
                $this->v["statHvac"]->resetRecFilt();
                $this->v["statHvac"]->addRecFilt('farm', $char, $ps->ps_id);
                
                $kwh = 0;
                $sqft = [ 0 => 0, 162 => 0, 161 => 0, 160 => 0, 237 => 0, 163 => 0 ];
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        if (isset($area->ps_area_type) && intVal($area->ps_area_has_stage) == 1) {
                            $aID = $area->ps_area_id;
                            $aTyp = $this->getAType($area);
                            $this->v["statSqft"]->addRecFilt('area', $area->ps_area_type, $ps->ps_id);
                            $this->v["statSqft"]->addRecDat('sqft', intVal($area->ps_area_size), $ps->ps_id);
                            $this->v["statSqft"]->delRecFilt('area');
                            
                            $hvac = 360;
                            if (isset($area->ps_area_hvac_type) && intVal($area->ps_area_hvac_type) > 0) {
                                $hvac = $area->ps_area_hvac_type;
                            }
                            $this->v["statHvac"]->addRecFilt('area', $area->ps_area_type, $ps->ps_id);
                            $this->v["statHvac"]->addRecFilt('hvac', $hvac, $aID);
                            $this->v["statHvac"]->addRecDat('sqft', intVal($area->ps_area_size), $aID);
                            $this->v["statHvac"]->addRecDat(
                                'kBtu/sqft', 
                                $GLOBALS["CUST"]->getHvacEffic($hvac), $aID
                            );
                            $this->v["statHvac"]->delRecFilt('hvac');
                            $this->v["statHvac"]->delRecFilt('area');
                            
                            $this->v["statLgts"]->addRecFilt('area', $area->ps_area_type, $aID);
                            $this->v["statLgts"]->addRecDat('sun', intVal($area->ps_area_lgt_sun), $aID);
                            $this->v["statLgts"]->addRecDat('dep', intVal($area->ps_area_lgt_dep), $aID);
                            $this->v["statLgts"]->addRecDat('arf', intVal($area->ps_area_lgt_artif), $aID);
                            $w = 0;
                            if (isset($area->ps_area_total_light_watts) && $area->ps_area_total_light_watts > 0) {
                                $w = ($area->ps_area_total_light_watts
                                    *$GLOBALS["CUST"]->getTypeHours($area->ps_area_type)*365)/1000;
                                $this->v["statLgts"]->addRecDat('W', $area->ps_area_total_light_watts, $aID);
                                if ($hasArtifLgt) {
                                    $this->v["statLarf"]->addRecFilt('area', $area->ps_area_type, $aID);
                                    $this->v["statLarf"]->addRecDat('sqft', intVal($area->ps_area_size), $aID);
                                    $this->v["statLarf"]->addRecDat('W', $area->ps_area_total_light_watts, $aID);
                                }
                            }
                            $this->v["statLgts"]->addRecDat('kWh', $w, $aID);
                            $foundLights = $foundHID = $foundLED = false;
                            $fixtureCnt = 0;
                            $lgts = RIIPsLightTypes::where('ps_lg_typ_area_id', $area->getKey())
                                ->get();
                            if ($lgts->isNotEmpty()) {
                                foreach ($lgts as $lgt) {
                                    if (isset($lgt->ps_lg_typ_light) 
                                        && intVal($lgt->ps_lg_typ_light) > 0
                                        && isset($lgt->ps_lg_typ_count) 
                                        && intVal($lgt->ps_lg_typ_count) > 0) {
                                        $this->v["statLgts"]->addRecFilt(
                                            'lgty', 
                                            $lgt->ps_lg_typ_light, 
                                            $aID
                                        );
                                        $this->v["statLgts"]->addRecDat(
                                            'sqft', 
                                            intVal($area->ps_area_size), 
                                            $aID
                                        );
                                        $this->v["statLgts"]->addRecDat(
                                            'lgtfx', 
                                            $lgt->ps_lg_typ_count, 
                                            $lgt->ps_lg_typ_id
                                        );
                                        $foundLights = true;
                                        if (in_array($lgt->ps_lg_typ_light, [168, 169, 170, 171])) {
                                            $foundHID = true;
                                        }
                                        if (in_array($lgt->ps_lg_typ_light, [165, 203])) {
                                            $foundLED = true;
                                        }
                                        $fixtureCnt += $lgt->ps_lg_typ_count;
                                    }
                                }
                            }
                            if (!$foundLights) { // && intVal($area->ps_area_lgt_artif) == 0
                                $this->v["statLgts"]->addRecFilt('lgty', 2, $aID); // no lights status
                                $this->v["statLgts"]->addRecDat('sqft', intVal($area->ps_area_size), $aID);
                                $this->v["statLgts"]->addRecDat('lgtfx', 0, $aID);
                                $this->v["statLgts"]->delRecFilt('lgty');
                            }
                            $this->v["statLgts"]->delRecFilt('area');
                            
                            if ($aTyp == 'flw' && $foundHID) {
                                $this->v["statMore"]["scrHID"][0] += $ps->ps_effic_overall;
                                $this->v["statMore"]["scrHID"][1] += $ps->ps_effic_facility;
                                $this->v["statMore"]["scrHID"][2] += $ps->ps_effic_production;
                                $this->v["statMore"]["scrHID"][3] += $ps->ps_effic_lighting;
                                $this->v["statMore"]["scrHID"][4] += $ps->ps_effic_hvac;
                                $this->v["statMore"]["scrHID"][5]++;
                                if ($fixtureCnt > 0) {
                                    $this->v["statMore"]["scrHID"][6] += $area->ps_area_size/$fixtureCnt;
                                }
                            }
                            if ($aTyp == 'flw' && $foundLED) {
                                $this->v["statMore"]["scrLED"][0] += $ps->ps_effic_overall;
                                $this->v["statMore"]["scrLED"][1] += $ps->ps_effic_facility;
                                $this->v["statMore"]["scrLED"][2] += $ps->ps_effic_production;
                                $this->v["statMore"]["scrLED"][3] += $ps->ps_effic_lighting;
                                $this->v["statMore"]["scrLED"][4] += $ps->ps_effic_hvac;
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
                    $this->v["statMore"][$lyt][$i] 
                        = $this->v["statMore"][$lyt][$i]
                            /$this->v["statMore"][$lyt][5];
                }
                if (in_array($i, [0, 2])) { // higher is better
                    $this->v["statMore"]["scrLHR"][$i] 
                        = ($this->v["statMore"]["scrLED"][$i]
                                -$this->v["statMore"]["scrHID"][$i])
                            /$this->v["statMore"]["scrHID"][$i];
                } else { // lower is better
                    $this->v["statMore"]["scrLHR"][$i] 
                        = ($this->v["statMore"]["scrHID"][$i]
                                -$this->v["statMore"]["scrLED"][$i])
                            /$this->v["statMore"]["scrHID"][$i];
                }
            }
            $this->v["statMore"]["flwrPercHID"] 
                = ($this->v["statLgts"]->getDatCnt('b162-c168')
                        +$this->v["statLgts"]->getDatCnt('b162-c169')
                        +$this->v["statLgts"]->getDatCnt('b162-c170')
                        +$this->v["statLgts"]->getDatCnt('b162-c171'))
                    /$this->v["statLgts"]->getDatCnt('b162');
            $this->v["statMore"]["sqftFxtHID"] 
                = $this->v["statMore"]["scrHID"][6]
                    /$this->v["statMore"]["scrHID"][5];
            
        }
        
        $chk = RIIPsRenewables::whereIn('ps_rnw_psid', [
                1427, 1447, 1503, 1628, 1648, 1669, 1681, 1690, 
                1725, 1756, 2101, 878, 881, 884, 914, 922, 929, 934])
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $renew) {
                if (!in_array($renew->ps_rnw_psid, $this->v["enrgys"]["extra"][1])) {
                    $this->v["enrgys"]["extra"][1][] = $renew->ps_rnw_psid;
                    $this->v["enrgys"]["extra"][0]++;
                    $this->v["enrgys"]["extra"][143][0]++;
                }
                if (isset($this->v["enrgys"]["extra"][143][$renew->ps_rnw_renewable])) {
                    $this->v["enrgys"]["extra"][143][$renew->ps_rnw_renewable]++;
                }
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') 
                as $j => $renew) {
                $color = $GLOBALS["SL"]->printColorFadeHex(
                    ($j*0.1), 
                    $GLOBALS["SL"]->getCssColor('color-main-on'), 
                    $GLOBALS["SL"]->getCssColor('color-main-bg')
                );
                $this->v["enrgys"]["data"][$type->def_id][] = [
                    $this->v["enrgys"]["cmpl"][$type->def_id][$renew->def_id],
                    $renew->def_value,
                    "'" . $color . "'"
                ];
            }
        }
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Farm Types') as $i => $type) {
            $this->v["enrgys"]["pie"][$type->def_id] 
                = ''; //$this->v["statScor"]->pieView($this->v["enrgys"]["data"][$type->def_id]);
        }
        $this->v["enrgys"]["data"][143143] = $this->v["enrgys"]["pie"][143143] = [];
        foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew) {
            $color1 = $GLOBALS["SL"]->getCssColor('color-main-on');
            $color2 = $GLOBALS["SL"]->getCssColor('color-main-bg');
            $this->v["enrgys"]["data"][143143][] = [
                $this->v["enrgys"]["extra"][143][$renew->def_id],
                $renew->def_value,
                "'" . $GLOBALS["SL"]->printColorFadeHex(($j*0.1), $color1, $color2) . "'"
                ];
        }
        $this->v["enrgys"]["pie"][143143] = ''; // $this->v["statScor"]->pieView($this->v["enrgys"]["data"][143143]);
        return true;
    }
    
    protected function getAType($area)
    {
        $aTyp = '';
        if ($area->ps_area_type == $this->v["areaTypes"]["Clone"]) {
            $aTyp = 'cln';
        } elseif ($area->ps_area_type == $this->v["areaTypes"]["Veg"]) {
            $aTyp = 'veg';
        } elseif ($area->ps_area_type == $this->v["areaTypes"]["Flower"]) {
            $aTyp = 'flw';
        }
        return $aTyp;
    }
    
    protected function loadPsTags($ps, $areas)
    {
        $psTags = [];
        $psTags[] = ['farm', $ps->ps_characterize];
        $chk = RIIPsForCup::where('ps_cup_psid', $ps->ps_id)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) {
                $psTags[] = ['cups', $c->ps_cup_cup_id];
            }
        }
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
            if (isset($ps->{ $fld }) && intVal($ps->{ $fld }) == 1) {
                $psTags[] = ['tech', $fld];
            }
        }
        $chk = RIIPsRenewables::where('ps_rnw_psid', $ps->ps_id)->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $renew) {
                $psTags[] = ['powr', $renew->ps_rnw_renewable];
            }
        }
        $foundLights = $foundHID = $foundLED = false;
        if ($areas->isNotEmpty()) {
            foreach ($areas as $area) {
                if (isset($area->ps_area_type) && intVal($area->ps_area_has_stage) == 1) {
                    $aTyp = $this->getAType($area);
                    if ($aTyp != '') {
                        if (intVal($area->ps_area_hvac_type) > 0) {
                            $psTags[] = [ $aTyp . '-hvac', $area->ps_area_hvac_type ];
                        }
                        $lgts = RIIPsLightTypes::where('ps_lg_typ_area_id', $area->getKey())
                            ->get();
                        if ($lgts->isNotEmpty()) {
                            foreach ($lgts as $lgt) {
                                if (isset($lgt->ps_lg_typ_light) && intVal($lgt->ps_lg_typ_light) > 0
                                    && isset($lgt->ps_lg_typ_count) && intVal($lgt->ps_lg_typ_count) > 0) {
                                    $foundLights = true;
                                    if (in_array($lgt->ps_lg_typ_light, [168, 169, 170, 171])) {
                                        $foundHID = true;
                                    }
                                    if (in_array($lgt->ps_lg_typ_light, [165, 203])) {
                                        $foundLED = true;
                                    }
                                    $psTags[] = [ $aTyp . '-lgty', $lgt->ps_lg_typ_light ];
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


