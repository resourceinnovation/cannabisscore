<?php
/**
  * ScoreVars is the mid-level extension of the Survloop class, TreeSurvForm.
  * This class handles lookups and processes for lighting makes and models.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\RIIPowerscore;
use App\Models\RIIPsGrowingRooms;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIILightModels;
use App\Models\RIIManufacturers;
use ResourceInnovation\CannabisScore\Controllers\ScoreVars;

class ScoreLightModels extends ScoreVars
{
    protected function loadManufactIDs()
    {
        if (!isset($this->v["manufacts"])) {
            $this->v["manufacts"] = $GLOBALS["CUST"]->loadManufactIDs();
        }
        return $this->v["manufacts"];
    }
    
    protected function convertLightScoreType2ImportType($scoreType = 0)
    {
        switch (intVal($scoreType)) {
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (double-ended HPS)'):
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (single-ended HPS)'):
                return ['Double Ended HPS', 'Single Ended HPS', 'HID', 'HPS'];
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (double-ended MH)'):
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (single-ended MH)'):
                return ['MH', 'MH/HPS Lamps'];
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'CMH'):
                return ['Ceramic Metal Halide'];
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'Fluorescent'):
                return ['Fluorescent', 'Fluorescent + Halogen', 'Fluorescent Induction'];
            case $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'LED'):
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
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (double-ended HPS)');
            case 'Single Ended HPS': 
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (single-ended HPS)');
            case 'MH': 
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'HID (double-ended MH)');
            case 'Ceramic Metal Halide':
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'CMH');
            case 'Fluorescent': 
            case 'Fluorescent + Halogen': 
            case 'Fluorescent Induction': 
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'Fluorescent');
            case 'LED':
                return $GLOBALS["SL"]->def->getID('PowerScore Light Types', 'LED');
            case 'MH/HPS Lamps': 
            case 'CFL': 
            case 'Plasma': 
        }
        return 0;
    }
    
    protected function loadLightImportTypeConverts()
    {
        $this->v["lightImportTypeConvert"] = [];
        $chk = DB::table('rii_light_models')
            ->distinct('lgt_mod_tech')
            ->select('lgt_mod_tech')
            ->get();
        if ($chk->isNotEmpty()) {
            foreach ($chk as $mod) {
                $this->v["lightImportTypeConvert"][$mod->lgt_mod_tech] 
                    = $this->convertLightImportType2ScoreType($mod->lgt_mod_tech);
            }
        }
        return $this->v["lightImportTypeConvert"];
    }
    
    protected function getAllLightModels()
    {
        if ($GLOBALS["SL"]->REQ->has('modelSrch')
            && trim($GLOBALS["SL"]->REQ->modelSrch) != '') {
            $srch = '%' . $GLOBALS["SL"]->REQ->modelSrch . '%';
            return DB::table('rii_light_models')
                ->join('rii_manufacturers', 'rii_manufacturers.manu_id', 
                    '=', 'rii_light_models.lgt_mod_manu_id')
                ->where('rii_manufacturers.manu_name', 'LIKE', $srch)
                ->orderBy('rii_manufacturers.manu_name', 'asc')
                ->orderBy('rii_light_models.lgt_mod_name', 'asc')
                ->select('rii_light_models.*')
                ->get();
        }
        return DB::table('rii_light_models')
            ->join('rii_manufacturers', 'rii_manufacturers.manu_id', 
                '=', 'rii_light_models.lgt_mod_manu_id')
            ->orderBy('rii_manufacturers.manu_name', 'asc')
            ->orderBy('rii_light_models.lgt_mod_name', 'asc')
            ->select('rii_light_models.*')
            ->get();
    }
    
    protected function ajaxLightSearch(Request $request)
    {
        $this->loadManufactIDs();
        $this->loadLightImportTypeConverts();
        $this->ajaxLightSearchReqs($request);
        return view(
            'vendor.cannabisscore.nodes.894-light-search-ajax', 
            $this->v
        )->render();
    }
    
    protected function saveAdminLightEdit(Request $request)
    {
        if ($this->isStaffOrAdmin() 
            && $request->has('lgt')
            && intVal($request->get('lgt')) > 0) {
            $lgtID = intVal($request->get('lgt'));
            $lgt = RIILightModels::find($lgtID);
            if ($lgt && isset($lgt->lgt_mod_id)) {
                $lgt->lgt_mod_type 
                    = $lgt->lgt_mod_is_dlc
                    = null;
                if ($request->has('type')) {
                    $lgt->lgt_mod_type = intVal($request->get('type'));
                }
                if ($request->has('dlc')) {
                    $lgt->lgt_mod_is_dlc = intVal($request->get('dlc'));
                }
                $lgt->save();
                return $GLOBALS["SL"]->saveIconAnim();
            }
        }
        return '.';
    }
    
    protected function ajaxLightSearchReqs(Request $request)
    {
        $this->v["results"] = [
            "ids"   => [],
            "set"   => [],
            "manus" => [],
            "type"  => 0
        ];
        $this->v["lgtSrch"] = [
            "type"  => '',
            "make"  => '',
            "model" => ''
        ];
        if ($request->has('group') && trim($request->get('group')) != '') {
            $this->v["lgtSrch"]["type"] = $GLOBALS["SL"]->def->getVal(
                'PowerScore Light Types', 
                intVal($request->get('group'))
            );
            $this->v["results"]["type"] = intVal($request->get('group'));
        }
        if ($request->has('make') && trim($request->get('make')) != '') {
            $this->v["lgtSrch"]["make"] = trim($request->get('make'));
        }
        if ($request->has('model') && trim($request->get('model')) != '') {
            $this->v["lgtSrch"]["model"] = trim($request->get('model'));
        }
        if ($request->has('all')) {
            $models = $this->getAllLightModels();
            if ($models->isNotEmpty()) {
                foreach ($models as $model) {
                    $this->v["results"]["ids"][] = $model->lgt_mod_id;
                    $this->v["results"]["set"][] = $model;
                }
            }
        } elseif ($this->v["lgtSrch"]["type"] != '' 
            && $this->v["lgtSrch"]["make"] == '' 
            && $this->v["lgtSrch"]["model"] == '') {
            $models = RIILightModels::where('lgt_mod_type', 
                    $this->v["results"]["type"])
                ->get();
            $this->addModelResults($models);
        } elseif ($this->v["lgtSrch"]["type"] != ''
            || $this->v["lgtSrch"]["make"] != '' 
            || $this->v["lgtSrch"]["model"] != '') {
            $this->addModelResults(DB::table('rii_light_models')
                ->join('rii_manufacturers', 'rii_manufacturers.manu_id', 
                    '=', 'rii_light_models.lgt_mod_manu_id')
                ->where('rii_manufacturers.manu_name', 
                    'LIKE', '%' . $this->v["lgtSrch"]["make"] . '%')
                ->where('rii_light_models.lgt_mod_name', 
                    'LIKE', '%' . $this->v["lgtSrch"]["model"] . '%')
                ->where('rii_light_models.lgt_mod_type', 
                    $this->v["results"]["type"])
                ->orderBy('rii_manufacturers.manu_name', 'asc')
                ->orderBy('rii_light_models.lgt_mod_name', 'asc')
                ->select('rii_light_models.*')
                ->get());
            $this->addModelResults(DB::table('rii_light_models')
                ->join('rii_manufacturers', 'rii_manufacturers.manu_id', 
                    '=', 'rii_light_models.lgt_mod_manu_id')
                ->where('rii_manufacturers.manu_name', 
                    'LIKE', '%' . $this->v["lgtSrch"]["make"] . '%')
                ->where('rii_light_models.lgt_mod_name', 
                    'LIKE', '%' . $this->v["lgtSrch"]["model"] . '%')
                ->orderBy('rii_manufacturers.manu_name', 'asc')
                ->orderBy('rii_light_models.lgt_mod_name', 'asc')
                ->select('rii_light_models.*')
                ->get());
            // Gather all Manufacturer IDs corresponding to this search
            foreach ([ 'make', 'model' ] as $searchLevel) {
                $this->addMakeSearch($this->v["lgtSrch"][$searchLevel]);
                if (strpos($this->v["lgtSrch"][$searchLevel], ' ') !== false) {
                    $searchWords = explode(' ', $this->v["lgtSrch"][$searchLevel]);
                    foreach ($searchWords as $word) {
                        $this->addMakeSearch($word);
                    }
                }
            }
            foreach ([ 'make', 'model' ] as $searchLevel) {
                $this->addModelSearch($this->v["lgtSrch"][$searchLevel]);
                if (strpos($this->v["lgtSrch"][$searchLevel], ' ') !== false) {
                    $searchWords = explode(' ', $this->v["lgtSrch"][$searchLevel]);
                    foreach ($searchWords as $word) {
                        $this->addModelSearch($word);
                    }
                }
            }
        }
        return true;
    }
    
    protected function addMakeSearch($make = '')
    {
        if (trim($make) != '') {
            $manus = RIIManufacturers::where('manu_name', 'LIKE', '%' . trim($make) . '%')
                ->get();
            if ($manus->isNotEmpty()) {
                foreach ($manus as $manu) {
                    if (!in_array($manu->manu_id, $this->v["results"]["manus"])) {
                        $this->v["results"]["manus"][] = $manu->manu_id;
                    }
                }
            }
        }
        return true;
    }
    
    protected function addModelSearch($model = '')
    {
        if (trim($model) != '') {
//echo '<pre>'; print_r($this->v["results"]); echo '</pre>'; exit;
            $models = null;
            if (isset($this->v["results"]["manus"]) 
                && sizeof($this->v["results"]["manus"]) > 0) {
                if ($this->v["lgtSrch"]["type"] != '') {
                    $models = RIILightModels::where('lgt_mod_name', 'LIKE', '%' . trim($model) . '%')
                        ->whereIn('lgt_mod_manu_id', $this->v["results"]["manus"])
                        ->where('lgt_mod_tech', $this->v["results"]["type"])
                        ->orderBy('lgt_mod_manu_id', 'asc')
                        ->orderBy('lgt_mod_name', 'asc')
                        ->get();
                } else {
                    $models = RIILightModels::where('lgt_mod_name', 'LIKE', '%' . trim($model) . '%')
                        ->whereIn('lgt_mod_manu_id', $this->v["results"]["manus"])
                        ->orderBy('lgt_mod_manu_id', 'asc')
                        ->orderBy('lgt_mod_name', 'asc')
                        ->get();
                }
            } else {
                if ($this->v["lgtSrch"]["type"] != '') {
                    $models = RIILightModels::where('lgt_mod_name', 'LIKE', '%' . trim($model) . '%')
                        ->where('lgt_mod_tech', $this->v["results"]["type"])
                        ->orderBy('lgt_mod_manu_id', 'asc')
                        ->orderBy('lgt_mod_name', 'asc')
                        ->get();
                } else {
                    $models = RIILightModels::where('lgt_mod_name', 'LIKE', '%' . trim($model) . '%')
                        ->orderBy('lgt_mod_manu_id', 'asc')
                        ->orderBy('lgt_mod_name', 'asc')
                        ->get();
                }
            }
            $this->addModelResults($models);
        }
        return true;
    }
    
    protected function addModelResults($models = null)
    {
        if ($models && $models->isNotEmpty()) {
            foreach ($models as $model) {
                if (!in_array($model->lgt_mod_id, $this->v["results"]["ids"])) {
                    $this->v["results"]["ids"][] = $model->lgt_mod_id;
                    $this->v["results"]["set"][] = $model;
                }
            }
        }
        return true;
    }
    
    protected function chkPsLightsDlc($psid)
    {
        $ps = RIIPowerscore::find($psid);
        if (!$ps || !isset($ps->ps_id)) {
            return -1;
        }
        $dlcBonus = 0;
        $idAreas = [];
        $idRooms = [];
        $areas = RIIPsAreas::where('ps_area_psid', intVal($psid))
            ->select('ps_area_id')
            ->get();
        if ($areas->isNotEmpty()) {
            foreach ($areas as $area) {
                $idAreas[] = $area->ps_area_id;
            }
        }
        $rooms = RIIPsGrowingRooms::where('ps_room_psid', intVal($psid))
            ->select('ps_room_id')
            ->get();
        if ($rooms->isNotEmpty()) {
            foreach ($rooms as $room) {
                $idRooms[] = $room->ps_room_id;
            }
        }
        $lgts = RIIPsLightTypes::whereIn('ps_lg_typ_area_id', $idAreas)
            ->orWhereIn('ps_lg_typ_room_id', $idRooms)
            ->get();
        foreach ($lgts as $lgt) {
            $manuID = $modID = $dlc = 0;
            $manuRec = RIIManufacturers::where('manu_name', $lgt->ps_lg_typ_make)
                ->first();
            if ($manuRec && isset($manuRec->manu_id)) {
                $manuID = $manuRec->manu_id;
            }
            $modelRec = RIILightModels::where('lgt_mod_manu_id', $manuID)
                ->where('lgt_mod_name', 'LIKE', '%' . $lgt->ps_lg_typ_model . '%')
                ->orderBy('lgt_mod_is_dlc', 'desc')
                ->first();
            if ($modelRec && isset($modelRec->lgt_mod_id)) {
                $modID = $modelRec->lgt_mod_id;
                $dlc = $modelRec->lgt_mod_is_dlc;
                if (isset($modelRec->lgt_mod_type)
                    && intVal($modelRec->lgt_mod_type) > 0
                    && $lgt->ps_lg_typ_light != $modelRec->lgt_mod_type) {
                    $lgt->ps_lg_typ_light = $modelRec->lgt_mod_type;
                    $lgt->save();
                }
            }
            if ($lgt->ps_lg_typ_model_id != $modID
                || $lgt->ps_lg_typ_dlc_bonus != $dlc) {
                $lgt->ps_lg_typ_model_id = $modID;
                $lgt->ps_lg_typ_dlc_bonus = $dlc;
                $lgt->save();
            }
            if ($dlcBonus < $dlc) {
                $dlcBonus = $dlc;
            }
        }
        if (!isset($ps->ps_dlc_bonus)
            || $ps->ps_dlc_bonus != $dlcBonus) {
            $ps->ps_dlc_bonus = $dlcBonus;
            $ps->save();
        }
        return $dlcBonus;
    }
    
}