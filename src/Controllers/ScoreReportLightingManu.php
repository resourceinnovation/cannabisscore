<?php
/**
  * ScoreReports is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since v0.2.3
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use App\Models\RIIPSRanks;
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use CannabisScore\Controllers\ScoreListings;

class ScoreReportLightingManu extends ScoreListings
{
    /**
     * Print lighting competitor report comparing their customers to 
     * their competitors, and broader averages.
     *
     * Example: A partner lighting manufacturer can login to see how they rank.
     *
     * @param int $nID
     * @return string
     */
    public function printCompareLightManu($nID = -3)
    {
        $this->v["lgtCompetData"] = new ScoreLgtManuData;
        $this->v["lightManuName"] = 'Largely Lumens, Inc.';
        if ($GLOBALS["SL"]->REQ->has('manu') 
            && trim($GLOBALS["SL"]->REQ->get('manu')) != '') {
            $this->v["lightManuName"] = trim($GLOBALS["SL"]->REQ->get('manu'));
            $this->printCompareLightManuGetStats($this->v["lightManuName"]);
        }
        $this->v["competitionGraphs"] = [];

        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view(
            'vendor.cannabisscore.nodes.979-compare-lighting-manufacturers', 
            $this->v
        )->render();
    }

    /**
     * Run calculations for the lighting competitor report.
     *
     * @param string $manu
     * @return boolean
     */
    protected function printCompareLightManuGetStats($manu = '')
    {
        if (trim($manu) == '') {
            return false;
        }
        $scoreIDs = $this->getLightManuScoreIDs($manu);
        $this->searcher = new CannabisScoreSearcher;
        $this->searcher->getSearchFilts(1);
        $this->searcher->loadAllScoresPublic(
            "->whereIn('PsID', [" . implode(", ", $scoreIDs) . "])"
            . "->where('PsStatus', '=', " . $this->v["defCmplt"] . ")"
        );
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);

        $this->v["lgtCompetData"]->addLine(
            'Your Customers', 
            [25.7, 4.46, 50.9, 15.2, 14.2, 34.0], // raw scores
            [  80,   93,   70,   78,   60,   42]  // relative rankings
        );
        $this->v["lgtCompetData"]->addLine(
            'Average', 
            [141, 3.42, 20.9, 81.9, 20.4, 31.2],
            [ 50,   50,   50,   50,   50,   50]
        );
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor A', 
            [163, 4.26, 30.7, 100, 18.9, 25.7],
            [ 70,   77,   60,  61,   55,   62]
        );
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor B', 
            [185,  0.96, 43.4, 90.2, 22.2, 37.7],
            [  33,   14,   45,   42,   37,   36]
        );
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor C', 
            [97.3, 2.43, 14.9, 67.8, 19.3, 33.1],
            [  55,   57,   54,   67,   54,   43]
        );

        $this->v["yourPsIDs"] = [];
        // clear Your Customers scores and ranks
        $this->v["lgtCompetData"]->dataLines[0]->clearScores();
        $this->v["lgtCompetData"]->dataLines[0]->clearRanks();
        if ($this->v["totCnt"] > 0) {
            foreach ($this->searcher->v["allscores"] as $i => $ps) {
                $this->v["lgtCompetData"]
                    ->addPowerScore('Your Customers', $ps, $this->v["defCmplt"]);
                $this->v["yourPsIDs"][] = $ps->PsID;
            }
            $this->v["lgtCompetData"]->calcScoreAvgs('Your Customers');
        }
        $this->v["lgtCompetData"]->checkScoresMax();
        return true;
    }

    /**
     * Get PowerScore IDs using a specific lighting manufacturer.
     *
     * @param string $manu
     * @return array
     */
    protected function getLightManuScoreIDs($manu = '')
    {
        $areaIDs = $scoreIDs = [];
        $chk = RIIPSLightTypes::where('PsLgTypMake', 'LIKE', '%' . $manu . '%')
            ->select('PsLgTypAreaID')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $light) {
                if (!in_array($light->PsLgTypAreaID, $areaIDs)) {
                    $areaIDs[] = $light->PsLgTypAreaID;
                }
            }
        }
        $chk = RIIPSAreas::whereIn('PsAreaID', $areaIDs)
            ->select('PsAreaPSID')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $light) {
                if (!in_array($light->PsAreaPSID, $scoreIDs)) {
                    $scoreIDs[] = $light->PsAreaPSID;
                }
            }
        }
        return $scoreIDs;
    }
}



class ScoreLgtManuData
{
    public $dataLines = [];
    public $dataLegend = [];

    /**
     * Initialize this collection of lighting competitor data
     *
     * @return void
     */
    public function __construct()
    {
        //  prod, fac, hvac, light, water, waste // number of records analyzed
        $this->dataLegend = [
            ['Facility',   'Facility Efficiency',   'kWh / sq ft',     95, 0],
            ['Production', 'Production Efficiency', 'g / kWh',         94, 0],
            ['Lighting',   'HVAC Efficiency',       'kWh / sq ft',     68, 0],
            ['Hvac',       'Lighting Efficiency',   'W / sq ft',       68, 0],
            ['Water',      'Water Efficiency',      'gallons / sq ft', 24, 0],
            ['Waste',      'Waste Efficiency',      'g / kWh',         25, 0]
        ];
    }

