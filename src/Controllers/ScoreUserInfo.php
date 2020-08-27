<?php
/**
  * ScoreUserInfo is a helper class for loading a partner user's information.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2
  */
namespace CannabisScore\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use App\Models\RIIUserCompanies;
use App\Models\RIIUserFacilities;
use App\Models\RIIUserPsPerms;
use App\Models\SLUsersRoles;
use App\Models\User;

class ScoreUserInfo extends ScoreCollector
{
    public $id           = 0;
    public $usrInfoID    = 0;
    public $name         = '';
    public $email        = '';
    public $company      = '';
    public $slug         = '';
    public $level        = 0;
    public $levelDef     = 0;
    public $trialStart   = '';
    public $expiration   = 0;
    public $expireTime   = 0;
    public $isExpired    = true;

    public $perms        = [];
    public $companies    = [];
    public $companyIDs   = [];
    public $facs         = [];

    public $partnerDef   = 368;
    public $partTierDefs = [];

    public function loadInvite($usrInfoID = 0)
    {
        $this->usrInfoID = $usrInfoID;
        $info = RIIUserInfo::find($usrInfoID);
        if ($info && isset($info->usr_id) && $info->usr_id > 0) {
            $this->loadInviteRow($info);
        }
        return true;
    }

    public function loadInviteRow($info = null)
    {
        if (!$info || !isset($info->usr_id)) {
            return false;
        }
        $this->usrInfoID  = $info->usr_id;
        $this->trialStart = $info->usr_trial_start;
        if (isset($info->usr_invite_email)) {
            $this->email  = trim($info->usr_invite_email);
        }
        if (isset($info->usr_user_id) && intVal($info->usr_user_id) > 0) {
            $user = User::find($info->usr_user_id);
            if (isset($user->id)) {
                $this->id    = $user->id;
                $this->name  = $user->name;
                $this->email = trim($user->email);
            }
        }
        $this->loadCompany($info);
        $this->loadCore();
        return true;
    }

    public function loadUser($userID = 0, $user = null, $company = '')
    {
        if (!$user && !isset($user->id)) {
            $user = User::find($userID);
        }
        if (isset($user->id)) {
            $this->id    = $user->id;
            $this->name  = $user->name;
            $this->email = $user->email;
        } else {
            $this->id = $userID;
        }
        $info = RIIUserInfo::where('usr_user_id', $this->id)
            ->first();
        if ((!$info || !isset($info->usr_user_id) || intVal($info->usr_user_id) <= 0) 
            && isset($user->email)) {
            $info = RIIUserInfo::where('usr_invite_email', $user->email)
                ->first();
            if ($info 
                && isset($this->id)
                && (!isset($info->usr_user_id) || intVal($info->usr_user_id) <= 0)) {
                $info->usr_user_id = $this->id;
                $info->save();
            }
        }
        if ($info && isset($info->usr_id)) {
            $this->usrInfoID  = $info->usr_id;
            $this->trialStart = $info->usr_trial_start;
        }
        $this->loadCompany($info, $company);
        $this->postCompanyName();
        $this->loadCore();
        return true;
    }
    
    protected function loadCore($user = null)
    {
        $this->getUserCompanies();
        if (Auth::user()->hasRole('administrator|staff')) {
            $this->level = 10;
        }
        $GLOBALS["SL"]->x["partnerID"]      = $this->id;
        $GLOBALS["SL"]->x["partnerCompany"] = $this->company;
        $GLOBALS["SL"]->x["partnerLevel"]   = $this->level;
        $GLOBALS["SL"]->x["partnerInfoID"]  = 0;
        $GLOBALS["SL"]->x["partnerPSIDs"]   = [];
        if ($this->usrInfoID > 0) {
            $GLOBALS["SL"]->x["partnerInfoID"] = $this->usrInfoID;
            $GLOBALS["SL"]->x["partnerPSIDs"]  = $this->psids;
        }
        return true;
    }
    
