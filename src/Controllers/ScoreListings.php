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
    private $v        = [];
    private $searcher = null;
    
    public function __construct()
    {
        $this->v["defCmplt"] = 243;
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
            if ($this->v["fltFarm"] == 0) {
                $exportFile .= ' Farms';
            } else {
                $exportFile .= ' ' . $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $this->v["fltFarm"]);
            }
            if ($this->v["fltClimate"] != '') $exportFile .= ' Climate Zone ' . $this->v["fltClimate"];
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        $GLOBALS["SL"]->loadStates();
        $this->searcher->loadCupScoreIDs();
        
        $this->v["allranks"] = [];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $s) {
                $this->v["allranks"][$s->PsID] = RIIPSRankings::where('PsRnkPSID', $s->PsID)
                    ->where('PsRnkFilters', '')
                    ->first();
            }
        }
        $this->searcher->v["psFiltChks"] = view('vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v)->render();
        $this->searcher->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v)->render();
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
                    ->where('RII_PSRankings.PsRnkFilters', '');
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
                    ->where('RII_PSRankings.PsRnkFilters', '');
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
    
}