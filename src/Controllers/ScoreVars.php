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

use Illuminate\Http\Request;
use App\Models\RIIPowerScore;
use SurvLoop\Controllers\TreeSurvForm;

class ScoreVars extends TreeSurvForm
{
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function initExtra(Request $request)
    {
        // Shortcuts...
        $this->v["defCmplt"] = $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete');
        $this->v["farmTypes"] = [
            'Indoor'           => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Indoor'),
            'Greenhouse/Mixed' => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Greenhouse/Hybrid/Mixed Light'),
            'Outdoor'          => $GLOBALS["SL"]->def->getID('PowerScore Farm Types', 'Outdoor')
            ];
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone Plants'),
            'Veg'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing')
            ];
        $this->v["areaTypesFilt"] = [
            'Flower' => $this->v["areaTypes"]["Flower"],
            'Veg'    => $this->v["areaTypes"]["Veg"],
            'Clone'  => $this->v["areaTypes"]["Clone"],
            'Mother' => $this->v["areaTypes"]["Mother"]
            ];
            
            
        // Establishing Main Navigation Organization, with Node ID# and Section Titles
        $this->majorSections = [];
        if ($GLOBALS["SL"]->treeID == 1) {
            $this->majorSections[] = [45,  'Your Farm',            'active'];
            $this->majorSections[] = [64,  'Growing Environments', 'active'];
            $this->majorSections[] = [608, 'Lighting',             'active'];
            $this->majorSections[] = [609, 'HVAC',                 'active'];
            $this->majorSections[] = [65,  'Annual Totals',        'active'];
            $this->majorSections[] = [844, 'Other Techniques',     'active'];
            //$this->majorSections[] = [67,  'Contact',              'active'];
            $this->minorSections = [ [], [], [], [], [], [], [] ];
        }
        
        //$GLOBALS["SL"]->addTopNavItem('Calculate PowerScore', '/start/calculator');
        return true;
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
    
    // Initializing a bunch of things which are not [yet] automatically determined by the software
    protected function loadExtra()
    {
        if (!session()->has('PowerScoreChecks') || $GLOBALS["SL"]->REQ->has('refresh')) {
            $chk = RIIPowerScore::where('PsSubmissionProgress', 'LIKE', '147') // redirection page
                ->where('PsStatus', '=', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
                ->update([ 'PsStatus' => $this->v["defCmplt"] ]);
            $chk = RIIPowerScore::where('PsZipCode', 'NOT LIKE', '')
                ->whereNull('PsAshrae')
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
            if (!isset($coreRec->PsSubmissionProgress) || intVal($coreRec->PsSubmissionProgress) <= 0) {
                return true;
            }
            if (!isset($coreRec->PsZipCode) || trim($coreRec->PsZipCode) == '') {
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
//echo 'recordIsIncomplete(' . $coreTbl . ', ' . $coreID . ', status#' . $coreRec->PsStatus . '<br />';
            return (!isset($coreRec->PsStatus) 
                || $coreRec->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'));
        }
        return false;
    }
    
    public function tblsInPackage()
    {
        if ($this->dbID == 1) {
            return ['PSUtilities', 'PSUtiliZips'];
        }
        return [];
    }
    
    public function getStageNick($defID)
    {
        switch ($defID) {
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Mother Plants'):     return 'Mother';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Clone Plants'):      return 'Clone';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Vegetating Plants'): return 'Veg';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Flowering Plants'):  return 'Flower';
            case $GLOBALS["SL"]->def->getID('PowerScore Growth Stages', 'Drying/Curing'):     return 'Dry';
        }
        return '';
    }
    
    public function xmlAllAccess()
    {
        return false;
    }
    
    protected function allTechEmpty()
    {
        $ret = [ 141 => 0, 142 => 0 ]; // medical, recreational
        foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name) $ret[$fld] = 0;
        foreach ($GLOBALS["CUST"]->psContact() as $fld => $name) $ret[$fld] = 0;
        
        return $ret;
    }
    
}