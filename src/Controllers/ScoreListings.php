<?php
/**
  * ScoreListings is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the main processes which crunch heavier filters of raw PowerScore data.
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
use App\Models\RIIPSRankings;
use App\Models\RIICompetitors;
use CannabisScore\Controllers\CannabisScoreSearcher;

class ScoreListings
{
    protected $v        = [];
    protected $searcher = null;
    
    public function __construct()
    {
        $this->v["defCmplt"] = 243;
        $this->v["defArch"]  = 364;
        $this->searcher = new CannabisScoreSearcher;
    }
    
    public function getAllPowerScoresPublic($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('random') && intVal($GLOBALS["SL"]->REQ->get('random')) == 1) {
            $randScore = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
                ->where('PsEfficFacility', '>', 0)
                ->where('PsEfficProduction', '>', 0)
                ->where('PsEfficLighting', '>', 0)
                ->where('PsEfficHvac', '>', 0)
                ->inRandomOrder()
                ->first();
            if ($randScore && isset($randScore->PsID)) {
                return '<script type="text/javascript"> setTimeout("window.location=\'/calculated/read-' 
                    . $randScore->PsID . '\'", 1); </script><br /><br /><center>'
                    . $GLOBALS["SL"]->sysOpts["spinner-code"] . '</center>';
            }
        }
        $this->searcher->getSearchFilts();
        //$this->searcher->searchResultsXtra();
        $xtra = "";
        if ($GLOBALS["SL"]->REQ->has('review')) {
            $this->v["fltCmpl"] = 0;
            $xtra = "->whereNotNull('PsNotes')->where('PsNotes', 'NOT LIKE', '')";
        }
        $this->searcher->loadAllScoresPublic($xtra);
        $this->searcher->v["allmores"] = [];
        $this->searcher->v["allights"] = [ 237 => [], 160 => [], 161 => [], 162 => [], 163 => [] ];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $ps) {
                $this->searcher->v["allmores"][$ps->PsID] = [ "areaIDs" => [] ];
                $this->searcher->v["allmores"][$ps->PsID]["areas"] = RIIPSAreas::where('PsAreaPSID', $ps->PsID)
                    ->get();
                if ($this->searcher->v["allmores"][$ps->PsID]["areas"]->isNotEmpty()) {
                    foreach ($this->searcher->v["allmores"][$ps->PsID]["areas"] as $area) {
                        $this->searcher->v["allmores"][$ps->PsID]["areaIDs"][] = $area->PsAreaID;
                    }
                }
                $this->searcher->v["allmores"][$ps->PsID]["lights"] = RIIPSLightTypes::whereIn('PsLgTypAreaID', 
                    $this->searcher->v["allmores"][$ps->PsID]["areaIDs"])
                    ->get();
            }
            if ($GLOBALS["SL"]->REQ->has('lighting') 
                && $this->searcher->v["allmores"][$ps->PsID]["lights"]->isNotEmpty()) {
                foreach ($this->searcher->v["allscores"] as $ps) {
                    foreach ($this->searcher->v["allmores"][$ps->PsID]["areas"] as $a => $area) {
                        foreach ($this->searcher->v["allmores"][$ps->PsID]["lights"] as $l => $lgt) {
                            if ($lgt->PsLgTypAreaID == $area->PsAreaID) {
                                if (!isset($this->searcher->v["allights"][$area->PsAreaType][$area->PsAreaPSID])) {
                                    $this->searcher->v["allights"][$area->PsAreaType][$area->PsAreaPSID] = [
                                        "type" => $GLOBALS["SL"]->def->getVal('PowerScore Light Types', 
                                            $lgt->PsLgTypLight),
                                        "wsft" => ((intVal($area->PsAreaSize) > 0) 
                                            ? ($lgt->PsLgTypCount*$lgt->PsLgTypWattage)/$area->PsAreaSize : '-'),
                                        "days" => intVal($area->PsAreaDaysCycle),
                                        "hour" => intVal($lgt->PsLgTypHours)
                                        ];
                                } else {
                                    $this->searcher->v["allights"][$area->PsAreaType][$area->PsAreaPSID]["type"] .= ', '
                                        . $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $lgt->PsLgTypLight);
                                    if (intVal($area->PsAreaSize) > 0) {
                                        $this->searcher->v["allights"][$area->PsAreaType][$area->PsAreaPSID]["wsft"] 
                                            += ($lgt->PsLgTypCount*$lgt->PsLgTypWattage)/$area->PsAreaSize;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->searcher->getAllscoresAvgFlds();
        $this->v["nID"] = $this->searcher->v["nID"] = $nID;
        $this->searcher->v["isExcel"] = $GLOBALS["SL"]->REQ->has('excel');
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $this->v["showFarmNames"] = $GLOBALS["SL"]->REQ->has('farmNames');
            if ($GLOBALS["SL"]->REQ->has('lighting')) {
                $innerTable = view('vendor.cannabisscore.nodes.170-all-powerscores-lighting', $this->searcher->v)->render();
            } else {
                $innerTable = view('vendor.cannabisscore.nodes.170-all-powerscores-excel', $this->searcher->v)->render();
            }
            $exportFile = 'Compare All';
            if ($this->searcher->v["fltFarm"] == 0) {
                $exportFile .= ' Farms';
            } else {
                $exportFile .= ' ' . $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $this->searcher->v["fltFarm"]);
            }
            if ($this->searcher->v["fltClimate"] != '') {
                $exportFile .= ' Climate Zone ' . $this->searcher->v["fltClimate"];
            }
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        $GLOBALS["SL"]->loadStates();
        $this->searcher->loadCupScoreIDs();
        
        $this->v["allranks"] = [];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $s) {
                $this->v["allranks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', '&fltFarm=0')
                    ->first();
            }
        }
        $this->searcher->v["psFiltChks"] = view('vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v)->render();
        $this->searcher->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v)->render();
        
        $this->printCompareGraphs();
        
        if ($GLOBALS["SL"]->REQ->has('lighting')) {
            return view('vendor.cannabisscore.nodes.170-all-powerscores-lighting', $this->searcher->v)->render();
        }
//echo '<pre>'; print_r($this->searcher->v["allscores"]); echo '</pre>'; exit;
        return view('vendor.cannabisscore.nodes.170-all-powerscores', $this->searcher->v)->render();
    }
    
    
    public function getCultClassicReport()
    {
        $this->v["farms"] = $this->v["psAdded"] = $this->v["namesChecked"] = [];
        $chk = RIICompetitors::where('CmptYear', '=', date("Y"))
            ->where('CmptCompetition', '=', $GLOBALS["SL"]->def->getID('PowerScore Competitions','Cultivation Classic'))
            ->orderBy('CmptName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $farm) {
                $this->loadCultClassicFarmName($i, $farm->CmptName);
            }
        }
        $chk = DB::table('RII_PowerScore')
            ->join('RII_PSForCup', function ($join) {
                $join->on('RII_PSForCup.PsCupPSID', '=', 'RII_PowerScore.PsID')
                    ->where('RII_PSForCup.PsCupCupID', 
                        $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic'));
            })
            ->leftJoin('RII_PSRankings', function ($join) {
                $join->on('RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                    ->where('RII_PSRankings.PsRnkFilters', '&fltFarm=0');
            })
            ->where('RII_PowerScore.PsYear', 'LIKE', (date("Y")-1))
            ->whereNotIn('RII_PowerScore.PsID', $this->v["psAdded"])
            ->orderBy('RII_PowerScore.PsName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                $this->loadCultClassicID($ps);
            }
        }
        $this->v["farmTots"] = [ 0, 0 ];
        if (sizeof($this->v["farms"]) > 0) {
            foreach ($this->v["farms"] as $i => $f) {
                if (isset($this->v["farms"][$i]["ps"]) && isset($this->v["farms"][$i]["ps"]->PsStatus)) {
                    if (in_array($this->v["farms"][$i]["ps"]->PsStatus, [$this->v["defCmplt"], 364])) {
                        $this->v["farmTots"][1]++;
                    } else {
                        $this->v["farmTots"][0]++;
                    }
                }
            }
        }
        //$chk = RIIPowerScore::get();
        //$this->v["entryFarmNames"] = $this->listSimilarNames($chk);
        if ($GLOBALS["SL"]->REQ->has('excel') && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view('vendor.cannabisscore.nodes.744-cult-classic-report-innertable', $this->v)->render();
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, 'CultClassic-PowerScoreReport-' . date("Y-m-d") . '.xls');
        }
        $GLOBALS["SL"]->pageBodyOverflowX();
        return view('vendor.cannabisscore.nodes.744-cult-classic-report', $this->v)->render();
    }
    
    protected function loadCultClassicFarmName($i, $farmName = '')
    {
        $this->v["namesChecked"][] = $farmName;
        $this->v["farms"][$i] = [
            "name" => $farmName,
            "ps"   => [],
            "srch" => []
            ];
        $chk2 = DB::table('RII_PowerScore')
            ->leftJoin('RII_PSRankings', function ($join) {
                $join->on('RII_PSRankings.PsRnkPSID', '=', 'RII_PowerScore.PsID')
                    ->where('RII_PSRankings.PsRnkFilters', '&fltFarm=0');
            })
            ->where('RII_PowerScore.PsName', 'LIKE', $farmName)
            ->where('RII_PowerScore.PsYear', 'LIKE', (date("Y")-1))
            ->whereIn('RII_PowerScore.PsStatus', [$this->v["defCmplt"], 364])
            ->orderBy('RII_PowerScore.PsID', 'desc')
            ->get();
        if ($chk2->isNotEmpty()) {
            foreach ($chk2 as $j => $ps) {
                if ($j == 0) {
                    $this->v["farms"][$i]["ps"] = $ps;
                    $this->v["psAdded"][] = $ps->PsID;
                }
            }
        } else {
            $chk2 = RIIPowerScore::where('PsName', 'LIKE', $farmName)
                ->where('PsStatus', 'LIKE', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
                ->where('PsYear', 'LIKE', (date("Y")-1))
                ->orderBy('PsID', 'desc')
                ->get();
            if ($chk2->isNotEmpty()) {
                foreach ($chk2 as $j => $ps) {
                    if ($j == 0) {
                        $this->v["farms"][$i]["ps"] = $ps;
                        $this->v["psAdded"][] = $ps->PsID;
                    }
                }
            } else {
                $srchs = $GLOBALS["SL"]->parseSearchWords($farmName);
                if (sizeof($srchs) > 0) {
                    foreach ($srchs as $srch) {
                        $chk2 = RIIPowerScore::where('PsName', 'LIKE', '%' . $srch . '%')
                            ->where('PsYear', 'LIKE', (date("Y")-1))
                            ->get();
                        if ($chk2->isNotEmpty()) {
                            foreach ($chk2 as $j => $ps) {
                                if (isset($ps->PsName) && trim($ps->PsName) != '' 
                                    && !isset($this->v["farms"][$i]["srch"][$ps->PsID])) {
                                    $this->v["farms"][$i]["srch"][$ps->PsID] = $ps->PsName;
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    protected function loadCultClassicID($ps)
    {
        if (!isset($ps->PsName) || !in_array($ps->PsName, $this->v["namesChecked"])) {
            $this->v["psAdded"][] = $ps->PsID;
            $this->v["farms"][] = [
                "name" => ((isset($ps->PsName)) ? trim($ps->PsName) : ''),
                "ps"   => $ps,
                "srch" => []
                ];
        }
        return true;
    }
    
    public function getMultiSiteRankings($nID)
    {
        
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
    
    public function getPowerScoresOutliers($nID)
    {
        $this->v["stats"] = [];
        $status = [$GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete')];
        if (!$GLOBALS["SL"]->REQ->has('status') || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') {
            $status[] = $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived');
        }
        $this->v["sizes"] = [375, 376, 377, 378]; // <5,000 sf, 5,000-10,000 sf, 10,000-50,000 sf, 50,000+ sf
        if ($GLOBALS["SL"]->REQ->has('sizes') && trim($GLOBALS["SL"]->REQ->get('sizes')) == 'no') {
            $this->v["sizes"] = [0];
        }
        $this->v["scores"] = DB::table('RII_PowerScore')
            ->join('RII_PSAreas', 'RII_PowerScore.PsID', '=', 'RII_PSAreas.PsAreaPSID')
            ->whereIn('RII_PowerScore.PsStatus', $status)
            ->where('RII_PowerScore.PsTimeType', $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'))
            ->where('RII_PowerScore.PsEfficFacility', '>', 0)
            ->where('RII_PowerScore.PsEfficProduction', '>', 0)
            ->where('RII_PowerScore.PsEfficLighting', '>', 0)
            ->where('RII_PowerScore.PsEfficHvac', '>', 0)
            ->where('RII_PSAreas.PsAreaType', 162) // flower
            ->select('RII_PowerScore.PsID', 'RII_PowerScore.PsCharacterize', 'RII_PowerScore.PsEfficOverall',
                'RII_PowerScore.PsEfficFacility', 'RII_PowerScore.PsEfficProduction', 'RII_PowerScore.PsEfficLighting', 
                'RII_PowerScore.PsEfficHvac', 'RII_PowerScore.PsEfficWater', 'RII_PowerScore.PsEfficWaste', 
                'RII_PowerScore.PsGrams', 'RII_PowerScore.PsKWH', 'RII_PowerScore.PsCounty', 'RII_PowerScore.PsState',
                'RII_PowerScore.PsStatus', 'RII_PSAreas.PsAreaSize')
            ->orderBy('RII_PowerScore.PsID', 'desc')
            ->get();
        if ($this->v["scores"]->isNotEmpty()) {
            foreach ([143, 144, 145] as $type) { // Outdoor, Indoor, Hybrid
                $this->v["stats"][$type] = [];
                foreach ($this->v["sizes"] as $size) {
                    $this->v["stats"][$type][$size] = $dat = [];
                    foreach (['Facility', 'Production', 'Lighting', 'Hvac'] as $scr) { // , 'Water', 'Waste'
                        $this->v["stats"][$type][$size][$scr] = [
                            "cnt" => 0,
                            "med" => 0,
                            "iqr" => 0,
                            "q1"  => 0,
                            "q3"  => 0,
                            "avg" => 0,
                            "sd"  => 0
                        ];
                        $dat = [];
                        foreach ($this->v["scores"] as $ps) {
//echo 'ps: ' . $ps->PsID . ', size: ' . $ps->PsAreaSize . ' ' . $GLOBALS["CUST"]->getSizeDefID($ps->PsAreaSize) . ' aka ' 
// . $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $GLOBALS["CUST"]->getSizeDefID($ps->PsAreaSize)) . ' <br />';
                            if (isset($ps->{ 'PsEffic' . $scr }) && $ps->{ 'PsEffic' . $scr } > 0 && $ps->PsCharacterize == $type
                                && ($size == 0 || $GLOBALS["CUST"]->getSizeDefID($ps->PsAreaSize) == $size)) {
                                $dat[] = $ps->{ 'PsEffic' . $scr };
                            }
                        }
                        if (sizeof($dat) > 4) {
                            sort($dat);
                            $this->v["stats"][$type][$size][$scr]["cnt"] = sizeof($dat);
                            $this->v["stats"][$type][$size][$scr]["med"]  = $dat[floor(sizeof($dat)/2)];
                            $this->v["stats"][$type][$size][$scr]["q1"]  = $dat[floor(sizeof($dat)/4)];
                            $this->v["stats"][$type][$size][$scr]["q3"]  = $dat[floor(sizeof($dat)*(3/4))];
                            $this->v["stats"][$type][$size][$scr]["iqr"]
                                = $this->v["stats"][$type][$size][$scr]["q3"]-$this->v["stats"][$type][$size][$scr]["q1"];
                            $this->v["stats"][$type][$size][$scr]["q1"] -= 1.5*$this->v["stats"][$type][$size][$scr]["iqr"];
                            $this->v["stats"][$type][$size][$scr]["q3"] += 1.5*$this->v["stats"][$type][$size][$scr]["iqr"];
                            $this->v["stats"][$type][$size][$scr]["avg"] = array_sum($dat)/sizeof($dat);
                            $this->v["stats"][$type][$size][$scr]["sd"]  = $GLOBALS["SL"]->arrStandardDeviation($dat);
                        }
                    }
                }
            }
        }
        return view('vendor.cannabisscore.nodes.966-score-outliers', $this->v)->render();
    }
    
    
}