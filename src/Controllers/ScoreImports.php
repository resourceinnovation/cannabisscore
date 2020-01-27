<?php
/**
  * ScoreImports is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which run largely one-time data imports,
  * also only to be run by staff.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use App\Models\RIIPSAreas;
use App\Models\RIIPSLightTypes;
use App\Models\RIIPSRenewables;
use App\Models\RIIPSUtilities;
use App\Models\RIIPSUtiliZips;
use App\Models\RIIPSForCup;
use App\Models\RIIPSLicenses;
use CannabisScore\Controllers\ScoreAdminMisc;

class ScoreImports extends ScoreAdminMisc
{
    protected function runImport()
    {
        $this->v["importResult"] = '';
    
        /*
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/cultivation-classic-import-data.csv';
        if (file_exists($file)) {
            $rawData = $colNames = [];
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $l) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if ($i > 0) {
                        echo '<pre>'; print_r($row); echo '</pre>';
                        $score = new RIIPowerScore;
                        $score->ps_name            = $row[0];
                        $score->ps_zip_code         = $row[1];
                        if (isset($row[2]) && trim($row[2]) != '') $score->ps_grams             = $row[2];
                        if (isset($row[3]) && trim($row[3]) != '') $score->ps_total_size       = $row[3];
                        if (isset($row[5]) && trim($row[5]) != '') $score->ps_effic_facility   = $row[5];
                        if (isset($row[6]) && trim($row[6]) != '') $score->ps_effic_production = $row[6];
                        $score->save();
                    }
                }
            }
        }
        */
        
        /*
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/cultivation_classic_2016-import.csv';
        if (file_exists($file)) {
            $rawData = $colNames = [];
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                foreach ($lines as $i => $l) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if ($i == 0) $colNames = $row;
                    elseif (isset($row[0])) {
                        if (!isset($rawData[$row[0]])) $rawData[$row[0]] = [];
                        $rawData[$row[0]][] = $row;
                    }
                }
            }
        }
        //echo '<pre>'; print_r($colNames); print_r($rawData); echo '</pre>';
        foreach ($rawData as $farm => $farmRows) {
            echo '<br /><br /><br /><table border=1 >';
            foreach ($colNames as $i => $name) {
                if ($i < 35) {
                    echo '<tr><th>' . $name . '</th>';
                    foreach ($farmRows as $j => $row) {
                        echo '<td>' . ((isset($row[$i])) ? $row[$i] : '') . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
        exit;
        */
        
        /*
        // Import Utility Zip Codes (Ran One-Time)
        // taken from https://openei.org/datasets/dataset/u-s-electric-utility-companies-and-rates-look-up-by-zipcode-feb-2011/resource/3f00482e-8ea0-4b48-8243-a212b6322e74
        $file = '../vendor/resourceinnovation/cannabisscore/src/Public/noniouzipcodes2011.csv';
        if (file_exists($file)) {
            $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
            if (sizeof($lines) > 0) {
                echo 'line count: ' . sizeof($lines) . '<br />';
                foreach ($lines as $i => $l) {
                if ($i > 16000) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if (substr($row[2], 0, 1) == '"' && isset($row[9])) {
                        $row[2] = substr($row[2], 1) . substr($row[3], 0, strlen($row[3])-1);
                        $row[3] = $row[4]; $row[4] = $row[5]; $row[5] = $row[6]; 
                        $row[6] = $row[7]; $row[7] = $row[8]; $row[8] = $row[9];
                    }
                    $ut = RIIPSUtilities::where('ps_ut_name', trim($row[2]))
                        ->first();
                    if (!$ut || !isset($ut->ps_ut_id)) {
                        $ut = new RIIPSUtilities;
                        $ut->ps_ut_name = trim($row[2]);
                        $ut->ps_ut_type = $GLOBALS["SL"]->def->getID('Utility Company Type', 'Non-Investor Owned Utilities');
                        $ut->save();
                    }
                    $utzip = RIIPSUtiliZips::where('ps_ut_zp_util_id', $ut->ps_ut_id)
                        ->where('ps_ut_zp_zip_code', trim($row[0]))
                        ->first();
                    if (!$utzip || !isset($utzip->ps_ut_zp_id)) {
                        $utzip = new RIIPSUtiliZips;
                        $utzip->ps_ut_zp_util_id = $ut->ps_ut_id;
                        $utzip->ps_ut_zp_zip_code = trim($row[0]);
                        $utzip->save();
                    } else {
                        //echo 'already found ' . $row[0] . ', ' . $row[2] . '??<br />';
                    }
                }
                }
            }
        }
        */
        return true;
    }
    
    protected function runNwpccImport()
    {
        $ret = '';
        if ($GLOBALS["SL"]->REQ->has('import')) {
            $this->v["nwpcc"] = [];
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-A.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        if ($i > 1) {
                            $row = $GLOBALS["SL"]->mexplode(';', $l);
                            $id = intVal($row[0]);
                            $this->v["nwpcc"][$id] = [];
                            $this->v["nwpcc"][$id]["PowerScore"] = new RIIPowerScore;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_name = 'NWPCC #' . $row[0];
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_email = 'NWPCC@NWPCC.com';
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_time_type = 232;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_status = 242;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_privacy = 361;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_user_id = 0;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_submission_progress = 44;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_unique_str = $this->getRandStr(
                                'powerscore', 
                                'ps_unique_str', 
                                20
                            );
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_is_mobile = 0;
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_ip_addy = '--import--';
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_zip_code = $row[1];
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_kwh = 0;
                            if (trim($row[4]) != '' 
                                || (trim($row[3]) != '' && trim($row[4]) != '') 
                                || (trim($row[3]) != '' && trim($row[5]) != '')
                                || (trim($row[4]) != '' && trim($row[5]) != '')) {
                                $this->v["nwpcc"][$id]["PowerScore"]->ps_characterize = 145;
                            } elseif (trim($row[3]) != '') {
                                $this->v["nwpcc"][$id]["PowerScore"]->ps_characterize = 143;
                            } elseif (trim($row[5]) != '') {
                                $this->v["nwpcc"][$id]["PowerScore"]->ps_characterize = 144;
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_total_size = intVal($row[8]);
                            $this->v["nwpcc"][$id]["PowerScore"]->ps_harvests_per_year 
                                = (isset($row[30]) && (intVal($row[30]) > 0) ? intVal($row[30]) : 1);
                            if (isset($row[31]) && intVal($row[31]) > 0) {
                                $this->v["nwpcc"][$id]["PowerScore"]->ps_grams 
                                    = $this->v["nwpcc"][$id]["PowerScore"]->ps_harvests_per_year
                                        *$GLOBALS["CUST"]->cnvrtLbs2Grm(intVal($row[31]));
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->save();
                            $this->v["nwpcc"][$id]["PSForCup"] = new RIIPSForCup;
                            $this->v["nwpcc"][$id]["PSForCup"]->ps_cup_cup_id = 369;
                            $this->v["nwpcc"][$id]["PSForCup"]->save();
                            if ($row[2] == 'Recreational') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPSLicenses;
                                $this->v["nwpcc"][$id]["PSLicenses"]->ps_lic_license = 142;
                                $this->v["nwpcc"][$id]["PSLicenses"]->save();
                            } elseif ($row[2] == 'Medical') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPSLicenses;
                                $this->v["nwpcc"][$id]["PSLicenses"]->ps_lic_license = 141;
                                $this->v["nwpcc"][$id]["PSLicenses"]->save();
                            }
                            $this->runNwpccImportInitAreas($id);
                            //$this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_size = $row[5];
                            $totSize = intVal($row[7]);
                            $this->v["nwpcc"][$id]["PSAreas"][0]->update([ 'ps_area_size' => $totSize*(10/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][1]->update([ 'ps_area_size' => $totSize*(10/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][2]->update([ 'ps_area_size' => $totSize*(68/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][3]->update([ 'ps_area_size' => $totSize*(245/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][4]->update([ 'ps_area_size' => $totSize*(23/366) ]);
                            $this->v["nwpcc"][$id]["PSAreas"][3]->update([
                                'ps_area_lgt_dep' => (($row[9] == 'TRUE') ? 1 : 0)
                            ]);
                            if (in_array($row[25], [ 'PGE', 'Portland General' ])) {
                                $row[25] = 'Portland General Electric';
                            }
                            if (trim($row[25]) != '') {
                                $chk = RIIPSUtilities::where('ps_ut_name', 'LIKE', $row[25])
                                    ->first();
                                if ($chk) {
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"] = new RIIPSUtiliLinks;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->ps_ut_lnk_utility_id = $chk->ps_ut_id;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->save();
                                } else {
                                    $this->v["nwpcc"][$id]["PowerScore"]->update([
                                        'ps_source_utility_other' => $row[25]
                                    ]);
                                }
                            }
                            if (intVal($row[27]) > 0 || intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPSRenewables;
                                $this->v["nwpcc"][$id]["PSRenewables"]->ps_rnw_renewable = 153;
                                $this->v["nwpcc"][$id]["PSRenewables"]->save();
                            }
                            if (intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPSRenewables;
                                $this->v["nwpcc"][$id]["PSRenewables"]->ps_rnw_renewable = 154;
                                $this->v["nwpcc"][$id]["PSRenewables"]->save();
                            }
                        } 
                    }
                }
            }
            
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-B.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(';', $l);
                        if ($i > 1) {
                            $id = intVal($row[0]);
                            $area = 3;
                            if ($row[1] == 'Greenhouse' && in_array($id, [61, 69])) {
                                $area = 2;
                            } elseif ($row[1] == 'Vegetative Room') {
                                $area = 2;
                            } elseif ($row[1] == 'Clone Room') {
                                $area = 1;
                            } elseif ($row[1] == 'Drying Room') {
                                $area = 4;
                            }
                            if (!isset($this->v["nwpcc"][$id]["PSLightTypes"])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"] = [];
                            }
                            if (!isset($this->v["nwpcc"][$id]["PSLightTypes"][$area])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area] = [];
                            }
                            $lgtInd = sizeof($this->v["nwpcc"][$id]["PSLightTypes"][$area]);
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd] = new RIIPSLightTypes;
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_area_id 
                                = $this->v["nwpcc"][$id]["PSAreas"][$area]->ps_area_id;
                            if (isset($row[5]) && intVal($row[5]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]
                                    ->ps_lg_typ_hours = intVal($row[5]);
                            }
                            if (in_array(trim($row[2]), ['Linear Fluorescent T5', 'Compact Fluorescent'])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 165;
                            } elseif (trim($row[2]) == 'High Pressure Sodium Double-Ended') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 168;
                            } elseif (trim($row[2]) == 'High Pressure Sodium') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 169;
                            } elseif (in_array(trim($row[2]), 
                                [ 'Metal Halide Ceramic', 'Metal Halide', 'High Intensity Discharge' ])) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 171;
                            } elseif (trim($row[2]) == 'LED') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 203;
                            } elseif (trim($row[2]) == 'LED') {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_light = 203;
                            }
                            if (intVal($row[3]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_count = $row[3];
                            }
                            if (intVal($row[4]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_wattage = $row[4];
                            }
                            if (intVal($row[5]) > 0) {
                                $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->ps_lg_typ_hours = $row[5];
                            }
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd]->save();
                            if (intVal($row[8]) > 0) {
                                $this->v["nwpcc"][$id]["PowerScore"]->ps_kwh += intVal($row[8]);
                            }
                            $this->v["nwpcc"][$id]["PowerScore"]->save();
                        }
                    }
                }
            }
            
            $hvacCool = $hvacDehum = [];
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-C.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(',', $l);
                        if ($i > 0 && isset($row[1])) {
                            $hvacCool[intVal($row[0])] = $row[1];
                        }
                    }
                }
            }
            $file = '../vendor/resourceinnovation/cannabisscore/src/Database/NWPCC-import-D.csv';
            if (file_exists($file)) {
                $lines = $GLOBALS["SL"]->mexplode("\n", file_get_contents($file));
                if (sizeof($lines) > 0) {
                    foreach ($lines as $i => $l) {
                        $row = $GLOBALS["SL"]->mexplode(',', $l);
                        if ($i > 0 && isset($row[1])) {
                            $hvacDehum[intVal($row[0])] = $row[1];
                        }
                    }
                }
            }
            foreach ($this->v["nwpcc"] as $id => $recs) {
                $hvac = 247; // System A, traditional
                if (isset($hvacDehum[$id]) && $hvacDehum[$id] == 'Standalone (High-Performance)') {
                    $hvac = 248; // System B, high-performance
                } elseif (isset($hvacDehum[$id]) && $hvacDehum[$id] == 'HVAC Integrated') {
                    if (isset($hvacCool[$id]) && $hvacCool[$id] == 'Air Cooled Chiller') {
                        $hvac = 356; // System E
                    } else {
                        $hvac = 250; // System D
                    }
                }
                foreach ($this->v["nwpcc"][$id]["PSAreas"] as $area => $row) {
                    $this->v["nwpcc"][$id]["PSAreas"][$area]->ps_area_hvac_type = $hvac;
                    $this->v["nwpcc"][$id]["PSAreas"][$area]->save();
                }
            }
            
        }
        $this->searchResultsXtra();
        $this->searcher->v["psFilter"] = '<a href="?showEmpty=1">'
            . '<i class="fa fa-toggle-off" aria-hidden="true"></i> Show Empties</a>';
        $this->searcher->v["allscores"] = RIIPowerScore::where('ps_name', 'LIKE', 'NWPCC%')
            ->where('ps_effic_facility', '>', 0)
            ->where('ps_effic_production', '>', 0)
            ->where('ps_effic_lighting', '>', 0)
            ->where('ps_effic_hvac', '>', 0)
            ->orderBy($this->searcher->v["sort"][0], $this->searcher->v["sort"][1])
            ->get();
        if ($GLOBALS["SL"]->REQ->has('showEmpty')) {
            $this->searcher->v["allscores"] = RIIPowerScore::where('ps_name', 'LIKE', 'NWPCC%')
                ->orderBy($this->searcher->v["sort"][0], $this->searcher->v["sort"][1])
                ->get();
            $this->searcher->v["psFilter"] = '<a href="?">'
                . '<i class="fa fa-toggle-on" aria-hidden="true"></i> Hide Empties</a>';
        } elseif ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $i => $score) {
                $this->searcher->v["allscores"][$i]->update([ 'ps_status' => 243 ]);
            }
        }
        if ($this->searcher->v["allscores"]->isNotEmpty()) {
            foreach ($this->searcher->v["allscores"] as $ps) {
                $chk = RIIPSForCup::where('ps_cup_psid', $ps->ps_id)
                    ->where('ps_cup_cup_id', 369)
                    ->first();
                if (!$chk) {
                    $chk = new RIIPSForCup;
                    $chk->ps_cup_psid = $ps->ps_id;
                    $chk->ps_cup_cup_id = 369;
                    $chk->save();
                }
            }
        }
        
        $this->searcher->getAllscoresAvgFlds();
        $this->searcher->v["isExcel"] = $GLOBALS["SL"]->REQ->has('excel');
        if ($GLOBALS["SL"]->REQ->has('excel')) {
            $innerTable = view(
                'vendor.cannabisscore.nodes.170-all-powerscores-excel', 
                $this->searcher->v
            )->render();
            $exportFile = 'NWPCC Import Into PowerScore';
            $exportFile = str_replace(' ', '_', $exportFile) . '-' . date("Y-m-d") . '.xls';
            $GLOBALS["SL"]->exportExcelOldSchool($innerTable, $exportFile);
        }
        $this->searcher->v["nID"] = 808;
        $GLOBALS["SL"]->loadStates();
        $this->searcher->loadCupScoreIDs();
        //$this->searcher->v["psFilters"] = view('vendor.cannabisscore.inc-filter-powerscores', $this->searcher->v)->render();
        $ret .= view(
            'vendor.cannabisscore.nodes.170-all-powerscores', 
            $this->searcher->v
        )->render();

        //$this->searcher->v["importResult"] .= '<pre>' . $xml . '</pre>';
        return $ret;
    }
    
    protected function runNwpccImportInitAreas($id)
    {
        if (!isset($this->v["nwpcc"][$id]["PSAreas"])) {
            $this->v["nwpcc"][$id]["PSAreas"] = [];
            $this->v["nwpcc"][$id]["PSAreas"][0] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_type = 237;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][0]->save();
            $this->v["nwpcc"][$id]["PSAreas"][1] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_type = 160;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][1]->save();
            $this->v["nwpcc"][$id]["PSAreas"][2] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_type = 161;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][2]->save();
            $this->v["nwpcc"][$id]["PSAreas"][3] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_type = 162;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][3]->save();
            $this->v["nwpcc"][$id]["PSAreas"][4] = new RIIPSAreas;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_type = 163;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][4]->save();
        }
        return true;
    }
    
}