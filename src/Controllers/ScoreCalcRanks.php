<?php
/**
  * ScoreCalcRanks is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains the processes which report 
  * Sub-Score calculations for transparency.
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
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use App\Models\RIIUserPsPerms;
use RockHopSoft\Survloop\Controllers\Globals\Globals;
use ResourceInnovation\CannabisScore\Controllers\ScoreCalcs;

class ScoreCalcRanks extends ScoreCalcs
{
    protected function calcCurrScoreRanks()
    {
        ini_set('max_execution_time', 180);
//echo '<pre>'; print_r($this->searcher->v); echo '</pre>'; exit;
        if (trim($this->searcher->v["urlFlts"]) == '') {
            $this->v["ranksCache"] = RIIPsRanks::whereNull('ps_rnk_filters')
                ->orWhere('ps_rnk_filters', 'LIKE', '')
                ->first();
        } else {
            $this->v["ranksCache"] = RIIPsRanks::where(
                    'ps_rnk_filters', 
                    $this->searcher->v["urlFlts"]
                )
                ->first();
        }
        if ($this->v["ranksCache"] 
            && isset($this->v["ranksCache"]->ps_rnk_id)
            && $GLOBALS["SL"]->REQ->has('refresh')) {
            $this->v["ranksCache"]->delete();
            $this->v["ranksCache"] = null;
        }
        if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->ps_rnk_id)) {
            $this->v["ranksCache"] = new RIIPsRanks;
            $this->v["ranksCache"]->ps_rnk_filters = $this->searcher->v["urlFlts"];
        }
        $eval = "\$allscores = " . $GLOBALS["SL"]->modelPath('powerscore') . "::" 
            . $this->searcher->filterAllPowerScoresPublic() . "->get();";
        eval($eval);
        $this->v["ranksCache"]->ps_rnk_tot_cnt = $allscores->count();
        $this->initRankCalcs();
        if ($allscores->isNotEmpty()) {
            $this->addValidSubScores($allscores);
            $this->rankValidSubScores($allscores);
            
            // Now store calculated ranks for individual scores...
            $currFlt = trim($this->searcher->v["urlFlts"]);
            foreach ($allscores as $i => $ps) {
                if (in_array($currFlt, [ '', '&fltFarm=0' ])) {
                    $ps->ps_effic_overall = $this->v["rank"][$ps->ps_id]["over"];
                    $ps->save();
                } elseif ($currFlt == '&fltFarm=' . $ps->ps_characterize) {
                    $ps->ps_effic_over_similar = $this->v["rank"][$ps->ps_id]["over"];
                    $ps->ps_effic_cat_energy   = $this->v["rank"][$ps->ps_id]["oenr"];
                    $ps->ps_effic_cat_water    = $this->v["rank"][$ps->ps_id]["owtr"];
                    $ps->ps_effic_cat_waste    = $this->v["rank"][$ps->ps_id]["owst"];
                    $ps->save();
                }
                $this->saveOneScoreRank($ps);
            }
        }
        
        // Now store listed raw sub-score values for filter...
        $this->v["ranksCache"]->ps_rnk_tot_cnt     = $allscores->count();
        $this->v["ranksCache"]->ps_rnk_overall_avg = implode(',', $this->v["rankList"]["oraw"]);
        $this->v["ranksCache"]->ps_rnk_cat_energy  = implode(',', $this->v["rankList"]["oenr"]);
        $this->v["ranksCache"]->ps_rnk_cat_water   = implode(',', $this->v["rankList"]["owtr"]);
        $this->v["ranksCache"]->ps_rnk_cat_waste   = implode(',', $this->v["rankList"]["owst"]);
        $this->v["ranksCache"]->ps_rnk_facility    = implode(',', $this->v["rankList"]["faci"]);
        $this->v["ranksCache"]->ps_rnk_fac_non     = implode(',', $this->v["rankList"]["facN"]);
        $this->v["ranksCache"]->ps_rnk_fac_all     = implode(',', $this->v["rankList"]["facA"]);
        $this->v["ranksCache"]->ps_rnk_production  = implode(',', $this->v["rankList"]["prod"]);
        $this->v["ranksCache"]->ps_rnk_prod_non    = implode(',', $this->v["rankList"]["proN"]);
        $this->v["ranksCache"]->ps_rnk_prod_all    = implode(',', $this->v["rankList"]["proA"]);
        $this->v["ranksCache"]->ps_rnk_emis        = implode(',', $this->v["rankList"]["emis"]);
        $this->v["ranksCache"]->ps_rnk_emis_prod   = implode(',', $this->v["rankList"]["emiP"]);
        $this->v["ranksCache"]->ps_rnk_lighting    = implode(',', $this->v["rankList"]["ligh"]);
        $this->v["ranksCache"]->ps_rnk_hvac        = implode(',', $this->v["rankList"]["hvac"]);
        $this->v["ranksCache"]->ps_rnk_water       = implode(',', $this->v["rankList"]["watr"]);
        $this->v["ranksCache"]->ps_rnk_water_prod  = implode(',', $this->v["rankList"]["watP"]);
        $this->v["ranksCache"]->ps_rnk_waste       = implode(',', $this->v["rankList"]["wste"]);
        $this->v["ranksCache"]->ps_rnk_waste_prod  = implode(',', $this->v["rankList"]["wstP"]);
        if ($this->v["ranksCache"]->ps_rnk_tot_cnt > 0) {
            $this->v["ranksCache"]->ps_rnk_avg_sqft_kwh 
                = $this->v["rankAvg"]["btu"]/$allscores->count();
            $this->v["ranksCache"]->ps_rnk_avg_sqft_grm 
                = $this->v["rankAvg"]["g"]/$allscores->count();
        }
        $this->v["ranksCache"]->save();
//echo '<h2>calcCurrScoreRanks(</h2><pre>'; print_r($this->v["ranksCache"]); print_r($this->v["rankList"]); echo '</pre>';
        return $this->v["ranksCache"];
    }

    protected function initRankCalcs()
    {
        $this->v["rank"] = [];
        $this->v["rankList"] = [
            "over" => [], 
            "oraw" => [], 
            "oenr" => [], 
            "faci" => [], 
            "facN" => [], 
            "facA" => [], 
            "prod" => [], 
            "proN" => [], 
            "proA" => [], 
            "emis" => [], 
            "emiP" => [], 
            "ligh" => [], 
            "hvac" => [], 
            "owtr" => [], 
            "watr" => [], 
            "watP" => [], 
            "owst" => [], 
            "wste" => [], 
            "wstP" => []
        ];
        $this->v["rankAvg"] = [
            "btu" => 0,
            "g"   => 0
        ];
        return true;
    }
    
    protected function addValidSubScores($allscores)
    {
        foreach ($allscores as $i => $ps) {
            $sqft = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                ->where('ps_area_type', $this->v["areaTypes"]["Flower"])
                ->select('ps_area_size')
                ->first();
            if ($sqft && isset($sqft->ps_area_size) && $sqft->ps_area_size > 0) {
                $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc);
                $this->v["rankAvg"]["btu"] += $btus/$sqft->ps_area_size;
                $this->v["rankAvg"]["g"]   += $ps->ps_grams/$sqft->ps_area_size;
            }
            if ($ps->ps_effic_facility > 0 
                && $ps->ps_effic_facility_status == $this->statusComplete) {
                $this->v["rankList"]["faci"][] = $ps->ps_effic_facility;
            }
            if ($ps->ps_effic_fac_non > 0 
                && $ps->ps_effic_fac_non_status == $this->statusComplete) {
                $this->v["rankList"]["facN"][] = $ps->ps_effic_fac_non;
            }
            if ($ps->ps_effic_fac_all > 0 
                && $ps->ps_effic_fac_all_status == $this->statusComplete) {
                $this->v["rankList"]["facA"][] = $ps->ps_effic_fac_all;
            }
            if ($ps->ps_effic_production > 0 
                && $ps->ps_effic_production_status == $this->statusComplete) {
                $this->v["rankList"]["prod"][] = $ps->ps_effic_production;
            }
            if ($ps->ps_effic_prod_non > 0 
                && $ps->ps_effic_prod_non_status == $this->statusComplete) {
                $this->v["rankList"]["proN"][] = $ps->ps_effic_prod_non;
            }
            if ($ps->ps_effic_prod_all > 0 
                && $ps->ps_effic_prod_all_status == $this->statusComplete) {
                $this->v["rankList"]["proA"][] = $ps->ps_effic_prod_all;
            }
            if ($ps->ps_effic_emis > 0 
                && $ps->ps_effic_emis_status == $this->statusComplete) {
                $this->v["rankList"]["emis"][] = $ps->ps_effic_emis;
            }
            if ($ps->ps_effic_emis_prod > 0 
                && $ps->ps_effic_emis_prod_status == $this->statusComplete) {
                $this->v["rankList"]["emiP"][] = $ps->ps_effic_emis_prod;
            }
            if ($ps->ps_effic_lighting > 0 
                && $ps->ps_effic_lighting_status == $this->statusComplete) {
                if ($ps->ps_effic_lighting > 0.00001) {
                    $this->v["rankList"]["ligh"][] = $ps->ps_effic_lighting;
                } else {
                    $this->v["rankList"]["ligh"][] = 0;
                }
            }
            if ($ps->ps_effic_hvac > 0 
                && $ps->ps_effic_hvac_status == $this->statusComplete) {
                if ($ps->ps_effic_hvac > 0.00001) {
                    $this->v["rankList"]["hvac"][] = $ps->ps_effic_hvac;
                } else {
                    $this->v["rankList"]["hvac"][] = 0;
                }
            }
            if ($ps->ps_effic_water > 0 
                && $ps->ps_effic_water_status == $this->statusComplete) {
                $this->v["rankList"]["watr"][] = $ps->ps_effic_water;
            }
            if ($ps->ps_effic_water_prod > 0 
                && $ps->ps_effic_water_prod_status == $this->statusComplete) {
                $this->v["rankList"]["watP"][] = $ps->ps_effic_water_prod;
            }
            if ($ps->ps_effic_waste > 0 
                && $ps->ps_effic_waste_status == $this->statusComplete) {
                $this->v["rankList"]["wste"][] = $ps->ps_effic_waste;
            }
            if ($ps->ps_effic_waste_prod > 0 
                && $ps->ps_effic_waste_prod_status == $this->statusComplete) {
                $this->v["rankList"]["wstP"][] = $ps->ps_effic_waste_prod;
            }
        }
//echo '<h2>addValidSubScores(</h2><pre>'; print_r($this->v["rankList"]); echo '</pre>';
        sort($this->v["rankList"]["faci"], SORT_NUMERIC);
        sort($this->v["rankList"]["facN"], SORT_NUMERIC);
        sort($this->v["rankList"]["facA"], SORT_NUMERIC);
        sort($this->v["rankList"]["prod"], SORT_NUMERIC);
        sort($this->v["rankList"]["proN"], SORT_NUMERIC);
        sort($this->v["rankList"]["proA"], SORT_NUMERIC);
        sort($this->v["rankList"]["emis"], SORT_NUMERIC);
        sort($this->v["rankList"]["emiP"], SORT_NUMERIC);
        sort($this->v["rankList"]["ligh"], SORT_NUMERIC);
        sort($this->v["rankList"]["hvac"], SORT_NUMERIC);
        sort($this->v["rankList"]["watr"], SORT_NUMERIC);
        sort($this->v["rankList"]["watP"], SORT_NUMERIC);
        sort($this->v["rankList"]["wste"], SORT_NUMERIC);
        sort($this->v["rankList"]["wstP"], SORT_NUMERIC);
        return true;
    }
    
    protected function rankValidSubScores($allscores)
    {
        $categories = [ 'oenr', 'owtr', 'owst' ];
        foreach ($allscores as $i => $ps) {
            // First, calculate KPI rankings and record category raw scores
            $this->rankOneValidSubScore($ps);
            // Add category raw scores to ranking lists
            foreach ($categories as $cat) {
                if ($this->v["rank"][$ps->ps_id][$cat] > 0) {
                    $this->v["rankList"][$cat][] = $this->v["rank"][$ps->ps_id][$cat];
                }
            }
        }
        // Sort category raw scores for ranking lists
        foreach ($categories as $cat) {
            sort($this->v["rankList"][$cat], SORT_NUMERIC);
        }
        foreach ($allscores as $i => $ps) {
            /*
            // Rank category raw scores into full 1-100% percentile range
            foreach ($categories as $cat) {
                $this->v["rank"][$ps->ps_id][$cat] = $GLOBALS["SL"]->getArrPercentile(
                    $this->v["rankList"][$cat], 
                    $this->v["rank"][$ps->ps_id][$cat]
                );
            }
            */
            // Merge category percentiles into overall raw score
            $this->v["rank"][$ps->ps_id]["oraw"] = $this->rankOverallRawScore($ps);
            $this->v["rankList"]["oraw"][] = $this->v["rank"][$ps->ps_id]["oraw"];
        }
