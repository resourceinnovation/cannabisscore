<?php
/**
  * ScoreAdminMisc is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which run secondary system functions
  * which only staff have access to.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Auth;
use App\Models\RIIPowerscore;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use App\Models\RIIUserCompanies;
use App\Models\RIIUserFacilities;
use App\Models\RIIUserPsPerms;
use App\Models\SLUsersRoles;
use App\Models\User;
use CannabisScore\Controllers\ScoreUserInfo;

class ScoreAdminManageManu
{
    // information to be passed to Views, and/or needed by dispersed functions
    protected $v = [];

    public function __construct()
    {
        $this->v["manuSrchWords"] = [];
        if ($GLOBALS["SL"]->REQ->has('manuSrch')) {
            $this->v["manuSrchWords"] = $GLOBALS["SL"]->mexplode(' ', 
                strtolower($GLOBALS["SL"]->REQ->manuSrch));
        }
        if (sizeof($this->v["manuSrchWords"]) > 0) {
            $this->v["manus"] = RIIManufacturers::orderBy('manu_name', 'asc')
                ->where('manu_name', 'LIKE', '%' . $GLOBALS["SL"]->REQ->manuSrch . '%')
                ->get();
        } else {
            $this->v["manus"] = RIIManufacturers::orderBy('manu_name', 'asc')
                ->get();
        }
        $this->v["isAdmin"] = (Auth::user() && Auth::user()->hasRole('administrator|staff'));
    }
    
    /**
     * Print admin overview tools to manage lighting manufacturers.
     *
     * @param int $nID
     * @return string
     */
    public function printMgmtManufacturers($nID = -3)
    {
        $this->cntManufacturerAdoption();
        if ($GLOBALS["SL"]->REQ->has('excel')
            && $GLOBALS["SL"]->x["partnerLevel"] > 4) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.914-manage-manufacturers-adoption-excel', 
                $this->v
            )->render();
            $filename = 'PowerScore_Manufacturer_Adoption-' . date("ymd") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $filename);
            exit;
        }
        $this->v["addManusForm"] = '<div class="p10"></div>';
        if ($this->v["isAdmin"]) {
            $addForm = '<form name="companyName" action="?add=1" method="post">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <div class="row"><div class="col-8">
                <div style="margin: -43px 0px 20px 160px;">One per line.</div>
                <textarea class="form-control w100" name="addManu"></textarea>
                </div><div class="col-4">
                <input type="submit" value="Add All" autocomplete="off"
                    class="btn btn-primary btn-block">
                </div></div></form>';
            $this->v["addManusForm"] = '<div class="pT15 pB30">'
                . $GLOBALS["SL"]->printAccordian(
                    'Add Manufacturers',
                    $addForm,
                    false,
                    false,
                    'text'
                ) . '</div>';
        }
        return view(
            'vendor.cannabisscore.nodes.914-manage-manufacturers', 
            $this->v
        )->render();
    }

    /**
     * Print admin overview tools to manage partners.
     *
     * @param int $nID
     * @return string
     */
    public function printMgmtPartners($nID = -3)
    {
        $this->v["isEditing"] = false;
        $this->v["editPartner"] = null;
        if ($GLOBALS["SL"]->REQ->has('addPartnerManu')
            && intVal($GLOBALS["SL"]->REQ->addPartnerManu) == 1) {
            $this->savePartnerInvite();
        } elseif ($GLOBALS["SL"]->REQ->has('edit')
            && intVal($GLOBALS["SL"]->REQ->edit) > 0) {
            $this->v["editPartner"] = new ScoreUserInfo;
            $this->v["editPartner"]->loadInvite(intVal($GLOBALS["SL"]->REQ->edit));
            if ($GLOBALS["SL"]->REQ->has('save')) {
                $this->savePartnerInvite();
                $this->v["editPartner"] = null;
            } else {
                $this->v["isEditing"] = true;
            }
        }
        if ($this->v["editPartner"] === null) {
            $this->v["editPartner"] = new ScoreUserInfo;
        }
        $this->loadPartnerUsers();
        $this->loadPartnerInvites();
        $GLOBALS["SL"]->pageAJAX .= view(
            'vendor.cannabisscore.nodes.915-manage-partners-edit-ajax'
        )->render();
        return view(
            'vendor.cannabisscore.nodes.915-manage-partners-edit', 
            $this->v
        )->render() . view(
            'vendor.cannabisscore.nodes.915-manage-partners', 
            $this->v
        )->render();
    }

    /**
     * Print admin overview tools to manage partner 
     * companies and their facilities.
     *
     * @param int $nID
     * @return string
     */
    public function printMgmtCompanyFacs($nID = -3)
    {
        $this->v["facLimit"]    = 10;
        $this->v["editPartner"] = null;
        $this->v["isEditing"]   = false;

        if ($GLOBALS["SL"]->REQ->has('save')
            && intVal($GLOBALS["SL"]->REQ->save) == 1) {
            $this->saveCompanyEdit();
        }

        if ($GLOBALS["SL"]->REQ->has('edit')
            && intVal($GLOBALS["SL"]->REQ->edit) > 0
            && !$GLOBALS["SL"]->REQ->has('save')) {
            $this->v["isEditing"] = true;
            $com = RIIUserCompanies::find(intVal($GLOBALS["SL"]->REQ->edit));
            $this->v["editPartner"] = new ScoreUserCompanies($com);
        } else {
            $this->v["editPartner"] = new ScoreUserCompanies;
        }

        $this->v["companies"] = [];
        $chk = RIIUserCompanies::whereNotNull('usr_com_name')
            ->orderBy('usr_com_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $com) {
                $this->v["companies"][] = new ScoreUserCompanies($com);
            }
        }
        $this->v["manufacts"] = $GLOBALS["CUST"]->loadManufactIDs();
