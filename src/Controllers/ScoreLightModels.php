<?php
/**
  * ScoreVars is the mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class handles lookups and processes for lighting makes and models.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\RIIManufacturers;
use App\Models\RIILightModels;
use CannabisScore\Controllers\ScoreVars;

class ScoreLightModels extends ScoreVars
{
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
    
    protected function ajaxLightSearch(Request $request)
    {
        $this->loadManufactIDs();
        $this->loadLightImportTypeConverts();
        $this->v["results"] = [
            "ids"   => [],
            "set"   => [],
            "manus" => [],
            "types" => []
            ];
        $this->v["lgtSrch"] = [
            "type"  => '',
            "make"  => '',
            "model" => ''
            ];
        if ($request->has('group') && trim($request->get('group')) != '') {
            $this->v["lgtSrch"]["type"] = $GLOBALS["SL"]->def
                ->getVal('PowerScore Light Types', intVal($request->get('group')));
            $this->v["results"]["types"] = $this
                ->convertLightScoreType2ImportType($request->get('group'));
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
                    $this->v["results"]["ids"][] = $model->LgtModID;
                    $this->v["results"]["set"][] = $model;
                }
            }
        } elseif ($this->v["lgtSrch"]["type"] != '' 
            && $this->v["lgtSrch"]["make"] == '' 
            && $this->v["lgtSrch"]["model"] == '') {
            $models = RIILightModels::whereIn('LgtModTech', 
                    $this->v["results"]["types"])
                ->get();
            $this->addModelResults($models);
        } elseif ($this->v["lgtSrch"]["type"] > 0 
            || $this->v["lgtSrch"]["make"] != '' 
            || $this->v["lgtSrch"]["model"] != '') {
            $this->addModelResults(DB::table('RII_LightModels')
                ->join('RII_Manufacturers', 'RII_Manufacturers.ManuID', 
                    '=', 'RII_LightModels.LgtModManuID')
                ->where('RII_Manufacturers.ManuName', 
                    'LIKE', '%' . $this->v["lgtSrch"]["make"] . '%')
                ->where('RII_LightModels.LgtModName', 
                    'LIKE', '%' . $this->v["lgtSrch"]["model"] . '%')
                ->whereIn('RII_LightModels.LgtModTech', $this->v["results"]["types"])
                ->orderBy('RII_Manufacturers.ManuName', 'asc')
                ->orderBy('RII_LightModels.LgtModName', 'asc')
                ->select('RII_LightModels.*')
                ->get());
            $this->addModelResults(DB::table('RII_LightModels')
                ->join('RII_Manufacturers', 'RII_Manufacturers.ManuID', 
                    '=', 'RII_LightModels.LgtModManuID')
                ->where('RII_Manufacturers.ManuName', 
                    'LIKE', '%' . $this->v["lgtSrch"]["make"] . '%')
                ->where('RII_LightModels.LgtModName', 
                    'LIKE', '%' . $this->v["lgtSrch"]["model"] . '%')
                ->orderBy('RII_Manufacturers.ManuName', 'asc')
                ->orderBy('RII_LightModels.LgtModName', 'asc')
                ->select('RII_LightModels.*')
                ->get());
            // Gather all Manufacturer IDs corresponding to this search
            foreach (['make', 'model'] as $searchLevel) {
                $this->addMakeSearch($this->v["lgtSrch"][$searchLevel]);
                if (strpos($this->v["lgtSrch"][$searchLevel], ' ') !== false) {
                    $searchWords = explode(' ', $this->v["lgtSrch"][$searchLevel]);
                    foreach ($searchWords as $word) {
                        $this->addMakeSearch($word);
                    }
                }
            }
            foreach (['make', 'model'] as $searchLevel) {
                $this->addModelSearch($this->v["lgtSrch"][$searchLevel]);
                if (strpos($this->v["lgtSrch"][$searchLevel], ' ') !== false) {
                    $searchWords = explode(' ', $this->v["lgtSrch"][$searchLevel]);
                    foreach ($searchWords as $word) {
                        $this->addModelSearch($word);
                    }
                }
            }
        }
        return view(
            'vendor.cannabisscore.nodes.894-light-search-ajax', 
            $this->v
        )->render();
    }
    
    protected function addMakeSearch($make = '')
    {
        if (trim($make) != '') {
            $manus = RIIManufacturers::where('ManuName', 
                    'LIKE', '%' . trim($make) . '%')
                ->get();
            if ($manus->isNotEmpty()) {
                foreach ($manus as $manu) {
                    if (!in_array($manu->ManuID, 
                        $this->v["results"]["manus"])) {
                        $this->v["results"]["manus"][] = $manu->ManuID;
                    }
                }
            }
        }
        return true;
    }
    
    protected function addModelSearch($model = '')
    {
        if (trim($model) != '') {
            $models = null;
            if (isset($this->v["results"]["manus"]) 
                && sizeof($this->v["results"]["manus"]) > 0) {
                if ($this->v["lgtSrch"]["type"] != '') {
                    $models = RIILightModels::where('LgtModName', 
                            'LIKE', '%' . trim($model) . '%')
                        ->whereIn('LgtModManuID', $this->v["results"]["manus"])
                        ->whereIn('LgtModTech', $this->v["results"]["types"])
                        ->orderBy('LgtModManuID', 'asc')
                        ->orderBy('LgtModName', 'asc')
                        ->get();
                } else {
                    $models = RIILightModels::where('LgtModName', 
                            'LIKE', '%' . trim($model) . '%')
                        ->whereIn('LgtModManuID', $this->v["results"]["manus"])
                        ->orderBy('LgtModManuID', 'asc')
                        ->orderBy('LgtModName', 'asc')
                        ->get();
                }
            } else {
                if ($this->v["lgtSrch"]["type"] != '') {
                    $models = RIILightModels::where('LgtModName', 
                            'LIKE', '%' . trim($model) . '%')
                        ->whereIn('LgtModTech', $this->v["results"]["types"])
                        ->orderBy('LgtModManuID', 'asc')
                        ->orderBy('LgtModName', 'asc')
                        ->get();
                } else {
                    $models = RIILightModels::where('LgtModName', 
                            'LIKE', '%' . trim($model) . '%')
                        ->orderBy('LgtModManuID', 'asc')
                        ->orderBy('LgtModName', 'asc')
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
                if (!in_array($model->LgtModID, $this->v["results"]["ids"])) {
                    $this->v["results"]["ids"][] = $model->LgtModID;
                    $this->v["results"]["set"][] = $model;
                }
            }
        }
        return true;
    }
    
}