    protected function loadCompany($info, $company = '')
    {
        if ($company != '') {
            $this->company = $company;
        } elseif ($info && isset($info->usr_company_name)) {
            $this->company = trim($info->usr_company_name);
        }
        if ($info && isset($info->usr_referral_slug)) {
            $this->slug = trim($info->usr_referral_slug);
        }
        if ($info && isset($info->usr_user_id)) {
            if (isset($info->usr_level)) {
                $this->getPartTierLevel($info->usr_level);
            }
            if (isset($info->usr_membership_expiration)) {
                $this->expiration = intVal($info->usr_membership_expiration);
                $this->chkEpiration();
            }
            if ($this->levelDef > 0) {
                $this->chkLevels($info);
            }
        }
        return true;
    }

    protected function chkEpiration()
    {
        if ($this->expiration == 0) {
            $this->isExpired = false;
        } elseif (trim($this->trialStart) != '') {
            $expDate = strtotime($this->trialStart);
            $this->expireTime = mktime(0, 0, 1, 
                date("n", $expDate), 
                date("j", $expDate)+$this->expiration+1, 
                date("Y", $expDate)
            );
            if ($this->expireTime < time()) {
                $this->isExpired = true;
            }
        }
        return true;
    }

    // 0 = Free, 1 = Tier 1, 2 = Tier 2, 3 = Founders, 
    // 4 = Cornerstone, 5 = Sustaining, 10 = Admin
    protected function getPartTierLevel($usrLevel)
    {
        $this->loadPartTierDefs();
        $this->levelDef = $usrLevel;
        $this->level = 0;
        foreach ($this->partTierDefs as $ind => $defID) {
            if ($usrLevel == $defID) {
                $this->level = $ind;
            }
        }
        return $this->level;
    }

    protected function loadPartTierDefs()
    {
        $this->partTierDefs = [];
        foreach ($GLOBALS["SL"]->def->getSet('Partner Levels') as $level) {
            $this->partTierDefs[] = $level->def_id;
        }
        return true;
    }   
    
    protected function chkLevels($info)
    {
        $role = SLUsersRoles::where('role_user_rid', $this->partnerDef)
            ->where('role_user_uid', $this->id)
            ->first();
        $hasPartnerFlag = ($role && isset($role->role_user_id));
        if ($this->level > 0 
            && isset($info->usr_invite_email)
            && strtolower(trim($info->usr_invite_email)) 
                == strtolower(trim($this->email))) {
            if (!isset($info->usr_trial_start)) {
                $info->usr_trial_start = date("Y-m-d");
                $info->save();
            }
            if (!$hasPartnerFlag) {
                $this->addPartnerRole();
            }
//echo 'id: ' . $this->id . ', email: ' . $this->email . ', company: ' . $this->company . ', levelDef: ' . $this->levelDef . ', partnerDef: ' . $this->partnerDef . '<br />info: <pre>'; print_r($role); print_r($info); echo '</pre>'; exit;

        } elseif ($hasPartnerFlag) {
            $this->isExpired = false;
        }
        $this->chkEpiration();
        return true;
    }
    
    protected function addPartnerRole()
    {
        $role = new SLUsersRoles;
        $role->role_user_rid = $this->partnerDef;
        $role->role_user_uid = $this->id;
        $role->save();
        echo view(
            'vendor.survloop.js.redir', 
            [ "redir" => '?refresh=1' ]
        )->render();
        exit;
    }
    
    protected function postCompanyName()
    {
        if ($GLOBALS["SL"]->REQ->has('companyName')
            && intVal($GLOBALS["SL"]->REQ->companyName) == 1
            && $GLOBALS["SL"]->REQ->has('myProfileCompanyName')) {
            $this->company = trim($GLOBALS["SL"]->REQ->myProfileCompanyName);
            if ($GLOBALS["SL"]->REQ->has('myProfileCompanySlug')) {
                $this->slug = trim($GLOBALS["SL"]->REQ->myProfileCompanySlug);
            }
            RIIUserInfo::where('usr_user_id', $this->usrInfoID)
                ->update([
                    'usr_company_name'  => $this->company,
                    'usr_referral_slug' => $this->slug
                ]);
        }
        return true;
    }
    
