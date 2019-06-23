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
use App\Models\RIIPowerScore;
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
		$all = RIIPowerScore::get();
        if ($all->isNotEmpty()) {
        	foreach ($all as $score) {
        		if (isset($score->PsState) && isset($this->v["completionStats"][$score->PsState])) {
        			$found = false;
        			if ($score->PsEfficFacility > 0 && $score->PsEfficProduction > 0 && $score->PsEfficLighting > 0 && $score->PsEfficHvac > 0) {
        				if ($score->PsStatus == $this->v["defArch"]) {
	        				$this->v["completionStats"]["ALL"][0]++;
	        				$this->v["completionStats"][$score->PsState][0]++;
	        				$found = true;
	        			} elseif ($score->PsStatus == $this->v["defCmplt"]) {
	        				$this->v["completionStats"]["ALL"][1]++;
	        				$this->v["completionStats"][$score->PsState][1]++;
	        				$found = true;
	        			}
        			}
        			if (!$found) {
        				$this->v["completionStats"]["ALL"][2]++;
        				$this->v["completionStats"][$score->PsState][2]++;
        			}
        		}
        	}
        }
        foreach ($this->v["completionStats"] as $abbr => $stats) {
        	if ($stats[0] == 0 && $stats[1] == 0 && $stats[2] == 0) {
        		unset($this->v["completionStats"][$abbr]);
        	}
        }
        return view('vendor.cannabisscore.nodes.976-basic-score-stats', $this->v)->render();
	}
    
}