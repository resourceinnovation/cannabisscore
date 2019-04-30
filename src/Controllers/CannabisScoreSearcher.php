<?php
/**
  * CannabisScoreSearcher extends the SurvLoop Searcher for some hard-coded overrides.
  *
  * Open Police Complaints
  * @package  flexyourrights/openpolice
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSForCup;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSOwners;
use SurvLoop\Controllers\Searcher;

class CannabisScoreSearcher extends Searcher
{
    public function initExtra()
    {
        $this->v["defCmplt"] = 243;
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
        return true;
    }
    
    protected function getArchivedCoreIDs($coreTbl = '')
    {
        $ret = [];
        $chk = RIIPowerScore::where('PsStatus', $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived'))
            ->select('PsID')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $rec) {
                $ret[] = $rec->PsID;
            }
        }
        return $ret;
    }
    
    public function searchResultsXtra($treeID = 1)
    {
        if ($treeID <= 0) {
            $treeID = $GLOBALS["SL"]->treeID;
        }
        $this->v["eff"] = (($GLOBALS["SL"]->REQ->has('eff')) ? trim($GLOBALS["SL"]->REQ->get('eff')) : 'Overall');
        $this->v["psid"] = (($GLOBALS["SL"]->REQ->has('ps')) ? intVal($GLOBALS["SL"]->REQ->get('ps')) : 0);
        $this->v["powerscore"] = RIIPowerScore::find($this->v["psid"]);
        $this->v["fltFarm"] = (($GLOBALS["SL"]->REQ->has('fltFarm')) ? intVal($GLOBALS["SL"]->REQ->get('fltFarm')) : 0);
        $this->v["fltFut"] = (($GLOBALS["SL"]->REQ->has('fltFut')) ? intVal($GLOBALS["SL"]->REQ->get('fltFut')) : 0);
        $this->v["fltState"] = (($GLOBALS["SL"]->REQ->has('fltState')) ? trim($GLOBALS["SL"]->REQ->get('fltState')):'');
        $this->v["fltClimate"] = (($GLOBALS["SL"]->REQ->has('fltClimate')) 
            ? trim($GLOBALS["SL"]->REQ->get('fltClimate')) : '');
        $this->v["fltLgtArt"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtArt')) : [ 0, 0 ]);
        $this->v["fltLgtDep"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtDep')) : [ 0, 0 ]);
        $this->v["fltLgtSun"] = (($GLOBALS["SL"]->REQ->has('fltLgtArt')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLgtSun')) : [ 0, 0 ]);
        $this->v["fltLght"] = (($GLOBALS["SL"]->REQ->has('fltLght')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltLght')) : [ 0, 0 ]);
        $this->v["fltHvac"] = (($GLOBALS["SL"]->REQ->has('fltHvac')) 
            ? $GLOBALS["SL"]->splitNumDash($GLOBALS["SL"]->REQ->get('fltHvac')) : [ 0, 0 ]);
        $this->v["fltSize"] = (($GLOBALS["SL"]->REQ->has('fltSize')) ? intVal($GLOBALS["SL"]->REQ->get('fltSize')) : 0);
        $this->v["fltPerp"] = (($GLOBALS["SL"]->REQ->has('fltPerp')) ? intVal($GLOBALS["SL"]->REQ->get('fltPerp')) : 0);
        $this->v["fltPump"] = (($GLOBALS["SL"]->REQ->has('fltPump')) ? intVal($GLOBALS["SL"]->REQ->get('fltPump')) : 0);
        $this->v["fltWtrh"] = (($GLOBALS["SL"]->REQ->has('fltWtrh')) ? intVal($GLOBALS["SL"]->REQ->get('fltWtrh')) : 0);
        $this->v["fltManu"] = (($GLOBALS["SL"]->REQ->has('fltManu')) ? intVal($GLOBALS["SL"]->REQ->get('fltManu')) : 0);
        $this->v["fltAuto"] = (($GLOBALS["SL"]->REQ->has('fltAuto')) ? intVal($GLOBALS["SL"]->REQ->get('fltAuto')) : 0);
        $this->v["fltVert"] = (($GLOBALS["SL"]->REQ->has('fltVert')) ? intVal($GLOBALS["SL"]->REQ->get('fltVert')) : 0);
        $this->v["fltRenew"] = (($GLOBALS["SL"]->REQ->has('fltRenew')) 
            ? $GLOBALS["SL"]->mexplode(',', $GLOBALS["SL"]->REQ->get('fltRenew')) : []);
        $this->v["fltCmpl"] = (($GLOBALS["SL"]->REQ->has('fltCmpl')) ? intVal($GLOBALS["SL"]->REQ->get('fltCmpl')):243);
        $this->v["fltCup"] = (($GLOBALS["SL"]->REQ->has('fltCup')) ? intVal($GLOBALS["SL"]->REQ->get('fltCup')) : 0);
        $this->v["prtnOwn"] = 0;
        if (isset($GLOBALS["SL"]->x["partnerVersion"]) && $GLOBALS["SL"]->x["partnerVersion"]
            && !$GLOBALS["SL"]->REQ->has('all')) {
            $this->v["prtnOwn"] = 1;
        }
        $this->searchFiltsURLXtra();
        return true;
    }
    
    public function searchFiltsURLXtra()
    {
        $this->v["sort"] = [ 'PsID', 'desc' ];
        if ($GLOBALS["SL"]->REQ->has('srt') && trim($GLOBALS["SL"]->REQ->get('srt')) != '') {
            $this->v["sort"][0] = $GLOBALS["SL"]->REQ->get('srt');
            if ($GLOBALS["SL"]->REQ->has('srta') && in_array(trim($GLOBALS["SL"]->REQ->get('srta')), ['asc', 'desc'])) {
                $this->v["sort"][1] = $GLOBALS["SL"]->REQ->get('srta');
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
        if ($this->v["fltLght"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltLght"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltLght"][0]) : '')
                . $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $this->v["fltLght"][1]);
            $this->v["urlFlts"] .= '&fltLght=' . $this->v["fltLght"][0] . '-' . $this->v["fltLght"][1];
        }
        if ($this->v["fltHvac"][1] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltHvac"][0] > 0) 
                ? $GLOBALS["SL"]->def->getVal('PowerScore Growth Stages', $this->v["fltHvac"][0]) : '')
                . strtolower($GLOBALS["SL"]->def->getVal('PowerScore HVAC Systems', $this->v["fltHvac"][1]));
            $this->v["urlFlts"] .= '&fltHvac=' . $this->v["fltHvac"][0] . '-' . $this->v["fltHvac"][1];
        }
        if ($this->v["fltSize"] > 0) {
            $this->v["xtraFltsDesc"] .= ', ' . (($this->v["fltSize"] > 0) 
                ? $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $this->v["fltSize"]) : '')
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
            $this->v["xtraFltsDesc"] = ' using <span class="wht">' . substr($this->v["xtraFltsDesc"], 2) . '</span>';
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
        if ($coreTbl == 'PowerScore') {
            if ($this->v["prtnOwn"] == 1) { // partner version filtered for their clients
                $list = DB::table('RII_PowerScore')
                    ->join('RII_PSOwners', function ($join) {
                        $join->on('RII_PowerScore.PsUserID', '=', 'RII_PSOwners.PsOwnClientUser')
                             ->where('RII_PSOwners.PsOwnPartnerUser', '=', Auth::user()->id)
                             ->orWhere('RII_PowerScore.PsUserID', '=', Auth::user()->id);
                    })
                    ->where('RII_PowerScore.PsStatus', $this->v["defCmplt"])
                    ->orderBy('RII_PSOwners.PsOwnClientName', 'asc')
                    ->orderBy('RII_PowerScore.PsID', 'desc')
                    ->get();
            } else {
                $list = RIIPowerScore::where('PsStatus', $this->v["defCmplt"])
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
        $eval = "whereIn('PsStatus', [" . (($this->v["fltCmpl"] == 0) ? (($this->v["isAdmin"]) ? "242, 243, 364" : 243)
            : $this->v["fltCmpl"]) . "])->where('PsTimeType', " 
            . $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past') . ")";
        $psidLgtARS = $psidLghts = $psidHvac = $psidRenew = $psidSize = $psidCups = [];
        foreach (["fltLgtArt", "fltLgtDep", "fltLgtSun"] as $flt) {
            $psidLgtARS[$flt] = [];
            if (isset($this->v[$flt][1])) {                 
                eval("\$chk = " . $GLOBALS["SL"]->modelPath('PSAreas') . "::where('" . (($flt == "fltLgtArt") 
                    ? 'PsAreaLgtArtif' : (($flt == "fltLgtDep") ? 'PsAreaLgtDep' : 'PsAreaLgtSun'))
                    . "', " . $this->v[$flt][1] . ")" . (($this->v[$flt][0] > 0) ? "->where('PsAreaType', " 
                    . $this->v[$flt][0] . ")" : "") . "->where('PsAreaPSID', '>', 0)->select('PsAreaPSID')->get();");
                if ($chk->isNotEmpty()) {
                    foreach ($chk as $ps) {
                        if (!in_array($ps->PsAreaPSID, $psidLgtARS[$flt])) {
                            $psidLgtARS[$flt][] = $ps->PsAreaPSID;
                        }
                    }
                }
            }
        }
        if ($this->v["fltLght"][1] > 0) {
            eval("\$chk = DB::table('RII_PSAreas')->join('RII_PSLightTypes', function (\$join) {
                    \$join->on('RII_PSAreas.PsAreaID', '=', 'RII_PSLightTypes.PsLgTypAreaID')
                        ->where('RII_PSLightTypes.PsLgTypLight', " . $this->v["fltLght"][1] . ");
                })" . (($this->v["fltLght"][0] > 0) ? "->where('PsAreaType', " . $this->v["fltLght"][0] . ")" : "")
                . "->where('RII_PSAreas.PsAreaPSID', '>', 0)->select('RII_PSAreas.PsAreaPSID')->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsAreaPSID, $psidLghts)) {
                        $psidLghts[] = $ps->PsAreaPSID;
                    }
                }
            }
        }
        if ($this->v["fltHvac"][1] > 0) {                       
            eval("\$chk = " . $GLOBALS["SL"]->modelPath('PSAreas') . "::where('PsAreaHvacType', " 
                . $this->v["fltHvac"][1] . ")" . (($this->v["fltHvac"][0] > 0) ? "->where('PsAreaType', " 
                . $this->v["fltHvac"][0] . ")" : "") . "->where('PsAreaPSID', '>', 0)->select('PsAreaPSID')->get();");
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsAreaPSID, $psidHvac)) {
                        $psidHvac[] = $ps->PsAreaPSID;
                    }
                }
            }
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            $chk = RIIPSRenewables::whereIn('PsRnwRenewable', $this->v["fltRenew"])
                ->where('PsRnwPSID', '>', 0)
                ->select('PsRnwPSID')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsRnwPSID, $psidRenew)) {
                        $psidRenew[] = $ps->PsRnwPSID;
                    }
                }
            }
        }
        if ($this->v["fltCup"] > 0) {
            $chk = RIIPSForCup::where('PsCupCupID', $this->v["fltCup"])
                ->where('PsCupPSID', '>', 0)
                ->select('PsCupPSID')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsCupPSID, $psidCups)) {
                        $psidCups[] = $ps->PsCupPSID;
                    }
                }
            }
        }
        if ($this->v["fltSize"] > 0) {
            $range = $this->getSizeDefRange($this->v["fltSize"]);
            $chk = RIIPSAreas::where('PsAreaType', $this->v["areaTypes"]["Flower"])
                ->where('PsAreaSize', '>=', $range[0])
                ->where('PsAreaSize', '<', $range[1])
                ->where('PsAreaPSID', '>', 0)
                ->select('PsAreaPSID')
                ->get();
            if ($chk->isNotEmpty()) {
                foreach ($chk as $ps) {
                    if (!in_array($ps->PsAreaPSID, $psidSize)) {
                        $psidSize[] = $ps->PsAreaPSID;
                    }
                }
            }
        }
        if ($this->v["fltState"] != '') {
            $GLOBALS["SL"]->loadStates();
            $eval .= "->whereIn('PsState', [ '" . implode("', '", 
                $GLOBALS["SL"]->states->getStateWhereIn($this->v["fltState"])) . "' ])";
        }
        if ($this->v["fltClimate"] != '') {
            if ($this->v["fltClimate"] == 'US') {
                $eval .= "->where('PsAshrae', 'NOT LIKE', 'Canada')";
            } else {
                $eval .= "->where('PsAshrae', '" . $this->v["fltClimate"] . "')";
            }
        }
        if ($this->v["fltFarm"] > 0) {
            $eval .= "->where('PsCharacterize', " . $this->v["fltFarm"] . ")";
        }
        if ($this->v["fltFut"] > 0) {
            $eval .= "->where('PsTimeType', " . $this->v["fltFut"] . ")";
        }
        foreach ($psidLgtARS as $flt => $list) {
            if ($this->v[$flt][1] > 0) {
                $eval .= "->whereIn('PsID', [" . ((sizeof($list) > 0) ? implode(', ', $list) : 0) . "])";
            }
        }
        if ($this->v["fltLght"][1] > 0) {
            $eval .= "->whereIn('PsID', [" . ((sizeof($psidLghts) > 0) ? implode(', ', $psidLghts) : 0) . "])";
        }
        if ($this->v["fltHvac"][1] > 0) {
            $eval .= "->whereIn('PsID', [" . ((sizeof($psidHvac) > 0) ? implode(', ', $psidHvac) : 0) . "])";
        }
        if ($this->v["fltSize"] > 0) {
            $eval .= "->whereIn('PsID', [" . ((sizeof($psidSize) > 0) ? implode(', ', $psidSize) : 0) . "])";
        }
        if ($this->v["fltPerp"] > 0) {
            $eval .= "->where('PsHarvestBatch', 1)";
        }
        if ($this->v["fltPerp"] > 0) {
            $eval .= "->where('PsHarvestBatch', 1)";
        }
        if ($this->v["fltPump"] > 0) {
            $eval .= "->where('PsHasWaterPump', 1)";
        }
        if ($this->v["fltWtrh"] > 0) {
            $eval .= "->where('PsHeatWater', 1)";
        }
        if ($this->v["fltManu"] > 0) {
            $eval .= "->where('PsControls', 1)";
        }
        if ($this->v["fltAuto"] > 0) {
            $eval .= "->where('PsControlsAuto', 1)";
        }
        if ($this->v["fltVert"] > 0) {
            $eval .= "->where('PsVerticalStack', 1)";
        }
        if (sizeof($this->v["fltRenew"]) > 0) {
            $eval .= "->whereIn('PsID', [" . ((sizeof($psidRenew) > 0) ? implode(', ', $psidRenew) : 0) . "])";
        }
        if ($this->v["fltCup"] > 0) {
            $eval .= "->whereIn('PsID', [" . ((sizeof($psidCups) > 0) ? implode(', ', $psidCups) : 0) . "])";
        }
        return $eval;
    }
    
    public function loadAllScoresPublic($xtra = '')
    {
        $eval = "\$this->v['allscores'] = " . $GLOBALS["SL"]->modelPath('PowerScore') . "::" 
            . $this->filterAllPowerScoresPublic() . $xtra
            . (($this->v["fltCmpl"] == 243) ? "->where('PsEfficFacility', '>', 0)->where('PsEfficProduction', '>', 0)"
                . "->where('PsEfficLighting', '>', 0)->where('PsEfficHvac', '>', 0)" : "")
            . "->orderBy('" . $this->v["sort"][0] . "', '" . $this->v["sort"][1] . "')->get();";
        eval($eval);
//echo '<br /><br /><br />' . $eval . '<br />getAllPowerScoreAvgsPublic( ' . $this->v["allscores"]->count() . '<br />';
        return true;
    }
    
    public function loadCurrScoreFltParams($dataSets = [])
    {
        $this->v["futureFlts"] = [];
        $this->v["futureFlts"][] = '&fltFarm=' . $dataSets["PowerScore"][0]->PsCharacterize;
        $this->v["futureFlts"][] = '&fltState=' . $dataSets["PowerScore"][0]->PsState;
        $this->v["futureFlts"][] = '&fltClimate=' . $dataSets["PowerScore"][0]->PsAshrae;
        $size = $this->getFlowerSizeDefID($dataSets);
        if ($size > 0) {
            $this->v["futureFlts"][] = '&fltSize=' . $size;
        }
        if (isset($dataSets["PowerScore"][0]->PsHarvestBatch) 
            && trim($dataSets["PowerScore"][0]->PsHarvestBatch) != '') {
            $this->v["futureFlts"][] = '&fltPerp=' . $dataSets["PowerScore"][0]->PsHarvestBatch;
        }
        if (isset($dataSets["PowerScore"][0]->PsHasWaterPump) 
            && trim($dataSets["PowerScore"][0]->PsHasWaterPump) != '') {
            $this->v["futureFlts"][] = '&fltPump=' . $dataSets["PowerScore"][0]->PsHasWaterPump;
        }
        if (isset($dataSets["PowerScore"][0]->PsHeatWater) 
            && trim($dataSets["PowerScore"][0]->PsHeatWater) != '') {
            $this->v["futureFlts"][] = '&fltWtrh=' . $dataSets["PowerScore"][0]->PsHeatWater;
        }
        if (isset($dataSets["PowerScore"][0]->PsControls) 
            && trim($dataSets["PowerScore"][0]->PsControls) != '') {
            $this->v["futureFlts"][] = '&fltManu=' . $dataSets["PowerScore"][0]->PsControls;
        }
        if (isset($dataSets["PowerScore"][0]->PsControlsAuto) 
            && trim($dataSets["PowerScore"][0]->PsControlsAuto) != '') {
            $this->v["futureFlts"][] = '&fltAuto=' . $dataSets["PowerScore"][0]->PsControlsAuto;
        }
        if (isset($dataSets["PowerScore"][0]->PsVerticalStack) 
            && trim($dataSets["PowerScore"][0]->PsVerticalStack) != '') {
            $this->v["futureFlts"][] = '&fltVert=' . $dataSets["PowerScore"][0]->PsVerticalStack;
        }
        if (isset($dataSets["PSRenewables"]) 
            && sizeof($dataSets["PSRenewables"]) > 0) {
            foreach ($dataSets["PSRenewables"] as $renew) {
                $this->v["futureFlts"][] = '&fltRenew=' . $renew->PsRnwRenewable;
            }
        }
        if (isset($dataSets["PSAreas"]) && sizeof($dataSets["PSAreas"]) > 0) {
            foreach ($dataSets["PSAreas"] as $area) {
                if (isset($area->PsAreaHasStage) && intVal($area->PsAreaHasStage) == 1
                    && $area->PsAreaType != $this->v["areaTypes"]["Dry"]) {
                    if (isset($area->PsAreaHvacType) && intVal($area->PsAreaHvacType) > 0) {
                        $this->v["futureFlts"][] = '&fltHvac=' . $area->PsAreaType . '-' . $area->PsAreaHvacType;
                    }
                    if (isset($dataSets["PSLightTypes"]) 
                        && sizeof($dataSets["PSLightTypes"]) > 0) {
                        foreach ($dataSets["PSLightTypes"] as $lgt) {
                            if ($lgt->PsLgTypAreaID == $area->PsAreaID && isset($lgt->PsLgTypLight) 
                                && intVal($lgt->PsLgTypLight) > 0) {
                                $this->v["futureFlts"][] = '&fltLght=' . $area->PsAreaType . '-' . $lgt->PsLgTypLight;
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
        if (isset($dataSets["PSAreas"]) && sizeof($dataSets["PSAreas"]) > 0) {
            foreach ($dataSets["PSAreas"] as $a) {
                if ($a->PsAreaType == $this->v["areaTypes"]["Flower"]) {
                    return $this->getSizeDefID($a->PsAreaSize);
                }
            }
        }
        return 0;
    }
    
    public function getAllscoresAvgFlds()
    {
        $this->v["avgFlds"] = ['PsEfficOverall', 'PsEfficFacility', 'PsEfficProduction', 'PsEfficLighting', 
            'PsEfficHvac', 'PsGrams', 'PsKWH', 'PsTotalSize'];
        $this->v["psAvg"] = new RIIPowerScore;
        foreach ($this->v["avgFlds"] as $fld) {
            $this->v["psAvg"]->{ $fld } = 0;
        }
        if ($this->v["allscores"] && $this->v["allscores"]->isNotEmpty()) {
            foreach ($this->v["allscores"] as $i => $ps) {
                foreach ($this->v["avgFlds"] as $fld) {
                    $this->v["psAvg"]->{ $fld } += (1*$ps->{ $fld });
                }
            }
            foreach ($this->v["avgFlds"] as $fld) {
                $this->v["psAvg"]->{ $fld } = $this->v["psAvg"]->{ $fld }/sizeof($this->v["allscores"]);
            }
        }
        return $this->v["psAvg"];
    }
    
    public function loadCupScoreIDs()
    {
        $this->v["cultClassicIds"] = $this->v["emeraldIds"] = [];
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Cultivation Classic'))
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) $this->v["cultClassicIds"][] = $c->PsCupPSID;
        }
        $chk = RIIPSForCup::where('PsCupCupID', 
                $GLOBALS["SL"]->def->getID('PowerScore Competitions', 'Emerald Cup Regenerative Award'))
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $c) $this->v["emeraldIds"][] = $c->PsCupPSID;
        }
        return true;
    }
    
    public function getSizeDefRange($defID)
    {
        if ($defID == $GLOBALS["SL"]->def->getID('Indoor Size Groups', '<5,000 sf')) {
            return [0, 5000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID('Indoor Size Groups', '5,000-10,000 sf')) {
            return [5000, 10000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID('Indoor Size Groups', '10,000-50,000 sf')) {
            return [10000, 50000];
        } elseif ($defID == $GLOBALS["SL"]->def->getID('Indoor Size Groups', '50,000+ sf')) {
            return [50000, 1000000000];
        }
        return [0, 1000000000];
    }
    
    public function getSizeDefID($size)
    {
        if ($size < 5000) {
            return $GLOBALS["SL"]->def->getID('Indoor Size Groups', '<5,000 sf');
        } elseif ($size <= 5000 && $size < 10000) {
            return $GLOBALS["SL"]->def->getID('Indoor Size Groups', '5,000-10,000 sf');
        } elseif ($size <= 10000 && $size < 50000) {
            return $GLOBALS["SL"]->def->getID('Indoor Size Groups', '10,000-50,000 sf');
        } elseif ($size >= 50000) {
            return $GLOBALS["SL"]->def->getID('Indoor Size Groups', '50,000+ sf');
        }
        return 0;
    }
    
    
}