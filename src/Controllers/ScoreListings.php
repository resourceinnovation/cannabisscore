<?php
/**
  * ScoreListings is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains the main processes which crunch heavier filters of raw PowerScore data.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use Auth;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIPsRankings;
use App\Models\RIICompetitors;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use ResourceInnovation\CannabisScore\Controllers\CannabisScoreSearcher;
use ResourceInnovation\CannabisScore\Controllers\ScoreListingsCore;

class ScoreListings extends ScoreListingsCore
{


    public function getAllPowerScoresPublic($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('random')
            && intVal($GLOBALS["SL"]->REQ->get('random')) == 1) {
            return $this->getRandomPowerScores();
        }
        $this->prepAllPowerScoresPublic($nID);

        if ($GLOBALS["SL"]->REQ->has('excel')
            && ($GLOBALS["SL"]->x["partnerLevel"] > 4
                || !isset($GLOBALS["SL"]->x["officialSet"])
                || !$GLOBALS["SL"]->x["officialSet"])) {
            $this->getAllPowerScoresPublicExcel($nID);
        }

        $this->searcher->v["manuList"] = RIIManufacturers::orderBy('manu_name', 'asc')
            ->get();
        $this->searcher->v["usrCompanies"] = RIIUserInfo::whereNotNull('usr_company_name')
            ->orderBy('usr_company_name', 'asc')
            ->get();
        $this->searcher->v["psFilters"] = view(
            'vendor.cannabisscore.inc-filter-powerscores',
            $this->searcher->v
        )->render();

        $defNew = $GLOBALS["SL"]->def->getID('PowerScore Status', 'New / Unreviewed');
        $this->searcher->v["unreviewedCnt"] = RIIPowerscore::where('ps_status', $defNew)
            ->count();

        $this->getAllPowerScoresPublicFixedHeader($nID);

        return view(
            'vendor.cannabisscore.nodes.170-all-powerscores',
            $this->searcher->v
        )->render();
    }

    public function getAllPowerScoresPublicFixedHeader($nID)
    {
        $nav2 = '<div class="w100" style="background: #FFF; '
            . 'padding: 0px 50px; overflow-y: hidden; height: 72px; '
            . 'border-bottom: 1px #777 solid;">'
            . '<table border=0 class="table w100 bgWht" '
            . 'style="margin: -8px 0px 0px 0px;">'
            . view(
                'vendor.cannabisscore.nodes.170-powerscore-listings-header',
                [
                    "nID"     => $nID,
                    "fixed"   => 'Fixed',
                    "sort"    => $this->searcher->v["sort"],
                    "fltCmpl" => $this->searcher->v["fltCmpl"],
                    "dataSet" => $this->searcher->v["dataSet"],
                    "psSum"   => $this->searcher->v["psSum"],
                    "isExcel" => $this->searcher->v["isExcel"]
                ]
            )->render()
            . '</table></div>';
        $GLOBALS["SL"]->setPageNav2Scroll(460, 550, 700);
        $GLOBALS["SL"]->setPageNav2($nav2, false);
        return true;
    }

    private function prepAllPowerScoresPublic($nID)
    {
        $GLOBALS["SL"]->loadStates();
        $this->searcher->v["fltStateClim"] = '';
        $this->searcher->getSearchFilts();
        $this->searcher->v["allListings"] = '';
        $origFltManuLgt = $this->chkAllPublicFiltsPartner($nID);
        if ($GLOBALS["SL"]->REQ->has('test')) {
            $this->fakeMultiSite();
        }
        if (in_array($origFltManuLgt, ['', '0'])) {
            //$this->searcher->searchResultsXtra();
            $this->searcher->v["allListings"] .= $this->getPowerScoresPublic($nID);
        } else {
            $this->getAllPowerScoresPublicManu($nID);
        }
        $this->v["nID"] = $this->searcher->v["nID"] = $nID;
        return true;
    }

    protected function fakeMultiSite()
    {
        return true;
    }

    private function getAllPowerScoresPublicExcel($nID)
    {
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
        $exportFile = str_replace(' ', '_', $exportFile)
            . '-' . date("Y-m-d") . '.xls';
        $GLOBALS["SL"]->exportExcelOldSchool(
            $this->searcher->v["allListings"],
            $exportFile
        );
    }

    protected function chkAllPublicFiltsPartner($nID)
    {
        $origFltManuLgt = $usrCompany = '';
        if ($this->v["user"]
            && $this->v["user"]->hasRole('partner')
            && isset($this->v["usrInfo"])
            && isset($this->v["usrInfo"]->companyIDs)
            && sizeof($this->v["usrInfo"]->companyIDs) > 0
            && $this->searcher->v["usrInfo"]->companyIDs[0] > 0
            && (!isset($GLOBALS["SL"]->x["officialSet"])
                || !$GLOBALS["SL"]->x["officialSet"])) {
            $usrCompany = trim($this->searcher->v["usrInfo"]->company);
            // $this->searcher->v["usrInfo"]->companyIDs[0]
        }

        if ($GLOBALS["SL"]->REQ->has('fltManuLgt')) {
            $origFltManuLgt = '';
            $chk = RIIManufacturers::find(intVal($GLOBALS["SL"]->REQ->fltManuLgt));
            if ($chk && isset($chk->manu_name)) {
                $origFltManuLgt = $chk->manu_name;
            }
            if (Auth::user()->hasRole('administrator|staff')
                || $origFltManuLgt == $usrCompany) {
                $psidManuLgt = [];
                $this->searcher->addAllManuPSIDs($psidManuLgt);
                if (sizeof($psidManuLgt) == 0) {
                    $origFltManuLgt = '';
                }
            } else {
                $origFltManuLgt = '';
            }
        /* } elseif ($usrCompany != '') {
            if (isset($GLOBALS["SL"]->x["partnerVersion"])
                && $GLOBALS["SL"]->x["partnerVersion"]) {
                $origFltManuLgt = $usrCompany;
            } */
            if ($nID == 799) {
                $this->searcher->v["fltPartner"] = 0;
            }
        } elseif ($nID == 799
            && $GLOBALS["SL"]->x["usrInfo"]
            && sizeof($GLOBALS["SL"]->x["usrInfo"]->companies) > 0
            && sizeof($GLOBALS["SL"]->x["usrInfo"]->companies[0]->manus) > 0
            && isset($GLOBALS["SL"]->x["usrInfo"]->companies[0]->manus[0]->id)) {
            $this->searcher->v["fltPartner"] = 0;
            $this->searcher->v["fltManuLgt"] = intVal(
                $GLOBALS["SL"]->x["usrInfo"]->companies[0]->manus[0]->id
            );
        }
        return $origFltManuLgt;
    }

    protected function getAllPowerScoresPublicManu($nID)
    {
        /*
        $this->searcher->v["fltManuLgt"] = $origFltManuLgt;
        if ($this->v["user"]->hasRole('partner')) {
            $this->searcher->v["fltManuLgt"] = $this->v["usrInfo"]->company;
        }
        */
        $admin = false;
        if ($nID == 1514) {
            if (Auth::user()
                && Auth::user()->hasRole('administrator|staff')) {
                $manus = RIIManufacturers::orderBy('manu_name', 'asc')
                    ->get();
                $admin = true;
            } elseif (isset($GLOBALS["SL"]->x["usrInfo"])
                && sizeof($GLOBALS["SL"]->x["usrInfo"]->companies) > 0
                && sizeof($GLOBALS["SL"]->x["usrInfo"]->companies[0]->manus) > 0) {
                $manuIDs = [];
                foreach ($GLOBALS["SL"]->x["usrInfo"]->companies[0]->manus as $manu) {
                    if (isset($manu->id)) {
                        $manuIDs[] = $manu->id;
                    }
                }
                $manus = RIIManufacturers::whereIn('manu_id', $manuIDs)
                    ->orderBy('manu_name', 'asc')
                    ->get();
            }
        } else {
            $manus = $this->searcher->getUsrCompanyManus();
        }
        if ($manus && sizeof($manus) > 0) {
            foreach ($manus as $manu) {
                $show = true;
                if ($nID == 1514 && $admin) {
                    $cnt = $manu->manu_cnt_flower+$manu->manu_cnt_veg
                        +$manu->manu_cnt_clone+$manu->manu_cnt_mother;
                    $show = ($cnt > 0);
                }
                if ($show) {
                    $this->searcher->getSearchFilts();
                    $this->searcher->v["fltPartner"] = 0;
                    //$this->searcher->searchResultsXtra();
                    $this->searcher->v["fltManuLgt"] = $manu->manu_id;
                    if ($GLOBALS["SL"]->REQ->has('excel')) {
                        $this->searcher->v["allListings"] .= '<tr><td colspan=12 ></td></tr>'
                            . '<tr><td colspan=12 ><b>' . $manu->manu_name . '</b></td></tr>'
                            . $this->getPowerScoresPublic($nID);
                    } else {
                        $this->searcher->v["allListings"] .= '<!-- start manu -->'
                            . '<a target="_blank" href="/dash/competitive-performance?manu='
                            . urlencode($manu->manu_name) . '"><h4>'
                            . $manu->manu_name . '</h4></a>'
                            . $this->getPowerScoresPublic($nID)
                            . '<!-- end manu -->';
                    }
                }
            }
        }
        return true;
    }

    protected function getRandomPowerScores()
    {
        $randScore = RIIPowerscore::where('ps_status', $this->v["defCmplt"])
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
            $xtra = "->whereNotNull('ps_notes')"
                . "->where('ps_notes', 'NOT LIKE', '')";
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
                $areas = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->get();
                if ($areas->isNotEmpty()) {
                    foreach ($areas as $area) {
                        $areaIDs[] = $area->ps_area_id;
                    }
                }
                $lgts = RIIPsLightTypes::whereIn('ps_lg_typ_area_id', $areaIDs)
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
        if ($this->searcher->v["isExcel"]
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $this->v["showFarmNames"] = $GLOBALS["SL"]->REQ->has('farmNames');
            if ($GLOBALS["SL"]->REQ->has('lighting')) {
                return view(
                    'vendor.cannabisscore.nodes.170-all-powerscores-lighting',
                    $this->searcher->v
                )->render();
            } else {
                return view(
                    'vendor.cannabisscore.nodes.170-all-powerscores-excel',
                    $this->searcher->v
                )->render();
            }
        }
        $this->searcher->loadCupScoreIDs();
        $this->loadAllRanksAllScores();
        $this->searcher->loadScoreYearMonths();
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

    public function loadAllRanksAllScores()
    {
        $this->v["allranks"] = [];
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $i => $s) {
                $flt = '&fltFarm=' . $s->ps_characterize;
                $rankings = RIIPsRankings::where('ps_rnk_psid', $s->ps_id)
                    ->where('ps_rnk_filters', $flt)
                    ->first();
                $this->v["allranks"][$s->ps_id] = $rankings;
                if ((!isset($s->ps_effic_overall)
                        || intVal($s->ps_effic_overall) <= 0)
                    && $rankings
                    && isset($rankings->ps_rnk_overall)) {
                    $this->searcher->v["allscores"][$i]->ps_effic_over_similar
                        = $this->v["allranks"][$s->ps_id]->ps_rnk_overall;
                    $chk = RIIPsRankings::where('ps_rnk_psid', $s->ps_id)
                        ->where('ps_rnk_filters', '&fltFarm=0')
                        ->first();
                    if ($chk && isset($chk->ps_rnk_overall)) {
                        $this->searcher->v["allscores"][$i]->ps_effic_overall
                            = $chk->ps_rnk_overall;
                    }
                    $this->searcher->v["allscores"][$i]->save();
                }
            }
        }
        return true;
    }

    /**
     * Print partner breakdown which analyses all lighting manufacturers.
     *
     * @param int $nID
     * @return string
     */
    public function printMakeModelAnalysis($nID = -3)
    {
        if ($GLOBALS["SL"]->x["partnerLevel"] <= 2) {
            return '<a href="https://resourceinnovation.org/joinwithus/"
                target="_blank">More reports are available
                with higher membership levels</a>';
        }
        $pageUrl = '/dash/lighting-manufacturer-report';
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $pageUrl .= '?excel=1';
        }
        $ret = '';
        if (Auth::user()->hasRole('administrator|staff')) {
            //$ret = $GLOBALS["SL"]->chkCache($pageUrl, 'page', 1);
        } else {
            $pageUrl .= '&user=' . Auth::user()->id;
        }
        if (trim($ret) == '' || $GLOBALS["SL"]->REQ->has('refresh')) {
            $this->searcher->v["allListings"] = '';
            $this->getAllPowerScoresPublicManu($nID);
            if ($GLOBALS["SL"]->REQ->has('excel')) {
                $ret = '<tr><td><b>Lighting Manufacturer Report</b></td></tr>'
                    . $this->searcher->v["allListings"];
            } else {
                $ret = '<div class="slCard">'
                    . '<a href="?excel=1" class="btn btn-secondary pull-right">'
                    . '<i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>'
                    . '<h2 class="mB30">Lighting Manufacturer Report</h2>'
                    . '</div>'
                    . str_replace('<!-- start manu -->', '<div class="slCard mT30 mB30">',
                        str_replace('<!-- end manu -->', '</div>',
                            $this->searcher->v["allListings"]));
            }
            if (Auth::user()->hasRole('administrator|staff')) {
                $GLOBALS["SL"]->putCache($pageUrl, $ret, 'page', 1);
            }
        }
