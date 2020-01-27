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
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use CannabisScore\Controllers\CannabisScoreSearcher;
use CannabisScore\Controllers\ScoreReportLightingManu;

class ScoreListings extends ScoreReportLightingManu
{
    
    public function copyUserToSearcher()
    {
        if (isset($this->v["uID"])) {
            $this->searcher->v["uID"] = $this->v["uID"];
            $this->searcher->v["user"] = $this->v["user"];
            if (isset($this->v["usrInfo"])) {
                $this->searcher->v["usrInfo"] = $this->v["usrInfo"];
            }
        }
        return true;
    }
    
    public function getAllPowerScoresPublic($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('random') 
            && intVal($GLOBALS["SL"]->REQ->get('random')) == 1) {
            return $this->getRandomPowerScores();
        }
        $GLOBALS["SL"]->loadStates();
        $this->searcher->v["allListings"] = '';
        $this->searcher->v["fltStateClim"] = '';

        $usrCompany = '';
        if ($this->v["user"] 
            && $this->v["user"]->hasRole('partner')
            && isset($this->v["usrInfo"]) 
            && isset($this->v["usrInfo"]->company)
            && trim($this->v["usrInfo"]->company) != '') {
            $usrCompany = trim($this->searcher->v["usrInfo"]->company);
        }

        $origFltManuLgt = '';
        if ($GLOBALS["SL"]->REQ->has('fltManuLgt')) {
            $origFltManuLgt = trim($GLOBALS["SL"]->REQ->fltManuLgt);
            if ($this->v["isAdmin"] || $origFltManuLgt == $usrCompany) {
                $psidManuLgt = [];
                $this->searcher->addAllManuPSIDs($psidManuLgt);
                if (sizeof($psidManuLgt) == 0) {
                    $origFltManuLgt = '';
                }
            } else {
                $origFltManuLgt = '';
            }
        } elseif ($usrCompany != '') {
            if (isset($GLOBALS["SL"]->x["partnerVersion"])
                && $GLOBALS["SL"]->x["partnerVersion"]) {
                $origFltManuLgt = $usrCompany;
            }
        }

        $this->fakeMultiSite();

