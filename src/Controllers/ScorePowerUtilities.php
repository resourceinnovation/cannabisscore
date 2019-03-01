<?php
/**
  * ScorePowerUtilities is the mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class handles lookups and processes for electric utilities.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use CannabisScore\Controllers\ScoreLightModels;

class ScorePowerUtilities extends ScoreLightModels
{
    
    protected function chkUtilityOffers()
    {
        $this->v["utilOffer"] = ['', ''];
        $GLOBALS["SL"]->loadStates();
        /*
        $this->v["utilOffer"][0] = $GLOBALS["SL"]->states->getState($this->sessData->dataSets["PowerScore"][0]->PsState)
            . ' Energy Group';
        $this->v["utilOffer"][1] = '/start/referral/?new=1&u=6&s=' . $this->coreID;
        */
        return $this->v["utilOffer"];
    }
    
    public function loadUtils()
    {
        $this->v["powerUtils"] = $this->v["powerUtilsInd"] = [];
        $chk = RIIPSUtilities::orderBy('PsUtName', 'asc')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $u) {
                $this->v["powerUtilsInd"][$u->PsUtID] = sizeof($this->v["powerUtils"]);
                $this->v["powerUtils"][] = [
                    "id"     => $u->PsUtID, 
                    "name"   => $u->PsUtName, 
                    "zips"   => [], 
                    "states" => []
                    ];
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilZips()
    {
        $this->loadUtils();
        $chk = RIIPSUtiliZips::get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $i => $uz) {
                if (isset($uz->PsUtZpZipCode) && isset($this->v["powerUtilsInd"][$uz->PsUtZpUtilID])) {
                    $ind = $this->v["powerUtilsInd"][$uz->PsUtZpUtilID];
                    if (!in_array($uz->PsUtZpZipCode, $this->v["powerUtils"][$ind]["zips"])) {
                        $this->v["powerUtils"][$ind]["zips"][] = $uz->PsUtZpZipCode;
                    }
                }
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilStates()
    {
        $this->getUtilZips();
        if (sizeof($this->v["powerUtils"]) > 0) {
            foreach ($this->v["powerUtils"] as $ind => $u) {
                $chk = SLZips::whereIn('ZipZip', $u["zips"])
                    ->select('ZipState')
                    ->distinct()
                    ->get();
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $i => $z) {
                        if (!in_array($z->ZipState, $this->v["powerUtils"][$ind]["states"])) {
                            $this->v["powerUtils"][$ind]["states"][] = $z->ZipState;
                        }
                    }
                }
            }
        }
        return $this->v["powerUtils"];
    }
    
    public function getStateUtils()
    {
        $this->getUtilStates();
        $this->v["statePowerUtils"] = [];
        if (sizeof($this->v["powerUtils"]) > 0) {
            $GLOBALS["SL"]->loadStates();
            foreach ($this->v["powerUtils"] as $ind => $u) {
                if (sizeof($u["states"]) > 0) {
                    foreach ($u["states"] as $s) {
                        $s = $GLOBALS["SL"]->states->getState($s);
                        if (!isset($this->v["statePowerUtils"][$s])) {
                            $this->v["statePowerUtils"][$s] = [];
                        }
                        if (!in_array($u["id"], $this->v["statePowerUtils"][$s])) {
                            $this->v["statePowerUtils"][$s][] = $u["id"];
                        }
                    }
                }
            }
        }
        return ksort($this->v["statePowerUtils"]);
    }
    
}