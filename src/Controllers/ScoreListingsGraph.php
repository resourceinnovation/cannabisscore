<?php
/**
  * ScoreReports is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.3
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsRanks;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIManufacturers;

class ScoreListingsGraph
{
    protected $v        = [];
    protected $searcher = null;
    
    public function __construct($uID = 0, $user = null, $usrInfo = null)
    {
        $this->v["uID"]     = $uID;
        $this->v["user"]    = $user;
        $this->v["usrInfo"] = $usrInfo;
        $this->v["isAdmin"] = ($this->v["user"] 
            && $this->v["user"]->hasRole('administrator|staff'));

        $this->v["defCmplt"] = 243;
        $this->v["defArch"]  = 364;
        $this->v["defInc"]   = 242;

        $defSet = 'PowerScore Farm Types';
        $this->v["frmTypOut"] = $GLOBALS["SL"]->def->getID($defSet, 'Outdoor');
        $this->v["frmTypIn"]  = $GLOBALS["SL"]->def->getID($defSet, 'Indoor');
        $this->v["frmTypGrn"] = $GLOBALS["SL"]->def->getID(
            $defSet, 
            'Greenhouse/Hybrid/Mixed Light'
        );

        $this->searcher = new CannabisScoreSearcher;
        $this->copyUserToSearcher();
    }


    
    public function loadManuData()
    {
        $this->v["lgtCompetData"] = new ScoreLgtManuData;
        $this->v["lightManuName"] = '';
        $this->v["yourPsIDs"]     = [];
        return true;
    }


    
    public function getMultiSiteRankings($nID)
    {
        return '<!-- -->';
    }
    
    protected function printCompareGraphs()
    {
        
        $this->searcher->v["allMultiSiteRankings"] = '';
        
        
        
        /*
        $this->searcher->v["psGraphDat"] = [
            "Facility"   => [ "dat" => '', "lab" => '', "bg" => '', "brd" => '' ],
            "Production" => [ "dat" => '', "lab" => '', "bg" => '', "brd" => '' ],
            "Lighting"   => [ "dat" => '', "lab" => '', "bg" => '', "brd" => '' ],
            "Hvac"       => [ "dat" => '', "lab" => '', "bg" => '', "brd" => '' ]
            ];
        
        
        $cnt = 0;
        $currTime = $this->v["genTots"]["date"][2];
        $currDate = date("Y-m-d", $currTime);
        while ($currDate != date("Y-m-d")) {
            $cma = (($cnt > 0) ? ", " : "");
            $this->v["graph2"]["dat"] .= $cma . ((isset($this->v["genTots"]["date"][3][$currDate])) 
                ? $this->v["genTots"]["date"][3][$currDate] : 0);
            $this->v["graph2"]["lab"] .= $cma . "\"" . $currDate . "\"";
            $this->v["graph2"]["bg"]  .= $cma . "\"" . $this->v["css"]["color-main-on"]  . "\"";
            $this->v["graph2"]["brd"] .= $cma . "\"" . $this->v["css"]["color-main-grey"] . "\"";
            $cnt++;
            $currTime += (24*60*60);
            $currDate = date("Y-m-d", $currTime);
        }
        
        $this->v["graph2print"] = view('vendor.survloop.reports.graph-bar', [
            "currGraphID" => 'treeSessCalen',
            "hgt"         => '380px',
            "yAxes"       => '# of Submission Attempts (Active Sessions)',
            "title"       => '<h3 class="mT0 mB10">Number of Submission Attempts by Date</h3>',
            "graph"       => $this->v["graph2"],
            "css"         => $this->v["css"]
            ])->render();
        */
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
        $chk = RIIPowerscore::whereIn('ps_id', $scoreIDs)
            ->where('ps_status', $this->v["defCmplt"])
            ->select('ps_effic_facility_status', 'ps_effic_production_status',
                'ps_effic_lighting_status', 'ps_effic_hvac_status')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ps) {
                if ($ps->ps_effic_facility_status == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->ps_effic_production_status == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->ps_effic_lighting_status == $this->v["defCmplt"]) {
                    $cnt++;
                }
                if ($ps->ps_effic_hvac_status == $this->v["defCmplt"]) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    /**
     * Add a data line with the current system indoor average.
     *
     * @return boolean
     */
    protected function loadCompareLightManuAvg($fltFarm = 144) // (Indoor)
    {
        $type = 'Indoor';
        if ($fltFarm == 145) {
            $type = 'Greenhouse';
        } elseif ($fltFarm == 143) {
            $type = 'Outdoor';
        }
        $flt = '&fltFarm=' . $fltFarm;
        $this->v["averageRanks"] = RIIPsRanks::where('ps_rnk_filters', $flt)
            ->first(); 
        $this->v["lgtCompetData"]->addLineFromRanking(
            $type . ' Average', 
            $this->v["averageRanks"],
            [50, 50, 50, 50, 50, 50]
        );
        return true;
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

    protected function fakeMultiSite()
    {
        $GLOBALS["SL"]->x["testMultiSite"] = '';
        $GLOBALS["SL"]->x["fakeSites"] = [
            'Worcester', '', '', 
            'Portland', '', 
            'Hershey', '', '', '', 
            'Detroit', '', '', '', '', 
            'Mendo', '', 
            'Thomas', '', ''
        ];
        if ($GLOBALS["SL"]->REQ->has('test')) {
            $GLOBALS["SL"]->x["needsCharts"] = true;
            $this->loadManuData();
            $this->v["lgtCompetData"]->addLine(
                'Your Customers', 
                [105, 1.46, 37.9, 55.2, 24.2, 34.0], // raw scores
                [ 80,   93,   70,   78,   60,   42]  // relative rankings
            );
            $this->loadCompareLightManuAvg();
            $this->loadCompareLightManuAvg(145);
            $this->searcher->v["lgtCompetData"] = $this->v["lgtCompetData"];
            
        }
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
            ['facility',   'Facility Efficiency',   'kBtu / sq ft',    0, 0],
            ['production', 'Production Efficiency', 'g / kBtu',        0, 0],
            ['water',      'Water Efficiency',      'gallons / sq ft', 0, 0],
            ['waste',      'Waste Efficiency',      'lbs / sq ft',     0, 0],
            ['hvac',       'HVAC Efficiency',       'kBtu / sq ft',    0, 0],
            ['lighting',   'Lighting Efficiency',   'kWh / day',       0, 0]
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
                    if (isset($ps->{ 'ps_effic_' . $dat[0] })
                        && isset($ps->{ 'ps_effic_' . $dat[0] . '_status' })
                        && intVal($ps->{ 'ps_effic_' . $dat[0] . '_status' }) == 243) {
                        $avgs[$d] += $ps->{ 'ps_effic_' . $dat[0] };
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
            if (isset($rnks->{ 'ps_rnk_' . $leg[0] })) {
                $fld = 'ps_rnk_' . $leg[0];
                $scores[] = $GLOBALS["SL"]->commaListAvg($rnks->{ $fld });
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
     * @param App\Models\RIIPowerscore $ps
     * @return int
     */
    public function addPowerScore($title, $ps, $defCmplt)
    {
        $ind = $this->getIndFromTitle($title);
        $flt = '&fltFarm=' . $ps->ps_characterize;
        $rankRow = RIIPsRanks::where('ps_rnk_filters', $flt)
            ->first();
        foreach ($this->dataLegend as $l => $leg) {
            $fld = 'ps_effic_' . strtolower($leg[0]);
            if (isset($ps->{ $fld }) 
                && $ps->{ $fld } > 0
                && $ps->{ $fld . '_status' } == $defCmplt) {
                $score = $ps->{ $fld };
                $this->dataLines[$ind]->scores[$l] += $score;
                $this->dataLines[$ind]->ids[$l][] = $ps->ps_id;
                $fldRnk = 'ps_rnk_' . strtolower($leg[0]);
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