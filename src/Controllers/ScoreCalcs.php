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
        if (isset($this->sessData->dataSets["PowerScore"]) 
            && isset($this->sessData->dataSets["PowerScore"][0])
            && isset($this->sessData->dataSets["PSAreas"])) {
            $ps = $this->sessData->dataSets["PowerScore"][0];
            $areas = $this->sessData->dataSets["PSAreas"];

            if (!isset($ps->PsEfficFacility) 
                || $GLOBALS["SL"]->REQ->has('refresh') 
                || $GLOBALS["SL"]->REQ->has('recalc')) {
                $this->chkPsType();
                
                // Next, Recalculate Raw Efficiency Numbers
                $ps->PsEfficFacility   = 0;
                $ps->PsEfficProduction = 0;
                $ps->PsEfficHvac       = 0;
                $ps->PsEfficLighting   = 0;
                $ps->PsEfficCarbon     = 0;
                $ps->PsEfficWater      = 0;
                $ps->PsEfficWaste      = 0;
                $ps->PsLightingError   = 0;
                if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
                    $ps->PsEfficLightingStatus = $this->statusComplete;
                }
                if ($this->v["totFlwrSqFt"] > 0 
                    && (!isset($ps->PsTotalSize) 
                        || intVal($ps->PsTotalSize) == 0)) {
                    $ps->PsTotalSize = $this->v["totFlwrSqFt"];
                }
                
                if ($ps->PsTimeType == $GLOBALS["SL"]->def
                    ->getID('PowerScore Submission Type', 'Future')) {
                    $ps = $this->calcFutureYields();
                } else {
                    if (isset($ps->PsKWH) && intVal($ps->PsKWH) > 0
                        && isset($ps->PsGrams) && intVal($ps->PsGrams) > 0) {
                        $ps->PsEfficProduction = $ps->PsGrams
                            /$ps->PsKWH;
                    }
                    if (isset($this->v["totFlwrSqFt"]) 
                        && intVal($this->v["totFlwrSqFt"]) > 0) {
                        if (isset($ps->PsKWH) && intVal($ps->PsKWH) > 0) {
                            $ps->PsEfficFacility = $ps->PsKWH
                                /$this->v["totFlwrSqFt"];
                        }
                        if (isset($row->PsGreenWasteLbs) 
                            && intVal($row->PsGreenWasteLbs) > 0) {
                            $ps->PsEfficWaste = $ps->PsGreenWasteLbs
                                /$this->v["totFlwrSqFt"];
                        }
                    }
                }
                
                $ps->PsTotalCanopySize = 0;
                $sqft = $watts = $wattsHvac = $gal = [];
                if (sizeof($areas) > 0) {
                    foreach ($areas as $a => $area) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->PsAreaType == $defID && $typ != 'Dry') {
                                $ps->PsTotalCanopySize += $area->PsAreaSize;
                                $sqft[$typ] = $area->PsAreaSize;
                                $watts[$typ] = $wattsHvac[$typ] 
                                    = $gal[$typ] = $fixCnt = 0;
                                if (!isset($area->PsAreaLgtArtif) 
                                    || intVal($area->PsAreaLgtArtif) == 0) {
                                    $watts[$typ] = 0.0000001;
                                } else {
                                    $this->chkLgtWatts($area, $typ, $watts, $fixCnt);
                                    if ($watts[$typ] <= 0 
                                        && !in_array($typ, ['Mother', 'Clone'])) {
                                        // give Mothers & Clones a pass for now
                                        $this->addLightingError($typ);
                                    }
                                }
                                $area->PsAreaTotalLightWatts = $watts[$typ];
                                $area->PsAreaSqFtPerFix2 = (($fixCnt > 0) 
                                        ? $sqft[$typ]/$fixCnt : 0);
                                if (isset($area->PsAreaLgtFixSize1) 
                                    && intVal($area->PsAreaLgtFixSize1) > 0
                                    && isset($area->PsAreaLgtFixSize2) 
                                    && intVal($area->PsAreaLgtFixSize2) > 0) {
                                    $area->PsAreaSqFtPerFix1
                                        = intVal($area->PsAreaLgtFixSize1)
                                            *intVal($area->PsAreaLgtFixSize2);
                                }
                                $area->save();
                                $this->sessData->dataSets["PSAreas"][$a] = $area;
                            }
                        }
                    }
                    
                    if (isset($ps[0]->PsMotherLoc)) {
                        if ($ps->PsMotherLoc == $GLOBALS["SL"]->def
                            ->getID('PowerScore Mother Location', 'With Clones')) {
                            $watts["Clone"] += $watts["Mother"];
                            $sqft["Clone"]  += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        } elseif ($ps->PsMotherLoc == $GLOBALS["SL"]->def
                            ->getID('PowerScore Mother Location', 'In Veg Room')) {
                            $watts["Veg"]   += $watts["Mother"];
                            $sqft["Veg"]    += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        }
                    }
                    
                    $hasLights = 0;
                    foreach ($areas as $a => $area) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->PsAreaType == $defID && $typ != 'Dry') {
                                $area->PsAreaCalcSize = $sqft[$typ];
                                $area->PsAreaCalcWatts = $watts[$typ];
                                $area->PsAreaLightingEffic = 0;
                                if (intVal($sqft[$typ]) > 0) {
                                    $sqftWeight = $sqft[$typ]
                                        /$ps->PsTotalCanopySize;
                                    if ($watts[$typ] > 0) {
                                        $area->PsAreaLgtArtif = 1;
                                        $hasLights++;
                                        $area->PsAreaLightingEffic = $watts[$typ]
                                            /$sqft[$typ];
                                        $ps->PsEfficLighting += $sqftWeight
                                            *$area->PsAreaLightingEffic;
                                    }
                                    if (isset($area->PsAreaHvacType) 
                                        && intVal($area->PsAreaHvacType) > 0) {
                                        $area->PsAreaHvacEffic = $GLOBALS["CUST"]
                                            ->getHvacEffic($area->PsAreaHvacType);
                                        $ps->PsEfficHvac += $sqftWeight
                                            *$area->PsAreaHvacEffic;
                                    }
                                    if (isset($area->PsAreaGallons) 
                                        && intVal($area->PsAreaGallons) > 0) {
                                        $area->PsAreaWaterEffic = $area->PsAreaGallons
                                                /$sqft[$typ];
                                        $ps->PsEfficWater += $sqftWeight
                                            *$area->PsAreaWaterEffic;
                                    }
                                }
                                $area->save();
                                $this->sessData->dataSets["PSAreas"][$a] = $area;
                            }
                        }
                    }
                    if ($hasLights == 0) {
                        $ps->PsEfficLighting = 0.00000001;
                    }
                }
                $ps->save();
                $this->sessData->dataSets["PowerScore"][0] = $ps;
            }
        }
        return true;
    }

    // First, Determine Farm Type
    protected function chkPsType()
    {
        $ps = $this->sessData->dataSets["PowerScore"][0];
        $prevFarmType = $ps->PsCharacterize;
        $flwrSun = (intVal($this->getAreaFld('Flower', 'PsAreaLgtSun')) == 1);
        $flwrDep = (intVal($this->getAreaFld('Flower', 'PsAreaLgtDep')) == 1);
        if (!$flwrSun && !$flwrDep) {
            $this->sessData->dataSets["PowerScore"][0]
                ->PsCharacterize = $this->frmTypIn;
        } else { // Uses the Sun during Flowering Stage
            $areaFlwrID = $this->getAreaFld('Flower', 'PsAreaID');
            $flwrOutdoor = $flwrGrnhse = false;
            if ($flwrDep) {
                $flwrGrnhse = true;
            } elseif ($flwrSun == 1) {
                $flwrOutdoor = true;
            }
            $areaFlwrBlds = $this->sessData
                ->getChildRows('Areas', $areaFlwrID, 'AreaBlds');
            if (sizeof($areaFlwrBlds) > 0) {
                foreach ($areaFlwrBlds as $bld) {
                    if ($bld->PsArBldType == $this->frmTypOut) {
                        $flwrOutdoor = true;
                    } elseif ($bld->PsArBldType == $this->frmTypGrn) {
                        $flwrGrnhse = true;
                    }
                }
            }
            if ($flwrGrnhse) {
                $ps->PsCharacterize = $this->frmTypGrn;
            } else {
                $ps->PsCharacterize = $this->frmTypOut;
            }
        }
        $this->sessData->dataSets["PowerScore"][0] = $ps;
        $this->sessData->dataSets["PowerScore"][0]->save();
        return true;
    }


    protected function chkLgtWatts($area, $typ, &$watts = 0, &$fixCnt = 0)
    {
        if (isset($this->sessData->dataSets["PSLightTypes"])) {
            $lgts = $this->sessData->dataSets["PSLightTypes"];
            if (sizeof($lgts) > 0) {
                foreach ($lgts as $lgt) {
                    if ($lgt->PsLgTypAreaID == $area->getKey()
                        && isset($lgt->PsLgTypCount) 
                        && intVal($lgt->PsLgTypCount) > 0 
                        && isset($lgt->PsLgTypWattage) 
                        && intVal($lgt->PsLgTypWattage) > 0) {
                        $watts[$typ] += ($lgt->PsLgTypCount
                            *$lgt->PsLgTypWattage);
                        $fixCnt += $lgt->PsLgTypCount;
                    }
                }
            }
        }
        return true;
    }
    
    protected function addLightingError($typ)
    {
        $this->sessData->dataSets["PowerScore"][0]->PsLightingError++;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $this->sessData->dataSets["PowerScore"][0]
                ->PsEfficLightingStatus = $this->statusArchive;
        }
        return true;
    }
    
    protected function calcCurrScoreRanks()
    {
//echo '<br /><br /><br /><h2>' . $this->searcher->v["urlFlts"] . '</h2>';
        $this->v["ranksCache"] = RIIPSRanks::where('PsRnkFilters', 
                $this->searcher->v["urlFlts"])
            ->first();
        if (!$this->v["ranksCache"] 
            || !isset($this->v["ranksCache"]->PsRnkID)) {
            $this->v["ranksCache"] = new RIIPSRanks;
            $this->v["ranksCache"]->PsRnkFilters 
                = $this->searcher->v["urlFlts"];
        } /* elseif (!$GLOBALS["SL"]->REQ->has('refresh') && !$GLOBALS["SL"]->REQ->has('recalc')) {
            return $this->v["ranksCache"];
        } */
        $eval = "\$allscores = " 
            . $GLOBALS["SL"]->modelPath('PowerScore') . "::" 
            . $this->searcher->filterAllPowerScoresPublic() 
            . "->get();";
        eval($eval);
        $this->v["ranksCache"]->PsRnkTotCnt = $allscores->count();
//return '';
        $r = [];
        $l = [
            "over" => [], 
            "oraw" => [], 
            "faci" => [], 
            "prod" => [], 
            "ligh" => [], 
            "hvac" => [], 
            "watr" => [], 
            "wste" => []
        ];
        $avg = [
            "kwh" => 0,
            "g" => 0
        ];
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
                if ($ps->PsEfficFacility > 0 
                    && $ps->PsEfficFacilityStatus == $this->statusComplete) {
                    $l["faci"][] = $ps->PsEfficFacility;
                }
                if ($ps->PsEfficProduction > 0 
                    && $ps->PsEfficProductionStatus == $this->statusComplete) {
                    $l["prod"][] = $ps->PsEfficProduction;
                }
                if ($ps->PsEfficLighting > 0 
                    && $ps->PsEfficLightingStatus == $this->statusComplete) {
                    $l["ligh"][] = $ps->PsEfficLighting;
                }
                if ($ps->PsEfficHvac > 0 
                    && $ps->PsEfficHvacStatus == $this->statusComplete) {
                    $l["hvac"][] = $ps->PsEfficHvac;
                }
                if ($ps->PsEfficWater > 0 
                    && $ps->PsEfficFacilityStatus == $this->statusComplete) {
                    $l["watr"][] = $ps->PsEfficWater;
                }
                if ($ps->PsEfficWaste > 0 
                    && $ps->PsEfficWasteStatus == $this->statusComplete) {
                    $l["wste"][] = $ps->PsEfficWaste;
                }
            }
            sort($l["faci"], SORT_NUMERIC);
            sort($l["prod"], SORT_NUMERIC);
            sort($l["ligh"], SORT_NUMERIC);
            sort($l["hvac"], SORT_NUMERIC);
            sort($l["watr"], SORT_NUMERIC);
            sort($l["wste"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->PsID] = [
                    "over" => 0,
                    "oraw" => 0,
                    "faci" => 0,
                    "prod" => 0,
                    "ligh" => 0,
                    "hvac" => 0,
                    "watr" => 0,
                    "wste" => 0
                ];
                $r[$ps->PsID]["faci"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["faci"], 
                    $ps->PsEfficFacility,
                    true
                );
                $r[$ps->PsID]["prod"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["prod"], 
                    $ps->PsEfficProduction
                );
                $r[$ps->PsID]["ligh"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["ligh"], 
                    $ps->PsEfficLighting,
                    true
                );
                $r[$ps->PsID]["hvac"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["hvac"], 
                    $ps->PsEfficHvac,
                    true
                );
                $r[$ps->PsID]["watr"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["watr"], 
                    $ps->PsEfficWater,
                    true
                );
                $r[$ps->PsID]["wste"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["wste"], 
                    $ps->PsEfficWaste,
                    true
                );
                $r[$ps->PsID]["oraw"] = ($r[$ps->PsID]["faci"]
                    +$r[$ps->PsID]["prod"]+$r[$ps->PsID]["ligh"]
                    +$r[$ps->PsID]["hvac"]+$r[$ps->PsID]["watr"]
                    +$r[$ps->PsID]["wste"])/6;
                $l["oraw"][] = $r[$ps->PsID]["oraw"];
            }
            
            sort($l["oraw"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->PsID]["over"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["oraw"], 
                    $r[$ps->PsID]["oraw"]
                );
            }
            
            // Now store calculated ranks for individual scores...
            $currFlt = trim($this->searcher->v["urlFlts"]);
            $allFilts = ['', '&fltFarm=0'];
            foreach ($allscores as $i => $ps) {
                if (in_array($currFlt, $allFilts)) {
                    RIIPowerScore::find($ps->PsID)->update([
                        'PsEfficOverall' => $r[$ps->PsID]["over"]
                    ]);
                }
                if ($currFlt == '&fltFarm=' . $ps->PsCharacterize) {
                    RIIPowerScore::find($ps->PsID)->update([
                        'PsEfficOverSimilar' => $r[$ps->PsID]["over"]
                    ]);
                }
                $tmp = RIIPSRankings::where('PsRnkPSID', $ps->PsID)
                    ->where('PsRnkFilters', $currFlt)
                    ->first();
                if (!$tmp) {
                    $tmp = new RIIPSRankings;
                    $tmp->PsRnkPSID = $ps->PsID;
                    $tmp->PsRnkFilters = $currFlt;
                    $tmp->save();
                }
                $tmp->PsRnkTotCnt     = $allscores->count();
                $tmp->PsRnkOverall    = $r[$ps->PsID]["over"];
                $tmp->PsRnkOverallAvg = $r[$ps->PsID]["oraw"];
                $tmp->PsRnkFacility   = $r[$ps->PsID]["faci"];
                $tmp->PsRnkProduction = $r[$ps->PsID]["prod"];
                $tmp->PsRnkLighting   = $r[$ps->PsID]["ligh"];
                $tmp->PsRnkHVAC       = $r[$ps->PsID]["hvac"];
                $tmp->PsRnkWater      = $r[$ps->PsID]["watr"];
                $tmp->PsRnkWaste      = $r[$ps->PsID]["wste"];
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
        $this->v["ranksCache"]->PsRnkWater      = implode(',', $l["watr"]);
        $this->v["ranksCache"]->PsRnkWaste      = implode(',', $l["wste"]);
        if ($this->v["ranksCache"]->PsRnkTotCnt > 0) {
            $this->v["ranksCache"]->PsRnkAvgSqftKwh 
                = $avg["kwh"]/$this->v["ranksCache"]->PsRnkTotCnt;
            $this->v["ranksCache"]->PsRnkAvgSqftGrm 
                = $avg["g"]/$this->v["ranksCache"]->PsRnkTotCnt;
        }
        $this->v["ranksCache"]->save();
        return $this->v["ranksCache"];
    }
    
    protected function calcFutureYields()
    {
        $this->loadTotFlwrSqFt();
        $this->initSearcher();
        $this->searcher->loadCurrScoreFltParams($this->sessData->dataSets);
        $matches = [
            "flt" => [], 
            "kwh" => 0, 
            "grm" => 0 
        ];
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
            $this->sessData->dataSets["PowerScore"][0]
                ->PsEfficFacility = $matches["kwh"]/sizeof($matches["flt"]);
            $this->sessData->dataSets["PowerScore"][0]
                ->PsKWH = $this->sessData->dataSets["PowerScore"][0]
                    ->PsEfficFacility*$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["PowerScore"][0]
                ->PsGrams = ($matches["grm"]/sizeof($matches["flt"]))
                    *$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["PowerScore"][0]->save();
        }
        return $this->sessData->dataSets["PowerScore"][0];
    }
    
    protected function recalc2AllSubScores()
    {
///////////////// One Time
/*
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
*/
/////////////////
        
        $hasMore = false;
        $doneIDs = [];
        if ($GLOBALS["SL"]->REQ->has('doneIDs') 
            && trim($GLOBALS["SL"]->REQ->get('doneIDs')) != '') {
            $doneIDs = $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('doneIDs'));
        }
        $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, $this->dbID, 1);
        $GLOBALS["SL"]->x["pageView"] = $GLOBALS["SL"]->x["dataPerms"] = 'public';
        $this->loadCustLoop($GLOBALS["SL"]->REQ, 1);
        $all = RIIPowerScore::select('PsID')
            ->where('PsStatus', 'NOT LIKE', $this->statusIncomplete)
            ->whereNotIn('PsID', $doneIDs)
            ->get();
        if ($all->isNotEmpty()) {
            foreach ($all as $i => $ps) {
                if ($i < 30) {
                    $this->custReport->loadAllSessData('PowerScore', $ps->PsID);
                    $this->custReport->calcCurrSubScores();
                    $doneIDs[] = $ps->PsID;
                } else {
                    $hasMore = true;
                }
            }
        }
        if ($hasMore) {
            return '<br /><br />Recalculating... '
                . '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'?recalc=1' 
                . (($GLOBALS["SL"]->REQ->has('fixLightingErrors')) 
                    ? '&fixLightingErrors=1' : '')
                . '&doneIDs=' . implode(',', $doneIDs) 
                . '\'", 1000); </script>'
                . '<style> #nodeSubBtns { display: none; } </style>';
        }
        return '<br /><br />Recalculations Complete<br />'
            . '<a href="/dash/powerscore-software-troubleshooting">Back</a>'
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
        $curr = (($GLOBALS["SL"]->REQ->has('currFlt')) 
            ? $GLOBALS["SL"]->REQ->get('currFlt') : '');
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
        $msg = '<i class="slGrey">Recalculating ' . (1+$freshDone) . '/'
            . sizeof($GLOBALS["CUST"]->v["fltComb"]) . '...</i>';
        if ($redir == 'report-ajax') {
            if ($nextFlt != '') {
                return view('vendor.cannabisscore.nodes.'
                    . '490-report-calculations-top-refresh-mid', [
                    "msg"     => $msg,
                    "nextFlt" => $nextFlt,
                    "psid"    => $this->v["ajax-psid"]
                ])->render();
            }
            return $msg . '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'/calculated/read-'
                . $this->v["ajax-psid"] . '\'", 1000); </script>';
        }
        if ($nextFlt != '') {
            return $msg . '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'/dash/powerscore-software'
                . '-troubleshooting?refresh=1&currFlt=' . $nextFlt 
                . '\'", 1000); </script>';
        }
        return '<br /><br />Recalculations Complete!<br />'
            . '<a href="/dash/powerscore-software-troubleshooting">Back</a>'
            . '<br /><style> #nodeSubBtns { display: none; } </style>';
    }
    
    protected function rankAllScores()
    {
        $this->v["allRnks"] = [];
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('PowerScore') 
            . "::" . $this->filterAllPowerScoresPublic() 
            . "->where('PsTimeType', " . $GLOBALS["SL"]->def
                ->getID('PowerScore Submission Type', 'Past') . ")"
            . "->select('PsID', 'PsEfficOverall', "
            . "'PsEfficFacility', 'PsEfficProduction', 'PsEfficHvac', "
            . "'PsEfficLighting', 'PsEfficWater', 'PsEfficWaste', "
            . "'PsEfficFacilityStatus', 'PsEfficProductionStatus', 'PsEfficHvacStatus', "
            . "'PsEfficLightingStatus', 'PsEfficWaterStatus', 'PsEfficWasteStatus')"
            . "->get();";
        eval($eval);
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $s) {
            // current PowerScore is "better" than X others, and "worse" than Y others
                $efficPercs = [
                    "Facility"   => [ "better" => 0, "worse" => 0 ], 
                    "Production" => [ "better" => 0, "worse" => 0 ], 
                    "HVAC"       => [ "better" => 0, "worse" => 0 ], 
                    "Lighting"   => [ "better" => 0, "worse" => 0 ],
                    "Carbon"     => [ "better" => 0, "worse" => 0 ], 
                    "Water"      => [ "better" => 0, "worse" => 0 ], 
                    "Waste"      => [ "better" => 0, "worse" => 0 ]
                ];
                foreach ($allscores as $s2) {
                    if ($s2->PsEfficFacilityStatus == $this->statusComplete) {
                        $efficPercs["Facility"][($s->PsEfficFacility <= $s2->PsEfficFacility) 
                            ? "better" : "worse"]++;
                    }
                    if ($s2->PsEfficProductionStatus == $this->statusComplete) {
                        $efficPercs["Production"][($s->PsEfficProduction >= $s2->PsEfficProduction) 
                            ? "better" : "worse"]++;
                    }
                    if ($s2->PsEfficHvacStatus == $this->statusComplete) {
                        $efficPercs["HVAC"][($s->PsEfficHvac <= $s2->PsEfficHvac) 
                            ? "better" : "worse"]++;
                    }
                    if ($s2->PsEfficLightingFacilityStatus == $this->statusComplete) {
                        $efficPercs["Lighting"][($s->PsEfficLighting <= $s2->PsEfficLighting) 
                            ? "better" : "worse"]++;
                    }
                    if ($s2->PsEfficWater > 0 
                        && $s2->PsEfficWaterStatus == $this->statusComplete) {
                        $efficPercs["Water"][($s->PsEfficWater <= $s2->PsEfficWater) 
                            ? "better" : "worse"]++;
                    }
                    if ($s2->PsEfficWaste > 0 
                        && $s2->PsEfficWasteStatus == $this->statusComplete) {
                        $efficPercs["Waste"][($s->PsEfficWaste <= $s2->PsEfficWaste) 
                            ? "better" : "worse"]++;
                    }
                }
                $this->v["allRnks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', $this->searcher->v["urlFlts"])
                    ->first();
                if (!$this->v["allRnks"][$s->PsID] 
                    || !isset($this->v["allRnks"][$s->PsID]->PsRnkID)) {
                    $this->v["allRnks"][$s->PsID] = new RIIPSRankings;
                    $this->v["allRnks"][$s->PsID]->PsRnkPSID = $s->PsID;
                    $this->v["allRnks"][$s->PsID]
                        ->PsRnkFilters = $this->searcher->v["urlFlts"];
                    $this->v["allRnks"][$s->PsID]
                        ->PsRnkTotCnt = $allscores->count();
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg = $ind = 0;
                foreach ($efficPercs as $type => $percs) {
                    $ind++;
                    $tot = $percs["better"]+$percs["worse"];
                    $this->v["lgtCompetData"]->dataLegend[$ind][3] = $tot;
                    $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type } 
                        = 100*($percs["better"]/$tot);
                    $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg 
                        += $this->v["allRnks"][$s->PsID]->{ 'PsRnk' . $type };
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg 
                    = $this->v["allRnks"][$s->PsID]->PsRnkOverallAvg/6;
                $this->v["allRnks"][$s->PsID]->save();
            }
            foreach ($allscores as $s) {
                $efficPercs = [ "better" => 0, "worse" => 0 ];
                foreach ($allscores as $s2) {
                    $efficPercs[($this->v["allRnks"][$s->PsID]->PsRnkOverallAvg 
                        >= $this->v["allRnks"][$s2->PsID]->PsRnkOverallAvg) 
                        ? "better" : "worse"]++;
                }
                $this->v["allRnks"][$s->PsID]->PsRnkOverall 
                    = 100*($efficPercs["better"]
                        /$this->v["allRnks"][$s->PsID]->PsRnkTotCnt);
                $this->v["allRnks"][$s->PsID]->save();
                if ($this->searcher->v["urlFlts"] == '') {
                    $s->PsEfficOverall = $this->v["allRnks"][$s->PsID]->PsRnkOverall;
                    $s->save();
                } elseif ($this->searcher->v["urlFlts"] 
                    == ('&fltFarm=' . $s->PsCharacterize)) {
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
            $this->v["hasRefresh"] 
                = (($GLOBALS["SL"]->REQ->has('refresh')) ? '&refresh=1' : '')
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