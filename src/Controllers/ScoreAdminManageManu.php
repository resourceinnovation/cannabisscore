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
use App\Models\SLUsersRoles;
use App\Models\User;

class ScoreAdminManageManu
{
    // information to be passed to Views, and/or needed by dispersed functions
    protected $v = [];

    public function __construct()
    {
        $this->addManufacturers();
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
        if ($GLOBALS["SL"]->REQ->has('excel')) {
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
        }
        if ($GLOBALS["SL"]->REQ->has('edit')
            && intVal($GLOBALS["SL"]->REQ->edit) > 0) {
            $this->v["editPartner"] = new ScoreUserInfo;
            $this->v["editPartner"]->loadInvite(intVal($GLOBALS["SL"]->REQ->edit));
            if ($GLOBALS["SL"]->REQ->has('save')) {
                $this->savePartnerInvite();
                $this->v["editPartner"] = new ScoreUserInfo;
                $this->v["editPartner"]->loadInvite(intVal($GLOBALS["SL"]->REQ->edit));
            } else {
                $this->v["isEditing"] = true;
            }
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
            $userID = $this->v["editPartner"]->id;
            $user = User::find($userID);
            $userInfo = RIIUserInfo::find($this->v["editPartner"]->usrInfoID);
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
            }
        }
        $userInfo->usr_invite_email = $inviteEmail;
        if ($GLOBALS["SL"]->REQ->has('partnerCompanyName')
            && trim($GLOBALS["SL"]->REQ->partnerCompanyName) != '') {
            $userInfo->usr_company_name = trim($GLOBALS["SL"]->REQ->partnerCompanyName);
        }
        if ($GLOBALS["SL"]->REQ->has('partnerManu')
            && intVal($GLOBALS["SL"]->REQ->partnerManu) > 0) {
            $chk = RIIUserManufacturers::where('usr_man_user_id', $userID)
                ->delete();
            $userInfo->usr_manu_ids = intVal($GLOBALS["SL"]->REQ->partnerManu);
        }
        if ($GLOBALS["SL"]->REQ->has('partnerLevel')
            && trim($GLOBALS["SL"]->REQ->partnerLevel) != '') {
            $userInfo->usr_level = trim($GLOBALS["SL"]->REQ->partnerLevel);
        }
        if ($GLOBALS["SL"]->REQ->has('partnerExpire')
            && trim($GLOBALS["SL"]->REQ->partnerExpire) != '') {
            $userInfo->usr_membership_expiration = trim($GLOBALS["SL"]->REQ->partnerExpire);
        }
        if ($inviteEmail != '') {
            $userInfo->usr_invite_email = $inviteEmail;
        }
        $userInfo->save();
        return true;
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
        return true;
    }
    
    /**
     * Load list of pending partner ivites.
     *
     * @return boolean
     */
    protected function loadPartnerInvites()
    {
        $GLOBALS["SL"]->x["partnerInvites"] = RIIUserInfo::where('usr_user_id', 0)
            ->whereNotNull('usr_invite_email')
            ->get();
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
            $this->v["manus"][$m]->{ $fld } = ',' 
                . implode(',', $found[$nick]) . ',';
        }
        return true;
    }
    
    /**
     * Admins can add manufacturers to the database.
     *
     * @return boolean
     */
    public function addManufacturers()
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
        } elseif ($GLOBALS["SL"]->REQ->has('partnerCompanyName')) {
            $usrInfo = null;
            $company = trim($GLOBALS["SL"]->REQ->partnerCompanyName);
            if ($company != '') {
                $usr = $manu = 0;
                if ($GLOBALS["SL"]->REQ->has('partnerUser')
                    && intVal($GLOBALS["SL"]->REQ->get('partnerUser')) > 0) {
                    $usr = intVal($GLOBALS["SL"]->REQ->get('partnerUser'));
                }
                if ($GLOBALS["SL"]->REQ->has('partnerManu') 
                    && intVal($GLOBALS["SL"]->REQ->get('partnerManu')) > 0) {
                    $manu = intVal($GLOBALS["SL"]->REQ->partnerManu);
                }
                $inviteEmail = '';
                if ($GLOBALS["SL"]->REQ->has('partnerInviteEmail') 
                    && trim($GLOBALS["SL"]->REQ->partnerInviteEmail) != '') {
                    $inviteEmail = trim($GLOBALS["SL"]->REQ->partnerInviteEmail);
                }
                if ($usr < 0 && $inviteEmail != '') {
                    $usrChk = User::where('email', 'LIKE', $inviteEmail)
                        ->first();
                    if ($usrChk && isset($usrChk->id) && intVal($usrChk->id) > 0) {
                        $usr = $usrChk->id;
                    }
                }
                if ($usr > 0) {
                    $role = SLUsersRoles::where('role_user_uid', $usr)
                        ->where('role_user_rid', 368) // partner def ID
                        ->first();
                    if (!$role || !isset($role->role_user_rid)) {
                        $role = new SLUsersRoles;
                        $role->role_user_uid = $usr;
                        $role->role_user_rid = 368;
                        $role->save();
                    }
                    $usrInfo = RIIUserInfo::where('usr_user_id', $usr)
                        ->first();
                    if (!$usrInfo || !isset($usrInfo->usr_user_id)) {
                        $usrInfo = new RIIUserInfo;
                        $usrInfo->usr_user_id = $usr;
                    }

                    $usrInfo->usr_company_name = $company;
                    $usrInfo->save();
                    $this->addManuUserLink($usr, $manu);
                }
            }
        }
        return true;
    }
    
    /**
     * Add a link between a user and a manufacturer.
     *
     * @param  int $usr
     * @param  int $manu
     * @return boolean
     */
    protected function addManuUserLink($usr, $manu)
    {
        $chk = RIIUserManufacturers::where('usr_man_user_id', $usr)
            ->where('usr_man_manu_id', $manu)
            ->first();
        if (!$chk || !isset($chk->usr_man_manu_id)) {
            $chk = new RIIUserManufacturers;
            $chk->usr_man_user_id = $usr;
            $chk->usr_man_manu_id = $manu;
            $chk->save();
        }
        return true;
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