<?php
/**
  * ScoreCalcs is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which collectively create PowerScore
  * calculations, first calculating raw metrics, then relative efficiency rankings.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\SLNodeSaves;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSRanks;
use App\Models\RIIPSRankings;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreUtils;

class ScoreCalcs extends ScoreUtils
{
    protected function calcCurrSubScores()
    {
        $this->loadTotFlwrSqFt();
        if (isset($this->sessData->dataSets["PowerScore"]) && isset($this->sessData->dataSets["PowerScore"][0])
            && (!isset($this->sessData->dataSets["PowerScore"][0]->PsEfficFacility) 
                || $GLOBALS["SL"]->REQ->has('refresh') || $GLOBALS["SL"]->REQ->has('recalc'))) {
            $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility   = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac       = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting   = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficWater      = 0;
            $this->sessData->dataSets["PowerScore"][0]->PsEfficWaste      = 0;
            $row = $this->sessData->dataSets["PowerScore"][0];
            if ($this->v["totFlwrSqFt"] > 0 && (!isset($row->PsTotalSize) || intVal($row->PsTotalSize) == 0)) {
                $this->sessData->dataSets["PowerScore"][0]->PsTotalSize = $this->v["totFlwrSqFt"];
            }
            
            if ($row->PsTimeType == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Future')) {
                $row = $this->calcFutureYields();
            } else {
                if (isset($row->PsKWH) && intVal($row->PsKWH) > 0 && isset($row->PsGrams) && intVal($row->PsGrams) > 0) {
                    $this->sessData->dataSets["PowerScore"][0]->PsEfficProduction = $row->PsGrams/$row->PsKWH;
                }
                if (isset($this->v["totFlwrSqFt"]) && intVal($this->v["totFlwrSqFt"]) > 0) {
                    if (isset($row->PsKWH) && intVal($row->PsKWH) > 0) {
                        $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility = $row->PsKWH/$this->v["totFlwrSqFt"];
                    }
                    if (isset($row->PsGreenWasteLbs) && intVal($row->PsGreenWasteLbs) > 0) {
                        $this->sessData->dataSets["PowerScore"][0]->PsEfficWaste = $row->PsGreenWasteLbs/$this->v["totFlwrSqFt"];
                    }
                }
            }
            
            $this->sessData->dataSets["PowerScore"][0]->PsTotalCanopySize = 0;
            $sqft = $watts = $wattsHvac = $gal = [];
            if (isset($this->sessData->dataSets["PSAreas"]) && sizeof($this->sessData->dataSets["PSAreas"]) > 0) {
                foreach ($this->sessData->dataSets["PSAreas"] as $a => $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
                            $this->sessData->dataSets["PowerScore"][0]->PsTotalCanopySize += $area->PsAreaSize;
                            $sqft[$typ] = $area->PsAreaSize;
                            $watts[$typ] = $wattsHvac[$typ] = $gal[$typ] = 0;
                            if (!isset($area->PsAreaLgtArtif) || intVal($area->PsAreaLgtArtif) == 0) {
                                $watts[$typ] = 0.0000001;
                            } elseif (isset($this->sessData->dataSets["PSLightTypes"]) 
                                && sizeof($this->sessData->dataSets["PSLightTypes"]) > 0) {
                                foreach ($this->sessData->dataSets["PSLightTypes"] as $lgt) {
                                    if ($lgt->PsLgTypAreaID == $area->getKey() 
                                        && isset($lgt->PsLgTypCount) && intVal($lgt->PsLgTypCount) > 0 
                                        && isset($lgt->PsLgTypWattage) && intVal($lgt->PsLgTypWattage) > 0) {
                                        $watts[$typ] += ($lgt->PsLgTypCount*$lgt->PsLgTypWattage);
//echo $typ . '<sup>' . $area->getKey() . '</sup> => ' . $watts[$typ] . ' (' . $lgt->PsLgTypCount . ' * ' . $lgt->PsLgTypWattage . ')<sup>' . $lgt->getKey() . '</sup> / ' . $area->PsAreaSize . '<br />';
                                    }
                                }
                            }
                            $this->sessData->dataSets["PSAreas"][$a]->PsAreaTotalLightWatts = $watts[$typ];
                        }
                    }
                }
                
//echo '#' . $this->sessData->dataSets["PowerScore"][0]->PsID . ' First <pre>'; print_r($watts); print_r($sqft); echo '</pre>';
                if (isset($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc)) {
                    if ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones')) {
                        $watts["Clone"]     += $watts["Mother"];
                        $sqft["Clone"]      += $sqft["Mother"];
                        $watts["Mother"] = $sqft["Mother"] = 0;
                    } elseif ($this->sessData->dataSets["PowerScore"][0]->PsMotherLoc 
                        == $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room')) {
                        $watts["Veg"]     += $watts["Mother"];
                        $sqft["Veg"]      += $sqft["Mother"];
                        $watts["Mother"] = $sqft["Mother"] = 0;
                    }
                }
                
                $hasLights = 0;
                foreach ($this->sessData->dataSets["PSAreas"] as $a => $area) {
                    foreach ($this->v["areaTypes"] as $typ => $defID) {
                        if ($area->PsAreaType == $defID && $typ != 'Dry') {
                            $this->sessData->dataSets["PSAreas"][$a]->PsAreaCalcSize = $sqft[$typ];
                            $this->sessData->dataSets["PSAreas"][$a]->PsAreaCalcWatts = $watts[$typ];
                            $this->sessData->dataSets["PSAreas"][$a]->PsAreaLightingEffic = 0;
                            if (intVal($sqft[$typ]) > 0) {
                                $sqftWeight = $sqft[$typ]/$this->sessData->dataSets["PowerScore"][0]->PsTotalCanopySize;
                                if ($watts[$typ] > 0) {
                                    $this->sessData->dataSets["PSAreas"][$a]->PsAreaLgtArtif = 1;
                                    $hasLights++;
                                    $this->sessData->dataSets["PSAreas"][$a]->PsAreaLightingEffic 
                                        = $watts[$typ]/$sqft[$typ];
                                    $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting
                                        += $sqftWeight*$this->sessData->dataSets["PSAreas"][$a]->PsAreaLightingEffic;
                                }
                                if (isset($area->PsAreaHvacType) && intVal($area->PsAreaHvacType) > 0) {
                                    $this->sessData->dataSets["PSAreas"][$a]->PsAreaHvacEffic 
                                        = $GLOBALS["CUST"]->getHvacEffic($area->PsAreaHvacType);
                                    $this->sessData->dataSets["PowerScore"][0]->PsEfficHvac
                                        += $sqftWeight*$this->sessData->dataSets["PSAreas"][$a]->PsAreaHvacEffic;
                                }
                                if (isset($area->PsAreaGallons) && intVal($area->PsAreaGallons) > 0) {
                                    $this->sessData->dataSets["PSAreas"][$a]->PsAreaWaterEffic = $area->PsAreaGallons/$sqft[$typ];
                                    $this->sessData->dataSets["PowerScore"][0]->PsEfficWater
                                        += $sqftWeight*$this->sessData->dataSets["PSAreas"][$a]->PsAreaWaterEffic;
                                }
                            }
                            $this->sessData->dataSets["PSAreas"][$a]->save();
                        }
                    }
                }
                if ($hasLights == 0) {
                    $this->sessData->dataSets["PowerScore"][0]->PsEfficLighting = 0.00000001;
                }
            }
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        return true;
    }
    
    protected function calcCurrScoreRanks()
    {
//echo '<br /><br /><br /><h2>' . $this->searcher->v["urlFlts"] . '</h2>';
        $this->v["ranksCache"] = RIIPSRanks::where('PsRnkFilters', $this->searcher->v["urlFlts"])
            ->first();
        if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->PsRnkID)) {
            $this->v["ranksCache"] = new RIIPSRanks;
            $this->v["ranksCache"]->PsRnkFilters = $this->searcher->v["urlFlts"];
        } /* elseif (!$GLOBALS["SL"]->REQ->has('refresh') && !$GLOBALS["SL"]->REQ->has('recalc')) {
            return $this->v["ranksCache"];
        } */
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') . "::" 
            . $this->searcher->filterAllPowerScoresPublic() . "->where('PsEfficFacility', '>', 0)"
            . "->where('PsEfficProduction', '>', 0)->where('PsEfficLighting', '>', 0)"
            . "->where('PsEfficHvac', '>', 0)->get();";
