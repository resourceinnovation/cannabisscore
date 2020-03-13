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
    protected $v = [];

    public function __construct()
    {
        $this->v["manus"] = RIIManufacturers::orderBy('manu_name', 'asc')
            ->get();
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
        return view(
            'vendor.cannabisscore.nodes.914-manage-manufacturers', 
            $this->v
        )->render();
    }
    
    /**
     * Print admin tool to add one or more lighting manufacturers.
     *
     * @param int $nID
     * @return string
     */
    public function printAddManufacturers($nID = -3)
    {
        $this->addManufacturers($nID);
        return view(
            'vendor.cannabisscore.nodes.1293-add-manufacturers', 
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
        if ($GLOBALS["SL"]->REQ->has('addPartnerManu')
            && intVal($GLOBALS["SL"]->REQ->addPartnerManu) == 1) {
            $this->addNewPartnerInvite();
        }
        $this->loadPartnerUsers();
        $this->loadPartnerInvites();
        return view(
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
    public function addNewPartnerInvite()
    {
        $userID = $manu = 0;
        $user = $userInfo = null;
        $inviteEmail = '';
        if ($GLOBALS["SL"]->REQ->has('partnerUser')) {
            $userID = intVal($GLOBALS["SL"]->REQ->partnerUser);
            if ($userID > 0) {
                $user = User::find($userID);
            }
        }
        if ((!$user || !isset($user->id)) 
            && $GLOBALS["SL"]->REQ->has('partnerInviteEmail')) {
            $inviteEmail = trim($GLOBALS["SL"]->REQ->partnerInviteEmail);
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
        $userInfo->usr_invite_email = $inviteEmail;
        if ($GLOBALS["SL"]->REQ->has('partnerCompanyName')
            && trim($GLOBALS["SL"]->REQ->partnerCompanyName) != '') {
            $userInfo->usr_company_name = trim($GLOBALS["SL"]->REQ->partnerCompanyName);
        }
        if ($GLOBALS["SL"]->REQ->has('partnerManu')
            && intVal($GLOBALS["SL"]->REQ->partnerManu) > 0) {
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
        if ($this->v["manus"]->isNotEmpty()) {
            foreach ($this->v["manus"] as $m => $manu) {
                $this->v["manus"][$m]->manu_cnt_flower 
                    = $this->v["manus"][$m]->manu_cnt_veg
                    = $this->v["manus"][$m]->manu_cnt_clone
                    = $this->v["manus"][$m]->manu_cnt_mother = 0;
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
                $found[$nick][] = $lgt->ps_area_psid;
            }
        }
        $stages = ['Flower', 'Veg', 'Clone', 'Mother'];
        foreach ($stages as $nick) {
            $this->v["manus"][$m]->{ 'manu_cnt_' . strtolower($nick) } += sizeof($found[$nick]);
            $this->v["manus"][$m]->{ 'manu_ids_' . strtolower($nick) } = ','
                . implode(',', $found[$nick]) . ',';
        }
        return true;
    }
    
    /**
     * Admins can add manufacturers to the database.
     *
     * @param  int $nID
     * @return boolean
     */
    public function addManufacturers($nID = -3)
    {
        if ($GLOBALS["SL"]->REQ->has('addManu') 
            && trim($GLOBALS["SL"]->REQ->get('addManu')) != '') {
            $lines = $GLOBALS["SL"]->mexplode("\n", $GLOBALS["SL"]->REQ->get('addManu'));
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
        switch ($defID) {
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'):
                return 'Mother';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone & Mother Plants'):
                return 'Clone';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'): 
                return 'Veg';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'):
                return 'Flower';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing'):
                return 'Dry';
        }
        return '';
    }

}