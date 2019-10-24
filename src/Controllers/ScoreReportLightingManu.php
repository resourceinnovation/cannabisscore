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
use App\Models\RIIManufacturers;
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
        $this->v["lightManuName"] = '';
        if ($GLOBALS["SL"]->REQ->has('manu')) {
            $manu = trim($GLOBALS["SL"]->REQ->get('manu'));
            if ($manu != '') {
                if ($this->v["user"]->hasRole('administrator|staff')) {
                    $this->v["lightManuName"] = $manu;
                } elseif ($this->v["user"]->hasRole('partner') 
                    && isset($this->v["usrInfo"])) {
                    if (isset($this->v["usrInfo"]->company)
                        && $manu == $this->v["usrInfo"]->company) {
                        $this->v["lightManuName"] = $manu;
                    } elseif (isset($this->v["usrInfo"]->manufacturers)
                        && sizeof($this->v["usrInfo"]->manufacturers) > 0) {
                        foreach ($this->v["usrInfo"]->manufacturers as $m) {
                            if ($m->ManuName == $manu) {
                                $this->v["lightManuName"] = $manu;
                            }
                        }
                    }
                }
            }
        } elseif ($this->v["user"]->hasRole('partner')
            && sizeof($this->v["usrInfo"]->manufacturers) > 0
            && isset($this->v["usrInfo"]->manufacturers[0]->ManuName)) {
            $this->v["lightManuName"] = $this->v["usrInfo"]
                ->manufacturers[0]->ManuName;
        }
        if ($this->v["lightManuName"] != '') {
            $this->v["yourPsIDs"] = $this->getLightManuScoreIDs($this->v["lightManuName"]);
            $validCnt = $this->cntValidScoreIn($this->v["yourPsIDs"]);
            if ($validCnt == 0) {
                $this->v["lightManuName"] = '';
            }
        }

        if ($this->v["lightManuName"] != '') {
            $this->printCompareLightManuGetStats($this->v["lightManuName"]);
        } else {
            $this->v["lightManuName"] = 'Largely Lumens, Inc.';
            $this->loadSearchLightManuFake();
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
            return $this->loadSearchLightManuFake();
        }
        $this->v["yourPsIDs"] = $this->getLightManuScoreIDs($manu);
        $this->loadSearchLightManu('Your Customers');
        $this->loadCompareLightManuAvg();
        foreach ($this->v["lgtCompetData"]->dataLegend as $l => $leg) {
            $fld = 'PsRnk' . str_replace('Hvac', 'HVAC', $leg[0]);
            if (isset( $this->v["averageRanks"]->{ $fld })) {
                $this->v["lgtCompetData"]->dataLegend[$l][3]
                    = $GLOBALS["SL"]->mexplodeSize(',', 
                         $this->v["averageRanks"]->{ $fld });
            }
        }

        $allManus = $this->getTopLightManuIDs(10, $manu);
        if (sizeof($allManus) > 0) {
            foreach ($allManus as $t => $currManu) {
                $this->v["yourPsIDs"] = $this->getLightManuScoreIDs($currManu);
                $scoreMissed = $this->loadSearchLightManu(
                    'Customers of Competitor ' . chr(65+$t)
                );
                if ($scoreMissed > 0) {
                    unset($this->v["lgtCompetData"]->dataLines[
                        sizeof($this->v["lgtCompetData"]->dataLines)]);
                }
            }
        }

        $this->v["lgtCompetData"]->checkScoresMax();