//echo str_replace("\$allscores = App\Models\RIIPowerScore", "", str_replace("where('PsEfficFacility', '>', 0)->where('PsEfficProduction', '>', 0)->where('PsEfficLighting', '>', 0)->where('PsEfficHvac', '>', 0)->get();", "", $eval)) . '<br /><br />'; return '';
        eval($eval);
        $this->v["ranksCache"]->PsRnkTotCnt = $allscores->count();
//return '';
        $r = [];
        $l = [ "over" => [], "oraw" => [], "faci" => [], "prod" => [], "ligh" => [], "hvac" => [] ];
        $avg = [ "kwh" => 0, "g" => 0 ];
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $i => $ps) {
                $sqft = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->where('PsAreaType', $this->v["areaTypes"]["Flower"])
                    ->select('PsAreaSize')
                    ->first();
                if ($sqft && isset($sqft->PsAreaSize)) {
                    $avg["kwh"] += $ps->PsKWH/$sqft->PsAreaSize;
                    $avg["g"] += $ps->PsGrams/$sqft->PsAreaSize;
                }
                $l["faci"][] = $ps->PsEfficFacility;
                $l["prod"][] = $ps->PsEfficProduction;
                $l["ligh"][] = $ps->PsEfficLighting;
                $l["hvac"][] = $ps->PsEfficHvac;
            }
            sort($l["faci"], SORT_NUMERIC);
            sort($l["prod"], SORT_NUMERIC);
            sort($l["ligh"], SORT_NUMERIC);
            sort($l["hvac"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->PsID] = [ "over" => 0, "oraw" => 0, "faci" => 0, "prod" => 0, "ligh" => 0, "hvac" => 0 ];
                $r[$ps->PsID]["faci"] = $GLOBALS["SL"]->getArrPercentile($l["faci"], $ps->PsEfficFacility);
                $r[$ps->PsID]["prod"] = $GLOBALS["SL"]->getArrPercentile($l["prod"], $ps->PsEfficProduction, true);
                $r[$ps->PsID]["ligh"] = $GLOBALS["SL"]->getArrPercentile($l["ligh"], $ps->PsEfficLighting);
                $r[$ps->PsID]["hvac"] = $GLOBALS["SL"]->getArrPercentile($l["hvac"], $ps->PsEfficHvac);
                $r[$ps->PsID]["oraw"] = ($r[$ps->PsID]["faci"]+$r[$ps->PsID]["prod"]+$r[$ps->PsID]["ligh"]
                    +$r[$ps->PsID]["hvac"])/4;
                $l["oraw"][] = $r[$ps->PsID]["oraw"];
            }
            
            sort($l["oraw"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->PsID]["over"] = $GLOBALS["SL"]->getArrPercentile($l["oraw"], $r[$ps->PsID]["oraw"], true);
            }
            
            // Now store calculated ranks for individual scores...
            foreach ($allscores as $i => $ps) {
                if (in_array(trim($this->searcher->v["urlFlts"]), ['', '&fltFarm=0'])) {
                    RIIPowerScore::find($ps->PsID)->update([ 'PsEfficOverall' => $r[$ps->PsID]["over"] ]);
                }
                if (trim($this->searcher->v["urlFlts"]) == '&fltFarm=' . $ps->PsCharacterize) {
                    RIIPowerScore::find($ps->PsID)->update([ 'PsEfficOverSimilar' => $r[$ps->PsID]["over"] ]);
                }
                $tmp = RIIPSRankings::where('PsRnkPSID', $ps->PsID)
                    ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                    ->first();
                if (!$tmp) {
                    $tmp = new RIIPSRankings;
                    $tmp->PsRnkPSID = $ps->PsID;
                    $tmp->PsRnkFilters = $this->searcher->v["urlFlts"];
                    $tmp->save();
                }
                $tmp->PsRnkTotCnt     = $allscores->count();
                $tmp->PsRnkOverall    = $r[$ps->PsID]["over"];
                $tmp->PsRnkOverallAvg = $r[$ps->PsID]["oraw"];
                $tmp->PsRnkFacility   = $r[$ps->PsID]["faci"];
                $tmp->PsRnkProduction = $r[$ps->PsID]["prod"];
                $tmp->PsRnkLighting   = $r[$ps->PsID]["ligh"];
                $tmp->PsRnkHVAC       = $r[$ps->PsID]["hvac"];
                $tmp->save();
            }
        }
        
        // Now store listed raw sub-score values for filter...
        $this->v["ranksCache"]->PsRnkTotCnt     = $allscores->count();
        $this->v["ranksCache"]->PsRnkOverallAvg = implode(',', $l["oraw"]);
        $this->v["ranksCache"]->PsRnkFacility   = implode(',', $l["faci"]);
        $this->v["ranksCache"]->PsRnkProduction = implode(',', $l["prod"]);
        $this->v["ranksCache"]->PsRnkLighting   = implode(',', $l["ligh"]);
        $this->v["ranksCache"]->PsRnkHVAC       = implode(',', $l["hvac"]);
        if ($this->v["ranksCache"]->PsRnkTotCnt > 0) {
            $this->v["ranksCache"]->PsRnkAvgSqftKwh = $avg["kwh"]/$this->v["ranksCache"]->PsRnkTotCnt;
            $this->v["ranksCache"]->PsRnkAvgSqftGrm = $avg["g"]/$this->v["ranksCache"]->PsRnkTotCnt;
        }
        $this->v["ranksCache"]->save();
        return $this->v["ranksCache"];
    }
    
    protected function calcFutureYields()
    {
        $this->loadTotFlwrSqFt();
        $this->initSearcher();
        $this->searcher->loadCurrScoreFltParams($this->sessData->dataSets);
        $matches = [ "flt" => [], "kwh" => 0, "grm" => 0 ];
        if (sizeof($this->searcher->v["futureFlts"]) > 0) {
            foreach ($this->searcher->v["futureFlts"] as $flt) {
                $chk = RIIPSRanks::where('PsRnkFilters', 'LIKE', $flt)
                    ->where('PsRnkTotCnt', '>', 3)
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $rnk) {
                        $matches["flt"][] = $rnk->PsRnkFilters;
                        $matches["kwh"] += $rnk->PsRnkAvgSqftKwh;
                        $matches["grm"] += $rnk->PsRnkAvgSqftGrm;
                    }
                }
            }
        }
        if (sizeof($matches["flt"]) > 0) {
            $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility = $matches["kwh"]/sizeof($matches["flt"]);
            $this->sessData->dataSets["PowerScore"][0]->PsKWH 
                = $this->sessData->dataSets["PowerScore"][0]->PsEfficFacility*$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["PowerScore"][0]->PsGrams 
                = ($matches["grm"]/sizeof($matches["flt"]))*$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        return $this->sessData->dataSets["PowerScore"][0];
    }
    
    protected function recalcAllSubScores()
    {
///////////////// One Time
        if ($GLOBALS["SL"]->REQ->has('recalc2')) {
            $chk = SLNodeSaves::where('NodeSaveTblFld', 'PSAreas:PsAreaSize')
                ->orderBy('created_at', 'asc')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $save) {
                    $area = RIIPSAreas::find($save->NodeSaveLoopItemID);
                    if ($area && isset($area->PsAreaID) && trim($save->NodeSaveNewVal) != '') {
                        $area->PsAreaSize = str_replace(',', '', $save->NodeSaveNewVal);
                        $area->save();
                    }
                }
            }
            exit;
        }
