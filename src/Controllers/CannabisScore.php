<?php
/**
  * CannabisScore extends ScoreImports extends ScoreAdminMisc extends ScoreReports extends ScoreReports
  * extends ScoreCalcs extends ScoreUtils extends ScorePowerUtilities extends ScoreLightModels
  * extends ScoreVars extends TreeSurvForm. This class contains the majority of
  * Survloop functions which are overwritten, and delegates most of the work.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SLNodeSaves;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIPsRenewables;
use App\Models\RIIPsUtilities;
use App\Models\RIIPsUtiliZips;
use App\Models\RIIPsForCup;
use App\Models\RIIPsRanks;
use App\Models\RIIPsRankings;
use App\Models\RIICompetitors;
use App\Models\RIIPsLicenses;
use App\Models\RIIUserInfo;
use ResourceInnovation\CannabisScore\Controllers\ScoreFormsCustom;
use ResourceInnovation\CannabisScore\Controllers\ScoreListings;
use ResourceInnovation\CannabisScore\Controllers\ScorePrintReport;

class CannabisScore extends ScorePrintReport
{
    protected function customNodePrint(&$curr = null)
    {
        return $this->customNodePrintPowerScore($curr);
    }

    protected function customNodePrintPowerScore(&$curr = null)
    {
        $ret = '';
        $nID = $curr->nID;
        $nIDtxt = $curr->nIDtxt;
        if ($nID == 824) {
            $this->firstPageChecks();
        } elseif ($nID == 393) {
            $areas = $this->sessData->getLoopRowIDs('Growth Stages');
            $GLOBALS["SL"]->pageAJAX .= view(
                'vendor.cannabisscore.nodes.393-area-lighting-ajax',
                [ "areas" => $areas ]
            )->render();
        } elseif (in_array($nID, [74, 396, 1124])) {
            $ret .= $this->printGramForm($nID, $nIDtxt);
        } elseif ($nID == 362) {
            $GLOBALS["SL"]->loadStates();
            $this->getStateUtils();
            $ret .= view(
                'vendor.cannabisscore.nodes.362-utilities-by-state',
                $this->v
            )->render();
        } elseif ($nID == 502) {
            $this->chkUtilityOffers();
            $ret .= view(
                'vendor.cannabisscore.nodes.502-utility-offers',
                $this->v
            )->render();
        } elseif (in_array($nID, [177, 457, 465, 471])) {
            return $this->printReportBlds($nID);
        } elseif (in_array($nID, [209, 432, 440, 448, 1360])) {
            return $this->printReportLgts($nID);
        } elseif ($nID == 1075) {
            $this->loadRenewOther($nID);
        } elseif ($nID == 1328) {
            $this->loadHeatPumpOther($nID);
        } elseif (in_array($nID, [1468, 1590, 1589, 1785, 1794,
            1793, 1798, 1792, 1795, 1799, 1796, 1797])) {
            $this->switchWaterUnits($nID);
        } elseif ($nID == 536) {
            $this->prepFeedbackSkipBtn();
            $GLOBALS["SL"]->pageJAVA .= view(
                'vendor.cannabisscore.nodes.536-feedback-skip-button-java',
                $this->v
            )->render();
        } elseif ($nID == 548) {
            $this->prepFeedbackSkipLnk();
            $ret .= view(
                'vendor.cannabisscore.nodes.548-powerscore-feedback-score-link',
                $this->v
            )->render();
        } elseif ($nID == 851) {
            $this->chkPsLightsDlc($this->coreID);
        } elseif ($nID == 148) { // this should be built-in
            $defNew = $GLOBALS["SL"]->def->getID('PowerScore Status', 'New / Unreviewed');
            $this->sessData->dataSets["powerscore"][0]->ps_status = $defNew;
            $this->sessData->dataSets["powerscore"][0]->save();
            session()->put('PowerScoreOwner', $this->coreID);
            session()->put(
                'PowerScoreOwner' . $this->coreID,
                $this->coreID
            );
            $this->calcCurrSubScores(true);
        } elseif ($nID == 1677) {
            $this->calcCurrSubScores(true);

        } elseif ($nID == 490) {
            if ($GLOBALS["SL"]->REQ->has('recalc')) {
                $this->calcCurrSubScores();
            }
            if (Auth::user()
                && Auth::user()->hasRole('administrator|staff|partner')) {
                $GLOBALS["SL"]->x["indivFilters"] = true;
            }
            $ret .= $this->customPrint490($nID);
        } elseif ($nID == 1008) {
            $ret .= view(
                'vendor.cannabisscore.nodes.1008-powerscore-calculations-mockup',
                $this->v
            )->render();
        } elseif ($nID == 946) {
            $ret .= $this->printPsRankingFilters($nID);
        } elseif (in_array($nID, [878])) { // , 1273
            $this->auditLgtAlerts($nID);
        } elseif (in_array($nID, [1089, 1090, 1091, 1092, 1093])) {
            return $this->printHvacInfoAccord($nID, $nIDtxt);
        } elseif ($nID == 860) {
            return $this->printReportForCompetition($nID);
        } elseif ($nID == 861) {
            return $this->printReportGrowingYear($nID);
        } elseif ($nID == 508) {
            $ret .= $this->printReportUtilRef($nID);
        } elseif ($nID == 1353) {
            return $this->printReportRoomArea($nID);
        } elseif ($nID == 1675) {
            $ret .= $this->reportPsMonths($nID);

        // PowerScore Reporting
        } elseif ($nID == 170) {
            $report = new ScoreListings($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $ret .= $report->getAllPowerScoresPublic($nID);
        } elseif ($nID == 966) {
            $report = new ScoreListings($this->v["uID"], $this->v["user"], $this->v["usrInfo"]);
            $ret .= $report->getPowerScoresOutliers($nID);


        // Misc
        } elseif ($nID == 1276) {
            $this->excelExportMyScores($nID);
        } elseif ($nID == 843) {
            $ret .= $this->printProfileExtraBtns();
        } elseif (in_array($nID, [1723, 1724])) {
            $ret .= $this->printProfileScores($nID);

        }
        return $ret;
    }

    protected function customResponses(&$curr)
    {
        return $this->customResponsesPowerScore($curr);
    }

    protected function customResponsesPowerScore(&$curr)
    {
        if (in_array($curr->nID, [57, 1073])) {
            $curr->clearResponses();
            if (isset($this->sessData->dataSets["powerscore"])
                && isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                $utIDs = RIIPsUtiliZips::where('ps_ut_zp_zip_code',
                        $this->sessData->dataSets["powerscore"][0]->ps_zip_code)
                    ->get();
                if ($utIDs->isNotEmpty()) {
                    $ids = [];
                    foreach ($utIDs as $u) {
                        $ids[] = $u->ps_ut_zp_util_id;
                    }
                    $uts = RIIPsUtilities::whereIn('ps_ut_id', $ids)
                        ->get(); // will be upgrade to check for farm's zip code
                    if ($uts->isNotEmpty()) {
                        foreach ($uts as $i => $ut) {
                            $curr->addTmpResponse($ut->ps_ut_id, $ut->ps_ut_name);
                        }
                    }
                }
            }
            $curr->addTmpResponse(0, 'Other:');
            $curr->dataStore = 'powerscore:ps_source_utility';
            $curr->chkFldOther();
            $curr->dataStore = 'ps_utili_links:ps_ut_lnk_utility_id';
        } elseif (in_array($curr->nID, [1074])) {
            $curr->clearResponses();
            $curr->addTmpResponse(1, 'Generated On-site');
            $curr->addTmpResponse(2, 'PEPCO');
            $curr->addTmpResponse(0, 'Other:');
            $curr->dataStore = 'powerscore:ps_source_utility';
            $curr->chkFldOther();
        } elseif (in_array($curr->nID, [1509, 1508])) {
            $curr->clearResponses();
            for ($i = 0; $i < 12; $i++) {
                $time = mktime(0, 0, 0, date("n")-$i, date("j"), date("Y"));
                $curr->addTmpResponse(date('n', $time), date('F Y', $time));
            }
            $curr->defaultVal = intVal(date("n"))-1;
        } elseif (in_array($curr->nID, [1248])
            && isset($this->sessData->dataSets["ps_areas"])
            && sizeof($this->sessData->dataSets["ps_areas"]) > 0) {
            $curr->clearResponses();
            foreach ($this->v["areaTypes"] as $area => $areaType) {
                if (!in_array($area, ['Dry', 'Mother'])) {
                    foreach ($this->sessData->dataSets["ps_areas"] as $psArea) {
                        if ($psArea->ps_area_type == $areaType
                            && isset($psArea->ps_area_has_stage)
                            && intVal($psArea->ps_area_has_stage) == 1) {
                            $lab = 'Flowering Plants';
                            if ($area == 'Veg') {
                                $lab = 'Vegetating Plants';
                            } elseif ($area == 'Clone') {
                                $lab = 'Clone or Mother Plants';
                            }
                            $curr->addTmpResponse($psArea->getKey(), $lab);
                        }
                    }
                }
            }
        }
        return $curr;
    }

    protected function postNodePublicCustom(&$curr)
    {
        return $this->postNodePublicCustomPowerScore($curr);
    }

    protected function postNodePublicCustomPowerScore(&$curr)
    {
        $nID = $curr->nID;
        if (empty($tmpSubTier)) {
            $tmpSubTier = $this->loadNodeSubTier($nID);
        }

        if (in_array($nID, [47, 1823])) {
            $this->postZipCode($nID);
        } elseif ($nID == 1508) {
            $this->postPsReportingMonth($nID);
        } elseif ($nID == 1835) {
            $this->postPsNotProSetRoom($nID);
        } elseif ($nID == 1075) {
            $this->postRenewOther($nID);
        } elseif ($nID == 1328) {
            $this->postHeatPumpOther($nID);
        } elseif ($nID == 1244) {
            $this->postRoomCnt($nID);
        } elseif ($nID == 1233) {
            $this->postRoomLightCnt($nID);
        } elseif ($nID == 1274) {
            return $this->postRoomLightTypeComplete($nID, $curr->nIDtxt);
        } elseif ($nID == 1292) {
            return $this->postRoomHvacType($nID, $curr->nIDtxt);
        } elseif ($nID == 1083) {
            $this->sessData->refreshDataSets();
        } elseif ($nID == 74) { // dump monthly grams
            $this->postMonthlies($curr->nIDtxt, 'ps_month_grams');
        } elseif ($nID == 70) { // dump monthly energy
            $this->postMonthlies($curr->nIDtxt, 'ps_month_kwh1');
        } elseif ($nID == 949) { // dump monthly green waste pounds
            $this->postMonthlies($curr->nIDtxt, 'ps_month_waste_lbs');
        } elseif ($nID == 1526) {
            $this->saveMonthKwh($nID);
        } elseif ($nID == 1713) {
            $this->saveOtherFuels($nID);
        } elseif ($nID == 1773) {
            return $this->saveWaterStoreAny($nID);
        } elseif (in_array($nID, [57, 1073, 1074])) {
            $foundOther = '';
            for ($i = 0; ($i < 20 && $foundOther == ''); $i++) {
                $fld = 'n' . $curr->nIDtxt . 'fldOther' . $i;
                if ($GLOBALS["SL"]->REQ->has($fld)
                    && trim($GLOBALS["SL"]->REQ->get($fld)) != '') {
                    $foundOther = trim($GLOBALS["SL"]->REQ->get($fld));
                }
            }
            $this->sessData->dataSets["powerscore"][0]->update([
                'ps_source_utility_other' => $foundOther
            ]);
            $this->sessData->dataSets["powerscore"][0]->save();
        } elseif (in_array($nID, [59, 80, 60, 61, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["ps_renewables"])
                && sizeof($this->sessData->dataSets["ps_renewables"]) > 0) {
                foreach ($this->sessData->dataSets["ps_renewables"] as $ind => $row) {
                    if (isset($row->ps_rnw_renewable)
                        && $row->ps_rnw_renewable == $sourceID) {
                        $fld = 'n' . $nID . 'fld';
                        $perc = (($GLOBALS["SL"]->REQ->has($fld))
                            ? intVal($GLOBALS["SL"]->REQ->get($fld)) : 0);
                        $this->sessData->dataSets["ps_renewables"][$ind]->update([
                            'ps_rnw_load_percent' => $perc
                        ]);
                    }
                }
            }
            return true;

        /*
        } elseif ($nID == 914) {
            if (!isset($this->v["manuAdmin"])) {
                $this->v["manuAdmin"] = new ScoreAdminManageManu;
            }
            return $this->v["manuAdmin"]->addManufacturers($nID);
        */
        }
        return false; // false to continue standard post processing
    }

    public function ajaxChecksCustom(Request $request, $type = '')
    {
        return $this->ajaxChecksCustomPowerScore($request, $type);
    }

    protected function ajaxChecksCustomPowerScore(Request $request, $type = '')
    {
        if ($type == 'report-ajax') {
            return $this->ajaxReportRefresh($request);
        } elseif ($type == 'powerscore-rank') {
            return $this->ajaxScorePercentiles();
        } elseif ($type == 'future-look') {
            return $this->ajaxFutureYields();
        } elseif ($type == 'light-search') {
            return $this->ajaxLightSearch($request);
        } elseif ($type == 'adm-score-facility') {
            return $this->saveAdminChangeScoreFacility($request);
        } elseif ($type == 'adm-lgt-edit') {
            return $this->saveAdminLightEdit($request);
        }
        return '';
    }

    // returns an array of overrides for ($currNodeSessionData, ???...
    protected function printNodeSessDataOverride(&$curr)
    {
        return $this->printNodeSessDataOverridePowerScore($curr);
    }

    // returns an array of overrides for ($currNodeSessionData, ???...
    protected function printNodeSessDataOverridePowerScore(&$curr)
    {
        if (sizeof($this->sessData->dataSets) == 0) {
            return [];
        }
        $nID = $curr->nID;
        $nIDtxt = $curr->nIDtxt;
        if ($nID == 49) {
            if (isset($this->sessData->dataSets["ps_farm"])
                && isset($this->sessData->dataSets["ps_farm"][0]->ps_frm_type)) {
                return [$this->sessData->dataSets["ps_farm"][0]->ps_frm_type];
            }
        } elseif (in_array($nID, [864, 865])) {
            if (!isset($this->sessData->dataSets["powerscore"])
                || !isset($this->sessData->dataSets["powerscore"][0]->ps_year)
                || trim($this->sessData->dataSets["powerscore"][0]->ps_year) == '') {
                if (intVal(date("n")) <= 6) {
                    return [intVal(date("Y"))-1];
                } else {
                    return [intVal(date("Y"))];
                }
            }
        } elseif ($nID == 1088) {
            return $this->printNodeSessRoomHvacType($nID, $nIDtxt);
        } elseif (in_array($nID, [57, 1073, 1074])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_source_utility_other)
                && trim($ps->ps_source_utility_other) != '') {
                $GLOBALS["SL"]->pageJAVA .= 'function fillUtilOther() { '
                    . 'for (var i=0; i<20; i++) { '
                    . 'if (document.getElementById("n' . $nID . 'fldOtherID"+i+"")) {'
                        . 'document.getElementById("n' . $nID . 'fldOtherID"+i+"").value="'
                        . str_replace('"', '\\"', $this->sessData
                            ->dataSets["powerscore"][0]->ps_source_utility_other)
                    . '"; } } return true; } setTimeout("fillUtilOther()", 10);';
            }
        } elseif (in_array($nID, [59, 80, 61, 60, 81])) {
            $sourceID = $this->nIDgetRenew($nID);
            if (isset($this->sessData->dataSets["ps_renewables"])
                && sizeof($this->sessData->dataSets["ps_renewables"]) > 0) {
                foreach ($this->sessData->dataSets["ps_renewables"] as $ind => $row) {
                    if (isset($row->ps_rnw_renewable) && $row->ps_rnw_renewable == $sourceID) {
                        $perc = 0;
                        if (isset($row->ps_rnw_load_percent)) {
                            $perc = intVal($row->ps_rnw_load_percent);
                        }
                        return [$perc];
                    }
                }
            }
        } elseif ($nID == 1773) {
            return $this->sessDataWaterStoreAny($nID);
        } elseif (in_array($nID, [1307, 1365, 1336, 1335, 1333, 1334])) {
            if ($curr->sessData < 0.00001) {
                return [0];
            }
        }
        return [];
    }

    public function sendEmailBlurbsCustom($emailBody, $deptID = -3)
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $rankSim = $this->getSimilarStats();
        }
        $dynamos = [
            '[{ PowerScore }]',
            '[{ PowerScore Percentile }]',
            '[{ PowerScore Report Link Similar }]',
            '[{ PowerScore Similar }]',
            '[{ PowerScore Dashboard Similar }]',
            '[{ Production Score }]',
            '[{ PowerScore Total Submissions }]',
            '[{ Zip Code }]',
            '[{ Farm Name }]',
            '[{ Farm Type }]',
            '[{ Partner Name }]',
            '[{ Partner Slug }]'
        ];
        foreach ($dynamos as $dy) {
            if (strpos($emailBody, $dy) !== false) {
                $swap = $dy;
                $dyCore = str_replace('[{ ', '', str_replace(' }]', '', $dy));
                switch ($dy) {
                    case '[{ PowerScore }]':
                    case '[{ PowerScore Percentile }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = round($this->sessData->dataSets["powerscore"][0]
                                    ->ps_effic_overall)
                                . $GLOBALS["SL"]->numSupscript(
                                    round($this->sessData->dataSets["powerscore"][0]
                                        ->ps_effic_overall)) . ' percentile';
                        }
                        break;
                    case '[{ Production Score }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $GLOBALS["SL"]->cnvrtLbs2Grm(
                                $this->sessData->dataSets["powerscore"][0]->ps_effic_production);
                            $swap = $GLOBALS["SL"]->sigFigs((1/$swap), 3);
                        }
                        break;
                    case '[{ PowerScore Total Submissions }]':
                        $chk = RIIPowerscore::where('ps_email', 'NOT LIKE', '')
                            ->get();
                        $swap = $chk->count();
                        break;
                    case '[{ PowerScore Report Link Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $GLOBALS["SL"]->sysOpts["app-url"]
                                . '/calculated/read-' . $this->coreID . '?fltFarm='
                                . $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                            $swap = '<a href="' . $swap . '" target="_blank">' . $swap . '</a>';
                        }
                        break;
                    case '[{ PowerScore Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = round($rankSim->ps_rnk_overall)
                                . $GLOBALS["SL"]->numSupscript(round($rankSim->ps_rnk_overall))
                                . ' percentile';
                        }
                        break;
                    case '[{ PowerScore Dashboard Similar }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $fltDesc = $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                            $fltDesc = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $fltDesc);
                            $fltDesc = str_replace('/', '/ ', strtolower($fltDesc));
                            $swap = view(
                                'vendor.cannabisscore.nodes.490-report-calculations-preview',
                                [
                                    "ps"       => $this->sessData->dataSets["powerscore"][0],
                                    "rank"     => $rankSim,
                                    "filtDesc" => $fltDesc
                                ]
                            )->render();
                        }
                        break;
                    case '[{ Zip Code }]':
                        if (isset($this->sessData->dataSets["powerscore"])
                            && isset($this->sessData->dataSets["powerscore"][0]->ps_zip_code)) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_zip_code;
                        }
                        break;
                    case '[{ Farm Name }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            if (isset($this->sessData->dataSets["powerscore"][0]->ps_name)) {
                                $swap = $this->sessData->dataSets["powerscore"][0]->ps_name;
                            } elseif (isset($this->sessData->dataSets["powerscore"][0]->ps_email)) {
                                $chkEma = User::where('email',
                                        $this->sessData->dataSets["powerscore"][0]->ps_email)
                                    ->first();
                                if ($chkEma && isset($chkEma->name) && trim($chkEma->name) != '') {
                                    $swap = $chkEma->name;
                                }
                            }
                        }
                        if (in_array(trim($swap), ['', $dy])) {
                            $swap = 'Resource Innovator';
                        }
                        break;
                    case '[{ Farm Type }]':
                        if (isset($this->sessData->dataSets["powerscore"])) {
                            $swap = $this->sessData->dataSets["powerscore"][0]->ps_characterize;
                            $swap = $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $swap);
                            $swap = str_replace('/', '/ ', strtolower($swap));
                        }
                        break;
                    case '[{ Partner Name }]':
                        if (isset($this->v["partnerName"])) {
                            $swap = $this->v["partnerName"];
                        } elseif (sizeof($this->v["usrInfo"]->companies) > 0
                            && isset($this->v["usrInfo"]->companies[0]->name)) {
                            $swap = $this->v["usrInfo"]->companies[0]->name;
                        }
                        break;
                    case '[{ Partner Slug }]':
                        if (isset($this->v["referralSlug"])) {
                            $swap = $this->v["referralSlug"];
                        } elseif (sizeof($this->v["usrInfo"]->companies) > 0
                            && isset($this->v["usrInfo"]->companies[0]->slug)) {
                            $swap = $this->v["usrInfo"]->companies[0]->slug;
                        }
                        break;
                }
                $emailBody = str_replace($dy, $swap, $emailBody);
            }
        }
        return $emailBody;
    }

    public function startForPartner(Request $request, $prtnSlug)
    {
        $this->loadPartnerSlug($prtnSlug);
        $this->loadPageVariation($request, 1, 89, '/start-for-');
        return $this->index($request);
    }

}