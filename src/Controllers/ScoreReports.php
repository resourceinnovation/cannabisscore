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
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSAreasBlds;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPSLicenses;
use SurvLoop\Controllers\SurvStatsGraph;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreListings;

class ScoreReports extends ScoreListings
{
    
    
}