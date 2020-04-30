<?php
/**
  * ScoreReports is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.3
  */
namespace CannabisScore\Controllers;

use DB;
use Auth;
use App\Models\RIIPowerscore;
use App\Models\RIIPsRanks;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIManufacturers;
use CannabisScore\Controllers\ScoreStats;
use CannabisScore\Controllers\ScoreListingsGraph;

class ScoreReportLightingManu extends ScoreListingsGraph
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
    public function printCompetitiveReport($nID = -3)
    {
        $uID = 0;
        if (Auth::user() && isset(Auth::user()->id)) {
            $uID = Auth::user()->id;
        }
        if ((isset($GLOBALS["SL"]->x["partnerManuIDs"])
                && sizeof($GLOBALS["SL"]->x["partnerManuIDs"]) > 0)
            || ($GLOBALS["SL"]->REQ->has('manu') 
                && trim($GLOBALS["SL"]->REQ->manu) != ''
                && $this->v["user"]->hasRole('administrator|staff'))) {
            return $this->printCompareLightManu($nID);
        }
        return $this->printPartnerCompetitive($nID, $uID);
    }

    /**
     * Print partner competitor report comparing their customers to 
     * their competitors, and broader averages.
     *
     * @param int $nID
     * @param int $uID
     * @return string
     */
    protected function printPartnerCompetitive($nID, $uID)
    {
        $this->v["statCompete"] = new ScoreStats;
        $this->initPartnerCompeteData();

        $avgs = [];
        foreach ($this->v["farmTypes"] as $label => $farm) {
            $avgs[$farm] = [];
            foreach ($this->v["dataLegend"] as $l => $leg) {
                $avgs[$farm][$leg[0]] = [ 0, [] ];
            }
        }
        $this->searcherAvgs = new CannabisScoreSearcher;
        $this->searcherAvgs->getSearchFilts(1);
        $this->searcherAvgs->v["fltPartner"] = 0;
        $this->searcherAvgs->v["fltCmpl"] = 243;
        $this->searcherAvgs->searchFiltsURL(true);
        $this->searcherAvgs->loadAllScoresPublic();
        if (sizeof($this->searcherAvgs->v["allscores"]) > 0) {
//echo '<pre>'; print_r($this->searcherAvgs->v["allscores"]); echo '</pre>'; exit;
            foreach ($this->searcherAvgs->v["allscores"] as $i => $ps) {
                $farm = $ps->ps_characterize;
                foreach ($this->v["dataLegend"] as $l => $leg) {
                    if ($ps->{ 'ps_effic_' . $leg[1] } > 0 
                        && $ps->{ 'ps_effic_' . $leg[1] . '_status' } 
                            == $this->v["defCmplt"]) {
                        $avgs[$farm][$leg[0]][1][] = $ps->{ 'ps_effic_' . $leg[1] };
                    }
                }
            }
            foreach ($this->v["farmTypes"] as $label => $farm) {
                foreach ($this->v["dataLegend"] as $l => $leg) {
                    $sum = array_sum($avgs[$farm][$leg[0]][1]);
                    $cnt = count($avgs[$farm][$leg[0]][1]);
                    if ($cnt > 0) {
                        $avgs[$farm][$leg[0]][0] = $sum/$cnt;
                    } else {
                        $avgs[$farm][$leg[0]][0] = 0;
                    }
                }
            }
        }
//echo '<pre>'; print_r($avgs); echo '</pre>'; exit;


        $this->searcher = new CannabisScoreSearcher;
        $this->v["partnerPSIDs"] = $this->searcher->getPartnerPSIDs($uID);
        $this->searcher->getSearchFilts(1);
        $this->searcher->loadAllScoresPublic(
            "->whereIn('ps_id', [" 
                . implode(", ", $this->v["partnerPSIDs"]) 
            . "])"
        );
        $this->v["fltClimate"] = $this->searcher->v["fltClimate"];
        $this->v["fltSize"] = $this->searcher->v["fltSize"];
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        if ($this->v["totCnt"] > 0) {
            foreach ($this->searcher->v["allscores"] as $i => $ps) {

                $char = $ps->ps_characterize;
                $this->v["statCompete"]->resetRecFilt();
                $this->v["statCompete"]->addRecFilt('farm', $char, $ps->ps_id);
                foreach ($this->v["dataLegend"] as $leg) {
                    $fld = 'ps_effic_' . $leg[1];
                    if (isset($ps->{ $fld }) && $ps->{ $fld } > 0) {
                        $this->v["statCompete"]->addRecDat(
                            $leg[0], 
                            $ps->{ $fld }, 
                            $ps->ps_id
                        );
                    }
                }

            }
        }
        $this->v["statCompete"]->resetRecFilt();
        $this->v["statCompete"]->calcStats(); 

        $this->v["graphData"] = [];
        foreach ($this->v["dataLegend"] as $l => $leg) {
            $datLet = $this->v["statCompete"]->dAbr($leg[0]);
            $this->v["graphData"][$l] = [
                $leg[0],
                [], 
                0, 
            ];
            foreach ($this->v["farmTypes"] as $label => $farm) {
                $this->v["graphData"][$l][1][$farm] = [];
                if (isset($this->v["statCompete"]->dat["a" . $farm])
                    && isset($this->v["statCompete"]->dat["a" . $farm]["cnt"])
                    && $this->v["statCompete"]->dat["a" . $farm]["cnt"] > 0) {
                    $avg = $this->v["statCompete"]->dat["a" . $farm]["dat"][$datLet]["avg"];
                    $this->v["graphData"][$l][1][$farm][] = [
                        $avg,
                        $this->v["statCompete"]->dat["a" . $farm]["cnt"],
                        'Your ' . $label . ' Customers'
                    ];
                    if ($this->v["graphData"][$l][2] < $avg) {
                        $this->v["graphData"][$l][2] = $avg;
                    }
                }
            }
        }


        foreach ($this->v["farmTypes"] as $label => $farm) {
            foreach ($this->v["dataLegend"] as $l => $leg) {
                $this->v["graphData"][$l][1][$farm][] = [
                    $avgs[$farm][$leg[0]][0],
                    sizeof($avgs[$farm][$leg[0]][1]),
                    $label . ' Average'
                ];
                if ($this->v["graphData"][$l][2] < $avgs[$farm][$leg[0]][0]) {
                    $this->v["graphData"][$l][2] = $avgs[$farm][$leg[0]][0];
                }
            }
        }
//echo '<pre>'; print_r($this->v["graphData"]); echo '</pre>'; exit;
//echo 'statComplete: <pre>'; print_r($this->v["statCompete"]); echo '</pre>'; exit;

        $this->searcher->v["nID"] = $nID;
        $this->searcher->loadFilterCheckboxes();
        $this->v["psFilters"] = view(
            'vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v
        )->render();
        $GLOBALS['SL']->x['partnerVersion'] = true;
        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view(
            'vendor.cannabisscore.nodes.979-partner-competitive', 
            $this->v
        )->render();
    }

    /**
     * Initialize the data calculator.
     *
     * @return boolean
     */
    protected function initPartnerCompeteData()
    {
        $this->v["dataLegend"] = [
            ['fac', 'facility',   'Facility Efficiency',   'kBtu / sq ft',    0, 0],
            ['pro', 'production', 'Production Efficiency', 'g / kBtu',        0, 0],
            ['lgt', 'lighting',   'Lighting Efficiency',   'kWh / day',       0, 0],
            ['hvc', 'hvac',       'HVAC Efficiency',       'kBtu / sq ft',    0, 0],
            ['wtr', 'water',      'Water Efficiency',      'gallons / sq ft', 0, 0],
            ['wst', 'waste',      'Waste Efficiency',      'lbs / sq ft',     0, 0]
        ];
        $this->v["farmTypes"] = [
            'Indoor'           => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Indoor'),
            'Greenhouse' => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Greenhouse/Hybrid/Mixed Light'),
            'Outdoor'          => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Outdoor')
        ];
        /*
        $this->v["statCompete"]->addFilt( // a
            'farm', 
            'Farm Type', 
            [144, 145, 143],
            ['Indoor', 'Greenhouse/Mixed', 'Outdoor']
        );

        $this->v["statCompete"]->addDataType( // stat var 'a'
            'fac',  
            '<nobr>Facility <sup class="slBlueDark">kBtu / sq ft</sup></nobr>'
        );
        $this->v["statCompete"]->addDataType( // stat var 'b'
            'pro',  
            '<nobr>Production <sup class="slBlueDark">g / kBtu</sup></nobr>'
        );
        $this->v["statCompete"]->addDataType( // stat var 'c'
            'hvc',  
            '<nobr>HVAC <sup class="slBlueDark">kBtu / sq ft</sup></nobr>'
        );
        $this->v["statCompete"]->addDataType( // stat var 'd'
            'lgt',  
            '<nobr>Lighting <sup class="slBlueDark">kWh / day</sup></nobr>'
        );
        $this->v["statCompete"]->addDataType( // stat var 'e'
            'wtr',  
            '<nobr>Water <sup class="slBlueDark">gallons / sq ft</sup></nobr>'
        );
        $this->v["statCompete"]->addDataType( // stat var 'f'
            'wst',  
            '<nobr>Waste <sup class="slBlueDark">lbs / sq ft</sup></nobr>'
        );
        */
        $this->v["statCompete"]->loadMap();
        return true;
    }

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
        $this->loadManuData();
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
                            if ($m->manu_name == $manu) {
                                $this->v["lightManuName"] = $manu;
                            }
                        }
                    }
                }
            }
        } elseif ($this->v["user"]->hasRole('partner')
            && sizeof($this->v["usrInfo"]->manufacturers) > 0
            && isset($this->v["usrInfo"]->manufacturers[0]->manu_name)) {
            $this->v["lightManuName"] = $this->v["usrInfo"]
                ->manufacturers[0]->manu_name;
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
        $this->v["yourPsIDs"] = $mainManuIDs = $this->getLightManuScoreIDs($manu);
        $this->loadSearchLightManu('Your Customers');
        $this->loadCompareLightManuAvg();
        foreach ($this->v["lgtCompetData"]->dataLegend as $l => $leg) {
            $fld = 'ps_rnk_' . $leg[0];
            if (isset( $this->v["averageRanks"]->{ $fld })) {
                $this->v["lgtCompetData"]->dataLegend[$l][3] = $GLOBALS["SL"]
                    ->mexplodeSize(',', $this->v["averageRanks"]->{ $fld });
            }
        }
        $allManus = $this->getTopLightManuIDs(10, $manu);
        if (sizeof($allManus) > 0) {
            foreach ($allManus as $t => $currManu) {
                $this->v["yourPsIDs"] = $this->getLightManuScoreIDs($currManu);
                $title = 'Customers of Competitor ' . chr(65+$t);
                $scoreMissed = $this->loadSearchLightManu($title);
                if ($scoreMissed > 0) {
                    $ind = sizeof($this->v["lgtCompetData"]->dataLines);
                    unset($this->v["lgtCompetData"]->dataLines[$ind]);
                }
            }
        }

        $this->v["lgtCompetData"]->checkScoresMax();
//echo 'allManus<pre>'; print_r($allManus); echo '</pre>lgtCompetData<pre>'; print_r($this->v["lgtCompetData"]); echo '</pre>'; exit;
        $this->v["yourPsIDs"] = $mainManuIDs;

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
            "->whereIn('ps_id', [" . implode(", ", $this->v["yourPsIDs"]) . "])"
            . "->where('ps_effic_lighting', '>', 0)"
            . "->where('ps_effic_lighting_status', '=', " . $this->v["defCmplt"] . ")"
        );
        $this->v["totCnt"] = sizeof($this->searcher->v["allscores"]);
        $this->v["lgtCompetData"]->addLine($title);
        if ($this->v["totCnt"] > 0) {
            foreach ($this->searcher->v["allscores"] as $i => $ps) {
                $this->v["lgtCompetData"]->addPowerScore($title, $ps, $this->v["defCmplt"]);
            }
            $this->v["lgtCompetData"]->calcScoreAvgs($title);
        }
        $scoreMissed = 0;
        foreach ($this->v["lgtCompetData"]->dataLines as $ind => $data) {
            $ranks = [];
            foreach ($this->v["lgtCompetData"]->dataLegend as $l => $leg) {
                $fld = 'ps_rnk_' . $leg[0];
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

        $chk = RIIManufacturers::where('manu_cnt_flower', '>', 0)
            ->orWhere('manu_cnt_veg', '>', 0)
            ->orWhere('manu_cnt_clone', '>', 0)
            ->orWhere('manu_cnt_mother', '>', 0)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $manu) {
                $cnts[$manu->manu_name] = intVal($manu->manu_cnt_flower)
                    + intVal($manu->manu_cnt_veg)
                    + intVal($manu->manu_cnt_clone)
                    + intVal($manu->manu_cnt_mother);
            }
            arsort($cnts);
            foreach ($cnts as $manuName => $cnt) {
                if (sizeof($ret) < $limit && $manuName != $except) {
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
        $chk = RIIPsLightTypes::where('ps_lg_typ_make', 'LIKE', '%' . $manu . '%')
            ->select('ps_lg_typ_area_id')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $light) {
                if (!in_array($light->ps_lg_typ_area_id, $areaIDs)) {
                    $areaIDs[] = $light->ps_lg_typ_area_id;
                }
            }
        }
        $chk = RIIPsAreas::whereIn('ps_area_id', $areaIDs)
            ->select('ps_area_psid')
            ->get();
        if ($chk && sizeof($chk) > 0) {
            foreach ($chk as $light) {
                if (!in_array($light->ps_area_psid, $scoreIDs)) {
                    $scoreIDs[] = $light->ps_area_psid;
                }
            }
        }
        return $scoreIDs;
    }



}



