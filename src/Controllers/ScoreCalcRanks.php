<?php
/**
  * ScoreCalcRanks is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the processes which report 
  * Sub-Score calculations for transparency.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use SurvLoop\Controllers\Globals\Globals;
use CannabisScore\Controllers\ScoreCalcs;

class ScoreCalcRanks extends ScoreCalcs
{
    protected function calcCurrScoreRanks()
    {
        $this->v["ranksCache"] = RIIPsRanks::where(
                'ps_rnk_filters', $this->searcher->v["urlFlts"])
            ->first();
        if (!$this->v["ranksCache"] || !isset($this->v["ranksCache"]->ps_rnk_id)) {
            $this->v["ranksCache"] = new RIIPsRanks;
            $this->v["ranksCache"]->ps_rnk_filters = $this->searcher->v["urlFlts"];
        } /* elseif (!$GLOBALS["SL"]->REQ->has('refresh') && !$GLOBALS["SL"]->REQ->has('recalc')) {
            return $this->v["ranksCache"];
        } */
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
            $allFilts = [ '', '&fltFarm=0' ];
            foreach ($allscores as $i => $ps) {
                if (in_array($currFlt, $allFilts)) {
                    $ps->ps_effic_overall = $this->v["rank"][$ps->ps_id]["over"];
                    $ps->save();
                }
                if ($currFlt == '&fltFarm=' . $ps->ps_characterize) {
                    $ps->ps_effic_over_similar = $this->v["rank"][$ps->ps_id]["over"];
                    $ps->save();
                }
                $this->saveOneScoreRank($ps);
            }
        }
        
        // Now store listed raw sub-score values for filter...
        $this->v["ranksCache"]->ps_rnk_tot_cnt     = $allscores->count();
        $this->v["ranksCache"]->ps_rnk_overall_avg 
            = implode(',', $this->v["rankList"]["oraw"]);
        $this->v["ranksCache"]->ps_rnk_facility
            = implode(',', $this->v["rankList"]["faci"]);
        $this->v["ranksCache"]->ps_rnk_production  
            = implode(',', $this->v["rankList"]["prod"]);
        $this->v["ranksCache"]->ps_rnk_lighting
            = implode(',', $this->v["rankList"]["ligh"]);
        $this->v["ranksCache"]->ps_rnk_hvac
            = implode(',', $this->v["rankList"]["hvac"]);
        $this->v["ranksCache"]->ps_rnk_water
            = implode(',', $this->v["rankList"]["watr"]);
        $this->v["ranksCache"]->ps_rnk_waste
            = implode(',', $this->v["rankList"]["wste"]);
        if ($this->v["ranksCache"]->ps_rnk_tot_cnt > 0) {
            $this->v["ranksCache"]->ps_rnk_avg_sqft_kwh 
                = $this->v["rankAvg"]["btu"]/$allscores->count();
            $this->v["ranksCache"]->ps_rnk_avg_sqft_grm 
                = $this->v["rankAvg"]["g"]/$allscores->count();
        }
        $this->v["ranksCache"]->save();
        return $this->v["ranksCache"];
    }

    protected function initRankCalcs()
    {
        $this->v["rank"] = [];
        $this->v["rankList"] = [
            "over" => [], 
            "oraw" => [], 
            "faci" => [], 
            "prod" => [], 
            "ligh" => [], 
            "hvac" => [], 
            "watr" => [], 
            "wste" => []
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
            if ($sqft && isset($sqft->ps_area_size)) {
                $btus = $GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc);
                $this->v["rankAvg"]["btu"] += $btus/$sqft->ps_area_size;
                $this->v["rankAvg"]["g"]   += $ps->ps_grams/$sqft->ps_area_size;
            }
            if ($ps->ps_effic_facility > 0 
                && $ps->ps_effic_facility_status == $this->statusComplete) {
                $this->v["rankList"]["faci"][] = $ps->ps_effic_facility;
            }
            if ($ps->ps_effic_production > 0 
                && $ps->ps_effic_production_status == $this->statusComplete) {
                $this->v["rankList"]["prod"][] = $ps->ps_effic_production;
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
            if ($ps->ps_effic_waste > 0 
                && $ps->ps_effic_waste_status == $this->statusComplete) {
                $this->v["rankList"]["wste"][] = $ps->ps_effic_waste;
            }
        }
        sort($this->v["rankList"]["faci"], SORT_NUMERIC);
        sort($this->v["rankList"]["prod"], SORT_NUMERIC);
        sort($this->v["rankList"]["ligh"], SORT_NUMERIC);
        sort($this->v["rankList"]["hvac"], SORT_NUMERIC);
        sort($this->v["rankList"]["watr"], SORT_NUMERIC);
        sort($this->v["rankList"]["wste"], SORT_NUMERIC);
        return true;
    }
    
    protected function rankValidSubScores($allscores)
    {
        foreach ($allscores as $i => $ps) {
            $this->rankOneValidSubScore($ps);
            $this->v["rankList"]["oraw"][] = $this->v["rank"][$ps->ps_id]["oraw"];
        }

        sort($this->v["rankList"]["oraw"], SORT_NUMERIC);
        foreach ($allscores as $i => $ps) {
            $this->v["rank"][$ps->ps_id]["over"] = $GLOBALS["SL"]->getArrPercentile(
                $this->v["rankList"]["oraw"], 
                $this->v["rank"][$ps->ps_id]["oraw"]
            );
        }
        return true;
    }
    
    protected function rankOneValidSubScore($ps)
    {
        $this->v["rank"][$ps->ps_id] = [
            "over" => 0,
            "oraw" => 0,
            "faci" => 0,
            "prod" => 0,
            "ligh" => 0,
            "hvac" => 0,
            "watr" => 0,
            "wste" => 0
        ];
        $efficLgt = $ps->ps_effic_lighting;
        if ($efficLgt < 0.00001) {
            $efficLgt = 0;
        }
        $efficHvac = $ps->ps_effic_hvac;
        if ($efficHvac < 0.00001) {
            $efficHvac = 0;
        }
        $this->v["rank"][$ps->ps_id]["faci"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["faci"], 
            $ps->ps_effic_facility,
            true
        );
        $this->v["rank"][$ps->ps_id]["prod"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["prod"], 
            $ps->ps_effic_production
        );
        $this->v["rank"][$ps->ps_id]["ligh"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["ligh"], 
            $efficLgt,
            true
        );
        $this->v["rank"][$ps->ps_id]["hvac"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["hvac"], 
            $efficHvac,
            true
        );
        $this->v["rank"][$ps->ps_id]["watr"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["watr"], 
            $ps->ps_effic_water,
            true
        );
        $this->v["rank"][$ps->ps_id]["wste"] = $GLOBALS["SL"]->getArrPercentile(
            $this->v["rankList"]["wste"], 
            $ps->ps_effic_waste,
            true
        );
        $this->v["rank"][$ps->ps_id]["oraw"] = ($this->v["rank"][$ps->ps_id]["faci"]
            +$this->v["rank"][$ps->ps_id]["prod"]
            +$this->v["rank"][$ps->ps_id]["ligh"]
            +$this->v["rank"][$ps->ps_id]["hvac"])/4; 
            // +$this->v["rank"][$ps->ps_id]["watr"]
            // +$this->v["rank"][$ps->ps_id]["wste"]
        return true;
    }
    
    protected function saveOneScoreRank($ps)
    {
        $currFlt = '';
        if (isset($this->searcher->v["urlFlts"])) {
            $currFlt = trim($this->searcher->v["urlFlts"]);
        }
        $tmp = RIIPsRankings::where('ps_rnk_psid', $ps->ps_id)
            ->where('ps_rnk_filters', $currFlt)
            ->first();
        if (!$tmp) {
            $tmp = new RIIPsRankings;
            $tmp->ps_rnk_psid    = $ps->ps_id;
            $tmp->ps_rnk_filters = $currFlt;
            $tmp->save();
        }
        $tmp->ps_rnk_tot_cnt        = $this->v["ranksCache"]->ps_rnk_tot_cnt;
        $tmp->ps_rnk_overall        = $this->v["rank"][$ps->ps_id]["over"];
        $tmp->ps_rnk_overall_avg    = $this->v["rank"][$ps->ps_id]["oraw"];
        $tmp->ps_rnk_facility       = $this->v["rank"][$ps->ps_id]["faci"];
        $tmp->ps_rnk_production     = $this->v["rank"][$ps->ps_id]["prod"];
        $tmp->ps_rnk_lighting       = $this->v["rank"][$ps->ps_id]["ligh"];
        $tmp->ps_rnk_hvac           = $this->v["rank"][$ps->ps_id]["hvac"];
        $tmp->ps_rnk_water          = $this->v["rank"][$ps->ps_id]["watr"];
        $tmp->ps_rnk_waste          = $this->v["rank"][$ps->ps_id]["wste"];
        $tmp->ps_rnk_facility_cnt   = sizeof($this->v["rankList"]["faci"]);
        $tmp->ps_rnk_production_cnt = sizeof($this->v["rankList"]["prod"]);
        $tmp->ps_rnk_lighting_cnt   = sizeof($this->v["rankList"]["ligh"]);
        $tmp->ps_rnk_hvac_cnt       = sizeof($this->v["rankList"]["hvac"]);
        $tmp->ps_rnk_water_cnt      = sizeof($this->v["rankList"]["watr"]);
        $tmp->ps_rnk_waste_cnt      = sizeof($this->v["rankList"]["wste"]);
        $tmp->save();
        return $tmp;
    }
    
    protected function calcFutureYields()
    {
        $this->loadTotFlwrSqFt();
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
            $ps = $this->sessData->dataSets["powerscore"][0];
            $ps->ps_effic_facility = $matches["btu"]/sizeof($matches["flt"]);
            $ps->ps_kwh = $ps->ps_effic_facility*$this->v["totFlwrSqFt"];
            $ps->ps_grams = ($matches["grm"]/sizeof($matches["flt"]))
                *$this->v["totFlwrSqFt"];
            $this->sessData->dataSets["powerscore"][0] = $ps;
            $this->sessData->dataSets["powerscore"][0]->save();
        }
        return $this->sessData->dataSets["powerscore"][0];
    }
    
    protected function recalc2AllSubScores()
    {   
        $hasMore = false;
        $doneIDs = [];
        if ($GLOBALS["SL"]->REQ->has('doneIDs') 
            && trim($GLOBALS["SL"]->REQ->get('doneIDs')) != '') {
            $doneIDs = $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('doneIDs'));
        }
        $GLOBALS["SL"] = new Globals($GLOBALS["SL"]->REQ, $this->dbID, 1);
        $GLOBALS["SL"]->x["pageView"] = $GLOBALS["SL"]->x["dataPerms"] = 'public';
        $this->loadCustLoop($GLOBALS["SL"]->REQ, 1);
        $all = RIIPowerscore::select('ps_id')
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
            return '<br /><br /><div class="slCard nodeWrap">'
                . '<h3>Recalculating All Scores...</h3> <script type="text/javascript"> '
                . 'setTimeout("window.location=\'?recalc=1' 
                . (($GLOBALS["SL"]->REQ->has('fixLightingErrors')) 
                    ? '&fixLightingErrors=1' : '')
                . '&doneIDs=' . implode(',', $doneIDs) . '\'", 1000); </script>'
                . '<br /><br />' . implode(', ', $doneIDs) 
                . '<style> #nodeSubBtns { display: none; } </style></div>';
        }
        return '<br /><br /><div class="slCard nodeWrap"><h3>All Scores Recalculated!</h3>'
            . '<a href="/dash/powerscore-software-troubleshooting" '
            . 'class="btn btn-primary btn-lg">Back</a><br /><br />'
            . implode(', ', $doneIDs) . '<style> #nodeSubBtns { display: none; } </style></div>';
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
            . 'Hang tight, we are calculating your KPIs... ' 
            . (1+$freshDone) . '/' . sizeof($GLOBALS["CUST"]->v["fltComb"]) 
            . '...</h3>';
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
            . '<a href="/dash/powerscore-software-troubleshooting"'
            . ' class="btn btn-primary btn-lg">Back</a>'
            . '<br /><style> #nodeSubBtns { display: none; } </style></div>';
    }
    
    
    protected function getEfficPercs($allscores = [], $s = NULL)
    {
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
        return $efficPercs;
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
            foreach ($arch as $ps) {
                $this->rankOneValidSubScore($ps);
                $this->v["rank"][$ps->ps_id]["over"] = $GLOBALS["SL"]->getArrPercentile(
                    $this->v["rankList"]["oraw"], 
                    $this->v["rank"][$ps->ps_id]["oraw"]
                );
                $this->saveOneScoreRank($ps);
            }
        }
        return true;
    }

}