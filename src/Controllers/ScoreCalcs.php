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
        if (isset($this->sessData->dataSets["powerscore"]) 
            && isset($this->sessData->dataSets["powerscore"][0])
            && isset($this->sessData->dataSets["ps_areas"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            $areas = $this->sessData->dataSets["ps_areas"];

            if (!isset($ps->ps_effic_facility) 
                || $GLOBALS["SL"]->REQ->has('refresh') 
                || $GLOBALS["SL"]->REQ->has('recalc')) {
                $this->chkPsType();
                
                // Next, Recalculate Raw Efficiency Numbers
                $ps->ps_effic_facility   = 0;
                $ps->ps_effic_production = 0;
                $ps->ps_effic_hvac       = 0;
                $ps->ps_effic_lighting   = 0;
                $ps->ps_effic_carbon     = 0;
                $ps->ps_effic_water      = 0;
                $ps->ps_effic_waste      = 0;
                $ps->ps_lighting_error   = 0;
                if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
                    $ps->ps_effic_lighting_status = $this->statusComplete;
                }
                if ($this->v["totFlwrSqFt"] > 0 
                    && (!isset($ps->ps_total_size) || intVal($ps->ps_total_size) == 0)) {
                    $ps->ps_total_size = $this->v["totFlwrSqFt"];
                }
                
                if ($ps->ps_time_type == $GLOBALS["SL"]->def
                    ->getID('PowerScore Submission Type', 'Future')) {
                    $ps = $this->calcFutureYields();
                } else {
                    if (isset($ps->ps_kwh) 
                        && intVal($ps->ps_kwh) > 0
                        && isset($ps->ps_grams) 
                        && intVal($ps->ps_grams) > 0) {
                        $ps->ps_effic_production = $ps->ps_grams/$ps->ps_kwh;
                    }
                    if (isset($this->v["totFlwrSqFt"]) 
                        && intVal($this->v["totFlwrSqFt"]) > 0) {
                        if (isset($ps->ps_kwh) && intVal($ps->ps_kwh) > 0) {
                            $ps->ps_effic_facility = $ps->ps_kwh/$this->v["totFlwrSqFt"];
                        }
                        if (isset($row->ps_green_waste_lbs) 
                            && intVal($row->ps_green_waste_lbs) > 0) {
                            $ps->ps_effic_waste = $ps->ps_green_waste_lbs/$this->v["totFlwrSqFt"];
                        }
                    }
                }
                
                $ps->ps_total_canopy_size = 0;
                $sqft = $watts = $wattsHvac = $gal = [];
                if (sizeof($areas) > 0) {
                    foreach ($areas as $a => $area) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->ps_area_type == $defID && $typ != 'Dry') {
                                $ps->ps_total_canopy_size += $area->ps_area_size;
                                $sqft[$typ] = $area->ps_area_size;
                                $watts[$typ] = $wattsHvac[$typ] = $gal[$typ] = $fixCnt = 0;
                                if (!isset($area->ps_area_lgt_artif) 
                                    || intVal($area->ps_area_lgt_artif) == 0) {
                                    $watts[$typ] = 0.0000001;
                                } else {
                                    $this->chkLgtWatts($area, $typ, $watts, $fixCnt);
                                    if ($watts[$typ] <= 0 && !in_array($typ, ['Mother', 'Clone'])) {
                                        // give Mothers & Clones a pass for now
                                        $this->addLightingError($typ);
                                    }
                                }
                                $area->ps_area_total_light_watts = $watts[$typ];
                                $area->ps_area_sq_ft_per_fix2 = (($fixCnt > 0) ? $sqft[$typ]/$fixCnt : 0);
                                if (isset($area->ps_area_lgt_fix_size1) 
                                    && intVal($area->ps_area_lgt_fix_size1) > 0
                                    && isset($area->ps_area_lgt_fix_size2) 
                                    && intVal($area->ps_area_lgt_fix_size2) > 0) {
                                    $area->ps_area_sq_ft_per_fix1 = intVal($area->ps_area_lgt_fix_size1)
                                        *intVal($area->ps_area_lgt_fix_size2);
                                }
                                $area->save();
                                $this->sessData->dataSets["ps_areas"][$a] = $area;
                            }
                        }
                    }
                    
                    if (isset($ps[0]->ps_mother_loc)) {
                        $withClones = $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'With Clones');
                        $vegRoom = $GLOBALS["SL"]->def->getID('PowerScore Mother Location', 'In Veg Room');
                        if ($ps->ps_mother_loc == $withClones) {
                            $watts["Clone"] += $watts["Mother"];
                            $sqft["Clone"]  += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        } elseif ($ps->ps_mother_loc == $vegRoom) {
                            $watts["Veg"]   += $watts["Mother"];
                            $sqft["Veg"]    += $sqft["Mother"];
                            $watts["Mother"] = $sqft["Mother"] = 0;
                        }
                    }
                    
                    $hasLights = 0;
                    foreach ($areas as $a => $area) {
                        foreach ($this->v["areaTypes"] as $typ => $defID) {
                            if ($area->ps_area_type == $defID && $typ != 'Dry') {
                                $area->ps_area_calc_size = $sqft[$typ];
                                $area->ps_area_calc_watts = $watts[$typ];
                                $area->ps_area_lighting_effic = 0;
                                if (intVal($sqft[$typ]) > 0) {
                                    $sqftWeight = $sqft[$typ]/$ps->ps_total_canopy_size;
                                    if ($watts[$typ] > 0) {
                                        $area->ps_area_lgt_artif = 1;
                                        $hasLights++;
                                        $area->ps_area_lighting_effic = $watts[$typ]/$sqft[$typ];
                                        $ps->ps_effic_lighting += $sqftWeight*$area->ps_area_lighting_effic;
                                    }
                                    if (isset($area->ps_area_hvac_type) 
                                        && intVal($area->ps_area_hvac_type) > 0) {
                                        $area->ps_area_hvac_effic = $GLOBALS["CUST"]->getHvacEffic(
                                            $area->ps_area_hvac_type
                                        );
                                        $ps->ps_effic_hvac += $sqftWeight*$area->ps_area_hvac_effic;
                                    }
                                    if (isset($area->ps_area_gallons) && intVal($area->ps_area_gallons) > 0) {
                                        $area->ps_area_water_effic = $area->ps_area_gallons/$sqft[$typ];
                                        $ps->ps_effic_water += $sqftWeight*$area->ps_area_water_effic;
                                    }
                                }
                                $area->save();
                                $this->sessData->dataSets["ps_areas"][$a] = $area;
                            }
                        }
                    }
                    if ($hasLights == 0) {
                        $ps->ps_effic_lighting = 0.00000001;
                    }
                }
                $ps->save();
                $this->sessData->dataSets["powerscore"][0] = $ps;
            }
        }
        return true;
    }

    // First, Determine Farm Type
    protected function chkPsType()
    {
        $ps = $this->sessData->dataSets["powerscore"][0];
        $prevFarmType = $ps->ps_characterize;
        $flwrSun = (intVal($this->getAreaFld('Flower', 'ps_area_lgt_sun')) == 1);
        $flwrDep = (intVal($this->getAreaFld('Flower', 'ps_area_lgt_dep')) == 1);
        if (!$flwrSun && !$flwrDep) {
            $this->sessData->dataSets["powerscore"][0]->ps_characterize = $this->frmTypIn;
        } else { // Uses the Sun during Flowering Stage
            $areaFlwrID = $this->getAreaFld('Flower', 'ps_area_id');
            $flwrOutdoor = $flwrGrnhse = false;
            if ($flwrDep) {
                $flwrGrnhse = true;
            } elseif ($flwrSun == 1) {
                $flwrOutdoor = true;
            }
            $areaFlwrBlds = $this->sessData->getChildRows('areas', $areaFlwrID, 'area_blds');
            if (sizeof($areaFlwrBlds) > 0) {
                foreach ($areaFlwrBlds as $bld) {
                    if ($bld->ps_ar_bld_type == $this->frmTypOut) {
                        $flwrOutdoor = true;
                    } elseif ($bld->ps_ar_bld_type == $this->frmTypGrn) {
                        $flwrGrnhse = true;
                    }
                }
            }
            if ($flwrGrnhse) {
                $ps->ps_characterize = $this->frmTypGrn;
            } else {
                $ps->ps_characterize = $this->frmTypOut;
            }
        }
        $this->sessData->dataSets["powerscore"][0] = $ps;
        $this->sessData->dataSets["powerscore"][0]->save();
        return true;
    }


    protected function chkLgtWatts($area, $typ, &$watts = 0, &$fixCnt = 0)
    {
        if (isset($this->sessData->dataSets["ps_light_types"])) {
            $lgts = $this->sessData->dataSets["ps_light_types"];
            if (sizeof($lgts) > 0) {
                foreach ($lgts as $lgt) {
                    if ($lgt->ps_lg_typ_area_id == $area->getKey()
                        && isset($lgt->ps_lg_typ_count) 
                        && intVal($lgt->ps_lg_typ_count) > 0 
                        && isset($lgt->ps_lg_typ_wattage) 
                        && intVal($lgt->ps_lg_typ_wattage) > 0) {
                        $watts[$typ] += ($lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage);
                        $fixCnt += $lgt->ps_lg_typ_count;
                    }
                }
            }
        }
        return true;
    }
    
    protected function addLightingError($typ)
    {
        $this->sessData->dataSets["powerscore"][0]->ps_lighting_error++;
        if ($GLOBALS["SL"]->REQ->has('fixLightingErrors')) {
            $this->sessData->dataSets["powerscore"][0]->ps_effic_lighting_status = $this->statusArchive;
        }
        return true;
    }
    
    protected function calcCurrScoreRanks()
    {
//echo '<br /><br /><br /><h2>' . $this->searcher->v["urlFlts"] . '</h2>';
        $this->v["ranksCache"] = RIIPSRanks::where('ps_rnk_filters', $this->searcher->v["urlFlts"])
            ->first();
        if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->ps_rnk_id)) {
            $this->v["ranksCache"] = new RIIPSRanks;
            $this->v["ranksCache"]->ps_rnk_filters = $this->searcher->v["urlFlts"];
        } /* elseif (!$GLOBALS["SL"]->REQ->has('refresh') && !$GLOBALS["SL"]->REQ->has('recalc')) {
            return $this->v["ranksCache"];
        } */
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('powerscore') . "::" 
            . $this->searcher->filterAllPowerScoresPublic() . "->get();";
        eval($eval);
        $this->v["ranksCache"]->ps_rnk_tot_cnt = $allscores->count();
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
            "g"   => 0
        ];
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $i => $ps) {
                $sqft = RIIPSAreas::where('ps_area_psid', $ps->ps_id)
                    ->where('ps_area_type', $this->v["areaTypes"]["Flower"])
                    ->select('ps_area_size')
                    ->first();
                if ($sqft && isset($sqft->ps_area_size)) {
                    $avg["kwh"] += $ps->ps_kwh/$sqft->ps_area_size;
                    $avg["g"] += $ps->ps_grams/$sqft->ps_area_size;
                }
                if ($ps->ps_effic_facility > 0 
                    && $ps->ps_effic_facility_status == $this->statusComplete) {
                    $l["faci"][] = $ps->ps_effic_facility;
                }
                if ($ps->ps_effic_production > 0 
                    && $ps->ps_effic_production_status == $this->statusComplete) {
                    $l["prod"][] = $ps->ps_effic_production;
                }
                if ($ps->ps_effic_lighting > 0 
                    && $ps->ps_effic_lighting_status == $this->statusComplete) {
                    $l["ligh"][] = $ps->ps_effic_lighting;
                }
                if ($ps->ps_effic_hvac > 0 
                    && $ps->ps_effic_hvac_status == $this->statusComplete) {
                    $l["hvac"][] = $ps->ps_effic_hvac;
                }
                if ($ps->ps_effic_water > 0 
                    && $ps->ps_effic_facility_status == $this->statusComplete) {
                    $l["watr"][] = $ps->ps_effic_water;
                }
                if ($ps->ps_effic_waste > 0 
                    && $ps->ps_effic_waste_status == $this->statusComplete) {
                    $l["wste"][] = $ps->ps_effic_waste;
                }
            }
            sort($l["faci"], SORT_NUMERIC);
            sort($l["prod"], SORT_NUMERIC);
            sort($l["ligh"], SORT_NUMERIC);
            sort($l["hvac"], SORT_NUMERIC);
            sort($l["watr"], SORT_NUMERIC);
            sort($l["wste"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->ps_id] = [
                    "over" => 0,
                    "oraw" => 0,
                    "faci" => 0,
                    "prod" => 0,
                    "ligh" => 0,
                    "hvac" => 0,
                    "watr" => 0,
                    "wste" => 0
                ];
                $r[$ps->ps_id]["faci"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["faci"], 
                    $ps->ps_effic_facility,
                    true
                );
                $r[$ps->ps_id]["prod"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["prod"], 
                    $ps->ps_effic_production
                );
                $r[$ps->ps_id]["ligh"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["ligh"], 
                    $ps->ps_effic_lighting,
                    true
                );
                $r[$ps->ps_id]["hvac"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["hvac"], 
                    $ps->ps_effic_hvac,
                    true
                );
                $r[$ps->ps_id]["watr"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["watr"], 
                    $ps->ps_effic_water,
                    true
                );
                $r[$ps->ps_id]["wste"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["wste"], 
                    $ps->ps_effic_waste,
                    true
                );
                $r[$ps->ps_id]["oraw"] = ($r[$ps->ps_id]["faci"]
                    +$r[$ps->ps_id]["prod"]+$r[$ps->ps_id]["ligh"]
                    +$r[$ps->ps_id]["hvac"]+$r[$ps->ps_id]["watr"]
                    +$r[$ps->ps_id]["wste"])/6;
                $l["oraw"][] = $r[$ps->ps_id]["oraw"];
            }
            
            sort($l["oraw"], SORT_NUMERIC);
            foreach ($allscores as $i => $ps) {
                $r[$ps->ps_id]["over"] = $GLOBALS["SL"]->getArrPercentile(
                    $l["oraw"], 
                    $r[$ps->ps_id]["oraw"]
                );
            }
            
            // Now store calculated ranks for individual scores...
            $currFlt = trim($this->searcher->v["urlFlts"]);
            $allFilts = [ '', '&fltFarm=0' ];
            foreach ($allscores as $i => $ps) {
                if (in_array($currFlt, $allFilts)) {
                    RIIPowerScore::find($ps->ps_id)->update([
                        'ps_effic_overall' => $r[$ps->ps_id]["over"]
                    ]);
                }
                if ($currFlt == '&fltFarm=' . $ps->ps_characterize) {
                    RIIPowerScore::find($ps->ps_id)->update([
                        'ps_effic_over_similar' => $r[$ps->ps_id]["over"]
                    ]);
                }
                $tmp = RIIPSRankings::where('ps_rnk_psid', $ps->ps_id)
                    ->where('ps_rnk_filters', $currFlt)
                    ->first();
                if (!$tmp) {
                    $tmp = new RIIPSRankings;
                    $tmp->ps_rnk_psid = $ps->ps_id;
                    $tmp->ps_rnk_filters = $currFlt;
                    $tmp->save();
                }
                $tmp->ps_rnk_tot_cnt     = $allscores->count();
                $tmp->ps_rnk_overall     = $r[$ps->ps_id]["over"];
                $tmp->ps_rnk_overall_avg = $r[$ps->ps_id]["oraw"];
                $tmp->ps_rnk_facility    = $r[$ps->ps_id]["faci"];
                $tmp->ps_rnk_production  = $r[$ps->ps_id]["prod"];
                $tmp->ps_rnk_lighting    = $r[$ps->ps_id]["ligh"];
                $tmp->ps_rnk_hvac        = $r[$ps->ps_id]["hvac"];
                $tmp->ps_rnk_water       = $r[$ps->ps_id]["watr"];
                $tmp->ps_rnk_waste       = $r[$ps->ps_id]["wste"];
                $tmp->save();
            }
        }
        
        // Now store listed raw sub-score values for filter...
        $this->v["ranksCache"]->ps_rnk_tot_cnt     = $allscores->count();
        $this->v["ranksCache"]->ps_rnk_overall_avg = implode(',', $l["oraw"]);
        $this->v["ranksCache"]->ps_rnk_facility    = implode(',', $l["faci"]);
        $this->v["ranksCache"]->ps_rnk_production  = implode(',', $l["prod"]);
        $this->v["ranksCache"]->ps_rnk_lighting    = implode(',', $l["ligh"]);
        $this->v["ranksCache"]->ps_rnk_hvac        = implode(',', $l["hvac"]);
        $this->v["ranksCache"]->ps_rnk_water       = implode(',', $l["watr"]);
        $this->v["ranksCache"]->ps_rnk_waste       = implode(',', $l["wste"]);
        if ($this->v["ranksCache"]->ps_rnk_tot_cnt > 0) {
            $this->v["ranksCache"]->ps_rnk_avg_sqft_kwh = $avg["kwh"]
                /$this->v["ranksCache"]->ps_rnk_tot_cnt;
            $this->v["ranksCache"]->ps_rnk_avg_sqft_grm = $avg["g"]
                /$this->v["ranksCache"]->ps_rnk_tot_cnt;
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
                $chk = RIIPSRanks::where('ps_rnk_filters', 'LIKE', $flt)
                    ->where('ps_rnk_tot_cnt', '>', 3)
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $rnk) {
                        $matches["flt"][] = $rnk->ps_rnk_filters;
                        $matches["kwh"] += $rnk->ps_rnk_avg_sqft_kwh;
                        $matches["grm"] += $rnk->ps_rnk_avg_sqft_grm;
                    }
                }
            }
        }
        if (sizeof($matches["flt"]) > 0) {
            $this->sessData->dataSets["powerscore"][0]->ps_effic_facility = $matches["kwh"]
                /sizeof($matches["flt"]);
            $this->sessData->dataSets["powerscore"][0]->ps_kwh = $this->sessData
                ->dataSets["powerscore"][0]->ps_effic_facility*$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["powerscore"][0]->ps_grams = ($matches["grm"]/sizeof($matches["flt"]))
                *$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["powerscore"][0]->save();
        }
        return $this->sessData->dataSets["powerscore"][0];
    }
    
    protected function recalc2AllSubScores()
    {   
        $hasMore = false;
        $doneIDs = [];
        if ($GLOBALS["SL"]->REQ->has('doneIDs') && trim($GLOBALS["SL"]->REQ->get('doneIDs')) != '') {
            $doneIDs = $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('doneIDs'));
        }
        $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, $this->dbID, 1);
        $GLOBALS["SL"]->x["pageView"] = $GLOBALS["SL"]->x["dataPerms"] = 'public';
        $this->loadCustLoop($GLOBALS["SL"]->REQ, 1);
        $all = RIIPowerScore::select('ps_id')
            ->where('ps_status', 'NOT LIKE', $this->statusIncomplete)
            ->whereNotIn('ps_id', $doneIDs)
            ->get();
        if ($all->isNotEmpty()) {
            foreach ($all as $i => $ps) {
                if ($i < 30) {
                    $this->custReport->loadAllSessData('powerscore', $ps->ps_id);
                    $this->custReport->calcCurrSubScores();
                    $doneIDs[] = $ps->ps_id;
                } else {
                    $hasMore = true;
                }
            }
        }
        if ($hasMore) {
            return '<br /><br />Recalculating... <script type="text/javascript"> '
                . 'setTimeout("window.location=\'?recalc=1' 
                . (($GLOBALS["SL"]->REQ->has('fixLightingErrors')) ? '&fixLightingErrors=1' : '')
                . '&doneIDs=' . implode(',', $doneIDs) . '\'", 1000); </script>'
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
        $msg = '<i class="slGrey">Recalculating ' . (1+$freshDone) . '/'
            . sizeof($GLOBALS["CUST"]->v["fltComb"]) . '...</i>';
        if ($redir == 'report-ajax') {
            if ($nextFlt != '') {
                return view(
                    'vendor.cannabisscore.nodes.490-report-calculations-top-refresh-mid', 
                    [
                        "msg"     => $msg,
                        "nextFlt" => $nextFlt,
                        "psid"    => $this->v["ajax-psid"]
                    ]
                )->render();
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
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('powerscore') 
            . "::" . $this->filterAllPowerScoresPublic() 
            . "->where('ps_time_type', " . $GLOBALS["SL"]->def
                ->getID('PowerScore Submission Type', 'Past') . ")"
            . "->select('ps_id', 'ps_effic_overall', 'ps_effic_facility', 'ps_effic_production', "
            . "'ps_effic_hvac', 'ps_effic_lighting', 'ps_effic_water', 'ps_effic_waste', "
            . "'ps_effic_facility_status', 'ps_effic_production_status', 'ps_effic_hvac_status', "
            . "'ps_effic_lighting_status', 'ps_effic_water_status', 'ps_effic_waste_status')"
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
                    $which = "worse";
                    if ($s2->ps_effic_facility_status == $this->statusComplete) {
                        if ($s->ps_effic_facility <= $s2->ps_effic_facility) {
                            $which = "better";
                        }
                        $efficPercs["Facility"][$which]++;
                    }
                    if ($s2->ps_effic_production_status == $this->statusComplete) {
                        if ($s->ps_effic_production >= $s2->ps_effic_production) {
                            $which = "better";
                        }
                        $efficPercs["Production"][$which]++;
                    }
                    if ($s2->ps_effic_hvac_status == $this->statusComplete) {
                        if ($s->ps_effic_hvac <= $s2->ps_effic_hvac) {
                            $which = "better";
                        }
                        $efficPercs["HVAC"][$which]++;
                    }
                    if ($s2->ps_effic_lightingFacilityStatus == $this->statusComplete) {
                        if ($s->ps_effic_lighting <= $s2->ps_effic_lighting) {
                            $which = "better";
                        }
                        $efficPercs["Lighting"][$which]++;
                    }
                    if ($s2->ps_effic_water > 0 
                        && $s2->ps_effic_water_status == $this->statusComplete) {
                        if ($s->ps_effic_water <= $s2->ps_effic_water) {
                            $which = "better";
                        }
                        $efficPercs["Water"][$which]++;
                    }
                    if ($s2->ps_effic_waste > 0 
                        && $s2->ps_effic_waste_status == $this->statusComplete) {
                        if ($s->ps_effic_waste <= $s2->ps_effic_waste) {
                            $which = "better";
                        }
                        $efficPercs["Waste"][$which]++;
                    }
                }
                $this->v["allRnks"][$s->ps_id] = RIIPSRankings::where('ps_rnk_psid', $s->ps_id)
                    ->where('ps_rnk_filters', $this->searcher->v["urlFlts"])
                    ->first();
                if (!$this->v["allRnks"][$s->ps_id] 
                    || !isset($this->v["allRnks"][$s->ps_id]->ps_rnk_id)) {
                    $this->v["allRnks"][$s->ps_id] = new RIIPSRankings;
                    $this->v["allRnks"][$s->ps_id]->ps_rnk_psid = $s->ps_id;
                    $this->v["allRnks"][$s->ps_id]->ps_rnk_filters = $this->searcher->v["urlFlts"];
                    $this->v["allRnks"][$s->ps_id]->ps_rnk_tot_cnt = $allscores->count();
                }
                $this->v["allRnks"][$s->ps_id]->ps_rnk_overall_avg = $ind = 0;
                foreach ($efficPercs as $type => $percs) {
                    $ind++;
                    $tot = $percs["better"]+$percs["worse"];
                    $this->v["lgtCompetData"]->dataLegend[$ind][3] = $tot;
                    $this->v["allRnks"][$s->ps_id]->{ 'ps_rnk_' . $type } = 100*($percs["better"]/$tot);
                    $this->v["allRnks"][$s->ps_id]->ps_rnk_overall_avg 
                        += $this->v["allRnks"][$s->ps_id]->{ 'ps_rnk_' . $type };
                }
                $this->v["allRnks"][$s->ps_id]->ps_rnk_overall_avg 
                    = $this->v["allRnks"][$s->ps_id]->ps_rnk_overall_avg/6;
                $this->v["allRnks"][$s->ps_id]->save();
            }
            foreach ($allscores as $s) {
                $efficPercs = [
                    "better" => 0,
                    "worse"  => 0
                ];
                foreach ($allscores as $s2) {
                    $which = "worse";
                    if ($this->v["allRnks"][$s->ps_id]->ps_rnk_overall_avg 
                        >= $this->v["allRnks"][$s2->ps_id]->ps_rnk_overall_avg) {
                        $which = "better";
                    }
                    $efficPercs[$which]++;
                }
                $this->v["allRnks"][$s->ps_id]->ps_rnk_overall = 100*($efficPercs["better"]
                    /$this->v["allRnks"][$s->ps_id]->ps_rnk_tot_cnt);
                $this->v["allRnks"][$s->ps_id]->save();
                if ($this->searcher->v["urlFlts"] == '') {
                    $s->ps_effic_overall = $this->v["allRnks"][$s->ps_id]->ps_rnk_overall;
                    $s->save();
                } elseif ($this->searcher->v["urlFlts"] == ('&fltFarm=' . $s->ps_characterize)) {
                    $s->ps_effic_over_similar = $this->v["allRnks"][$s->ps_id]->ps_rnk_overall;
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
        if (isset($this->sessData->dataSets["powerscore"])) {
            $this->v["psid"] = $this->sessData->dataSets["powerscore"][0]->getKey();
            $this->v["hasRefresh"] = (($GLOBALS["SL"]->REQ->has('refresh')) ? '&refresh=1' : '')
                . (($GLOBALS["SL"]->REQ->has('print')) ? '&print=1' : '');
            $GLOBALS["SL"]->loadStates();
            return true;
        }
        return false;
    }
    
    protected function getSimilarStats($ps = NULL)
    {
        if (!$ps 
            && isset($this->sessData->dataSets["powerscore"]) 
            && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
            $ps = $this->sessData->dataSets["powerscore"][0];
        } elseif ($this->coreID > 0) {
            $ps = RIIPowerScore::find($this->coreID);
        }
        if ($ps && isset($ps->ps_id)) {
            return RIIPSRankings::where('ps_rnk_psid', $ps->ps_id)
                ->where('ps_rnk_filters', '&fltFarm=' . $ps->ps_characterize)
                ->first();
        }
        return new RIIPSRankings;
    }
    
}