    protected function getUserCompanies()
    {
        $this->psids
            = $this->companies 
            = $this->companyIDs
            = $this->facs 
            = $GLOBALS["SL"]->x["partnerAllPSIDs"] 
            = $GLOBALS["SL"]->x["partnerCompanyIDs"] 
            = $GLOBALS["SL"]->x["partnerFacilityIDs"] 
            = [];
        $companyName = '';
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $this->perms = RIIUserPsPerms::where('usr_perm_user_id', $this->usrInfoID)
            ->where('usr_perm_permissions', 'NOT LIKE', $defNon)
            ->get();
        // First gather PSIDs directly tied to this user
        $this->psids = $GLOBALS["SL"]->resultsToArrIds($this->perms, 'usr_perm_psid');
        if ($this->perms->isNotEmpty()) {
            foreach ($this->perms as $i => $perm) {
                // Load tied partner user companies, and gather PSIDs
                if (isset($perm->usr_perm_psid)
                    && intVal($perm->usr_perm_psid) > 0) {
                    $this->addPSID($perm->usr_perm_psid);
                } elseif (isset($perm->usr_perm_company_id)
                    && intVal($perm->usr_perm_company_id) > 0) {
                    $chk = RIIUserCompanies::find($perm->usr_perm_company_id);
                    if ($chk && isset($chk->usr_com_id)) {
                        $com = new ScoreUserCompanies($chk);
                        $this->addPSIDs($com->psids);
                        $this->companies[]  = $com;
                        $this->companyIDs[] = $chk->usr_com_id;
                        if ($companyName == '') {
                            $companyName = $chk->usr_com_name;
                        }
                        if (sizeof($com->manus) > 0) {
                            foreach ($com->manus as $manu) {
                                $GLOBALS["SL"]->x["partnerManuIDs"][] = $manu->id;
                            }
                        }
                    }
                } elseif (isset($perm->usr_perm_facility_id)
                    && intVal($perm->usr_perm_facility_id) > 0) {
                    $chk = RIIUserFacilities::find($perm->usr_perm_facility_id);
                    if ($chk && isset($chk->usr_com_id)) {
                        $fac = new ScoreUserCompanies($chk);
                        $this->addPSIDs($fac->psids);
                        $this->facs[] = $fac;
                    }
                }
            }
        }
        $this->company = $companyName;

if ($GLOBALS["SL"]->REQ->has('dbg')) { echo 'psids:<pre>'; print_r($this->psids); echo '</pre>companies:<pre>'; print_r($this->companies); echo '</pre>facs:<pre>'; print_r($this->facs); echo '</pre>perms:<pre>'; print_r($this->perms); echo '</pre>'; exit; }

        return true;
    }

    public function listCompanyNames()
    {
        $ret = '';
        if (sizeof($this->companies) > 0) {
            foreach ($this->companies as $i => $com) {
                $ret .= (($i > 0) ? ', ' : '') . $com->name;
            }
        }
        return $ret;
    }

}


class ScoreUserCompanies extends ScoreCollector
{
    public $id        = 0;
    public $name      = '';
    public $slug      = '';
    public $totScores = 0;

    public $perms     = [];

    public $users     = [];
    public $manus     = [];
    public $facs      = [];