        if (in_array($origFltManuLgt, ['', '0'])) {

            $this->searcher->getSearchFilts();
            //$this->searcher->searchResultsXtra();
            $this->searcher->v["allListings"] .= $this->getPowerScoresPublic($nID);

        } else {

            $this->searcher->v["fltManuLgt"] = $origFltManuLgt;
            if ($this->v["user"]->hasRole('partner')) {
                $this->searcher->v["fltManuLgt"] = $this->v["usrInfo"]->company;
            }
            $manus = $this->searcher->getUsrCompanyManus();
            if ($manus && sizeof($manus) > 0) {
                foreach ($manus as $manu) {
                    $this->searcher->getSearchFilts();
                    //$this->searcher->searchResultsXtra();
                    $this->searcher->v["fltManuLgt"] = $manu->manu_id;
                    $this->searcher->v["allListings"] .= '<a target="_blank" '
                        . 'href="/dash/competitive-performance?manu='
                        . urlencode($manu->manu_name) . '"><h4>' . $manu->manu_name 
                        . '</h4></a>' . $this->getPowerScoresPublic($nID);
                }
            }

        }
        $this->v["nID"] = $this->searcher->v["nID"] = $nID;

        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $exportFile = 'Compare All';
            if ($this->searcher->v["fltFarm"] == 0) {
                $exportFile .= ' Farms';
            } else {
                $exportFile .= ' ' . $GLOBALS["SL"]->def->getVal(
                    'PowerScore Farm Types', 
                    $this->searcher->v["fltFarm"]
                );
            }
            if ($this->searcher->v["fltClimate"] != '') {
                $exportFile .= ' Climate Zone ' . $this->searcher->v["fltClimate"];
            }
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($ret, $exportFile);
        }

        $this->searcher->v["manuList"] = RIIManufacturers::orderBy('manu_name', 'asc')
            ->get();
        $this->searcher->v["usrCompanies"] = RIIUserInfo::orderBy('usr_company_name', 'asc')
            ->get();
        $this->searcher->v["psFiltChks"] = view(
            'vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v
        )->render();
        $this->searcher->v["psFilters"] = view(
            'vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v
        )->render();

        return view(
            'vendor.cannabisscore.nodes.170-all-powerscores', 
            $this->searcher->v
        )->render();
    }
    
    protected function getRandomPowerScores()
    {
        $randScore = RIIPowerScore::where('ps_status', $this->v["defCmplt"])
            ->where('ps_effic_facility', '>', 0)
            ->where('ps_effic_production', '>', 0)
            ->inRandomOrder()
            ->first();
        if ($randScore && isset($randScore->ps_id)) {
            return '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'/calculated/read-' 
                . $randScore->ps_id . '\'", 1); </script><br /><br /><center>'
                . $GLOBALS["SL"]->sysOpts["spinner-code"] . '</center>';
        }
        return 'None found.';
    }
    
    protected function getPowerScoresPublic($nID)
    {
        $ret = '';
        $xtra = "";
        if ($GLOBALS["SL"]->REQ->has('review')) {
            $this->v["fltCmpl"] = 0;
            $xtra = "->whereNotNull('ps_notes')->where('ps_notes', 'NOT LIKE', '')";
        }
        $this->searcher->loadAllScoresPublic($xtra);
        $this->searcher->v["allmores"] = [];
        $this->searcher->v["allights"] = [
            237 => [], 
            160 => [],
            161 => [], 
            162 => [], 
            163 => [] 
        ];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $ps) {
                $areaIDs = [];
                $areas = RIIPSAreas::where('ps_area_psid', $ps->ps_id)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $areaIDs[] = $area->ps_area_id;
                    }
                }
                $lgts = RIIPSLightTypes::whereIn('ps_lg_typ_area_id', $areaIDs)
                    ->get();
                $this->searcher->v["allmores"][$ps->ps_id] = [];
                $this->searcher->v["allmores"][$ps->ps_id]["areas"]   = $areas;
                $this->searcher->v["allmores"][$ps->ps_id]["areaIDs"] = $areaIDs;
                $this->searcher->v["allmores"][$ps->ps_id]["lights"]  = $lgts;
            }
            if ($GLOBALS["SL"]->REQ->has('lighting') 
                && $this->searcher->v["allmores"][$ps->ps_id]["lights"]->isNotEmpty()) {
                foreach ($this->searcher->v["allscores"] as $ps) {
                    $this->getAllPowerScoresPublicAreaLights($ps);
                }
            }
        }
        $this->searcher->getAllscoresAvgFlds();
        $this->v["nID"] = $this->searcher->v["nID"] = $nID;
        $this->searcher->v["isExcel"] = $GLOBALS["SL"]->REQ->has('excel');
        if ($this->searcher->v["isExcel"]) {
            $this->v["showFarmNames"] = $GLOBALS["SL"]->REQ->has('farmNames');
            if ($GLOBALS["SL"]->REQ->has('lighting')) {
                $ret .= view(
                    'vendor.cannabisscore.nodes.170-all-powerscores-lighting', 
                    $this->searcher->v
                )->render();
            } else {
                $ret .= view(
                    'vendor.cannabisscore.nodes.170-all-powerscores-excel', 
                    $this->searcher->v
                )->render();
            }
        }
        $this->searcher->loadCupScoreIDs();
        $this->loadAllRanksAllScores();

        if ($GLOBALS["SL"]->REQ->has('lighting')) {
            $ret .= view(
                'vendor.cannabisscore.nodes.170-all-powerscores-lighting', 
                $this->searcher->v
            )->render();
        } else {
            $ret .= view(
                'vendor.cannabisscore.nodes.170-powerscore-listings', 
                $this->searcher->v
            )->render();
        }
        return $ret;
    }
    
    protected function loadAllRanksAllScores()
    {
        $this->v["allranks"] = [];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $s) {
                $this->v["allranks"][$s->ps_id] = RIIPSRankings::where('ps_rnk_psid', $s->ps_id)
                    ->where('ps_rnk_filters', '&fltFarm=0')
                    ->first();
            }
        }
        return true;
    }
    
    protected function getAllPowerScoresPublicAreaLights($ps)
    {
        foreach ($this->searcher->v["allmores"][$ps->ps_id]["areas"] as $a => $area) {
            foreach ($this->searcher->v["allmores"][$ps->ps_id]["lights"] as $l => $lgt) {
                if ($lgt->ps_lg_typ_area_id == $area->ps_area_id) {
                    if (!isset($this->searcher->v["allights"][$area->ps_area_type][$ps->ps_id])) {
                        $lgtDef = $GLOBALS["SL"]->def->getVal(
                            'PowerScore Light Types', 
                            $lgt->ps_lg_typ_light
                        );
                        $wsft = '-';
                        if (intVal($area->ps_area_size) > 0) {
                            $wsft = ($lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage)
                                /$area->ps_area_size;
                        }
                        $this->searcher->v["allights"][$area->ps_area_type][$ps->ps_id] = [
                            "type" => $lgtDef,
                            "wsft" => $wsft,
                            "days" => intVal($area->ps_area_days_cycle),
                            "hour" => intVal($lgt->ps_lg_typ_hours)
                        ];
                    } else {
                        $this->searcher->v["allights"][$area->ps_area_type][$ps->ps_id]["type"] 
                            .= ', ' . $GLOBALS["SL"]->def->getVal(
                                'PowerScore Light Types', 
                                $lgt->ps_lg_typ_light
                            );
                        if (intVal($area->ps_area_size) > 0) {
                            $this->searcher->v["allights"][$area->ps_area_type][$ps->ps_id]["wsft"] 
                                += ($lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage)
                                    /$area->ps_area_size;
                        }
                    }
                }
            }
        }
        return true;
    }
    
    public function getCultClassicReport()
    {
        $this->v["farms"] = $this->v["psAdded"] = $this->v["namesChecked"] = [];
        $defCC = $GLOBALS["SL"]->def->getID(
            'PowerScore Competitions',
            'Cultivation Classic'
        );
        $chk = RIICompetitors::where('cmpt_year', '=', date("Y"))
            ->where('cmpt_competition', '=', $defCC)
            ->orderBy('cmpt_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $farm) {
                $this->loadCultClassicFarmName($i, $farm->cmpt_name);
            }
        }
        $chk = DB::table('rii_powerscore')
            ->join('rii_ps_for_cup', function ($join) {
                $join->on('rii_ps_for_cup.ps_cup_psid', '=', 'rii_powerscore.ps_id')
                    ->where('rii_ps_for_cup.ps_cup_cup_id', $defCC);
            })
            ->leftJoin('rii_ps_rankings', function ($join) {
                $join->on('rii_ps_rankings.ps_rnk_psid', '=', 'rii_powerscore.ps_id')
                    ->where('rii_ps_rankings.ps_rnk_filters', '&fltFarm=0');
            })
            ->where('rii_powerscore.ps_year', 'LIKE', (date("Y")-1))
            ->whereNotIn('rii_powerscore.ps_id', $this->v["psAdded"])
            ->orderBy('rii_powerscore.ps_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                $this->loadCultClassicID($ps);
            }
        }
        $this->v["farmTots"] = [ 0, 0 ];
        if (sizeof($this->v["farms"]) > 0) {
            foreach ($this->v["farms"] as $i => $f) {
                if (isset($this->v["farms"][$i]["ps"]) 
                    && isset($this->v["farms"][$i]["ps"]->ps_status)) {
                    if ($this->v["farms"][$i]["ps"]->ps_status == $this->v["defInc"]) {
                        $this->v["farmTots"][0]++;
                    } else {
                        $this->v["farmTots"][1]++;
                    }
                }
            }
        }
        //$chk = RIIPowerScore::get();
        //$this->v["entryFarmNames"] = $this->listSimilarNames($chk);
        if ($GLOBALS["SL"]->REQ->has('excel') 
            && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.744-cult-classic-report-innertable', 
                $this->v
            )->render();
            $GLOBALS["SL"]->exportExcelOldSchool(
                $innerTable, 
                'CultClassic-PowerScoreReport-' . date("Y-m-d") . '.xls'
            );
        }
        $GLOBALS["SL"]->pageBodyOverflowX();
        return view(
            'vendor.cannabisscore.nodes.744-cult-classic-report', 
            $this->v
        )->render();
    }
    
    protected function loadCultClassicFarmName($i, $farmName = '')
    {
        $this->v["namesChecked"][] = $farmName;
        $this->v["farms"][$i] = [
            "name" => $farmName,
            "ps"   => [],
            "srch" => []
        ];
        $chk2 = DB::table('rii_powerscore')
            ->leftJoin('rii_ps_rankings', function ($join) {
                $join->on('rii_ps_rankings.ps_rnk_psid', '=', 'rii_powerscore.ps_id')
                    ->where('rii_ps_rankings.ps_rnk_filters', '&fltFarm=0');
            })
            ->where('rii_powerscore.ps_name', 'LIKE', $farmName)
            ->where('rii_powerscore.ps_year', 'LIKE', (date("Y")-1))
            ->whereIn('rii_powerscore.ps_status', [$this->v["defCmplt"], 364])
            ->orderBy('rii_powerscore.ps_id', 'desc')
            ->get();
        if ($chk2->isNotEmpty()) {
            foreach ($chk2 as $j => $ps) {
                if ($j == 0) {
                    $this->v["farms"][$i]["ps"] = $ps;
                    $this->v["psAdded"][] = $ps->ps_id;
                }
            }
        } else {
            $chk2 = RIIPowerScore::where('ps_name', 'LIKE', $farmName)
                ->where('ps_status', 'LIKE', $this->v["defInc"])
                ->where('ps_year', 'LIKE', (date("Y")-1))
                ->orderBy('ps_id', 'desc')
                ->get();
            if ($chk2->isNotEmpty()) {
                foreach ($chk2 as $j => $ps) {
                    if ($j == 0) {
                        $this->v["farms"][$i]["ps"] = $ps;
                        $this->v["psAdded"][] = $ps->ps_id;
                    }
                }
            } else {
                $srchs = $GLOBALS["SL"]->parseSearchWords($farmName);
                if (sizeof($srchs) > 0) {
                    foreach ($srchs as $srch) {
                        $chk2 = RIIPowerScore::where('ps_name', 'LIKE', '%' . $srch . '%')
                            ->where('ps_year', 'LIKE', (date("Y")-1))
                            ->get();
                        if ($chk2->isNotEmpty()) {
                            foreach ($chk2 as $j => $ps) {
                                if (isset($ps->ps_name) 
                                    && trim($ps->ps_name) != '' 
                                    && !isset($this->v["farms"][$i]["srch"][$ps->ps_id])) {
                                    $this->v["farms"][$i]["srch"][$ps->ps_id] = $ps->ps_name;
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
        if (!isset($ps->ps_name) || !in_array($ps->ps_name, $this->v["namesChecked"])) {
            $this->v["psAdded"][] = $ps->ps_id;
            $this->v["farms"][] = [
                "name" => ((isset($ps->ps_name)) ? trim($ps->ps_name) : ''),
                "ps"   => $ps,
                "srch" => []
            ];
        }
        return true;
    }
    
    public function getPowerScoresOutliers($nID)
    {
        $this->v["stats"] = $this->v["showStats"] = $this->v["scoresVegSqFtFix"] = [];
        $status = [ $this->v["defCmplt"] ];
        if (!$GLOBALS["SL"]->REQ->has('status') 
            || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') {
            $status[] = $this->v["defArch"];
        }
        $this->v["outlierCols"] = [
            'Facility', 
            'Production', 
            'Hvac', 
            'Lighting', 
            'Flow SqFt/Fix', 
            'Veg SqFt/Fix'
        ]; // , 'Carbon', 'Water', 'Waste'
        $this->v["sizes"] = [
            375, // <5,000 sf
            376, // 5,000-10,000 sf
            431, // 10,000-30,000 sf
            377, // 30,000-50,000 sf
            378  // 50,000+ sf
        ];
        $this->v["farmTypesOrd"] = [
            $this->v["frmTypIn"], 
            $this->v["frmTypGrn"], 
            $this->v["frmTypOut"]
        ];
        if ($GLOBALS["SL"]->REQ->has('sizes') 
            && trim($GLOBALS["SL"]->REQ->get('sizes')) == 'no') {
            $this->v["sizes"] = [0];
        }
        $this->v["scores"] = DB::table('rii_powerscore')
            ->join('rii_ps_areas', 'rii_powerscore.ps_id', '=', 'rii_ps_areas.ps_area_psid')
            ->whereIn('rii_powerscore.ps_status', $status)
            ->where('rii_powerscore.ps_time_type', 
                $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'))
            ->where('rii_powerscore.ps_effic_facility', '>', 0)
            ->where('rii_powerscore.ps_effic_production', '>', 0)
            ->where('rii_ps_areas.ps_area_type', 162) // flower
            ->select(
                'rii_powerscore.ps_id', 
                'rii_powerscore.ps_characterize', 
                'rii_powerscore.ps_effic_overall',
                'rii_powerscore.ps_effic_facility', 
                'rii_powerscore.ps_effic_production', 
                'rii_powerscore.ps_effic_lighting', 
                'rii_powerscore.ps_effic_hvac', 
                'rii_powerscore.ps_effic_carbon', 
                'rii_powerscore.ps_effic_water', 
                'rii_powerscore.ps_effic_waste', 
                'rii_powerscore.ps_effic_facility_status', 
                'rii_powerscore.ps_effic_production_status', 
                'rii_powerscore.ps_effic_lighting_status', 
                'rii_powerscore.ps_effic_hvac_status', 
                'rii_powerscore.ps_effic_carbonStatus', 
                'rii_powerscore.ps_effic_water_status', 
                'rii_powerscore.ps_effic_waste_status', 
                'rii_powerscore.ps_grams', 
                'rii_powerscore.ps_kwh', 
                'rii_powerscore.ps_county', 
                'rii_powerscore.ps_state',
                'rii_powerscore.ps_status', 
                'rii_powerscore.ps_notes', 
                'rii_ps_areas.ps_area_size', 
                'rii_ps_areas.ps_area_sq_ft_per_fix2'
            )
            ->orderBy('rii_powerscore.ps_id', 'desc')
            ->get();
        if ($this->v["scores"]->isNotEmpty()) {

            if ($GLOBALS["SL"]->REQ->has('saveArchives')
                && $GLOBALS["SL"]->REQ->has('goodScores')
                && is_array($GLOBALS["SL"]->REQ->get('goodScores'))) {
                $scoreChecks = [];
                foreach ($this->v["scores"] as $p => $ps) {
                    $scoreChecks[$ps->ps_id] = [];
                }
                foreach ($GLOBALS["SL"]->REQ->get('goodScores') as $score) {
                    list($psID, $scr) = explode('s', str_replace('p', '', $score));
                    $scoreChecks[intVal($psID)][] = $scr;
                }
                foreach ($this->v["scores"] as $p => $ps) {
                    foreach ($this->v["outlierCols"] as $scr) {
                        if (strpos($scr, 'SqFt/Fix') === false) {
                            $status = ((in_array($scr, $scoreChecks[$ps->ps_id]))
                                ? $this->v["defCmplt"] : $this->v["defArch"]);
                            $this->v["scores"][$p]->{ 'ps_effic_' . $scr . '_status' } = $status;
                            $ps = RIIPowerScore::find($ps->ps_id);
                            $ps->{ 'ps_effic_' . $scr . '_status' } = $status;
                            $ps->save();
                        }
                    }
                }
            }

            foreach ($this->v["scores"] as $ps) {
                $this->v["showStats"][$ps->ps_id] = [];
                if (!$GLOBALS["SL"]->REQ->has('status') 
                    || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') {
                    $this->v["showStats"][$ps->ps_id] = $this->v["outlierCols"];
                } else { // hide archived
                    foreach ($this->v["outlierCols"] as $scr) {
                        if (!in_array($scr, ['Flow SqFt/Fix', 'Veg SqFt/Fix'])
                            && $ps->{ 'ps_effic_' . $scr . '_status' } == $this->v["defCmplt"]) {
                            $this->v["showStats"][$ps->ps_id][] = $scr;
                            if ($scr == 'Lighting') {
                                $this->v["showStats"][$ps->ps_id][] = 'Flow SqFt/Fix';
                                $this->v["showStats"][$ps->ps_id][] = 'Veg SqFt/Fix';
                            }
                        }
                    }
                }
                $this->v["scoresVegSqFtFix"][$ps->ps_id] = 0;
                $areaChk = RIIPSAreas::where('ps_area_psid', $ps->ps_id)
                    ->where('ps_area_type', 161) // veg
                    ->select('ps_area_sq_ft_per_fix2')
                    ->first();
                if ($areaChk && isset($areaChk->ps_area_sq_ft_per_fix2)) {
                    $this->v["scoresVegSqFtFix"][$ps->ps_id] = $areaChk->ps_area_sq_ft_per_fix2;
                }
            }

            foreach ($this->v["farmTypesOrd"] as $type) {
                $this->v["stats"][$type] = [];
                foreach ($this->v["sizes"] as $size) {
                    $this->v["stats"][$type][$size] = $dat = [];
                    foreach ($this->v["outlierCols"] as $scr) {
                        $this->v["stats"][$type][$size][$scr] = [
                            "cnt" => 0,
                            "med" => 0,
                            "iqr" => 0,
                            "q1"  => 0,
                            "q3"  => 0,
                            "avg" => 0,
                            "sd"  => 0
                        ];
                        $this->processPowerScoresOutliersRow($ps, $type, $size, $scr);
                    }
                }
            }
        }
        return view(
            'vendor.cannabisscore.nodes.966-score-outliers', 
            $this->v
        )->render();
    }
    
    protected function processPowerScoresOutliersRow($ps, $type, $size, $scr)
    {
        $dat = [];
        foreach ($this->v["scores"] as $ps) {
            $sizeDef = $GLOBALS["CUST"]->getSizeDefID($ps->ps_area_size);
            if ($ps->ps_characterize == $type && ($size == 0 || $sizeDef == $size)) {
                if ($scr == 'Flow SqFt/Fix') {
                    if (in_array('Lighting', $this->v["showStats"][$ps->ps_id])
                        && $ps->ps_area_sq_ft_per_fix2 > 0) {
                        $dat[] = $ps->ps_area_sq_ft_per_fix2;
                    }
                } elseif ($scr == 'Veg SqFt/Fix') {
                    if (in_array('Lighting', $this->v["showStats"][$ps->ps_id])
                        && $this->v["scoresVegSqFtFix"][$ps->ps_id] > 0) {
                        $dat[] = $this->v["scoresVegSqFtFix"][$ps->ps_id];
                    }
                } elseif (in_array($scr, $this->v["showStats"][$ps->ps_id]) 
                    && isset($ps->{ 'ps_effic_' . $scr }) 
                    && $ps->{ 'ps_effic_' . $scr } > 0) {
                    $dat[] = $ps->{ 'ps_effic_' . $scr };
                }
            }
        }

        $cnt = sizeof($dat);
        if ($cnt > 0) {
            sort($dat);
            $this->v["stats"][$type][$size][$scr]["cnt"] = $cnt;
            $this->v["stats"][$type][$size][$scr]["med"] = $dat[floor($cnt/2)];
            $this->v["stats"][$type][$size][$scr]["q1"]  = $dat[floor($cnt/4)];
            $this->v["stats"][$type][$size][$scr]["q3"]  = $dat[floor($cnt*(3/4))];
            $this->v["stats"][$type][$size][$scr]["iqr"]
                = $this->v["stats"][$type][$size][$scr]["q3"]
                    -$this->v["stats"][$type][$size][$scr]["q1"];
            $this->v["stats"][$type][$size][$scr]["q1"] 
                -= 1.5*$this->v["stats"][$type][$size][$scr]["iqr"];
            $this->v["stats"][$type][$size][$scr]["q3"] 
                += 1.5*$this->v["stats"][$type][$size][$scr]["iqr"];
            $this->v["stats"][$type][$size][$scr]["avg"] = array_sum($dat)/$cnt;
            $this->v["stats"][$type][$size][$scr]["sd"] 
                = $GLOBALS["SL"]->arrStandardDeviation($dat);
        }
        return true;
    }
    
}