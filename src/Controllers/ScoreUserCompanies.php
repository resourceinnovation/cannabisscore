<?php
/**
  * ScoreUserCompanies helps load a partner user's information.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.7
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use App\Models\RIIUserCompanies;
use App\Models\RIIUserFacilities;
use App\Models\RIIUserPsPerms;
use ResourceInnovation\CannabisScore\Controllers\ScoreCollector;

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