//echo 'allManus<pre>'; print_r($allManus); echo '</pre>lgtCompetData<pre>'; print_r($this->v["lgtCompetData"]); echo '</pre>'; exit;

        return true;
    }

    /**
     * Add a data line with the current system indoor average.
     *
     * @return boolean
     */
    protected function loadCompareLightManuAvg()
    {
         $this->v["averageRanks"] = RIIPSRanks::where(
                'PsRnkFilters', '&fltFarm=144')
            ->first(); // (Indoor)
        $this->v["lgtCompetData"]->addLineFromRanking(
            'Indoor Average', 
            $this->v["averageRanks"],
            [50, 50, 50, 50, 50, 50]
        );
        return true;
    }

    /**
     * Initialize the searcher with the current custom list of PSIDs.
     *
     * @return int
     */
    protected function loadSearchLightManu($title = '', $skipIfBad = false)
    {
        $this->searcher = new CannabisScoreSearcher;
        $this->searcher->getSearchFilts(1);
        $this->searcher->loadAllScoresPublic(
            "->whereIn('PsID', [" 
                . implode(", ", $this->v["yourPsIDs"]) . "])"
            . "->where('PsEfficLighting', '>', 0)"
            . "->where('PsEfficLightingStatus', '=', " 
                . $this->v["defCmplt"] . ")"
        );
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        $this->v["lgtCompetData"]->addLine($title);
        if ($this->v["totCnt"] > 0) {
            foreach ($this->searcher->v["allscores"] as $i => $ps) {
                $this->v["lgtCompetData"]->addPowerScore(
                    $title, 
                    $ps, 
                    $this->v["defCmplt"]
                );
            }
            $this->v["lgtCompetData"]->calcScoreAvgs($title);
        }
        $scoreMissed = 0;
        foreach ($this->v["lgtCompetData"]->dataLines as $ind => $data) {
            $ranks = [];
            foreach ($this->v["lgtCompetData"]->dataLegend as $l => $leg) {
                $fld = 'PsRnk' . str_replace('Hvac', 'HVAC', $leg[0]);
                if (isset($this->v["averageRanks"]->{ $fld })
                    && $this->v["averageRanks"]->{ $fld } > 0) {
                    $ranks[] = $GLOBALS["SL"]->getArrPercentileStr(
                        $this->v["averageRanks"]->{ $fld }, 
                        $data->scores[$l], 
                        ($l != 1)
                    );
                } else {
                    $scoreMissed++;
                    $ranks[] = 0;
                }
            }
            $this->v["lgtCompetData"]->dataLines[$ind]->ranks = $ranks;
        }
        return $scoreMissed;
    }


    /**
     * Get most adopted Manufacturers' IDs.
     *
     * @param int $limit
     * @return array
     */
    protected function getTopLightManuIDs($limit = 3, $except = '')
    {
        $ret = $cnts = [];

        $tmpTop = [
            'Fluence',
            'Bios Lighting',
            'Sunblaster',
            'BIOS'
        ];
        foreach ($tmpTop as $top) {
            if ($top != $except) {
                $ret[] = $top;
            }
        }
        return $ret;


        // Once there's more data... 

        $chk = RIIManufacturers::where('ManuCntFlower', '>', 0)
            ->orWhere('ManuCntVeg', '>', 0)
            ->orWhere('ManuCntClone', '>', 0)
            ->orWhere('ManuCntMother', '>', 0)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $manu) {
                $cnts[$manu->ManuName] = intVal($manu->ManuCntFlower)
                    + intVal($manu->ManuCntVeg)
                    + intVal($manu->ManuCntClone)
                    + intVal($manu->ManuCntMother);
            }
            arsort($cnts);
            foreach ($cnts as $manuName => $cnt) {
                if (sizeof($ret) < $limit 
                    && $manuName != $except) {
                    $ret[] = $manuName;
                }
            }
        }
        return $ret;
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
        $chk = RIIPSLightTypes::where('PsLgTypMake', 
                'LIKE', '%' . $manu . '%')
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

    /**
     * Get count of valid sub-scores from within PowerScore IDs array.
     *
     * @param string $manu
     * @return array
     */
    protected function cntValidScoreIn($scoreIDs = [])
    {
        $cnt = 0;
        $chk = RIIPowerScore::whereIn('PsID', $scoreIDs)
            ->where('PsStatus', $this->v["defCmplt"])
            ->select('PsEfficFacilityStatus', 'PsEfficProductionStatus',
              'PsEfficLightingStatus', 'PsEfficHvacStatus')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ps) {
                if ($ps->PsEfficFacilityStatus 
                    == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->PsEfficProductionStatus 
                    == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->PsEfficLightingStatus 
                    == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->PsEfficHvacStatus 
                    == $this->v["defCmplt"]) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    /**
     * 
     *
     * @return boolean
     */
    protected function loadSearchLightManuFake()
    {
        $this->v["yourPsIDs"] = [];
        $this->v["lgtCompetData"]->addLine(
            'Your Customers', 
            [105, 1.46, 37.9, 55.2, 24.2, 34.0], // raw scores
            [ 80,   93,   70,   78,   60,   42]  // relative rankings
        );
        $this->loadCompareLightManuAvg();
        $this->v["lgtCompetData"]->dataLines[1]->scores[5] = 37;
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor A', 
            [163, 1.26, 40.7, 100, 38.9, 25.7],
            [ 70,   77,   60,  61,   55,   62]
        );
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor B', 
            [185,  0.96, 43.4, 90.2, 42.2, 37.7],
            [  33,   14,   45,   42,   37,   36]
        );
        $this->v["lgtCompetData"]->addLine(
            'Customers of Competitor C', 
            [97.3, 1.43, 34.9, 67.8, 39.3, 33.1],
            [  55,   57,   54,   67,   54,   43]
        );
        $this->v["lgtCompetData"]->checkScoresMax();
        return true;
    }



}



