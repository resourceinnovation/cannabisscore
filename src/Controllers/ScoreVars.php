<?php
/**
  * ScoreVars is the bottom-level extension of the Survloop class, TreeSurvForm.
  * This class initializes the majority of simplest PowerScore-specific variables
  * and prepping various proccesses.
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
use App\Models\RIIPowerscore;
use App\Models\RIIPsOnsite;
use App\Models\RIIPsPageFeedback;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use App\Models\RIIUserCompanies;
use App\Models\RIIUserFacilities;
use App\Models\RIIUserPsPerms;
use App\Models\RIIComplianceMaMonths;
use App\Models\SLUsersRoles;
use App\Models\User;
use ResourceInnovation\CannabisScore\Controllers\ScoreUserInfo;
use ResourceInnovation\CannabisScore\Controllers\ScoreLookups;
use RockHopSoft\Survloop\Controllers\Tree\TreeSurvForm;

class ScoreVars extends TreeSurvForm
{
    protected $statusIncomplete = 0;
    protected $statusComplete   = 0;
    protected $statusArchive    = 0;

    protected $frmTypOut = 0;
    protected $frmTypIn  = 0;
    protected $frmTypGrn = 0;

    // Initializing a bunch of things which are
    // not [yet] automatically determined by the software
    protected function initExtra(Request $request)
    {
        $lookups = new ScoreLookups;
        foreach ($lookups->v as $var => $val) {
            $this->v[$var] = $val;
        }
        $this->loadCommonVars();
        $this->loadScoreUserVars();

        // Establishing Main Navigation Organization, with Node ID# and Section Titles
        $this->majorSections = [];
        if ($GLOBALS["SL"]->treeID == 1) {
            $this->majorSections[] = [971,  'Your Facility',     'active'];
            $this->majorSections[] = [1492, 'Lighting & HVAC',   'active'];
            $this->majorSections[] = [969,  'Annual Totals',     'active'];
            $this->majorSections[] = [970,  'Confirm & Submit',  'active'];

            $this->minorSections   = [ [], [], [], [], [] ];
            $this->minorSections[0][] = [45,   'Your Facility'];
            $this->minorSections[0][] = [1242, 'Growing Spaces'];
            $this->minorSections[0][] = [64,   'Growing Environments'];

            $this->minorSections[1][] = [911,  'Your Lighting'];
            $this->minorSections[1][] = [920,  'Your HVAC'];
            $this->minorSections[1][] = [972,  'Environment Conditions'];

            $this->minorSections[2][] = [1493, 'Water Sources & Usage'];
            $this->minorSections[2][] = [1494, 'Annual Totals'];
            $this->minorSections[2][] = [1495, 'Waste'];

            $this->minorSections[3][] = [67,   'Contact & Options'];
            $this->minorSections[3][] = [848,  'Confirm & Submit'];
        }

        //$GLOBALS["SL"]->addTopNavItem('Calculate PowerScore', '/start/calculator');
        return true;
    }

    protected function tweakExtraSurveyNav()
    {
        if ($this->v["isFlow"]) {
            $this->majorSections   = [];
            $this->majorSections[] = [971,  'Your Facility',         'active'];
            $this->majorSections[] = [1493, 'Water Sources & Usage', 'active'];
            $this->majorSections[] = [65,   'Annual Production',     'active'];
            $this->majorSections[] = [970,  'Confirm & Submit',      'active'];
            $this->minorSections   = [ [], [], [], [] ];
            return true;
        } elseif ($this->v["isGrow"]) {
            $this->majorSections   = [];
            $this->majorSections[] = [971,  'Your Facility',     'active'];
            $this->majorSections[] = [1494, 'Electricity Usage', 'active'];
            $this->majorSections[] = [65,   'Annual Production', 'active'];
            $this->majorSections[] = [970,  'Confirm & Submit',  'active'];
            $this->minorSections   = [ [], [], [], [] ];
            return true;
        }
        return false;
    }

    public function authMinimalInit(Request $request, $currPage = '')
    {
        if ($request->has('pro')) {
            $GLOBALS["SL"]->x["registerNotes"] = view(
                'vendor.cannabisscore.inc-register-pro-license'
            )->render();
        }
        return true;
    }

    protected function loadCommonVars()
    {
        $set = 'PowerScore Farm Types';
        $this->frmTypOut = $GLOBALS["SL"]->def->getID($set, 'Outdoor');
        $this->frmTypIn  = $GLOBALS["SL"]->def->getID($set, 'Indoor');
        $this->frmTypGrn = $GLOBALS["SL"]->def->getID($set, 'Greenhouse/Hybrid/Mixed Light');
        $set = 'PowerScore Status';
        $this->statusIncomplete = $GLOBALS["SL"]->def->getID($set, 'Incomplete');
        $this->statusComplete = $GLOBALS["SL"]->def->getID($set, 'Cannabis Ranked Data Set');
        $this->statusArchive = $GLOBALS["SL"]->def->getID($set, 'Archived');
        return true;
    }

    protected function getNewCurrUserInfo()
    {
        return new ScoreUserInfo;
    }

    protected function loadScoreUserVars()
    {
        $this->v["usrInfo"] = $this->getNewCurrUserInfo();
        if (isset($this->v["uID"]) && $this->v["uID"] > 0) {
            $this->v["usrInfo"]->loadUser($this->v["uID"], $this->v["user"]);
            $GLOBALS["SL"]->x["usrInfo"] = $this->v["usrInfo"];
            if (sizeof($this->v["usrInfo"]->companies) > 0
                && sizeof($this->v["usrInfo"]->companies[0]->manus) > 0) {
                $GLOBALS["SL"]->pageCSS .= ' #admMenu1Tier4 { display: none; } ';
            }
        }
        return true;
    }

    protected function loadMiscUserVars($uID, $user)
    {
        $usrInfo = $this->getNewCurrUserInfo();
        if (isset($uID) && $uID > 0 && $user && isset($user->id)) {
            $usrInfo->loadUser($uID, $user);
        }
        return $usrInfo;
    }

    protected function getUserCompany($userID)
    {
        if ($userID <= 0 && isset($this->v["uID"])) {
            $userID = $this->v["uID"];
        }
        $chk = RIIUserInfo::where('usr_user_id', $userID)
            ->first();
        if ($chk && isset($chk->usr_user_id) && isset($chk->usr_company_name)) {
            return trim($chk->usr_company_name);
        }
        return '';
    }

    public function getPartnerCompany($userID = 0)
    {
        if ($userID <= 0
            && isset($this->v["uID"])
            && $this->v["uID"] > 0
            && isset($this->v["usrInfo"])
            && isset($this->v["usrInfo"]->company)
            && trim($this->v["usrInfo"]->company) != '') {
            return $this->v["usrInfo"]->company;
        }
        return $this->getUserCompany($userID);
    }

    protected function autoLabelClass($nIDtxt = '')
    {
        if ($GLOBALS["SL"]->treeID == 1) {
            return 'txtInfo';
        }
        return 'slBlueDark';
    }

    protected function getAreaAbbr($typeDefID)
    {
        foreach ($this->v["areaTypes"] as $abbr => $defID) {
            if ($defID == $typeDefID) {
                return $abbr;
            }
        }
        return '';
    }

    // Initializing a bunch of things which are
    // not [yet] automatically determined by the software
    protected function loadExtra()
    {
        if (isset($this->sessData->dataSets["powerscore"])
            && sizeof($this->sessData->dataSets["powerscore"]) == 1) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_is_pro) && intVal($ps->ps_is_pro) == 1) {
                $this->v["isPro"] = true;
            } elseif (isset($ps->ps_is_flow) && intVal($ps->ps_is_flow) == 1) {
                $this->v["isFlow"] = true;
            } else {
                $this->v["isGrow"] = true;
            }
        }
        if ($this->treeID == 1) {
            // these are supposed to be auto-generated by survey data structures :(
            if (isset($this->sessData->dataSets["powerscore"])
                && $this->coreID > 0) {
                $this->tweakExtraSurveyNav();
                if (!isset($this->sessData->dataSets["ps_onsite"])) {
                    $rec = new RIIPsOnsite;
                    $rec->ps_on_psid = $this->coreID;
                    $rec->save();
                    $this->sessData->dataSets["ps_onsite"] = [ $rec ];
                    $this->sessData->addToMap(
                        'powerscore',
                        $this->coreID,
                        0,
                        'ps_onsite',
                        $rec->ps_on_id,
                        0
                    );
                }
                if (!isset($this->sessData->dataSets["ps_page_feedback"])) {
                    $rec = new RIIPsPageFeedback;
                    $rec->ps_pag_feed_psid = $this->coreID;
                    $rec->save();
                    $this->sessData->dataSets["ps_page_feedback"] = [ $rec ];
                    $this->sessData->addToMap(
                        'powerscore',
                        $this->coreID,
                        0,
                        'ps_page_feedback',
                        $rec->ps_pag_feed_id,
                        0
                    );
                }
                $this->loadExtraLinkPartner();
            }
            $this->sessData->dataSets["ps_monthly"] = $this->sortMonths();
        } elseif ($this->treeID == 71 && $this->coreID > 0) {
            $this->checkComplianceMonths();
        }

        if (!session()->has('PowerScoreChecks')
            || $GLOBALS["SL"]->REQ->has('refresh')) {
            $chk = RIIPowerscore::where('ps_submission_progress', 'LIKE', '147')
                    // redirection page
                ->where('ps_status', '=', $this->statusIncomplete)
                ->update([ 'ps_status' => $this->v["defCmplt"] ]);
            $chk = RIIPowerscore::where('ps_zip_code', 'NOT LIKE', '')
                ->whereNull('ps_climate_label')
                ->get();
            if ($chk->isNotEmpty()) {
                $GLOBALS["SL"]->loadStates();
                foreach ($chk as $score) {
                    $zipRow = $GLOBALS["SL"]->states->getZipRow($score->ps_zip_code);
                    $score->ps_ashrae = $GLOBALS["SL"]->states->getAshrae($zipRow);
                    if (!isset($score->ps_state) || trim($score->ps_state) == '') {
                        if ($zipRow && isset($zipRow->zip_zip)) {
                            $score->ps_state  = $zipRow->zip_state;
                            $score->ps_county = $zipRow->zip_county;
                        }
                    }
                    $score->ps_climate_label = $GLOBALS["SL"]->states
                        ->getAshraeZoneLabel($score->ps_ashrae);
                    $score->save();
                }
            }
            session()->put('PowerScoreChecks', true);
        }
        return true;
    }

    protected function loadPartnerSlug($slug)
    {
        $this->v["referralSlug"] = $slug;
        $this->v["referralType"] = $this->v["partnerName"] = '';
        $this->v["companyRec"] = RIIUserCompanies::where('usr_com_slug', 'LIKE', $slug)
            ->first();
        if ($this->v["companyRec"] && isset($this->v["companyRec"]->usr_com_id)) {
            $this->v["referralType"] = 'company';
            $this->v["partnerName"]  = $this->v["companyRec"]->usr_com_name;
        } else {
            $this->v["facilityRec"] = $f = RIIUserFacilities::where('usr_fac_slug', 'LIKE', $slug)
                ->first();
            if ($f && isset($f->usr_fac_id)) {
                $this->v["referralType"] = 'facility';
                $this->v["partnerName"]  = $f->usr_fac_name;
                $perm = RIIUserPsPerms::where('usr_perm_facility_id', $f->usr_fac_id)
                    ->where('usr_perm_company_id', '>', 0)
                    ->first();
                if ($perm && isset($perm->usr_perm_company_id)) {
                    $com = RIIUserCompanies::find($perm->usr_perm_company_id);
                    if ($com && isset($com->usr_com_id)) {
                        $this->v["partnerName"] = $com->usr_com_name
                            . ' (' . $this->v["partnerName"] . ')';
                    }
                }
            } else {
                $this->v["partnerRec"] = RIIUserInfo::where('usr_referral_slug', 'LIKE', $slug)
                    ->first();
                if ($this->v["partnerRec"] && isset($this->v["partnerRec"]->usr_id)) {
                    $this->v["referralType"] = 'partner';
                    $this->v["partnerName"]  = $this->v["partnerRec"]->usr_company_name;
                }
            }
        }
//echo 'loadPartnerSlug(' . $slug . ', partnerName: ' . $this->v["partnerName"] . '<br />';
        return true;
    }

    protected function loadExtraLinkPartner()
    {
        if ($GLOBALS["SL"]->REQ->has('partner')
            && trim($GLOBALS["SL"]->REQ->get('partner')) != '') {
            $slug = trim($GLOBALS["SL"]->REQ->get('partner'));
            $this->loadPartnerSlug($slug);

            if (isset($this->v["referralType"]) && trim($this->v["referralType"]) != '') {
                $found = false;
                if (!isset($this->sessData->dataSets["user_ps_perms"])) {
                    $this->sessData->dataSets["user_ps_perms"] = [];
                }
                if (sizeof($this->sessData->dataSets["user_ps_perms"]) > 0) {
                    foreach ($this->sessData->dataSets["user_ps_perms"] as $perm) {
                        if (isset($perm->usr_perm_user_id)
                            && intVal($perm->usr_perm_user_id) == $id) {
                            $found = true;

                        }
                    }
                }
                if (!$found) {
                    $defOwn = $GLOBALS["SL"]->def->getID('Permissions', 'Own');
                    $perm = new RIIUserPsPerms;
                    $perm->usr_perm_psid = $this->coreID;
                    $perm->usr_perm_permissions = $defOwn;
                    if ($this->v["referralType"] == 'company'
                        && isset($this->v["companyRec"]->usr_com_id)) {
                        $perm->usr_perm_company_id = $this->v["companyRec"]->usr_com_id;
                    } elseif ($this->v["referralType"] == 'facility'
                        && isset($this->v["facilityRec"]->usr_fac_id)) {
                        $perm->usr_perm_facility_id = $this->v["facilityRec"]->usr_fac_id;
                    } elseif ($this->v["referralType"] == 'partner'
                        && isset($this->v["partnerRec"]->usr_id)) {
                        $perm->usr_perm_user_id = $this->v["partnerRec"]->usr_id;
                    }
                    $perm->save();
                    $this->sessData->dataSets["user_ps_perms"][] = $perm;
                }
            }
        }
        return true;
    }

    public function chkCoreRecEmpty($coreID = -3, $coreRec = NULL)
    {
        if ($this->treeID == 1) {
            if ($coreID <= 0) {
                $coreID = $this->coreID;
            }
            if (!$coreRec && $coreID > 0) {
                $coreRec = RIIPowerscore::find($coreID);
            }
            if (!$coreRec) {
                return false;
            }
            if (!isset($coreRec->ps_submission_progress)
                || intVal($coreRec->ps_submission_progress) <= 0) {
                return true;
            }
            if (!isset($coreRec->ps_zip_code)
                || trim($coreRec->ps_zip_code) == '') {
                return true;
            }
        }
        return false;
    }

    protected function recordIsIncomplete($coreTbl, $coreID, $coreRec = NULL)
    {
        if ($coreID > 0) {
            if ($coreTbl == 'powerscore') {
                if (!isset($coreRec->ps_id)) {
                    $coreRec = RIIPowerscore::find($coreID);
                }
                return (!isset($coreRec->ps_status)
                    || $coreRec->ps_status == $this->statusIncomplete);
            }
        }
        return false;
    }

    public function tblsInPackage()
    {
        if ($this->dbID == 1) {
            return [ 'ps_utilities', 'ps_utili_zips' ];
        }
        return [];
    }

    public function getStageNick($defID)
    {
        $defSet = 'PowerScore Growth Stages';
        switch ($defID) {
            case $GLOBALS["SL"]->def->getID($defSet, 'Mother Plants'):
                return 'Mother';
            case $GLOBALS["SL"]->def->getID($defSet, 'Clone or Mother Plants'):
                return 'Clone';
            case $GLOBALS["SL"]->def->getID($defSet, 'Vegetating Plants'):
                return 'Veg';
            case $GLOBALS["SL"]->def->getID($defSet, 'Flowering Plants'):
                return 'Flower';
            case $GLOBALS["SL"]->def->getID($defSet, 'Drying/Curing'):
                return 'Dry';
        }
        return '';
    }

    protected function getAreaIdTypeName($areaID)
    {
        $area = $this->sessData->getRowById('ps_areas', $areaID);
        if ($area && isset($area->ps_area_type)) {
            return $GLOBALS["SL"]->def->getVal(
                'PowerScore Growth Stages',
                $area->ps_area_type
            );
        }
        return '';
    }

    protected function xmlAccess()
    {
        if (isset($this->v["user"])
            && $this->v["user"]
            && $this->v["user"]->hasRole('administrator|staff')) {
            return true;
        }
        return false;
    }

    protected function xmlAllAccess()
    {
        if (isset($this->v["user"])
            && $this->v["user"]
            && $this->v["user"]->hasRole('administrator|staff')) {
            return true;
        }
        return false;
    }

    protected function allTechEmpty()
    {
        $ret = [
            141 => 0, // medical
            142 => 0  // recreational
        ];
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) {
            $ret[$fld] = 0;
        }
        foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) {
            $ret[$fld] = 0;
        }
        return $ret;
    }

    protected function loadManufactIDs()
    {
        if (!isset($this->v["manufacts"])) {
            $this->v["manufacts"] = [];
            $chk = RIIManufacturers::get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $manu) {
                    $this->v["manufacts"][$manu->manu_id] = $manu->manu_name;
                }
            }
        }
        asort($this->v["manufacts"]);
        return $this->v["manufacts"];
    }

    protected function convertLightScoreType2ImportType($scoreType = 0)
    {
        $defSet = 'PowerScore Light Types';
        switch (intVal($scoreType)) {
            case $GLOBALS["SL"]->def->getID($defSet, 'HID (double-ended HPS)'):
            case $GLOBALS["SL"]->def->getID($defSet, 'HID (single-ended HPS)'):
                return ['Double Ended HPS', 'Single Ended HPS', 'HID', 'HPS'];
            case $GLOBALS["SL"]->def->getID($defSet, 'HID (double-ended MH)'):
            case $GLOBALS["SL"]->def->getID($defSet, 'HID (single-ended MH)'):
                return ['MH', 'MH/HPS Lamps'];
            case $GLOBALS["SL"]->def->getID($defSet, 'CMH'):
                return ['Ceramic Metal Halide'];
            case $GLOBALS["SL"]->def->getID($defSet, 'Fluorescent'):
                return ['Fluorescent', 'Fluorescent + Halogen', 'Fluorescent Induction'];
            case $GLOBALS["SL"]->def->getID($defSet, 'LED'):
                return ['LED'];
        }
        return [];
    }

    protected function convertLightImportType2ScoreType($importType = '')
    {
        $defSet = 'PowerScore Light Types';
        switch (trim($importType)) {
            case 'Double Ended HPS':
            case 'HID':
            case 'HPS':
                return $GLOBALS["SL"]->def->getID($defSet, 'HID (double-ended HPS)');
            case 'Single Ended HPS':
                return $GLOBALS["SL"]->def->getID($defSet, 'HID (single-ended HPS)');
            case 'MH':
                return $GLOBALS["SL"]->def->getID($defSet, 'HID (double-ended MH)');
            case 'Ceramic Metal Halide':
                return $GLOBALS["SL"]->def->getID($defSet, 'CMH');
            case 'Fluorescent':
            case 'Fluorescent + Halogen':
            case 'Fluorescent Induction':
                return $GLOBALS["SL"]->def->getID($defSet, 'Fluorescent');
            case 'LED':
                return $GLOBALS["SL"]->def->getID($defSet, 'LED');
            case 'MH/HPS Lamps':
            case 'CFL':
            case 'Plasma':
        }
        return 0;
    }

    protected function loadLightImportTypeConverts()
    {
        $this->v["lightImportTypeConvert"] = [];
        $chk = DB::table('rii_light_models')
            ->distinct('lgt_mod_tech')
            ->select('lgt_mod_tech')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $mod) {
                $this->v["lightImportTypeConvert"][$mod->lgt_mod_tech]
                    = $this->convertLightImportType2ScoreType($mod->lgt_mod_tech);
            }
        }
        return $this->v["lightImportTypeConvert"];
    }

    protected function checkComplianceMonths()
    {
        $t = "compliance_ma";
        if (isset($this->sessData->dataSets[$t])
            && !isset($this->sessData->dataSets[$t][0]->com_ma_year)) {
            $this->sessData->dataSets[$t][0]->com_ma_year = intVal(date("Y"));
        }
        if (isset($this->sessData->dataSets[$t])
            && !isset($this->sessData->dataSets[$t][0]->com_ma_start_month)) {
            $this->sessData->dataSets[$t][0]->com_ma_start_month = intVal(date("n"));
        }
        if ($GLOBALS["SL"]->REQ->has('go')
            && trim($GLOBALS["SL"]->REQ->get('go')) == 'pro') {
            $this->sessData->dataSets[$t][0]->com_ma_go_pro = 1;
        } elseif (!isset($this->sessData->dataSets["compliance_ma"][0]->com_ma_go_pro)) {
            $this->sessData->dataSets[$t][0]->com_ma_go_pro = 0;
        }
        $this->sessData->dataSets[$t][0]->save();
        if (!isset($this->sessData->dataSets["compliance_ma_months"])
            || sizeof($this->sessData->dataSets["compliance_ma_months"]) == 0) {
            $this->sessData->dataSets["compliance_ma_months"] = [];
            for ($m = 1; $m <= 12; $m++) {
                $new = new RIIComplianceMaMonths;
                $new->com_ma_month_com_ma_id = $this->coreID;
                $new->com_ma_month_month     = $m;
                $new->save();
                $this->sessData->dataSets["compliance_ma_months"][] = $new;
            }
        }
        $tmp = [];
        foreach ($this->sessData->dataSets["compliance_ma_months"] as $month) {
            $tmp[($month->com_ma_month_month-1)] = $month;
        }
        $this->sessData->dataSets["compliance_ma_months"] = $tmp;
        return true;
    }

    protected function chkPartnerExpire()
    {
        if ($this->isStaffOrAdmin()) {
            return false;
        }
        if (isset($this->v["usrInfo"]) && $this->v["usrInfo"]->isExpired) {
            echo '<script type="text/javascript"> setTimeout('
                . '"window.location=\'/membership-expired\'", 10); </script>';
            exit;
        }
        return false;
    }

}