    public function __construct($company = null)
    {
        if ($company && isset($company->usr_com_id)) {
            $this->id        = $company->usr_com_id;
            $this->name      = $company->usr_com_name;
            $this->slug      = $company->usr_com_slug;
            $this->totScores = intVal($company->usr_com_count);
            $this->loadScores();
            $this->loadUsers();
            $this->loadManufacturers();
            $this->loadFacilities();
//if ($this->id == 35) { echo '?? ' . $this->totScores . ' ? = ' . sizeof($this->psids); exit; }
            if ($this->totScores != sizeof($this->psids)) {
                $this->totScores = sizeof($this->psids);
                $com = RIIUserCompanies::find($this->id)
                    ->update([ 'usr_com_count' => $this->totScores ]);
            }
        }
    }

    private function loadScores()
    {
        if ($this->id <= 0) {
            return false;
        }
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $this->perms = RIIUserPsPerms::where('usr_perm_psid', '>', 0)
            ->where('usr_perm_company_id', $this->id)
            ->where('usr_perm_permissions', 'NOT LIKE', $defNon)
            ->get();
        $this->psids = $GLOBALS["SL"]->resultsToArrIds($this->perms, 'usr_perm_psid');
        return true;
    }

    private function loadUsers()
    {
        $this->users = [];
        if ($this->id <= 0) {
            return false;
        }
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $chk = DB::table('rii_user_ps_perms')
            ->join('rii_user_info', 'rii_user_info.usr_id', 
                '=', 'rii_user_ps_perms.usr_perm_user_id')
            ->join('users', 'users.id', '=', 'rii_user_info.usr_user_id')
            ->where('rii_user_ps_perms.usr_perm_company_id', $this->id)
            ->where('rii_user_ps_perms.usr_perm_user_id', '>', 0)
            ->where('rii_user_ps_perms.usr_perm_permissions', 'NOT LIKE', $defNon)
            ->select(
                'users.name',
                'users.email',
                'rii_user_info.*',
                'rii_user_ps_perms.usr_perm_permissions'
            )
            ->orderBy('users.name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $user) {
                $this->users[] = new ScoreCompaniesMember($user);
            }
        }
        return true;
    }

    private function loadManufacturers()
    {
        $this->manus = [];
        $chk = DB::table('rii_manufacturers')
            ->join('rii_user_manufacturers', 'rii_manufacturers.manu_id', 
                '=', 'rii_user_manufacturers.usr_man_manu_id')
            ->where('rii_user_manufacturers.usr_man_company_id', $this->id)
            ->select(
                'rii_manufacturers.manu_id',
                'rii_manufacturers.manu_name'
            )
            ->orderBy('rii_manufacturers.manu_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $m) {
                $this->manus[] = new ScoreUserManufact($m->manu_id, $m->manu_name);
            }
        }
        return true;
    }

    private function loadFacilities()
    {
        $this->facs = [];
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $chk = DB::table('rii_user_ps_perms')
            ->join('rii_user_facilities', 'rii_user_facilities.usr_fac_id', 
                '=', 'rii_user_ps_perms.usr_perm_facility_id')
            ->where('rii_user_ps_perms.usr_perm_company_id', $this->id)
            ->where('rii_user_facilities.usr_fac_id', '>', 0)
            ->where('rii_user_ps_perms.usr_perm_permissions', 'NOT LIKE', $defNon)
            ->select(
                'rii_user_facilities.*',
                'rii_user_ps_perms.usr_perm_permissions'
            )
            ->orderBy('rii_user_facilities.usr_fac_name', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $fac) {
                $this->facs[$i] = new ScoreUserFacilities($fac);
                $this->addPSIDs($this->facs[$i]->psids);
            }
        }
        return true;
    }

    public function getFacID($ind = 0)
    {
        if (isset($this->facs[$ind]) && isset($this->facs[$ind]->id)) {
            return $this->facs[$ind]->id;
        }
        return 0;
    }

    public function getFacName($ind = 0)
    {
        if (isset($this->facs[$ind]) && isset($this->facs[$ind]->name)) {
            return $this->facs[$ind]->name;
        }
        return '';
    }

    public function getFacSlug($ind = 0)
    {
        if (isset($this->facs[$ind]) && isset($this->facs[$ind]->slug)) {
            return $this->facs[$ind]->slug;
        }
        return '';
    }

    public function hasManuID($manuID = 0)
    {
        foreach ($this->manus as $m) {
            if (isset($m->id) && $m->id == $manuID) {
                return true;
            }
        }
        return false;
    }

    public function listManufacturerNames()
    {
        $ret = '';
        if (sizeof($this->manus) > 0) {
            foreach ($this->manus as $i => $manu) {
                $ret .= (($i > 0) ? ', ' : '') 
                    . '<a href="/dash/competitive-performance?manu='
                    . urlencode($manu->name) . '">' . $manu->name . '</a>';
            }
        }
        return $ret;
    }

    public function listUsers($break = ', ')
    {
        $ret = '';
        if (sizeof($this->users) > 0) {
            foreach ($this->users as $i => $user) {
                $ret .= (($i > 0) ? $break : '') . '<a href="/profile/'
                    . urlencode($user->name) . '">' . $user->name . '</a>';
            }
        }
        return $ret;
    }

}