//echo $ret; exit;
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $filename = 'PowerScore-Make-Model-Analysis-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($ret, $filename);
        }
        return $ret;
    }

    protected function getAllPowerScoresPublicAreaLights($ps)
    {
        foreach ($this->searcher->v["allmores"][$ps->ps_id]["areas"] as $a => $area) {
            foreach ($this->searcher->v["allmores"][$ps->ps_id]["lights"] as $l => $lgt) {
                if ($lgt->ps_lg_typ_area_id == $area->ps_area_id) {
                    $aType = $area->ps_area_type;
                    if (!isset($this->searcher->v["allights"][$aType][$ps->ps_id])) {
                        $lgtDef = $GLOBALS["SL"]->def->getVal(
                            'PowerScore Light Types',
                            $lgt->ps_lg_typ_light
                        );
                        $wsft = '-';
                        if (intVal($area->ps_area_size) > 0) {
                            $wsft = ($lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage)
                                /$area->ps_area_size;
                        }
                        $this->searcher->v["allights"][$aType][$ps->ps_id] = [
                            "type" => $lgtDef,
                            "wsft" => $wsft,
                            "days" => intVal($area->ps_area_days_cycle),
                            "hour" => intVal($lgt->ps_lg_typ_hours)
                        ];
                    } else {
                        $this->searcher->v["allights"][$aType][$ps->ps_id]["type"]
                            .= ', ' . $GLOBALS["SL"]->def->getVal(
                                'PowerScore Light Types',
                                $lgt->ps_lg_typ_light
                            );
                        if (intVal($area->ps_area_size) > 0) {
                            $this->searcher->v["allights"][$aType][$ps->ps_id]["wsft"]
                                += ($lgt->ps_lg_typ_count*$lgt->ps_lg_typ_wattage)
                                    /$area->ps_area_size;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getCultClassicReportInit($year = 0)
    {
        if ($year == 0) {
            if (intVal(date("m")) > 2) {
                $year = intVal(date("Y"));
            } else {
                $year = intVal(date("Y"))-1;
            }
        }
        if ($GLOBALS["SL"]->REQ->has('year')
            && intVal($GLOBALS["SL"]->REQ->get('year')) > 2016) {
            $year = intVal($GLOBALS["SL"]->REQ->get('year'));
        }
        $this->v["year"] = $year;
        $this->v["farms"]
            = $this->v["psAdded"]
            = $this->v["namesChecked"]
            = [];
        $this->v["defCC"] = $GLOBALS["SL"]->def->getID(
            'PowerScore Competitions',
            'Cultivation Classic'
        );
        $this->v["startDate"] = date(
            "Y-m-j H:i:s",
            mktime(0, 0, 0, 6, 1, ($this->v["year"]-1))
        );
        $this->v["endDate"] = date(
            "Y-m-j H:i:s",
            mktime(23, 59, 59, 5, 31, $this->v["year"])
        );
        return true;
    }

    public function getCultClassicReport()
    {
        $this->getCultClassicReportInit();
        $chk = RIICompetitors::where('cmpt_year', '=', $this->v["year"])
            ->where('cmpt_competition', '=', $this->v["defCC"])
            ->orderBy('cmpt_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $farm) {
                $this->loadCultClassicFarmName($i, $farm->cmpt_name);
            }
        }
        /*
        $chk = DB::table('rii_powerscore')
            ->join('rii_ps_for_cup', function ($join) use ($this->v["defCC"]) {
                $join->on('rii_ps_for_cup.ps_cup_psid', '=', 'rii_powerscore.ps_id')
                    ->where('rii_ps_for_cup.ps_cup_cup_id', $this->v["defCC"]);
            })
            //->leftJoin('rii_ps_rankings', function ($join) {
            //    $join->on('rii_ps_rankings.ps_rnk_psid', '=', 'rii_powerscore.ps_id')
            //        ->where('rii_ps_rankings.ps_rnk_filters', '&fltFarm=0');
            //})
            ->where('rii_powerscore.created_at', '>', $this->v["startDate"] . ' 00:00:00')
            ->whereNotIn('rii_powerscore.ps_id', $this->v["psAdded"])
            ->orderBy('rii_powerscore.ps_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $ps) {
                $this->loadCultClassicID($ps);
            }
        }
        */
        $this->calcCultClassicFarmTots();
        //$chk = RIIPowerscore::get();
        //$this->v["entryFarmNames"] = $this->listSimilarNames($chk);
//echo '<pre>'; print_r($this->v["farms"]); echo '</pre>'; exit;
        if ($GLOBALS["SL"]->REQ->has('excel')
            && intVal($GLOBALS["SL"]->REQ->get('excel')) == 1) {
            $innerTable = view(
                'vendor.cannabisscore-rii.nodes.744-cult-classic-report-innertable',
                $this->v
            )->render();
            $filename = 'CultClassic-PowerScoreReport-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $filename);
        }
        $GLOBALS["SL"]->pageBodyOverflowX();
        return view(
            'vendor.cannabisscore-rii.nodes.744-cult-classic-report',
            $this->v
        )->render();
    }

    protected function calcCultClassicFarmTots()
    {
        $this->v["farmTots"] = [ 0, 0 ];
        if (sizeof($this->v["farms"]) > 0) {
            foreach ($this->v["farms"] as $i => $f) {
                if (isset($this->v["farms"][$i]["ps"])
                    && isset($this->v["farms"][$i]["ps"]->ps_status)) {
                    if ($this->v["farms"][$i]["ps"]->ps_status
                        == $this->v["defInc"]) {
                        $this->v["farmTots"][0]++;
                    } else {
                        $this->v["farmTots"][1]++;
                    }
                }
            }
        }
        return true;
    }

    protected function loadCultClassicFarmName($i, $farmName = '')
    {
        $this->v["namesChecked"][] = $farmName;
        $this->v["farms"][$i] = [
            "name"  => $farmName,
            "ps"    => [],
            "srch"  => [],
            "ranks" => []
        ];
        $chk2 = DB::table('rii_powerscore')
            //->leftJoin('rii_ps_rankings', function ($join) {
            //    $join->on('rii_ps_rankings.ps_rnk_psid', '=', 'rii_powerscore.ps_id')
            //        ->where('rii_ps_rankings.ps_rnk_filters', '&fltFarm=0');
            //})
            ->where('rii_powerscore.ps_name', 'LIKE', $farmName)
            ->where('rii_powerscore.created_at', '>', $this->v["startDate"])
            ->where('rii_powerscore.created_at', '<', $this->v["endDate"])
            //->where('rii_powerscore.ps_year', 'LIKE', (date("Y")-1))
            ->whereIn('rii_powerscore.ps_status', [ $this->v["defCmplt"], 364 ])
            ->orderBy('rii_powerscore.ps_id', 'desc')
            ->get();
        if ($chk2->isNotEmpty()) {
            foreach ($chk2 as $j => $ps) {
                if ($j == 0) {
                    $this->v["farms"][$i]["ps"]    = $ps;
                    $this->v["farms"][$i]["ranks"] = $this->getCurrSimilarRanks($ps);
                    $this->v["psAdded"][]          = $ps->ps_id;
                }
            }
        } else {
            $chk2 = RIIPowerscore::where('ps_name', 'LIKE', $farmName)
                ->where('ps_status', 'LIKE', $this->v["defInc"])
                ->where('created_at', '>', $this->v["startDate"])
                ->where('created_at', '<', $this->v["endDate"])
                //->where('ps_year', 'LIKE', (date("Y")-1))
                ->orderBy('ps_id', 'desc')
                ->get();
            if ($chk2->isNotEmpty()) {
                foreach ($chk2 as $j => $ps) {
                    if ($j == 0) {
                        $this->v["farms"][$i]["ps"]    = $ps;
                        $this->v["farms"][$i]["ranks"] = $this->getCurrSimilarRanks($ps);
                        $this->v["psAdded"][]          = $ps->ps_id;
                    }
                }
            } else {
                $srchs = $GLOBALS["SL"]->parseSearchWords($farmName);
                if (sizeof($srchs) > 0) {
                    foreach ($srchs as $srch) {
                        $chk2 = RIIPowerscore::where('ps_name', 'LIKE', '%' . $srch . '%')
                            //->where('ps_year', 'LIKE', (date("Y")-1))
                            ->where('created_at', '>', $this->v["startDate"])
                            ->where('created_at', '<', $this->v["endDate"])
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
        if (!isset($ps->ps_name)
            || !in_array($ps->ps_name, $this->v["namesChecked"])) {
            $this->v["psAdded"][] = $ps->ps_id;
            $this->v["farms"][] = [
                "name"  => ((isset($ps->ps_name)) ? trim($ps->ps_name) : ''),
                "ps"    => $ps,
                "srch"  => [],
                "ranks" => $this->getCurrSimilarRanks($ps)
            ];
        }
        return true;
    }

    public function getCultClassicMultiYearReport()
    {
        $this->getCultClassicReportInit();
        $chk = RIICompetitors::where('cmpt_competition', '=', $this->v["defCC"])
            ->orderBy('cmpt_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            $status = [ $this->v["defCmplt"], 364 ];
            foreach ($chk as $i => $farm) {
                $name = $farm->cmpt_name;
                $chk2 = DB::table('rii_powerscore')
                    ->where('rii_powerscore.ps_name', 'LIKE', $name)
                    ->whereIn('rii_powerscore.ps_status', $status)
                    ->orderBy('rii_powerscore.ps_year', 'asc')
                    ->orderBy('rii_powerscore.ps_id', 'desc')
                    ->get();
                if ($chk2->isNotEmpty()) {
                    foreach ($chk2 as $j => $ps) {
                        $y = intVal($ps->ps_year);
                        if (!isset($this->v["farms"][$name])) {
                            $this->v["farms"][$name] = [];
                        }
                        if (!isset($this->v["farms"][$name][$y])) {
                            $this->v["farms"][$name][$y] = [
                                "ps"  => $ps,
                                "rnk" => $this->getCurrSimilarRanks($ps)
                            ];
                        }
                    }
                }
            }
        }
        $GLOBALS["SL"]->pageBodyOverflowX();
        return view(
            'vendor.cannabisscore-rii.nodes.1381-cult-classic-multi-year',
            $this->v
        )->render();
    }

    public function getCurrSimilarRanks($ps)
    {
        $flt = '&fltFarm=' . ((isset($ps->ps_characterize)) ? $ps->ps_characterize : 0);
        return RIIPsRankings::where('ps_rnk_filters', $flt)
            ->where('ps_rnk_psid', $ps->ps_id)
            ->first();
    }

    public function getPowerScoresOutliers($nID)
    {
        $this->v["stats"]
            = $this->v["showStats"]
            = $this->v["scoresVegSqFtFix"]
            = [];
        $status = [ $this->v["defCmplt"], 556 ]; // new/unreviewed
        if (!$GLOBALS["SL"]->REQ->has('status')
            || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') {
            $status[] = $this->v["defArch"];
        }
        $this->v["outlierCols"] = [
            ['Facility Electric',       'ps_effic_facility'],
            ['Facility Non-Electric',   'ps_effic_fac_non'],
            ['Production Electric',     'ps_effic_production'],
            ['Production Non-Electric', 'ps_effic_prod_non'],
            ['Water Facility',          'ps_effic_water'],
            ['Water Productivity',      'ps_effic_water_prod'],
            ['Waste Facility',          'ps_effic_waste'],
            ['Waste Productivity',      'ps_effic_waste_prod'],
            ['HVAC',                    'ps_effic_hvac'],
            ['Lighting',                'ps_effic_lighting'],
            ['Flow SqFt/Fix',           'lgt_sqft_fix_flower'],
            ['Veg SqFt/Fix',            'lgt_sqft_fix_veg']
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

        $this->v["catAlerts"] = [];
        foreach ($this->v["farmTypesOrd"] as $type) {
            $this->v["catAlerts"][$type] = [];
            foreach ($this->v["sizes"] as $size) {
                $this->v["catAlerts"][$type][$size] = 0;
            }
        }
        $def = $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past');
        $this->v["scores"] = DB::table('rii_powerscore')
            ->join('rii_ps_areas', 'rii_powerscore.ps_id',
                '=', 'rii_ps_areas.ps_area_psid')
            ->whereIn('rii_powerscore.ps_status', $status)
            ->where('rii_powerscore.ps_time_type', $def)
            //->where('rii_powerscore.ps_effic_facility', '>', 0)
            //->where('rii_powerscore.ps_effic_production', '>', 0)
            ->where('rii_ps_areas.ps_area_type', 162) // flower
            ->select(
                'rii_powerscore.ps_id',
                'rii_powerscore.ps_characterize',
                'rii_powerscore.ps_effic_overall',
                'rii_powerscore.ps_effic_cat_energy',
                'rii_powerscore.ps_effic_cat_water',
                'rii_powerscore.ps_effic_cat_waste',
                'rii_powerscore.ps_effic_fac_all',
                'rii_powerscore.ps_effic_facility',
                'rii_powerscore.ps_effic_fac_non',
                'rii_powerscore.ps_effic_prod_all',
                'rii_powerscore.ps_effic_production',
                'rii_powerscore.ps_effic_prod_non',
                'rii_powerscore.ps_effic_lighting',
                'rii_powerscore.ps_effic_hvac',
                'rii_powerscore.ps_effic_carbon',
                'rii_powerscore.ps_effic_water',
                'rii_powerscore.ps_effic_water_prod',
                'rii_powerscore.ps_effic_waste',
                'rii_powerscore.ps_effic_waste_prod',
                'rii_powerscore.ps_effic_fac_all_status',
                'rii_powerscore.ps_effic_facility_status',
                'rii_powerscore.ps_effic_fac_non_status',
                'rii_powerscore.ps_effic_prod_all_status',
                'rii_powerscore.ps_effic_production_status',
                'rii_powerscore.ps_effic_prod_non_status',
                'rii_powerscore.ps_effic_lighting_status',
                'rii_powerscore.ps_effic_hvac_status',
                'rii_powerscore.ps_effic_carbon_status',
                'rii_powerscore.ps_effic_water_status',
                'rii_powerscore.ps_effic_water_prod_status',
                'rii_powerscore.ps_effic_waste_status',
                'rii_powerscore.ps_effic_waste_prod_status',
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
                $this->getPowerScoresOutliersSave();
            }

            foreach ($this->v["scores"] as $ps) {
                $this->v["showStats"][$ps->ps_id] = [];
                if (!$GLOBALS["SL"]->REQ->has('status')
                    || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') {
                    $this->v["showStats"][$ps->ps_id] = [];
                    foreach ($this->v["outlierCols"] as $scr) {
                        $this->v["showStats"][$ps->ps_id][] = $scr[0];
                    }
                } else { // hide archived
                    foreach ($this->v["outlierCols"] as $scr) {
                        if (!in_array($scr[0], ['Flow SqFt/Fix', 'Veg SqFt/Fix'])
                            && $ps->{ $scr[1] . '_status' }
                                == $this->v["defCmplt"]) {
                            $this->v["showStats"][$ps->ps_id][] = $scr[0];
                            if ($scr[0] == 'Lighting') {
                                $this->v["showStats"][$ps->ps_id][] = 'Flow SqFt/Fix';
                                $this->v["showStats"][$ps->ps_id][] = 'Veg SqFt/Fix';
                            }
                        }
                    }
                }
                $this->v["scoresVegSqFtFix"][$ps->ps_id] = 0;
                $areaChk = RIIPsAreas::where('ps_area_psid', $ps->ps_id)
                    ->where('ps_area_type', 161) // veg
                    ->select('ps_area_sq_ft_per_fix2')
                    ->first();
                if ($areaChk && isset($areaChk->ps_area_sq_ft_per_fix2)) {
                    $this->v["scoresVegSqFtFix"][$ps->ps_id] = $areaChk->ps_area_sq_ft_per_fix2;
                }
                if (!isset($ps->ps_effic_facility_status)
                    && isset($ps->ps_status)
                    && $ps->ps_status == $this->v["defNew"]) {
                    $sizeDef = $GLOBALS["CUST"]->getSizeDefID($ps->ps_area_size);
                    $this->v["catAlerts"][$ps->ps_characterize][$sizeDef]++;
                }
            }

            foreach ($this->v["farmTypesOrd"] as $type) {
                $this->v["stats"][$type] = [];
                foreach ($this->v["sizes"] as $size) {
                    $this->v["stats"][$type][$size] = $dat = [];
                    foreach ($this->v["outlierCols"] as $scr) {
                        $this->v["stats"][$type][$size][$scr[0]] = [
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

    private function getPowerScoresOutliersSave()
    {
        $scoreChecks = [];
        foreach ($this->v["scores"] as $p => $ps) {
            $scoreChecks[$ps->ps_id] = [];
        }
        foreach ($GLOBALS["SL"]->REQ->get('goodScores') as $score) {
            list($psID, $scr) = explode('K', str_replace('P', '', $score));
            $scoreChecks[intVal($psID)][] = $scr;
        }
        foreach ($this->v["scores"] as $p => $ps) {
            $ps = RIIPowerscore::find($ps->ps_id);
            foreach ($this->v["outlierCols"] as $scr) {
                if (strpos($scr[1], 'lgt_sqft_fix') === false) {
                    $status = ((in_array($scr[1], $scoreChecks[$ps->ps_id]))
                        ? $this->v["defCmplt"] : $this->v["defArch"]);
                    $fld = $scr[1] . '_status';
                    $ps->{ $fld } = $this->v["scores"][$p]->{ $fld } = $status;
                }
            }
            if (isset($ps->ps_effic_facility)
                && isset($ps->ps_effic_fac_non)
                && isset($ps->ps_effic_facility_status)
                && isset($ps->ps_effic_fac_non_status)
                && $ps->ps_effic_facility > 0
                && $ps->ps_effic_fac_non > 0
                && $ps->ps_effic_facility_status == $this->v["defCmplt"]
                && $ps->ps_effic_fac_non_status == $this->v["defCmplt"]) {
                $ps->ps_effic_fac_all_status = $this->v["defCmplt"];
            } else {
                $ps->ps_effic_fac_all_status = $this->v["defArch"];
            }
            if (isset($ps->ps_effic_production)
                && isset($ps->ps_effic_prod_non)
                && isset($ps->ps_effic_production_status)
                && isset($ps->ps_effic_prod_non_status)
                && $ps->ps_effic_production > 0
                && $ps->ps_effic_prod_non > 0
                && $ps->ps_effic_production_status == $this->v["defCmplt"]
                && $ps->ps_effic_prod_non_status == $this->v["defCmplt"]) {
                $ps->ps_effic_prod_all_status = $this->v["defCmplt"];
            } else {
                $ps->ps_effic_prod_all_status = $this->v["defArch"];
            }
            $ps->save();
        }
        return true;
    }

    protected function processPowerScoresOutliersRow($ps, $type, $size, $scr)
    {
        $dat = [];
        foreach ($this->v["scores"] as $ps) {
            $sizeDef = $GLOBALS["CUST"]->getSizeDefID($ps->ps_area_size);
            if ($ps->ps_characterize == $type && ($size == 0 || $sizeDef == $size)) {
                if ($scr[0] == 'Flow SqFt/Fix') {
                    if (in_array('Lighting', $this->v["showStats"][$ps->ps_id])
                        && $ps->ps_area_sq_ft_per_fix2 > 0) {
                        $dat[] = $ps->ps_area_sq_ft_per_fix2;
                    }
                } elseif ($scr[0] == 'Veg SqFt/Fix') {
                    if (in_array('Lighting', $this->v["showStats"][$ps->ps_id])
                        && $this->v["scoresVegSqFtFix"][$ps->ps_id] > 0) {
                        $dat[] = $this->v["scoresVegSqFtFix"][$ps->ps_id];
                    }
                } elseif (in_array($scr[0], $this->v["showStats"][$ps->ps_id])
                    && isset($ps->{ $scr[1] })
                    && $ps->{ $scr[1] } > 0) {
                    $dat[] = $ps->{ $scr[1] };
                }
            }
        }

        $cnt = sizeof($dat);
        if ($cnt > 0) {
            sort($dat);
            $this->v["stats"][$type][$size][$scr[0]]["cnt"] = $cnt;
            $this->v["stats"][$type][$size][$scr[0]]["med"] = $dat[floor($cnt/2)];
            $this->v["stats"][$type][$size][$scr[0]]["q1"]  = $dat[floor($cnt/4)];
            $this->v["stats"][$type][$size][$scr[0]]["q3"]  = $dat[floor($cnt*(3/4))];
            $this->v["stats"][$type][$size][$scr[0]]["iqr"]
                = $this->v["stats"][$type][$size][$scr[0]]["q3"]
                    -$this->v["stats"][$type][$size][$scr[0]]["q1"];
            $this->v["stats"][$type][$size][$scr[0]]["q1"]
                -= 1.5*$this->v["stats"][$type][$size][$scr[0]]["iqr"];
            $this->v["stats"][$type][$size][$scr[0]]["q3"]
                += 1.5*$this->v["stats"][$type][$size][$scr[0]]["iqr"];
            $this->v["stats"][$type][$size][$scr[0]]["avg"] = array_sum($dat)/$cnt;
            $this->v["stats"][$type][$size][$scr[0]]["sd"]
                = $GLOBALS["SL"]->arrStandardDeviation($dat);
        }
        return true;
    }

}