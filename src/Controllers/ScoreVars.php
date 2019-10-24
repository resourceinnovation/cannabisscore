<?php
/**
  * ScoreVars is the bottom-level extension of the SurvLoop class, TreeSurvForm.
  * This class initializes the majority of simplest PowerScore-specific variables
  * and prepping various proccesses.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\RIIPowerScore;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use CannabisScore\Controllers\ScoreLookups;
use SurvLoop\Controllers\Tree\TreeSurvForm;

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
            $this->majorSections[] = [971, 'Your Farm',        'active'];
            $this->majorSections[] = [911, 'Lighting & HVAC',  'active'];
            $this->majorSections[] = [969, 'Annual Totals',    'active'];
            $this->majorSections[] = [844, 'Other Techniques', 'active'];
            $this->majorSections[] = [972, 'Water',            'active'];
            $this->majorSections[] = [970, 'Waste',            'active'];
            $this->minorSections = [ [], [], [], [], [], [], [] ];
        }
        
        //$GLOBALS["SL"]->addTopNavItem('Calculate PowerScore', '/start/calculator');
        return true;
    }
    
    protected function loadCommonVars()
    {
        $this->frmTypOut = $GLOBALS["SL"]->def->getID(
            'PowerScore Farm Types', 
            'Outdoor'
        );
        $this->frmTypIn  = $GLOBALS["SL"]->def->getID(
            'PowerScore Farm Types', 
            'Indoor'
        );
        $this->frmTypGrn = $GLOBALS["SL"]->def->getID(
            'PowerScore Farm Types', 
            'Greenhouse/Hybrid/Mixed Light'
        );

        $this->statusIncomplete = $GLOBALS["SL"]->def->getID(
            'PowerScore Status', 
            'Incomplete'
        );
        $this->statusComplete = $GLOBALS["SL"]->def->getID(
            'PowerScore Status', 
            'Complete'
        );
        $this->statusArchive = $GLOBALS["SL"]->def->getID(
            'PowerScore Status', 
            'Archived'
        );
        return true;
    }
    
    protected function loadScoreUserVars()
    {
        $this->v["usrInfo"] = new ScoreUserInfo;
        if (isset($this->v["uID"]) && $this->v["uID"] > 0) {
            $this->v["usrInfo"]->loadUser(
                $this->v["uID"], 
                $this->v["user"]
            );
        }
        return true;
    }
    
    protected function getUserCompany($userID)
    {
        if ($userID <= 0 && isset($this->v["uID"])) {
            $userID = $this->v["uID"];
        }
        $chk = RIIUserInfo::where('UsrUserID', $userID)
            ->first();
        if ($chk && isset($chk->UsrUserID)) {
            if (isset($chk->UsrCompanyName)) {
                return trim($chk->UsrCompanyName);
            }
        }
        return '';
    }
    
    public function getPartnerCompany($userID = 0)
    {
        if ($userID <= 0 && isset($this->v["uID"]) 
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
        if (!session()->has('PowerScoreChecks') 
            || $GLOBALS["SL"]->REQ->has('refresh')) {
            $chk = RIIPowerScore::where('PsSubmissionProgress', 'LIKE', '147') 
                    // redirection page
                ->where('PsStatus', '=', $this->statusIncomplete)
                ->update([ 'PsStatus' => $this->v["defCmplt"] ]);
            $chk = RIIPowerScore::where('PsZipCode', 'NOT LIKE', '')
                ->whereNull('PsClimateLabel')
                ->get();
            if ($chk->isNotEmpty()) {
                $GLOBALS["SL"]->loadStates();
                foreach ($chk as $score) {
                    $zipRow = $GLOBALS["SL"]->states->getZipRow($score->PsZipCode);
                    $score->PsAshrae = $GLOBALS["SL"]->states->getAshrae($zipRow);
                    if (!isset($score->PsState) || trim($score->PsState) == '') {
                        if ($zipRow && isset($zipRow->ZipZip)) {
                            $score->PsState  = $zipRow->ZipState;
                            $score->PsCounty = $zipRow->ZipCounty;
                        }
                    }
                    $score->PsClimateLabel = $GLOBALS["SL"]->states
                        ->getAshraeZoneLabel($score->PsAshrae);
                    $score->save();
                }
            }
            session()->put('PowerScoreChecks', true);
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
                $coreRec = RIIPowerScore::find($coreID);
            }
            if (!$coreRec) {
                return false;
            }
            if (!isset($coreRec->PsSubmissionProgress) 
                || intVal($coreRec->PsSubmissionProgress) <= 0) {
                return true;
            }
            if (!isset($coreRec->PsZipCode) 
                || trim($coreRec->PsZipCode) == '') {
                return true;
            }
        }
        return false;
    }
    
    protected function recordIsIncomplete($coreTbl, $coreID, $coreRec = NULL)
    {
        if ($coreID > 0) {
            if (!isset($coreRec->PsID)) {
                $coreRec = RIIPowerScore::find($coreID);
            }
            return (!isset($coreRec->PsStatus) 
                || $coreRec->PsStatus == $this->statusIncomplete);
        }
        return false;
    }
    
    public function tblsInPackage()
    {
        if ($this->dbID == 1) {
            return [ 'PSUtilities', 'PSUtiliZips' ];
        }
        return [];
    }
    
    public function getStageNick($defID)
    {
        switch ($defID) {
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Mother Plants'):
                return 'Mother';
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Clone Plants'):
                return 'Clone';
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Vegetating Plants'): 
                return 'Veg';
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Flowering Plants'):
                return 'Flower';
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Drying/Curing'):
                return 'Dry';
        }
        return '';
    }
    
    protected function getAreaIdTypeName($areaID)
    {
        $area = $this->sessData->getRowById('PSAreas', $areaID);
        if ($area && isset($area->PsAreaType)) {
            return $GLOBALS["SL"]->def->getVal(
                'PowerScore Growth Stages', 
                $area->PsAreaType
            );
        }
        return '';
    }
    
    public function xmlAllAccess()
    {
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
                    $this->v["manufacts"][$manu->ManuID] = $manu->ManuName;
                }
            }
        }
        asort($this->v["manufacts"]);
        return $this->v["manufacts"];
    }
    
    protected function convertLightScoreType2ImportType($scoreType = 0)
    {
        switch (intVal($scoreType)) {
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'HID (double-ended HPS)'):
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'HID (single-ended HPS)'):
                return ['Double Ended HPS', 'Single Ended HPS', 'HID', 'HPS'];
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'HID (double-ended MH)'):
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'HID (single-ended MH)'):
                return ['MH', 'MH/HPS Lamps'];
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'CMH'):
                return ['Ceramic Metal Halide'];
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'Fluorescent'):
                return ['Fluorescent', 'Fluorescent + Halogen', 'Fluorescent Induction'];
            case $GLOBALS["SL"]->def
                ->getID('PowerScore Light Types', 'LED'):
                return ['LED'];
        }
        return [];
    }
    
    protected function convertLightImportType2ScoreType($importType = '')
    {
        switch (trim($importType)) {
            case 'Double Ended HPS': 
            case 'HID':
            case 'HPS':
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'HID (double-ended HPS)');
            case 'Single Ended HPS': 
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'HID (single-ended HPS)');
            case 'MH': 
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'HID (double-ended MH)');
            case 'Ceramic Metal Halide':
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'CMH');
            case 'Fluorescent': 
            case 'Fluorescent + Halogen': 
            case 'Fluorescent Induction': 
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'Fluorescent');
            case 'LED':
                return $GLOBALS["SL"]->def
                    ->getID('PowerScore Light Types', 'LED');
            case 'MH/HPS Lamps': 
            case 'CFL': 
            case 'Plasma': 
        }
        return 0;
    }
    
    protected function loadLightImportTypeConverts()
    {
        $this->v["lightImportTypeConvert"] = [];
        $chk = DB::table('RII_LightModels')
            ->distinct('LgtModTech')
            ->select('LgtModTech')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $mod) {
                $this->v["lightImportTypeConvert"][$mod->LgtModTech] 
                    = $this->convertLightImportType2ScoreType($mod->LgtModTech);
            }
        }
        return $this->v["lightImportTypeConvert"];
    }
    
    protected function getAllLightModels()
    {
        return DB::table('RII_LightModels')
            ->join('RII_Manufacturers', 'RII_Manufacturers.ManuID', 
                '=', 'RII_LightModels.LgtModManuID')
            ->orderBy('RII_Manufacturers.ManuName', 'asc')
            ->orderBy('RII_LightModels.LgtModName', 'asc')
            ->select('RII_LightModels.*')
            ->get();
    }
    
}

class ScoreUserInfo
{
    public $id            = 0;
    public $name          = '';
    public $email         = '';
    public $company       = '';
    public $manufacturers = [];

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
        if ($company != '') {
            $this->company = $company;
        } else {
            $chk = RIIUserInfo::where('UsrUserID', $this->id)
                ->first();
            if ($chk && isset($chk->UsrUserID)) {
                if (isset($chk->UsrCompanyName)) {
                    $this->company = trim($chk->UsrCompanyName);
                }
            }
        }
        $this->manufacturers = $this->getUserManufacturers();
        return true;
    }
    
    public function getUserManufacturers()
    {
        $ret = [];
        $chk = DB::table('RII_Manufacturers')
            ->join('RII_UserManufacturers', 'RII_Manufacturers.ManuID', 
                '=', 'RII_UserManufacturers.UsrManManuID')
            ->where('RII_UserManufacturers.UsrManUserID', $this->id)
            ->select(
                'RII_Manufacturers.ManuID',
                'RII_Manufacturers.ManuName'
            )
            ->orderBy('ManuName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $manu) {
                $ret[] = $manu;
            }
        }
        return $ret;
    }

}
