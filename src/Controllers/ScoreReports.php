<?php
/**
  * ScoreReports is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use CannabisScore\Controllers\ScoreListings;

class ScoreReports extends ScoreListings
{
    
    public function printBasicStats($nID = -3)
    {
      	$this->v["completionStats"] = [ "ALL" => [ 0, 0, 0 ] ];
      	$GLOBALS["SL"]->loadStates();
      	foreach ($GLOBALS["SL"]->states->stateList as $abbr => $state) {
      		$this->v["completionStats"][$abbr] = [ 0, 0, 0 ];
      	}
      	foreach ($GLOBALS["SL"]->states->stateListCa as $abbr => $state) {
      		$this->v["completionStats"][$abbr] = [ 0, 0, 0 ];
      	}
      	$all = RIIPowerscore::get();
        if ($all->isNotEmpty()) {
          	foreach ($all as $score) {
            		if (isset($score->ps_state) 
                    && isset($this->v["completionStats"][$score->ps_state])) {
              			$found = false;
              			if ($score->ps_effic_facility > 0 
                        && $score->ps_effic_production > 0 
                        && $score->ps_effic_lighting > 0 
                        && $score->ps_effic_hvac > 0) {
                				if ($score->ps_status == $this->v["defArch"]) {
          	        				$this->v["completionStats"]["ALL"][0]++;
          	        				$this->v["completionStats"][$score->ps_state][0]++;
          	        				$found = true;
        	        			} elseif ($score->ps_status == $this->v["defCmplt"]) {
          	        				$this->v["completionStats"]["ALL"][1]++;
          	        				$this->v["completionStats"][$score->ps_state][1]++;
          	        				$found = true;
        	        			}
              			}
              			if (!$found) {
                				$this->v["completionStats"]["ALL"][2]++;
                				$this->v["completionStats"][$score->ps_state][2]++;
              			}
            		}
          	}
        }
        foreach ($this->v["completionStats"] as $abbr => $stats) {
          	if ($stats[0] == 0 && $stats[1] == 0 && $stats[2] == 0) {
          	    unset($this->v["completionStats"][$abbr]);
          	}
        }
        return view(
            'vendor.cannabisscore.nodes.976-basic-score-stats', 
            $this->v
        )->render();
    }
    
}