/////////////////
        
        $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, $this->dbID, 1);
        $GLOBALS["SL"]->x["pageView"] = $GLOBALS["SL"]->x["dataPerms"] = 'public';
        $this->loadCustLoop($GLOBALS["SL"]->REQ, 1);
        $all = RIIPowerScore::select('PsID')
            ->where('PsStatus', 'NOT LIKE', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
            ->get();
        if ($all->isNotEmpty()) {
            foreach ($all as $ps) {
                $this->custReport->loadAllSessData('PowerScore', $ps->PsID);
                $this->custReport->calcCurrSubScores();
            }
        }
        return '<br /><br />Recalculations Complete<br /><a href="/dash/powerscore-software-troubleshooting">Back</a>'
            . '<br /><style> #nodeSubBtns { display: none; } </style>';
    }
    
    // New ranking procedures built in Aug '18
    protected function calcAllScoreRanks($redir = 'all')
    {
        $this->searchResultsXtra();
        $this->calcCurrScoreRanks();
        $GLOBALS["CUST"]->chkScoreFiltCombs();
        $nextFlt = '';
        $freshDone = $cnt = -1;
        $curr = (($GLOBALS["SL"]->REQ->has('currFlt')) ? $GLOBALS["SL"]->REQ->get('currFlt') : '');
        foreach ($GLOBALS["CUST"]->v["fltComb"] as $flt => $opts) {
            if ($curr == '') {
                $curr = $flt;
            }
            $cnt++;
            if ($nextFlt == '' && $freshDone >= 0) {
                $nextFlt = $flt;
            }
            if ($freshDone < 0 && $curr == $flt) {
                $freshDone = $cnt;
                foreach ($GLOBALS["CUST"]->v["fltComb"] as $f => $o) {
                    $this->searcher->v[$f] = $o[0];
                }
                foreach ($opts as $j => $opt) {
                    $this->searcher->v[$flt] = $opt;
                    $this->searcher->searchFiltsURLXtra();
                    $this->calcCurrScoreRanks();
                }
            }
        }
        $msg = '<i class="slGrey">Recalculating ' . (1+$freshDone) . '/' . sizeof($GLOBALS["CUST"]->v["fltComb"]) . '...</i>';
        if ($redir == 'report-ajax') {
            if ($nextFlt != '') {
                return view('vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', [
                    "msg"     => $msg,
                    "nextFlt" => $nextFlt,
                    "psid"    => $this->v["ajax-psid"]
                    ])->render();
            }
            return $msg . '<script type="text/javascript"> setTimeout("window.location=\'/calculated/read-'
                . $this->v["ajax-psid"] . '\'", 1000); </script>';
        }
        if ($nextFlt != '') {
            return $msg . '<script type="text/javascript"> setTimeout("window.location=\'/dash/powerscore-software'
                . '-troubleshooting?refresh=1&currFlt=' . $nextFlt . '\'", 1000); </script>';
        }
        return '<br /><br />Recalculations Complete!<br /><a href="/dash/powerscore-software-troubleshooting">Back</a>'
            . '<br /><style> #nodeSubBtns { display: none; } </style>';
    }
    
    protected function rankAllScores()
    {
        $this->v["allRnks"] = [];
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') 
            . "::" . $this->filterAllPowerScoresPublic() . "->where('PsTimeType', "
            . $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past') . ")->where('PsEfficFacility', '>', 0)"
            . "->where('PsEfficProduction', '>', 0)->where('PsEfficHvac', '>', 0)->where('PsEfficLighting', '>', 0)"
            . "->select('PsID', 'PsEfficOverall', 'PsEfficFacility', 'PsEfficProduction', 'PsEfficHvac', "
            . "'PsEfficLighting')->get();";
        eval($eval);
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $s) {
                $efficPercs = [ // current PowerScore is "better" than X others, and "worse" than Y others
                    "Facility"   => [ "better" => 0, "worse" => 0 ], 
                    "Production" => [ "better" => 0, "worse" => 0 ], 
                    "HVAC"       => [ "better" => 0, "worse" => 0 ], 
                    "Lighting"   => [ "better" => 0, "worse" => 0 ]
                    ];
                foreach ($allscores as $s2) {
                    $efficPercs["Facility"][($s->PsEfficFacility <= $s2->PsEfficFacility) ? "better" : "worse"]++;
                    $efficPercs["Production"][($s->PsEfficProduction >= $s2->PsEfficProduction) ? "better" : "worse"]++;
                    $efficPercs["HVAC"][($s->PsEfficHvac <= $s2->PsEfficHvac) ? "better" : "worse"]++;
                    $efficPercs["Lighting"][($s->PsEfficLighting <= $s2->PsEfficLighting) ? "better" : "worse"]++;
                }
                $this->v["allRnks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                    ->first();
                if (!$this->v["allRnks"][$s->PsID] || !isset($this->v["allRnks"][$s->PsID]->PsRnkID)) {
                    $this->v["allRnks"][$s->PsID] = new RIIPSRankings;
                    $this->v["allRnks"][$s->PsID]->PsRnkPSID    = $s->PsID;
                    $this->v["allRnks"][$s->PsID]->PsRnkFilters = $this->searcher->v["urlFlts"];
                    $this->v["allRnks"][$s->PsID]->PsRnkTotCnt  = $allscores->count();
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg = 0;
                foreach ($efficPercs as $type => $percs) {
                    $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type } 
                        = 100*($percs["better"]/$this->v["allRnks"][$s->PsID]->PsRnkTotCnt);
                    $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg += $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type};
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg = $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg/4;
                $this->v["allRnks"][$s->PsID]->save();
            }
            foreach ($allscores as $s) {
                $efficPercs = [ "better" => 0, "worse" => 0 ];
                foreach ($allscores as $s2) {
                    $efficPercs[($this->v["allRnks"][$s->PsID]->PsRnkOverallAvg 
                        >= $this->v["allRnks"][$s2->PsID]->PsRnkOverallAvg) ? "better" : "worse"]++;
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverall 
                    = 100*($efficPercs["better"]/$this->v["allRnks"][$s->PsID]->PsRnkTotCnt);
                $this->v["allRnks"][$s->PsID]->save();
                if ($this->searcher->v["urlFlts"] == '') {
                    $s->PsEfficOverall = $this->v["allRnks"][$s->PsID]->PsRnkOverall;
                    $s->save();
                } elseif ($this->searcher->v["urlFlts"] == ('&fltFarm=' . $s->PsCharacterize)) {
                    $s->PsEfficOverSimilar = $this->v["allRnks"][$s->PsID]->PsRnkOverall;
                    $s->save();
                }
            }
        }
        return true;
    }
    
    protected function getAllReportCalcs()
    {
        $this->loadTotFlwrSqFt();
        $this->calcCurrSubScores();
        $this->prepPrintEfficLgt();
        $this->chkUnprintableSubScores();
        $this->v["sessData"] = $this->sessData->dataSets;
        if (isset($this->sessData->dataSets["PowerScore"])) {
            $this->v["psid"] = $this->sessData->dataSets["PowerScore"][0]->getKey();
            $this->v["hasRefresh"] = (($GLOBALS["SL"]->REQ->has('refresh')) ? '&refresh=1' : '')
                . (($GLOBALS["SL"]->REQ->has('print')) ? '&print=1' : '');
            $GLOBALS["SL"]->loadStates();
            return true;
        }
        return false;
    }
    
    protected function getSimilarStats($ps = NULL)
    {
        if (!$ps && isset($this->sessData->dataSets["PowerScore"]) 
            && sizeof($this->sessData->dataSets["PowerScore"]) > 0) {
            $ps = $this->sessData->dataSets["PowerScore"][0];
        } elseif ($this->coreID > 0) {
            $ps = RIIPowerScore::find($this->coreID);
        }
        if ($ps && isset($ps->PsID)) {
            return RIIPSRankings::where('PsRnkPSID', $ps->PsID)
                ->where('PsRnkFilters', '&fltFarm=' . $ps->PsCharacterize)
                ->first();
        }
        return new RIIPSRankings;
    }
    
}