class ScoreLgtManuData
{
    public $dataLines  = [];
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
            ['Facility',   'Facility Efficiency',   'kWh / sq ft',     0, 0],
            ['Production', 'Production Efficiency', 'g / kWh',         0, 0],
            ['Lighting',   'Lighting Efficiency',   'W / sq ft',       0, 0],
            ['Hvac',       'HVAC Efficiency',       'kWh / sq ft',     0, 0],
            ['Water',      'Water Efficiency',      'gallons / sq ft', 0, 0],
            ['Waste',      'Waste Efficiency',      'lbs / sq ft',     0, 0]
        ];
    }

    /**
     * Add a line of data to this collection using the raw scores results.
     *
     * @param string $title
     * @param array $allscores
     * @return boolean
     */
    /*
    public function calcAndAddLine($title = '', $allscores = [])
    {
        $avgs = $cnts = [];
        foreach ($this->dataLegend as $d => $dat) {
            $avgs[$d] = $cnts[$d] = 0;
        }
        if ($allscores->isNotEmpty()) {
            foreach ($allscores as $ps) {
                foreach ($this->dataLegend as $d => $dat) {
                    if (isset($ps->{ 'PsEffic' . $dat[0] })
                        && isset($ps->{ 'PsEffic' . $dat[0] . 'Status' })
                        && intVal($ps->{ 'PsEffic' . $dat[0] 
                            . 'Status' }) == 243) {
                        $avgs[$d] += $ps->{ 'PsEffic' . $dat[0] };
                        $cnts[$d]++;
                    }
                }
            }
        }
        
        foreach ($this->dataLegend as $d => $dat) {
            $avgs[$d] = $avgs[$d]/$cnts[$d];
        }

        $this->addLine(
            $title, 
            $avgs, // raw scores
            [  80,   93,   70,   78,   60,   42]  // relative rankings
        );
    }
    */

    /**
     * Add a line of data to this collection from a rankings record.
     *
     * @param string $title
     * @param array $rnks
     * @param array $ranks
     * @return boolean
     */
    public function addLineFromRanking($title = '', $rnks = [], $ranks = [])
    {
        $scores = [];
        foreach ($this->dataLegend as $l => $leg) {
            $type = (($leg[0] == 'Hvac') ? 'HVAC' : $leg[0]);
            if (isset($rnks->{ 'PsRnk' . $type })) {
                $scores[] = $GLOBALS["SL"]
                    ->commaListAvg($rnks->{ 'PsRnk' . $type });
            } else {
                $scores[] = 0;
            }
        }
        $this->addLine($title, $scores, $ranks);
        return true;
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
        $ind = sizeof($this->dataLines);
        $this->dataLines[] = new ScoreLgtManuDataLine(
            $title, 
            $scores, 
            $ranks, 
            $ids
        );
        if (sizeof($scores) == 0) {
            $this->dataLines[$ind]->clearScores();
        }
        if (sizeof($ranks) == 0) {
            $this->dataLines[$ind]->clearRanks();
        }
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
        $rankRow = RIIPSRanks::where('PsRnkFilters', 
                '&fltFarm=' . $ps->PsCharacterize)
            ->first();
        foreach ($this->dataLegend as $l => $leg) {
            $fld = 'PsEffic' . $leg[0];
            if (isset($ps->{ $fld }) && $ps->{ $fld } > 0
                && $ps->{ $fld . 'Status' } == $defCmplt) {
                $score = $ps->{ $fld };
                $this->dataLines[$ind]->scores[$l] += $score;
                $this->dataLines[$ind]->ids[$l][] = $ps->PsID;
                $fldRnk = 'PsRnk' . $leg[0];
                if ($rankRow && isset($rankRow->{ $fldRnk })) {
                    $rnk = $rankRow->{ $fldRnk };
                    $r = $this->calcScoreRank($score, $rnk);
                    $this->dataLines[$ind]->ranks[$l] += $r;
                }
            }
        }
        return true;
    }

    /**
     * 
     *
     * @param string $title
     * @param array $ranks
     * @return boolean
     */
    public function addDataLineRanks($title, $ranks)
    {
        $ind = $this->getIndFromTitle($title);
        $this->dataLines[$ind]->ranks = $ranks;
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
                $this->dataLines[$ind]->scores[$l] 
                    = $this->dataLines[$ind]->scores[$l]
                    /sizeof($this->dataLines[$ind]->ids[$l]);
                $this->dataLines[$ind]->ranks[$l] 
                    = $this->dataLines[$ind]->ranks[$l]
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