//echo '<pre>'; print_r($this->v["companies"]); echo '</pre>'; exit;
        $GLOBALS["SL"]->pageAJAX .= view(
            'vendor.cannabisscore.nodes.1560-company-edit-ajax', 
            $this->v
        )->render() . view(
            'vendor.cannabisscore.nodes.1560-facilities-edit-ajax', 
            $this->v
        )->render();
        return view(
            'vendor.cannabisscore.nodes.1560-company-edit', 
            $this->v
        )->render() . view(
            'vendor.cannabisscore.nodes.1560-manage-company-facilities', 
            $this->v
        )->render();
    }
    
    /**
     * Update partner company details.
     *
     * @return boolean
     */
    protected function saveCompanyEdit()
    {
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $defWrt = $GLOBALS["SL"]->def->getID('Permissions', 'Write');

        $com = new RIIUserCompanies;
        if ($GLOBALS["SL"]->REQ->has('edit')
            && intVal($GLOBALS["SL"]->REQ->edit) > 0
            && $GLOBALS["SL"]->REQ->has('save')) {
            $com = RIIUserCompanies::find(intVal($GLOBALS["SL"]->REQ->edit));
        }
        if ($GLOBALS["SL"]->REQ->has('partnerCompanyName')
            && trim($GLOBALS["SL"]->REQ->get('partnerCompanyName')) != '') {
            $com->usr_com_name = trim($GLOBALS["SL"]->REQ->get('partnerCompanyName'));
        }
        if ($GLOBALS["SL"]->REQ->has('partnerCompanySlug')
            && trim($GLOBALS["SL"]->REQ->get('partnerCompanySlug')) != '') {
            $com->usr_com_slug = trim($GLOBALS["SL"]->REQ->get('partnerCompanySlug'));
        } else {
            $com->usr_com_slug = $GLOBALS["SL"]->slugify($com->usr_com_name);
        }
        $com->save();

        $chk = RIIUserManufacturers::where('usr_man_company_id', $com->usr_com_id)
            ->delete();
        if ($GLOBALS["SL"]->REQ->has('partnerManu')
            && intVal($GLOBALS["SL"]->REQ->partnerManu) > 0) {
            $manu = intVal($GLOBALS["SL"]->REQ->partnerManu);
            $perm = RIIUserManufacturers::where('usr_man_manu_id', $manu)
                ->where('usr_man_company_id', $com->usr_com_id)
                ->first();
            if (!$perm || !isset($perm->usr_man_id)) {
                $perm = new RIIUserManufacturers;
                $perm->usr_man_company_id  = $com->usr_com_id;
                $perm->usr_man_manu_id     = $manu;
                $perm->save();
            }
        }
        $this->saveCompanyFacilities($com->usr_com_id);
        return true;
    }
    
    /**
     * Update partner company details.
     *
     * @return boolean
     */
    protected function saveCompanyFacilities($companyID)
    {
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $defWrt = $GLOBALS["SL"]->def->getID('Permissions', 'Write');
        for ($i = 0; $i < $this->v["facLimit"]; $i++) {
            $fac = null;
            if ($GLOBALS["SL"]->REQ->has('facID' . $i)
                && intVal($GLOBALS["SL"]->REQ->get('facID' . $i)) > 0) {
                $fac = RIIUserFacilities::find($GLOBALS["SL"]->REQ->get('facID' . $i));
            } else {
                $fac = new RIIUserFacilities;
            }
            if ($GLOBALS["SL"]->REQ->has('facName' . $i)
                && trim($GLOBALS["SL"]->REQ->get('facName' . $i)) != '') {
                $fac->usr_fac_name = trim($GLOBALS["SL"]->REQ->get('facName' . $i));
                $fac->usr_fac_slug = trim($GLOBALS["SL"]->REQ->get('facSlug' . $i));
                if ($fac->usr_fac_slug == '') {
                    $fac->usr_fac_slug = $GLOBALS["SL"]->slugify($fac->usr_fac_name);
                }
                $fac->save();
                $perm = RIIUserPsPerms::where('usr_perm_company_id', $companyID)
                    ->where('usr_perm_facility_id', $fac->usr_fac_id)
                    ->where('usr_perm_permissions', 'NOT LIKE', $defNon)
                    ->first();
                if (!$perm || !isset($perm->usr_perm_facility_id)) {
                    $perm = new RIIUserPsPerms;
                    $perm->usr_perm_company_id  = $companyID;
                    $perm->usr_perm_facility_id = $fac->usr_fac_id;
                    $perm->usr_perm_permissions = $defWrt;
                    $perm->save();
                }
            } elseif (isset($fac->usr_fac_id)) {
                RIIUserPsPerms::where('usr_perm_company_id', $companyID)
                    ->where('usr_perm_facility_id', $fac->usr_fac_id)
                    ->delete();
                $fac->delete();
            }
        }
        return true;
    }

    /**
     * Print admin overview tools to manage partner 
     * companies and their facilities.
     *
     * @param int $nID
     * @return string
     */
    public function printMyCompanyFacs($nID = -3)
    {
        $this->v["facLimit"]    = 10;
        $com = null;
        if (isset($GLOBALS["SL"]->x["usrInfo"])
            && sizeof($GLOBALS["SL"]->x["usrInfo"]->companies) > 0) {
            $com = $GLOBALS["SL"]->x["usrInfo"]->companies[0];
            $com = RIIUserCompanies::find($com->id);
        }
        if (isset($com->usr_com_id)
            && $GLOBALS["SL"]->REQ->has('save')
            && intVal($GLOBALS["SL"]->REQ->save) == 1) {
            $this->saveCompanyFacilities($com->usr_com_id);
            echo '<script type="text/javascript"> '
                . 'setTimeout("window.location=\'/dashboard#refLinks\'", 100); '
                . '</script>';
            exit;
        }
        $this->v["editPartner"] = new ScoreUserCompanies($com);
//echo '<pre>'; print_r($this->v["editPartner"]); print_r($GLOBALS["SL"]->x["usrInfo"]); echo '</pre>'; exit;
        $GLOBALS["SL"]->pageAJAX .= view(
            'vendor.cannabisscore.nodes.1560-facilities-edit-ajax', 
            $this->v
        )->render();
        return view(
            'vendor.cannabisscore.nodes.1563-facilities-edit', 
            $this->v
        )->render();
    }

    /**
     * Update manufacturer counts for how many rooms 
     * in the official data set use their lights.
     *
     * @return boolean
     */
    protected function cntManufacturerAdoption()
    {
        $this->v["errorNotes"] = '';
        $this->v["stageTots"] = [
            "flower" => 0,
            "veg"    => 0,
            "clone"  => 0,
            "mother" => 0
        ];
        $this->v["manusTots"] = [];
        $this->v["manusTotSum"] = 0;
        if ($this->v["manus"]->isNotEmpty()) {
            foreach ($this->v["manus"] as $m => $manu) {
                $this->v["manus"][$m]->manu_cnt_flower 
                    = $this->v["manus"][$m]->manu_cnt_veg
                    = $this->v["manus"][$m]->manu_cnt_clone
                    = $this->v["manus"][$m]->manu_cnt_mother 
                    = 0;
                $this->v["manusTots"][$m] = [];
            }
            $areaIDs = $areaIDsDone = [];
            $chk = RIIPsAreas::whereIn('ps_area_psid', function($query){
                    $query->select('ps_id')
                        ->from(with(new RIIPowerscore)->getTable())
                        ->where('ps_status', 243)
                        ->where('ps_effic_lighting_status', 243);
                })
                ->select('ps_area_id')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $area) {
                    $areaIDs[] = $area->ps_area_id;
                }
            }
            $lgts = DB::table('rii_ps_light_types')
                ->join('rii_ps_areas', 'rii_ps_areas.ps_area_id', 
                    '=', 'rii_ps_light_types.ps_lg_typ_area_id')
                ->where('rii_ps_light_types.ps_lg_typ_make', 'NOT LIKE', '')
                ->whereNotNull('rii_ps_light_types.ps_lg_typ_make')
                ->whereIn('rii_ps_light_types.ps_lg_typ_area_id', $areaIDs)
                ->select(
                    'rii_ps_light_types.ps_lg_typ_make', 
                    'rii_ps_areas.ps_area_type', 
                    'rii_ps_areas.ps_area_psid'
                )
                ->get();
            foreach ($this->v["manus"] as $m => $manu) {
                if ($lgts->isNotEmpty()) {
                    $this->cntManufacturerAdoptionOne($m, $lgts);
                }
                $this->v["manus"][$m]->save();
                $this->v["stageTots"]["flower"] += $this->v["manus"][$m]->manu_cnt_flower;
                $this->v["stageTots"]["veg"]    += $this->v["manus"][$m]->manu_cnt_veg;
                $this->v["stageTots"]["clone"]  += $this->v["manus"][$m]->manu_cnt_clone;
                $this->v["stageTots"]["mother"] += $this->v["manus"][$m]->manu_cnt_mother;
                $this->v["manusTotSum"] += sizeof($this->v["manusTots"][$m]);
            }
        }
        return true;
    }
    
    /**
     * Load all partner users' info, and extended info.
     *
     * @return boolean
     */
    protected function loadPartnerUsers()
    {
        $GLOBALS["SL"]->x["partners"] 
            = $GLOBALS["SL"]->x["partnerLookup"] 
            = $GLOBALS["SL"]->x["allCompanies"] 
            = [];
        $chk = User::whereIn('id', function($query){
                $query->select('role_user_uid')
                ->from(with(new SLUsersRoles)->getTable())
                ->where('role_user_rid', 368); // partner def ID
            })
            ->select('id', 'name', 'email')
            ->orderBy('name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $ind => $usr) {
                $GLOBALS["SL"]->x["partners"][$ind] = new ScoreUserInfo;
                $GLOBALS["SL"]->x["partners"][$ind]->loadUser($usr->id, $usr);
                $GLOBALS["SL"]->x["partnerLookup"][$usr->id] = $ind;
            }
        }
        $this->v["manufacts"] = $GLOBALS["CUST"]->loadManufactIDs();
        $GLOBALS["SL"]->x["allCompanies"] = RIIUserCompanies::whereNotNull('usr_com_name')
            ->orderBy('usr_com_name', 'asc')
            ->get();
        return true;
    }
    
    /**
     * Load list of pending partner ivites.
     *
     * @return boolean
     */
    protected function loadPartnerInvites()
    {
        $GLOBALS["SL"]->x["partnerInvites"] = [];
        $chk = RIIUserInfo::where('usr_user_id', 0)
            ->whereNotNull('usr_invite_email')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $partner) {
                $info = new ScoreUserInfo;
                $info->loadInviteRow($partner);
                $GLOBALS["SL"]->x["partnerInvites"][] = $info;
            }
        }
        return true;
    }
    
    /**
     * Count how many rooms in the official 
     * use this manufacturer's lights.
     *
     * @param  int $m
     * @param  array $lgts
     * @return boolean
     */
    protected function cntManufacturerAdoptionOne($m, $lgts)
    {
        $found = [
            'Flower' => [],
            'Veg'    => [],
            'Clone'  => [],
            'Mother' => []
        ];
        foreach($lgts as $lgt) {
            $nick = $this->getStageNick($lgt->ps_area_type);
            $pos = stripos(
                ' ' . $lgt->ps_lg_typ_make . ' ', 
                ' ' . $this->v["manus"][$m]->manu_name . ' '
            );
            if ($pos !== false
                && !in_array($lgt->ps_area_psid, $found[$nick])) {
                $match = false;
                if (sizeof($this->v["manuSrchWords"]) == 0) {
                    $match = true;
                } else {
                    foreach ($this->v["manuSrchWords"] as $word) {
                        if (strpos(strtolower($lgt->ps_lg_typ_make), $word) !== false) {
                            $match = true;
                        }
                    }
                }
                if ($match) {
                    $found[$nick][] = $lgt->ps_area_psid;
                    if (!in_array($lgt->ps_area_psid, $this->v["manusTots"][$m])) {
                        $this->v["manusTots"][$m][] = $lgt->ps_area_psid;
                    }
                }
            }
        }
        $stages = ['Flower', 'Veg', 'Clone', 'Mother'];
        foreach ($stages as $nick) {
            $fld = 'manu_cnt_' . strtolower($nick);
            $this->v["manus"][$m]->{ $fld } += sizeof($found[$nick]);
            $fld = 'manu_ids_' . strtolower($nick);
            $this->v["manus"][$m]->{ $fld } = ',' . implode(',', $found[$nick]) . ',';
        }
        return true;
    }

    /**
     * Process the custom admin form to add a new partner user.
     *
     * @param int $nID
     * @return string
     */
    public function savePartnerInvite()
    {
        $userID = $manu = 0;
        $user = $userInfo = null;
        $inviteEmail = '';
        if ($GLOBALS["SL"]->REQ->has('partnerInviteEmail')
            && trim($GLOBALS["SL"]->REQ->partnerInviteEmail) != '') {
            $inviteEmail = trim($GLOBALS["SL"]->REQ->partnerInviteEmail);
        }
        if (isset($this->v["editPartner"])) {
            $user = User::find($this->v["editPartner"]->id);
            $userInfo = RIIUserInfo::find($this->v["editPartner"]->usrInfoID);
            if ($userInfo->usr_invite_email != $inviteEmail) {
                $userInfo->usr_invite_email = $inviteEmail;
                $userInfo->save();
            }
        } else { // Create new invite records
            if ($GLOBALS["SL"]->REQ->has('partnerUser')) {
                $userID = intVal($GLOBALS["SL"]->REQ->partnerUser);
                if ($userID > 0) {
                    $user = User::find($userID);
                }
            }
            if ((!$user || !isset($user->id)) && $inviteEmail != '') {
                $user = User::where('email', 'LIKE', $inviteEmail)
                    ->orderBy('id', 'asc')
                    ->first();
            }
            if ($user && isset($user->id)) {
                $userID = $user->id;
                $userInfo = RIIUserInfo::where('usr_user_id', $userID)
                    ->first();
                if (!$userInfo || !isset($userInfo->usr_id)) {
                    $userInfo = RIIUserInfo::where('usr_invite_email', $user->email)
                        ->first();
                }
            } elseif ($inviteEmail != '') {
                $userInfo = RIIUserInfo::where('usr_invite_email', $inviteEmail)
                    ->first();
            }
            if (!$userInfo || !isset($userInfo->usr_id)) {
                $userInfo = new RIIUserInfo;
                $userInfo->usr_user_id = $userID;
                if ($inviteEmail != '') {
                    $userInfo->usr_invite_email = $inviteEmail;
                }
                $userInfo->save();
            }
            $this->v["editPartner"] = new ScoreUserInfo;
            $this->v["editPartner"]->loadInvite($userInfo->usr_id);
        }
        $this->savePartnerLevels($userInfo);
        $this->savePartnerCompany($userInfo);
        return true;
    }
    
    /**
     * Admins can manage partner access levels.
     *
     * @return boolean
     */
    private function savePartnerLevels(&$userInfo)
    {
        if ($this->v["editPartner"]->id > 0) {
            // Then full user account was created, not just an invitation
            $defPartner = 368;
            $role = SLUsersRoles::where('role_user_rid', $defPartner)
                ->where('role_user_uid', $this->v["editPartner"]->id)
                ->first();
            if (!$role || !isset($role->role_user_rid)) {
                $role = new SLUsersRoles;
                $role->role_user_uid = $this->v["editPartner"]->id;
                $role->role_user_rid = $defPartner;
                $role->save();
            }
        }
        if ($GLOBALS["SL"]->REQ->has('partnerLevel')
            && trim($GLOBALS["SL"]->REQ->partnerLevel) != '') {
            $userInfo->usr_level = trim($GLOBALS["SL"]->REQ->partnerLevel);
        }
        if ($GLOBALS["SL"]->REQ->has('partnerExpire')
            && trim($GLOBALS["SL"]->REQ->partnerExpire) != '') {
            $userInfo->usr_membership_expiration = trim($GLOBALS["SL"]->REQ->partnerExpire);
        }
        $userInfo->save();
        return true;
    }
    
    /**
     * Admins can manage company and manufacturer assignments to partners.
     *
     * @return boolean
     */
    public function savePartnerCompany(&$userInfo)
    {
        $this->clearUserCompanyPerms();
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $companyID = 0;
        if ($GLOBALS["SL"]->REQ->has('partnerCompany')
            && intVal($GLOBALS["SL"]->REQ->partnerCompany) > 0) {
            $companyID = intVal($GLOBALS["SL"]->REQ->partnerCompany);
        } elseif ($GLOBALS["SL"]->REQ->has('partnerCompanyName')
            && trim($GLOBALS["SL"]->REQ->partnerCompanyName) != '') {
            $userInfo->usr_company_name = trim($GLOBALS["SL"]->REQ->partnerCompanyName);
            $userInfo->save();
            $chk = RIIUserCompanies::where('usr_com_name', 'LIKE', $userInfo->usr_company_name)
                ->orderBy('usr_com_id', 'asc')
                ->first();
            if ($chk && isset($chk->usr_com_id)) {
                $companyID = $chk->usr_com_id;
            } else {
                $companyID = $this->createPartnerCompanyRecord($userInfo->usr_company_name);
            }
        }
        if ($companyID > 0) {
            $this->makeUserCompanyPerm($companyID);
        }
        return true;
    }
    
    /**
     * Admins can manage company and manufacturer assignments to partners.
     *
     * @return boolean
     */
    public function createPartnerCompanyRecord($name)
    {
        $com = new RIIUserCompanies;
        $com->usr_com_name = trim($name);
        $com->usr_com_slug = $GLOBALS["SL"]->slugify(trim($name));
        $com->save();
        return $com->usr_com_id;
    }

    /**
     * Admins can add manufacturers to the database.
     *
     * @return boolean
     */
    private function addManufacturers()
    {
        if ($GLOBALS["SL"]->REQ->has('addManu') 
            && trim($GLOBALS["SL"]->REQ->get('addManu')) != '') {
            $lines = $GLOBALS["SL"]->mexplode(
                "\n", 
                $GLOBALS["SL"]->REQ->get('addManu')
            );
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($line != '') {
                        $chk = RIIManufacturers::where('manu_name', 'LIKE', $line)
                            ->first();
                        if (!$chk || !isset($chk->manu_id)) {
                            $chk = new RIIManufacturers;
                            $chk->manu_name = $line;
                            $chk->save();
                        }
                    }
                }
            }
        }
        return true;
    }
    
    /**
     * Add a link between a user and a company/organization.
     *
     * @param  int $company
     * @return boolean
     */
    protected function makeUserCompanyPerm($companyID = 0, $usrInfoID = 0)
    {
        if ($usrInfoID <= 0 
            && isset($this->v["editPartner"])
            && isset($this->v["editPartner"]->usrInfoID)) {
            $usrInfoID = $this->v["editPartner"]->usrInfoID;
        }
        $perm = RIIUserPsPerms::where('usr_perm_company_id', $companyID)
            ->where('usr_perm_user_id', $usrInfoID)
            ->first();
        if (!$perm || !isset($perm->usr_perm_user_id)) {
            $defWrt = $GLOBALS["SL"]->def->getID('Permissions', 'Write');
            $perm = new RIIUserPsPerms;
            $perm->usr_perm_company_id  = $companyID;
            $perm->usr_perm_user_id     = $usrInfoID;
            $perm->usr_perm_permissions = $defWrt;
            $perm->save();
        }
        return $perm;
    }
    
    /**
     * Delete any links between a user and a company/organization.
     *
     * @param  int $company
     * @return boolean
     */
    protected function clearUserCompanyPerms($companyID = 0)
    {
        if ($companyID <= 0) {
            return RIIUserPsPerms::where('usr_perm_company_id', '>', 0)
                ->where('usr_perm_user_id', $this->v["editPartner"]->usrInfoID)
                ->delete();
        }
        return RIIUserPsPerms::where('usr_perm_company_id', $companyID)
            ->where('usr_perm_user_id', $this->v["editPartner"]->usrInfoID)
            ->delete();
    }


// Bad duplicate function
    public function getStageNick($defID)
    {
        $detSet = 'PowerScore Growth Stages';
        switch ($defID) {
            case $GLOBALS["SL"]->def->getID($detSet, 'Mother Plants'):
                return 'Mother';
            case $GLOBALS["SL"]->def->getID($detSet, 'Clone or Mother Plants'):
                return 'Clone';
            case $GLOBALS["SL"]->def->getID($detSet, 'Vegetating Plants'): 
                return 'Veg';
            case $GLOBALS["SL"]->def->getID($detSet, 'Flowering Plants'):
                return 'Flower';
            case $GLOBALS["SL"]->def->getID($detSet, 'Drying/Curing'):
                return 'Dry';
        }
        return '';
    }

}