    /**
     * Add a line of data to this collection.
     *
     * @param string $title
     * @param array $scores
     * @param array $ranks
     * @param array $ids
     * @return boolean
     */
    public function addLine($title = '', $scores = [], $ranks = [], $ids = [])
    {
        $this->dataLines[] = new ScoreLgtManuDataLine($title, $scores, $ranks, $ids);
        return true;
    }

    /**
     * Retreive the array index of a given data set title.
     *
     * @param string $title
     * @return int
     */
    public function getIndFromTitle($title = '')
    {
        if (sizeof($this->dataLines) > 0) {
            foreach ($this->dataLines as $ind => $line) {
                if ($line->title == $title) {
                    return $ind;
                }
            }
        }
        return -1;
    }

    /**
     * Add all sub-scores fit to include into each line of data.
     *
     * @param string $title
     * @param App\Models\RIIPowerScore $ps
     * @return int
     */
    public function addPowerScore($title, $ps, $defCmplt)
    {
        $ind = $this->getIndFromTitle($title);
        $rankRow = RIIPSRanks::where('PsRnkFilters', '&fltFarm=' . $ps->PsCharacterize)
            ->first();
        foreach ($this->dataLegend as $l => $leg) {
            if (isset($ps->{ 'PsEffic' . $leg[0] })
                && $ps->{ 'PsEffic' . $leg[0] } > 0
                && $ps->{ 'PsEffic' . $leg[0] . 'Status' } == $defCmplt) {
                $this->dataLines[$ind]->ids[$l][] = $ps->PsID;
                $score = $ps->{ 'PsEffic' . $leg[0] };
                $this->dataLines[$ind]->scores[$l] += $score;
                if ($rankRow && isset($rankRow->{ 'PsRnk' . $leg[0] })) {
                    $r = $this->calcScoreRank($score, $rankRow->{ 'PsRnk' . $leg[0] });
                    $this->dataLines[$ind]->ranks[$l] += $r;
                }
            }
        }
        return true;
    }

    /**
     * Calculate a individual score's rank within a comma-separated list.
     *
     * @param double $curr
     * @param array $list
     * @return double
     */
    public function calcScoreRank($curr, $list)
    {
        $min = 0;
        $scores = $GLOBALS["SL"]->mexplode(',', $list);
        if (sizeof($scores) > 0) {
            foreach ($scores as $i => $score) {
                if ($curr < $score) {
                    $min = $i;
                }
            }
            return (100*($min/sizeof($scores)));
        }
        return 0;
    }

    /**
     * Calculate each sub-score's average in raw score, and in rank.
     *
     * @param string $title
     * @return boolean
     */
    public function calcScoreAvgs($title = '')
    {
        $ind = $this->getIndFromTitle($title);
        foreach ($this->dataLegend as $l => $leg) {
            if (sizeof($this->dataLines[$ind]->ids[$l]) > 0) {
                $this->dataLines[$ind]->scores[$l] = $this->dataLines[$ind]->scores[$l]
                    /sizeof($this->dataLines[$ind]->ids[$l]);
                $this->dataLines[$ind]->ranks[$l] = $this->dataLines[$ind]->ranks[$l]
                    /sizeof($this->dataLines[$ind]->ids[$l]);
            } else {
                $this->dataLines[$ind]->scores[$l] = 0;
                $this->dataLines[$ind]->ranks[$l]  = 0;
            }
        }
        return true;
    }

    /**
     * Calculate each sub-score's maximum value, to let the charts know.
     *
     * @return boolean
     */
    public function checkScoresMax()
    {
        if (sizeof($this->dataLines) > 0) {
            foreach ($this->dataLegend as $l => $leg) {
                $this->dataLegend[$l][4] = 0; // reset sub-score maximum
                foreach ($this->dataLines as $line) {
                    if ($this->dataLegend[$l][4] < $line->scores[$l]) {
                        $this->dataLegend[$l][4] = $line->scores[$l];
                    }
                }
                $this->dataLegend[$l][4] = ceil($this->dataLegend[$l][4]);
            }
        }
        return true;
    }

}

class ScoreLgtManuDataLine
{
    public $title  = '';
    public $scores = [];
    public $ranks  = [];
    public $ids    = [];

    /**
     * Tracks the various data points within one line of data.
     *
     * @param string $title
     * @param array $scores
     * @param array $ranks
     * @param array $ids
     * @return double
     */
    public function __construct($title = '', $scores = [], $ranks = [], $ids = [])
    {
        $this->title  = $title;
        $this->scores = $scores;
        $this->ranks  = $ranks;
        $this->ids    = $ids;
        if (sizeof($this->scores) == 0) {
            $this->clearScores();
        }
        if (sizeof($this->ranks) == 0) {
            $this->clearRanks();
        }
        if (sizeof($this->ids) == 0) {
            $this->clearIds();
        }
    }

    /**
     * Initialize scores tracked with this data line.
     *
     * @return void
     */
    public function clearScores()
    {
        $this->scores = $this->initDataArr();
    }

    /**
     * Initialize ranks tracked with this data line.
     *
     * @return void
     */
    public function clearRanks()
    {
        $this->ranks = $this->initDataArr();
    }

    /**
     * Initialize PowerScore IDs tracked with this data line.
     *
     * @return void
     */
    public function clearIds()
    {
        $this->ids = [ [], [], [], [], [], [] ];
    }

    /**
     * Get array of zeros for each sub-score.
     *
     * @return array
     */
    public function initDataArr()
    {
        return [ 0, 0, 0, 0, 0, 0 ];
    }
    
}
