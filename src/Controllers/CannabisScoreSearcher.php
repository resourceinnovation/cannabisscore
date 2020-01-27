<?php
/**
  * CannabisScoreSearcher extends the SurvLoop Searcher for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSOwners;
use App\Models\RIIManufacturers;
use App\Models\RIIUserInfo;
use App\Models\RIIUserManufacturers;
use SurvLoop\Controllers\Searcher;

class CannabisScoreSearcher extends Searcher
{
    public function initExtra()
    {
        $this->v["defCmplt"] = 243;
        $this->v["defArch"]  = 364;
        $set = 'PowerScore Growth Stages';
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->def->getID($set, 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->def->getID($set, 'Clone Plants'),
            'Veg'    => $GLOBALS["SL"]->def->getID($set, 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->def->getID($set, 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->def->getID($set, 'Drying/Curing')
        ];
        $this->v["areaTypesFilt"] = [
            'Flower' => $this->v["areaTypes"]["Flower"],
            'Veg'    => $this->v["areaTypes"]["Veg"],
            'Clone'  => $this->v["areaTypes"]["Clone"],
            'Mother' => $this->v["areaTypes"]["Mother"]
        ];
        return true;
    }
    
    protected function getArchivedCoreIDs($coreTbl = '')
    {
        $ret = [];
        $chk = RIIPowerScore::where('ps_status', $this->v["defArch"])
            ->select('ps_id')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $rec) {
                $ret[] = $rec->ps_id;
            }
        }
        return $ret;
    }
    
    public function searchResultsXtra($treeID = 1)
    {
        if ($treeID <= 0) {
            $treeID = $GLOBALS["SL"]->treeID;
        }
        $this->v["eff"] = (($GLOBALS["SL"]->REQ->has('eff')) 
            ? trim($GLOBALS["SL"]->REQ->get('eff')) : 'Overall');
        $this->v["psid"] = (($GLOBALS["SL"]->REQ->has('ps')) 
            ? intVal($GLOBALS["SL"]->REQ->get('ps')) : 0);
        $this->v["powerscore"] = RIIPowerScore::find($this->v["psid"]);
        $this->v["fltFarm"] = (($GLOBALS["SL"]->REQ->has('fltFarm')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltFarm')) : 0);
        $this->v["fltFut"] = (($GLOBALS["SL"]->REQ->has('fltFut')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltFut')) : 0);
        $this->v["fltState"] = (($GLOBALS["SL"]->REQ->has('fltState')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltState')) : '');
        $this->v["fltClimate"] = (($GLOBALS["SL"]->REQ->has('fltClimate')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltClimate')) : '');
        $this->v["fltStateClim"] = (($GLOBALS["SL"]->REQ->has('fltStateClim')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltStateClim')) : '');
        $this->v["fltNoNWPCC"] = (($GLOBALS["SL"]->REQ->has('fltNoNWPCC')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltNoNWPCC')) : '');
        $this->v["fltNoLgtError"] = (($GLOBALS["SL"]->REQ->has('fltNoLgtError')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltNoLgtError')) : '');
        $this->v["fltLgtArt"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtArt')) 
            : [ 0, 0 ]);
        $this->v["fltLgtDep"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtDep')) 
            : [ 0, 0 ]);
        $this->v["fltLgtSun"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtSun')) 
            : [ 0, 0 ]);
        $this->v["fltLght"] = (($GLOBALS["SL"]->REQ->has('fltLght')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLght')) 
            : [ 0, 0 ]);
        $this->v["fltHvac"] = (($GLOBALS["SL"]->REQ->has('fltHvac')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltHvac')) 
            : [ 0, 0 ]);
        $this->v["fltSize"] = (($GLOBALS["SL"]->REQ->has('fltSize')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltSize')) : 0);
        $this->v["fltPerp"] = (($GLOBALS["SL"]->REQ->has('fltPerp')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltPerp')) : 0);
        $this->v["fltPump"] = (($GLOBALS["SL"]->REQ->has('fltPump')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltPump')) : 0);
        $this->v["fltWtrh"] = (($GLOBALS["SL"]->REQ->has('fltWtrh')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltWtrh')) : 0);
        $this->v["fltManu"] = (($GLOBALS["SL"]->REQ->has('fltManu')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltManu')) : 0);
        $this->v["fltAuto"] = (($GLOBALS["SL"]->REQ->has('fltAuto')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltAuto')) : 0);
        $this->v["fltVert"] = (($GLOBALS["SL"]->REQ->has('fltVert')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltVert')) : 0);
        $this->v["fltRenew"] = (($GLOBALS["SL"]->REQ->has('fltRenew')) 
            ? $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('fltRenew')) 
            : []);
        $this->v["fltCmpl"] = (($GLOBALS["SL"]->REQ->has('fltCmpl')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltCmpl')):243);
        $this->v["fltManuLgt"] = (($GLOBALS["SL"]->REQ->has('fltManuLgt')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltManuLgt')) : '');
        $this->v["fltCup"] = (($GLOBALS["SL"]->REQ->has('fltCup')) 
            ? intVal($GLOBALS["SL"]->REQ->get('fltCup')) : 0);
        $this->v["prtnOwn"] = 0;
        if (isset($GLOBALS["SL"]->x["partnerVersion"]) 
            && $GLOBALS["SL"]->x["partnerVersion"]
            && !$GLOBALS["SL"]->REQ->has('all')) {
            $this->v["prtnOwn"] = 1;
        }
        $this->searchFiltsURLXtra();
        return true;
    }
    
    public function searchFiltsURLXtra()
    {
        $this->v["sort"] = [ 'ps_id', 'desc' ];
        if ($GLOBALS["SL"]->REQ->has('sSort') && trim($GLOBALS["SL"]->REQ->sSort) != '') {
            $this->v["sort"][0] = $GLOBALS["SL"]->REQ->sSort;
            if ($GLOBALS["SL"]->REQ->has('sSortDir') 
                && in_array(trim($GLOBALS["SL"]->REQ->sSortDir), ['asc', 'desc'])) {
                $this->v["sort"][1] = $GLOBALS["SL"]->REQ->sSortDir;
            }
        }
        $this->v["xtraFltsDesc"] = '';
        $this->v["urlFlts"] = '&fltFarm=' . $this->v["fltFarm"];
        if ($GLOBALS["SL"]->REQ->has('lighting')) {
            $this->v["urlFlts"] .= '&lighting=1';
        }
        //if ($this->v["psid"] > 0) $this->v["urlFlts"] .= '&ps=' . $this->v["psid"];
        if (intVal($this->v["fltFut"]) > 0) {
            $this->v["urlFlts"] .= '&fltFut=' . $this->v["fltFut"];
        }
        if (intVal($this->v["fltCmpl"]) != 243) {
            $this->v["urlFlts"] .= '&fltCmpl=' . $this->v["fltCmpl"];
        }
        if ($this->v["fltState"] != '') {
            $this->v["urlFlts"] .= '&fltState=' . $this->v["fltState"];
        }
        if ($this->v["fltClimate"] != '') {
            $this->v["urlFlts"] .= '&fltClimate=' . $this->v["fltClimate"];
        }
        if ($this->v["fltStateClim"] != '') {
            $this->v["urlFlts"] .= '&fltStateClim=' . $this->v["fltStateClim"];
        }
        if ($this->v["fltNoNWPCC"] != '') {
            $this->v["urlFlts"] .= '&fltNoNWPCC=' . $this->v["fltNoNWPCC"];
        }
        if ($this->v["fltNoLgtError"] != '') {
            $this->v["urlFlts"] .= '&fltNoLgtError=' . $this->v["fltNoLgtError"];
        }
        if ($this->v["fltLght"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltLght"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltLght"][0]) 
                : '')
                . $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $this->v["fltLght"][1]);
            $this->v["urlFlts"] .= '&fltLght=' . $this->v["fltLght"][0] 
                . '-' . $this->v["fltLght"][1];
        }
        if ($this->v["fltHvac"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltHvac"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltHvac"][0]) 
                : '')
                . strtolower($GLOBALS["SL"]->def->getVal(
                    'PowerScore HVAC Systems', 
                    $this->v["fltHvac"][1]
                ));
            $this->v["urlFlts"] .= '&fltHvac=' . $this->v["fltHvac"][0] 
                . '-' . $this->v["fltHvac"][1];
        }
        if ($this->v["fltSize"] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltSize"] > 0) 
                ? $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $this->v["fltSize"]) 
                : '')
                . strtolower($GLOBALS["SL"]->def->getVal('Indoor Size Groups', $this->v["fltSize"]));
            $this->v["urlFlts"] .= '&fltSize=' . $this->v["fltSize"];
        }
        if ($this->v["fltPerp"] > 0) {
            $this->v["xtraFltsDesc"] .= ', perpetual farming';
            $this->v["urlFlts"] .= '&fltPerp=' . $this->v["fltPerp"];
        }
        if ($this->v["fltPump"] > 0) {
            $this->v["xtraFltsDesc"] .= ', water pumps';
            $this->v["urlFlts"] .= '&fltPump=' . $this->v["fltPump"];
        }
        if ($this->v["fltWtrh"] > 0) {
            $this->v["xtraFltsDesc"] .= ', mechanical water heating';
            $this->v["urlFlts"] .= '&fltWtrh=' . $this->v["fltWtrh"];
        }
        if ($this->v["fltManu"] > 0) {
            $this->v["xtraFltsDesc"] .= ', manual environmental controls';
            $this->v["urlFlts"] .= '&fltManu=' . $this->v["fltManu"];
        }
        if ($this->v["fltAuto"] > 0) {
            $this->v["xtraFltsDesc"] .= ', automatic environmental controls';
            $this->v["urlFlts"] .= '&fltAuto=' . $this->v["fltAuto"];
        }
        if ($this->v["fltVert"] > 0) {
            $this->v["xtraFltsDesc"] .= ', vertical stacking';
            $this->v["urlFlts"] .= '&fltVert=' . $this->v["fltVert"];
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            foreach ($this->v["fltRenew"] as $renew) {
                $this->v["xtraFltsDesc"] .= ', ' 
                    . $GLOBALS["SL"]->def->getVal('PowerScore Onsite Power Sources', $renew);
            }
            $this->v["urlFlts"] .= '&fltRenew=' . implode(',', $this->v["fltRenew"]);
        }
        if ($this->v["xtraFltsDesc"] != '') {
            $this->v["xtraFltsDesc"] = ' using <span class="wht">' 
                . substr($this->v["xtraFltsDesc"], 2) . '</span>';
        }
        return '';
    }
    
    public function getAllPublicCoreIDs($coreTbl = '')
    {
        if (trim($coreTbl) == '') {
            $coreTbl = $GLOBALS["SL"]->coreTbl;
        }
        $this->allPublicCoreIDs = [];
        $list = NULL;
        if ($coreTbl == 'powerscore') {
            if (isset($this->v["prtnOwn"]) 
                && $this->v["prtnOwn"] == 1) {
                // partner version filtered for their clients
                $list = DB::table('rii_powerscore')
                    ->join('rii_ps_owners', function ($join) {
                        $join->on('rii_powerscore.ps_user_id', '=', 'rii_ps_owners.ps_own_client_user')
                             ->where('rii_ps_owners.ps_own_partner_user', '=', Auth::user()->id)
                             ->orWhere('rii_powerscore.ps_user_id', '=', Auth::user()->id);
                    })
                    ->where('rii_powerscore.ps_status', $this->v["defCmplt"])
                    ->orderBy('rii_ps_owners.ps_own_client_name', 'asc')
                    ->orderBy('rii_powerscore.ps_id', 'desc')
                    ->get();
            } else {
                $list = RIIPowerScore::where('ps_status', $this->v["defCmplt"])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }   
        }
        if ($list && $list->isNotEmpty()) {
            foreach ($list as $l) {
                $this->allPublicCoreIDs[] = $l->getKey();
            }
        }
        return $this->allPublicCoreIDs;
    }
    
    public function filterAllPowerScoresPublic()
    {
        if (!isset($this->v["fltCmpl"])) {
            $this->searchResultsXtra();
        }
        $eval = "whereIn('ps_status', [" . (($this->v["fltCmpl"] == 0) 
            ? (($this->v["isAdmin"]) ? "242, 243, 364" : 243)
            : $this->v["fltCmpl"]) . "])->where('ps_time_type', " 
            . $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past') . ")";
        $psidLgtARS = $psidLghts = $psidHvac = $psidRenew 
            = $psidSize = $psidCups = $psidManuLgt = [];
        foreach (["flt_lgt_art", "flt_lgt_dep", "flt_lgt_sun"] as $flt) {
            $psidLgtARS[$flt] = [];
            if (isset($this->v[$flt][1])) {                 
                eval("\$chk = " . $GLOBALS["SL"]->modelPath('ps_areas') 
                    . "::where('" . (($flt == "fltLgtArt") ? 'ps_area_lgt_artif' 
                        : (($flt == "fltLgtDep") ? 'ps_area_lgt_dep' : 'ps_area_lgt_sun'))
                    . "', " . $this->v[$flt][1] . ")" . (($this->v[$flt][0] > 0) 
                        ? "->where('ps_area_type', " . $this->v[$flt][0] . ")" : "")
                    . "->where('ps_area_psid', '>', 0)"
                    . "->select('ps_area_psid')"
                    . "->get();");
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $ps) {
                        if (!in_array($ps->ps_area_psid, $psidLgtARS[$flt])) {
                            $psidLgtARS[$flt][] = $ps->ps_area_psid;
                        }
                    }
                }
            }
        }
        if ($this->v["fltLght"][1] > 0) {
            eval("\$chk = DB::table('rii_ps_areas')->join('rii_ps_light_types', function (\$join) {
                    \$join->on('rii_ps_areas.ps_area_id', '=', 'rii_ps_light_types.ps_lg_typ_area_id')
                        ->where('rii_ps_light_types.ps_lg_typ_light', " . $this->v["fltLght"][1] . ");
                })" . (($this->v["fltLght"][0] > 0) 
                    ? "->where('ps_area_type', " . $this->v["fltLght"][0] . ")" : "")
                . "->where('rii_ps_areas.ps_area_psid', '>', 0)"
                . "->select('rii_ps_areas.ps_area_psid')"
                . "->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->ps_area_psid, $psidLghts)) {
                        $psidLghts[] = $ps->ps_area_psid;
                    }
                }
            }
        }
        if ($this->v["fltHvac"][1] > 0) {                       
            eval("\$chk = " . $GLOBALS["SL"]->modelPath('PSAreas') . "::where('ps_area_hvac_type', " 
                . $this->v["fltHvac"][1] . ")" . (($this->v["fltHvac"][0] > 0) 
                    ? "->where('ps_area_type', " . $this->v["fltHvac"][0] . ")" 
                    : "")
                . "->where('ps_area_psid', '>', 0)->select('ps_area_psid')->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->ps_area_psid, $psidHvac)) {
                        $psidHvac[] = $ps->ps_area_psid;
                    }
                }
            }
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            $chk = RIIPSRenewables::whereIn('ps_rnw_renewable', $this->v["fltRenew"])
                ->where('ps_rnw_psid', '>', 0)
                ->select('ps_rnw_psid')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->ps_rnw_psid, $psidRenew)) {
                        $psidRenew[] = $ps->ps_rnw_psid;
                    }
                }
            }
        }
        if (trim($this->v["fltManuLgt"]) != '') {
            $chk = RIIManufacturers::find($this->v["fltManuLgt"]);
            if ($chk && isset($chk->manu_name)) {
                $this->addManuPSIDs($psidManuLgt, $chk);
            } else {
                $this->addAllManuPSIDs($psidManuLgt);
            }
        }
        if ($this->v["fltCup"] > 0) {
            $chk = RIIPSForCup::where('ps_cup_cup_id', $this->v["fltCup"])
                ->where('ps_cup_psid', '>', 0)
                ->select('ps_cup_psid')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->ps_cup_psid, $psidCups)) {
                        $psidCups[] = $ps->ps_cup_psid;
                    }
                }
            }
        }
        if ($this->v["fltSize"] > 0) {
            $range = $GLOBALS["CUST"]->getSizeDefRange($this->v["fltSize"]);
            $chk = RIIPSAreas::where('ps_area_type', $this->v["areaTypes"]["Flower"])
                ->where('ps_area_size', '>=', $range[0])
                ->where('ps_area_size', '<', $range[1])
                ->where('ps_area_psid', '>', 0)
                ->select('ps_area_psid')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->ps_area_psid, $psidSize)) {
                        $psidSize[] = $ps->ps_area_psid;
                    }
                }
            }
        }
        $state = '';
        if (isset($this->searchFilts["fltStateClim"]) 
            && trim($this->searchFilts["fltStateClim"]) != '') {
            $zones = $GLOBALS["SL"]->states->getAshraeGroupZones(
                $this->searchFilts["fltStateClim"]);
            if (sizeof($zones) > 0) {
                $eval .= "->whereIn('ps_ashrae', ['" . implode("', '", $zones) . "'])";
            } else { // is state
                $state = trim($this->searchFilts["fltStateClim"]);
            }
        } elseif (isset($this->searchFilts["state"]) && trim($this->searchFilts["state"]) != '') {
            $state = $this->searchFilts["state"];
        } elseif ($this->v["fltState"] != '') {
            $state = $this->v["fltState"];
        }
        if ($state != '') {
            $GLOBALS["SL"]->loadStates();
            $eval .= "->whereIn('ps_state', [ '" . implode("', '", 
                $GLOBALS["SL"]->states->getStateWhereIn($state)) . "' ])";
        }
        if ($this->v["fltClimate"] != '') {
            if ($this->v["fltClimate"] == 'US') {
                $eval .= "->where('ps_ashrae', 'NOT LIKE', 'Canada')";
            } else {
                $eval .= "->where('ps_ashrae', '" . $this->v["fltClimate"] . "')";
            }
        }

        if ($this->v["fltFarm"] > 0) {
            $eval .= "->where('ps_characterize', " . $this->v["fltFarm"] . ")";
        }
        if ($this->v["fltFut"] > 0) {
            $eval .= "->where('ps_time_type', " . $this->v["fltFut"] . ")";
        }
        foreach ($psidLgtARS as $flt => $list) {
            if ($this->v[$flt][1] > 0) {
                $eval .= "->whereIn('ps_id', [" . ((sizeof($list) > 0) 
                    ? implode(', ', $list) : 0) . "])";
            }
        }
        if ($this->v["fltLght"][1] > 0) {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidLghts) > 0) 
                ? implode(', ', $psidLghts) : 0) . "])";
        }
        if ($this->v["fltHvac"][1] > 0) {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidHvac) > 0) 
                ? implode(', ', $psidHvac) : 0) . "])";
        }
        if ($this->v["fltSize"] > 0) {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidSize) > 0) 
                ? implode(', ', $psidSize) : 0) . "])";
        }
        if ($this->v["fltPerp"] > 0) {
            $eval .= "->where('ps_harvest_batch', 1)";
        }
        if ($this->v["fltPump"] > 0) {
            $eval .= "->where('ps_has_water_pump', 1)";
        }
        if ($this->v["fltWtrh"] > 0) {
            $eval .= "->where('ps_heat_water', 1)";
        }
        if ($this->v["fltManu"] > 0) {
            $eval .= "->where('ps_controls', 1)";
        }
        if ($this->v["fltAuto"] > 0) {
            $eval .= "->where('ps_controls_auto', 1)";
        }
        if ($this->v["fltVert"] > 0) {
            $eval .= "->where('ps_vertical_stack', 1)";
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidRenew) > 0) 
                ? implode(', ', $psidRenew) : 0) . "])";
        }
        if (trim($this->v["fltManuLgt"]) != '') {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidManuLgt) > 0) 
                ? implode(', ', $psidManuLgt) : 0) . "])";
        }
        if ($this->v["fltCup"] > 0) {
            $eval .= "->whereIn('ps_id', [" . ((sizeof($psidCups) > 0) 
                ? implode(', ', $psidCups) : 0) . "])";
        }
        if (isset($this->v["fltNoNWPCC"]) 
            && trim($this->v["fltNoNWPCC"]) != '') {
            $eval .= "->where('ps_name', 'NOT LIKE', 'NWPCC%')";
        }
        if (isset($this->v["fltNoLgtError"]) 
            && trim($this->v["fltNoLgtError"]) != '') {
            $eval .= "->where('ps_lighting_error', 0)";
        }
        return $eval;
    }
    
    public function loadAllScoresPublic($xtra = '')
    {
        $eval = "\$this->v['allscores'] = "
            . $GLOBALS["SL"]->modelPath('powerscore') . "::" 
            . $this->filterAllPowerScoresPublic() . $xtra;
        if ($this->v["fltCmpl"] == 243) {
            $eval .= "->where('ps_effic_facility', '>', 0)"
                . "->where('ps_effic_production', '>', 0)";
        }
        $eval .= "->orderBy('" . $this->v["sort"][0] . "', '" 
            . $this->v["sort"][1] . "')->get();";
        eval($eval);
//echo '<br /><br /><br />' . $eval . '<br />getAllPowerScoreAvgsPublic( ' . $this->v["allscores"]->count() . '<br />'; exit;
        return true;
    }

    public function addAllManuPSIDs(&$psidManuLgt)
    {
        if (!isset($this->v["fltManuLgt"])) {
            return false;
        }
        $manus = $this->getUsrCompanyManuLnks($this->v["fltManuLgt"]);
        if ($manus && $manus->isNotEmpty()) {
            foreach ($manus as $userManu) {
                $manu = RIIManufacturers::find($userManu->usr_man_manu_id);
                $this->addManuPSIDs($psidManuLgt, $manu);
            }
        }
        return true;
    }

    protected function getUsrCompanyManuLnks($company = '')
    {
        $chk = RIIUserInfo::where('usr_company_name', 'LIKE', $company)
            ->first();
        if ($chk && isset($chk->usr_user_id)) {
            return RIIUserManufacturers::where('usr_man_user_id', $chk->usr_user_id)
                ->get();
        }
        return null;
    }

    public function getUsrCompanyManus()
    {
        $ret = [];
        if (isset($this->v["fltManuLgt"])) {
            $manus = $this->getUsrCompanyManuLnks($this->v["fltManuLgt"]);
            if ($manus && $manus->isNotEmpty()) {
                foreach ($manus as $userManu) {
                    $ret[] = RIIManufacturers::find($userManu->usr_man_manu_id);
                }
            }
        }
        return $ret;
    }

    protected function addManuPSIDs(&$psidManuLgt, $manu)
    {
        foreach ($this->v["areaTypes"] as $area => $areaType) {
            if (isset($manu->{ 'manu_ids' . $area })) {
                $tmpIDs = $GLOBALS["SL"]->mexplode(',', $manu->{ 'manu_ids' . $area });
                if (sizeof($tmpIDs) > 0) {
                    foreach ($tmpIDs as $ps) {
                        $psidManuLgt[] = $ps;
                    }
                }
            }
        }
        return true;
    }
    
    public function loadCurrScoreFltParams($dataSets = [])
    {
        $this->v["futureFlts"] = [];
        $this->v["futureFlts"][] = '&fltFarm=' . $dataSets["powerscore"][0]->ps_characterize;
        //$this->v["futureFlts"][] = '&fltState=' . $dataSets["powerscore"][0]->ps_state;
        $this->v["futureFlts"][] = '&fltClimate=' . $dataSets["powerscore"][0]->ps_ashrae;
        $size = $this->getFlowerSizeDefID($dataSets);
        if ($size > 0) {
            $this->v["futureFlts"][] = '&fltSize=' . $size;
        }
        if (isset($dataSets["powerscore"][0]->ps_harvest_batch) 
            && trim($dataSets["powerscore"][0]->ps_harvest_batch) != '') {
            $this->v["futureFlts"][] = '&fltPerp=' . $dataSets["powerscore"][0]->ps_harvest_batch;
        }
        if (isset($dataSets["powerscore"][0]->ps_has_water_pump) 
            && trim($dataSets["powerscore"][0]->ps_has_water_pump) != '') {
            $this->v["futureFlts"][] = '&fltPump=' . $dataSets["powerscore"][0]->ps_has_water_pump;
        }
        if (isset($dataSets["powerscore"][0]->ps_heat_water) 
            && trim($dataSets["powerscore"][0]->ps_heat_water) != '') {
            $this->v["futureFlts"][] = '&fltWtrh=' . $dataSets["powerscore"][0]->ps_heat_water;
        }
        if (isset($dataSets["powerscore"][0]->ps_controls) 
            && trim($dataSets["powerscore"][0]->ps_controls) != '') {
            $this->v["futureFlts"][] = '&fltManu=' . $dataSets["powerscore"][0]->ps_controls;
        }
        if (isset($dataSets["powerscore"][0]->ps_controls_auto) 
            && trim($dataSets["powerscore"][0]->ps_controls_auto) != '') {
            $this->v["futureFlts"][] = '&fltAuto=' . $dataSets["powerscore"][0]->ps_controls_auto;
        }
        if (isset($dataSets["powerscore"][0]->ps_vertical_stack) 
            && trim($dataSets["powerscore"][0]->ps_vertical_stack) != '') {
            $this->v["futureFlts"][] = '&fltVert=' . $dataSets["powerscore"][0]->ps_vertical_stack;
        }
        if (isset($dataSets["ps_renewables"]) && sizeof($dataSets["ps_renewables"]) > 0) {
            foreach ($dataSets["ps_renewables"] as $renew) {
                $this->v["futureFlts"][] = '&fltRenew=' . $renew->ps_rnw_renewable;
            }
        }
        if (isset($dataSets["ps_areas"]) && sizeof($dataSets["ps_areas"]) > 0) {
            foreach ($dataSets["ps_areas"] as $area) {
                if (isset($area->ps_area_has_stage) 
                    && intVal($area->ps_area_has_stage) == 1
                    && $area->ps_area_type != $this->v["areaTypes"]["Dry"]) {
                    if (isset($area->ps_area_hvac_type) && intVal($area->ps_area_hvac_type) > 0) {
                        $this->v["futureFlts"][] = '&fltHvac=' . $area->ps_area_type 
                            . '-' . $area->ps_area_hvac_type;
                    }
                    if (isset($dataSets["ps_light_types"]) 
                        && sizeof($dataSets["ps_light_types"]) > 0) {
                        foreach ($dataSets["ps_light_types"] as $lgt) {
                            if ($lgt->ps_lg_typ_area_id == $area->ps_area_id && isset($lgt->ps_lg_typ_light) 
                                && intVal($lgt->ps_lg_typ_light) > 0) {
                                $this->v["futureFlts"][] = '&fltLght=' . $area->ps_area_type 
                                    . '-' . $lgt->ps_lg_typ_light;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    protected function getFlowerSizeDefID($dataSets = [])
    {
        if (isset($dataSets["ps_areas"]) && sizeof($dataSets["ps_areas"]) > 0) {
            foreach ($dataSets["ps_areas"] as $a) {
                if ($a->ps_area_type == $this->v["areaTypes"]["Flower"]) {
                    return $GLOBALS["CUST"]->getSizeDefID($a->ps_area_size);
                }
            }
        }
        return 0;
    }
    
    public function getAllscoresAvgFlds()
    {
        $this->v["avgFlds"] = [
            'ps_effic_overall',
            'ps_effic_facility',
            'ps_effic_production',
            'ps_effic_lighting', 
            'ps_effic_hvac',
            'ps_grams',
            'ps_kwh',
            'ps_total_size'
        ];
        $this->v["psAvg"] = new RIIPowerScore;
        $this->v["psCnt"] = new RIIPowerScore;
        foreach ($this->v["avgFlds"] as $fld) {
            $this->v["psAvg"]->{ $fld } = $this->v["psCnt"]->{ $fld } = 0;
        }
        if ($this->v["allscores"] && $this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $i => $ps) {
                foreach ($this->v["avgFlds"] as $fld) {
                    if (strpos($fld, 'ps_effic') === false 
                        || (isset($ps->{ $fld . '_status' })
                            && intVal($ps->{ $fld . '_status' }) == $this->v["defCmplt"])) {
                        $this->v["psAvg"]->{ $fld } += (1*$ps->{ $fld });
                        $this->v["psCnt"]->{ $fld }++;
                    }
                }
            }
            foreach ($this->v["avgFlds"] as $fld) {
                if ($this->v["psCnt"]->{ $fld } > 0) {
                    $this->v["psAvg"]->{ $fld } = $this->v["psAvg"]->{ $fld }
                        /$this->v["psCnt"]->{ $fld };
                }
            }
            //$this->v["psAvg"]->ps_effic_facility 
            //    = $this->v["psAvg"]->ps_kwh/$this->v["psAvg"]->ps_total_size;
            //$this->v["psAvg"]->ps_effic_production 
            //    = $this->v["psAvg"]->ps_grams/$this->v["psAvg"]->ps_kwh;
        }
        return $this->v["psAvg"];
    }
    
    public function loadCupScoreIDs()
    {
        $this->v["cultClassicIds"] = $this->v["emeraldIds"] = [];
        $cupDef = $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic');
        $chk = RIIPSForCup::where('ps_cup_cup_id', $cupDef)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) {
                $this->v["cultClassicIds"][] = $c->ps_cup_psid;
            }
        }
        $cupDef = $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Emerald Cup Regenerative Award');
        $chk = RIIPSForCup::where('ps_cup_cup_id', $cupDef)
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) {
                $this->v["emeraldIds"][] = $c->ps_cup_psid;
            }
        }
        return true;
    }
    
    
}