//echo '<pre>'; print_r($this->v["rank"][47496513]); echo '</pre>'; exit;
//echo '<pre>'; print_r($this->v["rank"][47496513]); print_r($this->v["rankList"]); echo '</pre>'; exit;
        // Rank overall raw scores into full 1-100% percentile range
        sort($this->v["rankList"]["oraw"], SORT_NUMERIC);
        foreach ($allscores as $i => $ps) {
            $this->v["rank"][$ps->ps_id]["over"] = $GLOBALS["SL"]->getArrPercentile(
                $this->v["rankList"]["oraw"], 
                $this->v["rank"][$ps->ps_id]["oraw"]
            );
        }
        return true;
    }
    
    protected function rankOverallRawScore($ps)
    {
        return $this->calcOverallRawScore(
            $this->v["rank"][$ps->ps_id]["oenr"],
            $this->v["rank"][$ps->ps_id]["owtr"],
            $this->v["rank"][$ps->ps_id]["owst"]
        );
    }
    
    protected function calcOverallRawScore($catEnr, $catWtr, $catWst)
    {
        $raw = 0;
        if ($catEnr > 0 && $catWtr > 0 && $catWst > 0) {
            $raw = ($catEnr*0.5)+($catWtr*0.25)+($catWst*0.25);
        } elseif ($catWtr == 0 && $catWst == 0) {
            $raw = $catEnr;
        } elseif ($catEnr == 0) {
            if ($catWtr > 0 && $catWst > 0) {
                $raw = ($catWtr*0.5)+($catWst*0.5);
            } elseif ($catWtr > 0) {
                $raw = $catWtr;
            } else {
                $raw = $catWst;
            }
        } elseif ($catWtr > 0) {
            $raw = ($catEnr*0.666666667)+($catWtr*0.3333333333);
        } elseif ($catWst > 0) {
            $raw = ($catEnr*0.666666667)+($catWst*0.3333333333);
        }
        return $raw;
    }
    
    protected function basicEfficFlds()
    {
        return [
            [ 'fac_all',    'facA' ], 
            [ 'facility',   'faci' ], 
            [ 'fac_non',    'facN' ], 
            [ 'prod_all',   'proA' ], 
            [ 'production', 'prod' ], 
            [ 'prod_non',   'proN' ], 
            [ 'emis',       'emis' ], 
            [ 'emis_prod',  'emiP' ], 
            [ 'lighting',   'ligh' ], 
            [ 'hvac',       'hvac' ], 
            [ 'water',      'watr' ], 
            [ 'water_prod', 'watP' ], 
            [ 'waste',      'wste' ], 
            [ 'waste_prod', 'wstP' ]
        ];
    }
    
    protected function rankOneValidSubScore($ps)
    {
        $efficsOver = $this->basicEfficFlds();
        $this->v["rank"][$ps->ps_id] = [];
        $this->v["rank"][$ps->ps_id]["over"] 
            = $this->v["rank"][$ps->ps_id]["oraw"] 
            = $this->v["rank"][$ps->ps_id]["oenr"] 
            = $this->v["rank"][$ps->ps_id]["owtr"] 
            = $this->v["rank"][$ps->ps_id]["owst"] 
            = $cntOenr
            = $cntOwtr
            = $cntOwst
            = 0;
        foreach ($efficsOver as $effic) {
            $rank = 0;
            if (isset($ps->{ 'ps_effic_' . $effic[0] })
                && $ps->{ 'ps_effic_' . $effic[0] } > 0) {
                $val = $ps->{ 'ps_effic_' . $effic[0] };
                if ($val < 0.000001) {
                    $val = 0;
                }
                $isGolf = (strpos($effic[0], 'prod') === false);
                $list = $this->v["rankList"][$effic[1]];
                $rank = $GLOBALS["SL"]->getArrPercentile($list, $val, $isGolf);
            }
            $this->v["rank"][$ps->ps_id][$effic[1]] = $rank;
        }
        foreach ($efficsOver as $effic) {
            if (isset($ps->{ 'ps_effic_' . $effic[0] })
                && $ps->{ 'ps_effic_' . $effic[0] } > 0
                && $this->v["rank"][$ps->ps_id][$effic[1]] > 0) {
                if (in_array($effic[1], ['faci', 'facN', 'prod', 'proN', 'emis', 'emiP'])) {
                    $this->v["rank"][$ps->ps_id]["oenr"] 
                        += $this->v["rank"][$ps->ps_id][$effic[1]];
                    $cntOenr++;
                } elseif (in_array($effic[1], ['watr', 'watP'])) {
                    $this->v["rank"][$ps->ps_id]["owtr"] 
                        += $this->v["rank"][$ps->ps_id][$effic[1]];
                    $cntOwtr++;
                } elseif (in_array($effic[1], ['wste', 'wstP'])) {
                    $this->v["rank"][$ps->ps_id]["owst"] 
                        += $this->v["rank"][$ps->ps_id][$effic[1]];
                    $cntOwst++;
                }
            }
        }
        if ($cntOenr > 0) {
            $this->v["rank"][$ps->ps_id]["oenr"] 
                = $this->v["rank"][$ps->ps_id]["oenr"]/$cntOenr;
            if (isset($ps->ps_dlc_bonus)) {
// Reveal when LIVE:    $this->v["rank"][$ps->ps_id]["oenr"] += $ps->ps_dlc_bonus;
            }
        }
        if ($cntOwtr > 0) {
            $this->v["rank"][$ps->ps_id]["owtr"] 
                = $this->v["rank"][$ps->ps_id]["owtr"]/$cntOwtr;
        }
        if ($cntOwst > 0) {
            $this->v["rank"][$ps->ps_id]["owst"] 
                = $this->v["rank"][$ps->ps_id]["owst"]/$cntOwst;
        }
//if ($ps->ps_id == 47496890) { echo '<pre>'; print_r($this->v["rank"][$ps->ps_id]); echo '</pre>'; exit; }
        return true;
    }
    
    protected function saveOneScoreRank($ps)
    {
        $currFlt = '';
        if (isset($this->searcher->v["urlFlts"])) {
            $currFlt = trim($this->searcher->v["urlFlts"]);
        }
        $tmp = null;
        if (trim($this->searcher->v["urlFlts"]) == '') {
            $tmp = RIIPsRankings::where('ps_rnk_psid', $ps->ps_id)
                ->where(function($query) {
                    $query->where('ps_rnk_filters', 'LIKE', '')
                          ->whereNull('ps_rnk_filters');
                })
                ->first();
        } else {
            $tmp = RIIPsRankings::where('ps_rnk_psid', $ps->ps_id)
                ->where('ps_rnk_filters', 'LIKE', $currFlt)
                ->first();
        }
        if (!$tmp) {
            $tmp = new RIIPsRankings;
            $tmp->ps_rnk_psid    = $ps->ps_id;
            $tmp->ps_rnk_filters = $currFlt;
            $tmp->save();
        }
        $tmp->ps_rnk_tot_cnt        = $this->v["ranksCache"]->ps_rnk_tot_cnt;
        $tmp->ps_rnk_overall        = $this->v["rank"][$ps->ps_id]["over"];
        $tmp->ps_rnk_overall_avg    = $this->v["rank"][$ps->ps_id]["oraw"];
        $tmp->ps_rnk_cat_energy     = $this->v["rank"][$ps->ps_id]["oenr"];
        $tmp->ps_rnk_cat_water      = $this->v["rank"][$ps->ps_id]["owtr"];
        $tmp->ps_rnk_cat_waste      = $this->v["rank"][$ps->ps_id]["owst"];
        $tmp->ps_rnk_facility       = $this->v["rank"][$ps->ps_id]["faci"];
        $tmp->ps_rnk_fac_non        = $this->v["rank"][$ps->ps_id]["facN"];
        $tmp->ps_rnk_fac_all        = $this->v["rank"][$ps->ps_id]["facA"];
        $tmp->ps_rnk_production     = $this->v["rank"][$ps->ps_id]["prod"];
        $tmp->ps_rnk_prod_non       = $this->v["rank"][$ps->ps_id]["proN"];
        $tmp->ps_rnk_prod_all       = $this->v["rank"][$ps->ps_id]["proA"];
        $tmp->ps_rnk_emis           = $this->v["rank"][$ps->ps_id]["emis"];
        $tmp->ps_rnk_emis_prod      = $this->v["rank"][$ps->ps_id]["emiP"];
        $tmp->ps_rnk_lighting       = $this->v["rank"][$ps->ps_id]["ligh"];
        $tmp->ps_rnk_hvac           = $this->v["rank"][$ps->ps_id]["hvac"];
        $tmp->ps_rnk_water          = $this->v["rank"][$ps->ps_id]["watr"];
        $tmp->ps_rnk_water_prod     = $this->v["rank"][$ps->ps_id]["watP"];
        $tmp->ps_rnk_waste          = $this->v["rank"][$ps->ps_id]["wste"];
        $tmp->ps_rnk_waste_prod     = $this->v["rank"][$ps->ps_id]["wstP"];
        $tmp->ps_rnk_cat_energy_cnt = sizeof($this->v["rankList"]["oenr"]);
        $tmp->ps_rnk_cat_water_cnt  = sizeof($this->v["rankList"]["owtr"]);
        $tmp->ps_rnk_cat_waste_cnt  = sizeof($this->v["rankList"]["owst"]);
        $tmp->ps_rnk_facility_cnt   = sizeof($this->v["rankList"]["faci"]);
        $tmp->ps_rnk_fac_non_cnt    = sizeof($this->v["rankList"]["facN"]);
        $tmp->ps_rnk_fac_all_cnt    = sizeof($this->v["rankList"]["facA"]);
        $tmp->ps_rnk_production_cnt = sizeof($this->v["rankList"]["prod"]);
        $tmp->ps_rnk_prod_non_cnt   = sizeof($this->v["rankList"]["proN"]);
        $tmp->ps_rnk_prod_all_cnt   = sizeof($this->v["rankList"]["proA"]);
        $tmp->ps_rnk_emis_cnt       = sizeof($this->v["rankList"]["emis"]);
        $tmp->ps_rnk_emis_prod_cnt  = sizeof($this->v["rankList"]["emiP"]);
        $tmp->ps_rnk_lighting_cnt   = sizeof($this->v["rankList"]["ligh"]);
        $tmp->ps_rnk_hvac_cnt       = sizeof($this->v["rankList"]["hvac"]);
        $tmp->ps_rnk_water_cnt      = sizeof($this->v["rankList"]["watr"]);
        $tmp->ps_rnk_water_prod_cnt = sizeof($this->v["rankList"]["watP"]);
        $tmp->ps_rnk_waste_cnt      = sizeof($this->v["rankList"]["wste"]);
        $tmp->ps_rnk_waste_prod_cnt = sizeof($this->v["rankList"]["wstP"]);
        $tmp->save();
        return $tmp;
    }
    
    protected function calcFutureYields(&$ps)
    {
        $this->initSearcher();
        $this->searcher->loadCurrScoreFltParams($this->sessData->dataSets);
        $matches = [
            "flt" => [], 
            "btu" => 0, 
            "grm" => 0 
        ];
        if (sizeof($this->searcher->v["futureFlts"]) > 0) {
            foreach ($this->searcher->v["futureFlts"] as $flt) {
                $chk = RIIPsRanks::where('ps_rnk_filters', 'LIKE', $flt)
                    ->where('ps_rnk_tot_cnt', '>', 3)
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $rnk) {
                        $matches["flt"][] = $rnk->ps_rnk_filters;
                        $matches["btu"]  += $rnk->ps_rnk_avg_sqft_kwh;
                        $matches["grm"]  += $rnk->ps_rnk_avg_sqft_grm;
                    }
                }
            }
        }
        if (sizeof($matches["flt"]) > 0) {
            $ps->ps_effic_facility = $matches["btu"]/sizeof($matches["flt"]);
            $ps->ps_kwh = $ps->ps_effic_facility*$this->v["totFlwrSqFt"];
            $ps->ps_grams = ($matches["grm"]/sizeof($matches["flt"]))
                *$this->v["totFlwrSqFt"];
        }
        return $ps;
    }
    
    protected function recalc2AllSubScores()
    {
        $hasMore = false;
        $doneIDs = [];
        if ($GLOBALS["SL"]->REQ->has('doneIDs') 
            && trim($GLOBALS["SL"]->REQ->get('doneIDs')) != '') {
            $dIDs = $GLOBALS["SL"]->REQ->get('doneIDs');
            $doneIDs = $GLOBALS["SL"]->mexplode(',', $dIDs);
        }
        $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, $this->dbID, 1);
        $GLOBALS["SL"]->x["pageView"] 
            = $GLOBALS["SL"]->x["dataPerms"] 
            = 'public';
        $this->loadCustLoop($GLOBALS["SL"]->REQ, 1);
        $all = RIIPowerscore::select('ps_id')
            ->where('ps_status', 'NOT LIKE', $this->statusIncomplete)
            ->whereNotIn('ps_id', $doneIDs)
            ->get();
        if ($all->isNotEmpty()) {
            foreach ($all as $i => $ps) {
                if ($i < 20) {
                    $this->custReport->loadAllSessData('powerscore', $ps->ps_id);
                    $this->custReport->calcCurrSubScores();
                    $doneIDs[] = $ps->ps_id;
                } else {
                    $hasMore = true;
                }
            }
        }
        if ($hasMore) {
            return '<br /><br /><div class="slCard nodeWrap">'
                . '<h3>Recalculating All Scores...</h3> '
                . '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'?recalc=1' 
                . (($GLOBALS["SL"]->REQ->has('fixLightingErrors')) 
                    ? '&fixLightingErrors=1' : '')
                . '&doneIDs=' . implode(',', $doneIDs) . '\'", 1000); </script>'
                . '<br /><br />' . implode(', ', $doneIDs) 
                . '<style> #nodeSubBtns { display: none; } </style></div>';
        }
        return '<br /><br /><div class="slCard nodeWrap">'
            . '<h3>All Scores Recalculated!</h3>'
            . '<a href="/dash/powerscore-software-troubleshooting" '
            . 'class="btn btn-primary btn-lg">Back</a><br /><br />'
            . implode(', ', $doneIDs) 
            . '<style> #nodeSubBtns { display: none; } </style></div>';
    }
    
    protected function calcAllScoreRanks($redir = 'all')
    {
        $this->searchResultsXtra();
        $GLOBALS["CUST"]->chkScoreFiltCombs();
        $nextFlt = '';
        $freshDone = $cnt = -1;
        $curr = '';
        if ($GLOBALS["SL"]->REQ->has('currFlt')) {
            $curr = $GLOBALS["SL"]->REQ->get('currFlt');
        } else {
            $this->searcher->searchFiltsURLXtra();
            $this->calcCurrScoreRanks();
            $this->calcArchiveRanks();
        }
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
                    if ($flt == 'fltFarm') {
                        $this->calcArchiveRanks($opt);
                    }
                }
            }
        }
        $msg = '<div class="slCard nodeWrap"><h3>'
            . 'Hang tight, we are calculating your KPIs... ' . (1+$freshDone) 
            . '/' . sizeof($GLOBALS["CUST"]->v["fltComb"]) . '...</h3>';
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
            return $msg . '</div><script type="text/javascript"> '
                . 'setTimeout("window.location=\'/calculated/read-'
                . $this->v["ajax-psid"] . '\'", 1000); </script>';
        }
        if ($nextFlt != '') {
            return $msg . '</div><script type="text/javascript"> '
                . 'setTimeout("window.location=\'/dash/powerscore-software'
                . '-troubleshooting?refresh=1&currFlt=' . $nextFlt 
                . '\'", 1000); </script>';
        }
        return '<br /><br /><div class="slCard nodeWrap">'
            . '<h3>All Rankings Recalculated!</h3>'
            . '<a href="/dash/powerscore-software-troubleshooting" '
            . 'class="btn btn-primary btn-lg">Back</a><br />'
            . '<style> #nodeSubBtns { display: none; } </style></div>';
    }
    
    protected function getSimilarStats($ps = NULL)
    {
        if (!$ps 
            && isset($this->sessData->dataSets["powerscore"]) 
            && sizeof($this->sessData->dataSets["powerscore"]) > 0) {
            $ps = $this->sessData->dataSets["powerscore"][0];
        } elseif ($this->coreID > 0) {
            $ps = RIIPowerscore::find($this->coreID);
        }
        if ($ps && isset($ps->ps_id)) {
            return RIIPsRankings::where('ps_rnk_psid', $ps->ps_id)
                ->where('ps_rnk_filters', '&fltFarm=' . $ps->ps_characterize)
                ->first();
        }
        return new RIIPsRankings;
    }

    protected function calcArchiveRanks($farmType = 0)
    {
        $arch = null;
        if ($farmType > 0) {
            $arch = RIIPowerscore::where('ps_status', $this->statusArchive)
                ->where('ps_characterize', $farmType)
                ->get();
        } else {
            $arch = RIIPowerscore::where('ps_status', $this->statusArchive)
                ->get();
        }
        if ($arch->isNotEmpty()) {
            $this->addValidSubScores($arch);
            $this->rankValidSubScores($arch);
            foreach ($arch as $ps) {
                $this->saveOneScoreRank($ps);
            }
        }
        return true;
    }

    public function chkPowerScoreRanked()
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];

            if (!isset($GLOBALS["SL"]->x["psCompany"][$ps->ps_id])) {
                $GLOBALS["SL"]->x["psCompany"][$ps->ps_id] = '';
                $chk = DB::table('rii_user_ps_perms')
                    ->join('rii_user_facilities', function ($join) {
                        $join->on('rii_user_ps_perms.usr_perm_facility_id', 
                            '=', 'rii_user_facilities.usr_fac_id');
                    })
                    ->where('rii_user_ps_perms.usr_perm_psid', $ps->ps_id)
                    ->first();
                if ($chk 
                    && isset($chk->usr_fac_name) 
                    && trim($chk->usr_fac_name) != '') {
                    $GLOBALS["SL"]->x["psCompany"][$ps->ps_id] = 'Facility: ' 
                        . $chk->usr_fac_name;
                } else {
                    $chk = DB::table('rii_user_ps_perms')
                        ->join('rii_user_companies', function ($join) {
                            $join->on('rii_user_ps_perms.usr_perm_company_id', 
                                '=', 'rii_user_companies.usr_com_id');
                        })
                        ->where('rii_user_ps_perms.usr_perm_psid', $ps->ps_id)
                        ->first();
                    if ($chk 
                        && isset($chk->usr_com_name) 
                        && trim($chk->usr_com_name) != '') {
                        $GLOBALS["SL"]->x["psCompany"][$ps->ps_id] = $chk->usr_com_name;
                    }
                }
            }


        /*
            if ((!isset($ps->ps_effic_over_similar)
                    || $ps->ps_effic_over_similar <= 0)
                && isset($ps->ps_kwh)
                && intVal($ps->ps_kwh) > 0) {
                $flt = '&fltFarm=' . $ps->ps_characterize;
                $ranks = RIIPsRanks::where('ps_rnk_filters', $flt)
                    ->first();
                if ($ranks && isset($ranks->ps_rnk_overall_avg)) {
                    $this->sessData->dataSets["powerscore"][0]->ps_effic_over_similar
                        = $GLOBALS["SL"]->getArrPercentileStr($ranks->ps_rnk_overall_avg);
                }

                $ranks = RIIPsRanks::where('ps_rnk_filters', '&fltFarm=0')
                    ->first();

            }
        */
        }
        return true;
    }

}