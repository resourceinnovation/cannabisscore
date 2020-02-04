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
use App\Models\RIIPowerscore;
use App\Models\RIIPsRanks;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIManufacturers;
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
                $this->v["lgtCompetData"]->dataLegend[$l][3] = $GLOBALS["SL"]->mexplodeSize(
                    ',', 
                    $this->v["averageRanks"]->{ $fld }
                );
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