class ScoreCompaniesMember
{
    public $id         = 0;
    public $usrInfoID  = 0;
    public $name       = '';
    public $email      = '';
    public $compPerm   = 0;

    public function __construct($user = null)
    {
        if ($user && isset($user->usr_user_id)) {
            $this->id        = $user->usr_user_id;
            $this->usrInfoID = $user->usr_id;
            $this->compPerm  = $user->usr_perm_permissions;
            if (isset($user->name)) {
                $this->name  = $user->name;
                $this->email = $user->email;
            }
        }
    }
}

class ScoreUserManufact
{
    public $id   = 0;
    public $name = '';

    public function __construct($manuID = 0, $manuName = '')
    {
        $this->id   = $manuID;
        $this->name = $manuName;
    }
}

class ScoreUserFacilities extends ScoreCollector
{
    public $id         = 0;
    public $name       = '';
    public $slug       = '';
    public $totScores  = 0;

    public $companyID  = 0;
    public $compPerm   = 0;

    public $perms      = [];

    public function __construct($facility = null)
    {
        if ($facility && isset($facility->usr_fac_id)) {
            $this->id        = $facility->usr_fac_id;
            $this->name      = $facility->usr_fac_name;
            $this->slug      = $facility->usr_fac_slug;
            $this->totScores = intVal($facility->usr_fac_count);
            $this->loadScores();
        }
        if ($facility && isset($facility->usr_perm_permissions)) {
            $this->compPerm = $facility->usr_perm_permissions;
        }
    }

    private function loadScores()
    {
        $this->psids = [];
        if ($this->id <= 0) {
            return false;
        }
        $defNon = $GLOBALS["SL"]->def->getID('Permissions', 'None');
        $this->perms = RIIUserPsPerms::where('usr_perm_facility_id', $this->id)
            ->where('usr_perm_psid', '>', 0)
            ->where('usr_perm_permissions', 'NOT LIKE', $defNon)
            ->get();
        $this->psids = $GLOBALS["SL"]->resultsToArrIds($this->perms, 'usr_perm_psid');
        if ($this->totScores != sizeof($this->psids)) {
            $this->totScores = sizeof($this->psids);
            $com = RIIUserFacilities::find($this->id)
                ->update([ 'usr_fac_count' => $this->totScores ]);
        }
        return true;
    }

}



class ScoreCollector
{
    public $psids = [];

    protected function addPSID($psid)
    {
        $psid = intVal($psid);
        if ($psid > 0 && !in_array($psid, $this->psids)) {
            $this->psids[] = $psid;
        }
        return true;
    }
    
    protected function addPSIDs($psids)
    {
        if (sizeof($psids) > 0) {
            foreach ($psids as $ps) {
                $this->addPSID($ps);
            }
        }
        return true;
    }

}