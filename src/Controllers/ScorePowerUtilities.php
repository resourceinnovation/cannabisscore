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

use Illuminate\Support\Facades\Storage;
use App\Models\SLZips;
use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use CannabisScore\Controllers\ScoreLightModels;

class ScorePowerUtilities extends ScoreLightModels
{
    
    protected function chkUtilityOffers()
    {
        $this->v["utilOffer"] = [ '', '' ];
        $GLOBALS["SL"]->loadStates();
        /*
        $this->v["utilOffer"][0] = $GLOBALS["SL"]->states->getState($this->sessData->dataSets["powerscore"][0]->ps_state)
            . ' Energy Group';
        $this->v["utilOffer"][1] = '/start/referral/?new=1&u=6&s=' . $this->coreID;
        */
        return $this->v["utilOffer"];
    }
    
    public function loadUtils()
    {
        $this->v["powerUtils"] = $this->v["powerUtilsInd"] = [];
        $cacheFile = '/cache/php/rii-utils.php';
        if ((!$GLOBALS["SL"]->REQ->has('refresh') 
                || intVal($GLOBALS["SL"]->REQ->refresh) != 1)
            && file_exists($cacheFile)) {
            $cache = Storage::get($cacheFile);
            eval($cache);
        } else {
            $cache = '';
            $chk = RIIPSUtilities::orderBy('ps_ut_name', 'asc')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $i => $u) {
                    $cache .= '$'.'this->v["powerUtilsInd"][' . $u->ps_ut_id 
                        . '] = ' . sizeof($this->v["powerUtils"]) . ';' . "\n"
                        . '$' . 'this->v["powerUtils"][] = [
                            "id"     => ' . $u->ps_ut_id . ', 
                            "name"   => "' . $u->ps_ut_name . '", 
                            "zips"   => [], 
                            "states" => []
                        ];' . "\n";
                }
            }
            if (file_exists($cacheFile)) {
                Storage::delete($cacheFile);
            }
            Storage::put($cacheFile, $cache);
            eval($cache);
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilZips()
    {
        $this->loadUtils();
        $cacheFile = '/cache/php/rii-util-zips.php';
        if ((!$GLOBALS["SL"]->REQ->has('refresh') 
                || intVal($GLOBALS["SL"]->REQ->refresh) != 2)
            && file_exists($cacheFile)) {
            $cache = Storage::get($cacheFile);
            eval($cache);
        } else {
            $cache = '';
            $chk = RIIPSUtiliZips::get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $i => $uz) {
                    if (isset($uz->ps_ut_zp_zip_code) 
                        && isset($this->v["powerUtilsInd"][$uz->ps_ut_zp_util_id])) {
                        $ind = $this->v["powerUtilsInd"][$uz->ps_ut_zp_util_id];
                        if (!in_array($uz->ps_ut_zp_zip_code, 
                            $this->v["powerUtils"][$ind]["zips"])) {
                            $this->v["powerUtils"][$ind]["zips"][] = $uz->ps_ut_zp_zip_code;
                        }
                    }
                }
                if (sizeof($this->v["powerUtils"]) > 0) {
                    foreach ($this->v["powerUtils"] as $ind => $utils) {
                        if (sizeof($this->v["powerUtils"][$ind]["zips"]) > 0) {
                            $cache .= '$'.'this->v["powerUtils"][' . $ind . ']["zips"] = ["' 
                                . implode('", "', $this->v["powerUtils"][$ind]["zips"])
                                . '"];';
                        }
                    }
                }
            }
            if (file_exists($cacheFile)) {
                Storage::delete($cacheFile);
            }
            Storage::put($cacheFile, $cache);
        }
        return $this->v["powerUtils"];
    }
    
    public function getUtilStates()
    {
        $this->getUtilZips();
        $cacheFile = '/cache/php/rii-util-states.php';
        if ((!$GLOBALS["SL"]->REQ->has('refresh') 
                || intVal($GLOBALS["SL"]->REQ->refresh) != 3) 
            && file_exists($cacheFile)) {
            $cache = Storage::get($cacheFile);
            eval($cache);
        } else {
            $cache = '';
            if (sizeof($this->v["powerUtils"]) > 0) {
                foreach ($this->v["powerUtils"] as $ind => $u) {
                    $chk = SLZips::whereIn('zip_zip', $u["zips"])
                        ->select('zip_state')
                        ->distinct()
                        ->get();
                    if ($chk->isNotEmpty()) {
                        foreach ($chk as $i => $z) {
                            if (!in_array($z->zip_state, 
                                $this->v["powerUtils"][$ind]["states"])) {
                                $this->v["powerUtils"][$ind]["states"][] = $z->zip_state;
                            }
                        }
                    }
                }
                if (sizeof($this->v["powerUtils"]) > 0) {
                    foreach ($this->v["powerUtils"] as $ind => $utils) {
                        if (sizeof($this->v["powerUtils"][$ind]["states"]) > 0) {
                            $cache .= '$'.'this->v["powerUtils"][' . $ind . ']["states"] = ["' 
                                . implode('", "', $this->v["powerUtils"][$ind]["states"])
                                . '"];';
                        }
                    }
                }
            }
            if (file_exists($cacheFile)) {
                Storage::delete($cacheFile);
            }
            Storage::put($cacheFile, $cache);
        }
        return $this->v["powerUtils"];
    }
    
    public function getStateUtils()
    {
        $this->getUtilStates();
        $this->v["statePowerUtils"] = [];
        $cacheFile = '/cache/php/rii-util-by-state.php';
        if ((!$GLOBALS["SL"]->REQ->has('refresh') 
                || intVal($GLOBALS["SL"]->REQ->refresh) != 4) 
            && file_exists($cacheFile)) {
            $cache = Storage::get($cacheFile);
            eval($cache);
        } else {
            $cache = '';
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
                ksort($this->v["statePowerUtils"]);
                if (sizeof($this->v["statePowerUtils"]) > 0) {
                    foreach ($this->v["statePowerUtils"] as $s => $utils) {
                        if (sizeof($this->v["statePowerUtils"][$s]) > 0) {
                            $cache .= '$'.'this->v["statePowerUtils"][' . $s 
                                . '] = [' . implode(', ', $utils) . '];';
                        }
                    }
                }
            }
            if (file_exists($cacheFile)) {
                Storage::delete($cacheFile);
            }
            Storage::put($cacheFile, $cache);
        }
        return ;
    }
    
}