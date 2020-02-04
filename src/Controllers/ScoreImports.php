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
use App\Models\RIIPowerscore;
use App\Models\RIIPsOnsite;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIPsRenewables;
use App\Models\RIIPsUtilities;
use App\Models\RIIPsUtiliZips;
use App\Models\RIIPsForCup;
use App\Models\RIIPsLicenses;
use App\Models\RIIPsPageFeedback;
use CannabisScore\Controllers\ScoreAdminMisc;

class ScoreImports extends ScoreAdminMisc
{
    protected function runImport()
    {
        $this->v["importResult"] = '';

        if ($GLOBALS["SL"]->REQ->has('fixLightTypes') 
            && intVal($GLOBALS["SL"]->REQ->fixLightTypes) > 0) {
            $chk = DB::table('rii_ps_light_types')
                ->join('rii_ps_areas', 'rii_ps_light_types.ps_lg_typ_area_id', 
                    '=', 'rii_ps_areas.ps_area_id')
                ->select('rii_ps_areas.ps_area_psid', 
                    'rii_ps_light_types.ps_lg_typ_psid', 
                    'rii_ps_light_types.ps_lg_typ_id')
                ->get();
            if ($chk && $chk->isNotEmpty()) {
                foreach ($chk as $rec) {
                    if (!isset($rec->ps_lg_typ_psid) || intVal($rec->ps_lg_typ_psid) <= 0) {
                        RIIPsLightTypes::find($rec->ps_lg_typ_id)
                            ->update([ "ps_lg_typ_psid" => $rec->ps_area_psid ]);
                    }
                }
            }
        }


        if ($GLOBALS["SL"]->REQ->has('feedbackDups') 
            && intVal($GLOBALS["SL"]->REQ->feedbackDups) > 0) {
            $ids = $done = [];
            $chk = DB::select( DB::raw( "SELECT ps_pag_feed_psid, COUNT(*) FROM rii_ps_page_feedback
                GROUP BY ps_pag_feed_psid HAVING COUNT(*) > 1" ) );
            if ($chk && sizeof($chk) > 0) {
                foreach ($chk as $ps) {
                    $ids[] = $ps->ps_pag_feed_psid;
                }
            }
            $chk = RIIPsPageFeedback::whereIn('ps_pag_feed_psid', $ids)
                ->orderBy('ps_pag_feed_id', 'asc')
                ->get();
            if ($chk && sizeof($chk) > 0 && isset($GLOBALS["SL"]->fldTypes["ps_page_feedback"])) {
                foreach ($chk as $ps) {
                    if (!isset($done[$ps->ps_pag_feed_psid])) {
                        $done[$ps->ps_pag_feed_psid] = $ps;
                    } else {
                        $changes = 0;
                        foreach ($GLOBALS["SL"]->fldTypes["ps_page_feedback"] as $fld => $type) {
                            if ($fld != 'ps_pag_feed_id' 
                                && isset($ps->{ $fld }) 
                                && trim($ps->{ $fld }) != ''
                                && trim($ps->{ $fld }) != trim($done[$ps->ps_pag_feed_psid]->{ $fld })) {
                                if (trim($done[$ps->ps_pag_feed_psid]->{ $fld }) == '') {
                                    $done[$ps->ps_pag_feed_psid]->{ $fld } = trim($ps->{ $fld });
                                } else {
                                    echo 'hmm #' . $ps->ps_pag_feed_psid . ', wanted to overwrite <br ><pre>' 
                                        . $done[$ps->ps_pag_feed_psid]->{ $fld } . '</pre>with<pre>' 
                                        . $ps->{ $fld } . '</pre>';
                                }
                                $changes++;
                            }
                        }
                        if ($changes > 0) {
                            $done[$ps->ps_pag_feed_psid]->save();
                        }
                        $ps->delete();
                    }
                }
            }
        }

        $limit = 250;
        $offset = 0;
        if ($GLOBALS["SL"]->REQ->has('offset') && intVal($GLOBALS["SL"]->REQ->offset) > 0) {
            $offset = intVal($GLOBALS["SL"]->REQ->offset);
        }
        if ($GLOBALS["SL"]->REQ->has('feedback') && intVal($GLOBALS["SL"]->REQ->feedback) > 0) {
            $powerscores = RIIPowerscore::select('ps_id', 
                'ps_feedback1', 'ps_feedback2', 'ps_feedback3', 'ps_feedback4', 
                'ps_feedback5', 'ps_feedback6', 'ps_feedback7', 'ps_feedback8', 
                'ps_uniqueness1', 'ps_uniqueness2', 'ps_uniqueness3', 'ps_uniqueness4', 
                'ps_uniqueness5', 'ps_uniqueness6', 'ps_uniqueness7', 'ps_uniqueness8',
                'ps_water_innovation', 'ps_incentive_used', 'ps_incentive_wants', 
                'ps_consider_upgrade', 'ps_newsletter', 'ps_member_interest'
                )
                ->orderBy('ps_id', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();
            if ($powerscores->isNotEmpty()) {
                foreach ($powerscores as $ps) {
                    $feedback = RIIPsPageFeedback::where('ps_pag_feed_psid', $ps->ps_id)
                        ->first();
                    if (!$feedback || !isset($feedback->ps_pag_feed_psid)) {
                        $feedback = new RIIPsPageFeedback;
                        $feedback->ps_pag_feed_psid = $ps->ps_id;
                    }
                    for ($i = 1; $i < 9; $i++) {
                        $fld1 = 'ps_feedback' . $i;
                        $fld2 = 'ps_pag_feed_feedback' . $i;
                        if (isset($ps->{ $fld1 }) && trim($ps->{ $fld1 }) != '') {
                            $feedback->{ $fld2 } = trim($ps->{ $fld1 });
                            echo ' — adding feedback to ' . $ps->ps_id . ': ' 
                                . $feedback->{ $fld2 } . ' — ';
                        }
                        $fld1 = 'ps_uniqueness' . $i;
                        $fld2 = 'ps_pag_feed_uniqueness' . $i;
                        if (isset($ps->{ $fld1 }) && trim($ps->{ $fld1 }) != '') {
                            $feedback->{ $fld2 } = trim($ps->{ $fld1 });
                            echo ' — adding uniqueness to ' . $ps->ps_id . ': ' 
                                . $feedback->{ $fld2 } . ' — ';
                        }
                    }
                    if (isset($ps->ps_water_innovation) && trim($ps->ps_water_innovation) != '') {
                        $feedback->ps_pag_feed_water_innovation = trim($ps->ps_water_innovation);
                        echo ' — adding water innovation to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_water_innovation . ' — ';
                    }
                    if (isset($ps->ps_incentive_used) && trim($ps->ps_incentive_used) != '') {
                        $feedback->ps_pag_feed_incentive_used = trim($ps->ps_incentive_used);
                        echo ' — adding used incentive to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_incentive_used . ' — ';
                    }
                    if (isset($ps->ps_incentive_wants) && trim($ps->ps_incentive_wants) != '') {
                        $feedback->ps_pag_feed_incentive_wants = trim($ps->ps_incentive_wants);
                        echo ' — adding wants incentive to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_incentive_wants . ' — ';
                    }
                    if (isset($ps->ps_consider_upgrade) && trim($ps->ps_consider_upgrade) != '') {
                        $feedback->ps_pag_feed_consider_upgrade = trim($ps->ps_consider_upgrade);
                        echo ' — adding consider upgrade to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_consider_upgrade . ' — ';
                    }
                    if (isset($ps->ps_newsletter) && trim($ps->ps_newsletter) != ''
                        && (!isset($feedback->ps_pag_feed_newsletter) 
                            || trim($feedback->ps_pag_feed_newsletter) == '')) {
                        $feedback->ps_pag_feed_newsletter = trim($ps->ps_newsletter);
                        echo ' — adding newsletter to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_newsletter . ' — ';
                    }
                    if (isset($ps->ps_member_interest) && trim($ps->ps_member_interest) != '') {
                        $feedback->ps_pag_feed_member_interest = trim($ps->ps_member_interest);
                        echo ' — adding member interest to ' . $ps->ps_id . ': ' 
                            . $feedback->ps_pag_feed_member_interest . ' — ';
                    }
                    $feedback->save();
                }
                $nextUrl = '?import=1&feedback=1&offset=' . ($offset+$limit);
                echo '<br />Feedback relocating... ' . $nextUrl;
                echo $this->redir($nextUrl, true);
                exit;
            } else {
                $nextUrl = '?import=1&onsite=1';
                echo '<br />Feedback DONE!.. ' . $nextUrl;
                echo $this->redir($nextUrl, true);
                exit;
            }
        }


        if ($GLOBALS["SL"]->REQ->has('onsite') && intVal($GLOBALS["SL"]->REQ->onsite) > 0) {
            $powerscores = RIIPowerscore::select('ps_id', 'ps_onsite_type', 
                'ps_extracting_onsite', 'ps_processing_onsite', 
                'ps_cures_offsite', 'ps_cures_outdoor', 'ps_cures_indoor'
                )
                ->orderBy('ps_id', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();
            if ($powerscores->isNotEmpty()) {
                foreach ($powerscores as $ps) {
                    $onsite = RIIPsOnsite::where('ps_on_psid', $ps->ps_id)
                        ->first();
                    if (!$onsite || !isset($onsite->ps_on_psid)) {
                        $onsite = new RIIPsOnsite;
                        $onsite->ps_on_psid = $ps->ps_id;
                    }
                    if (isset($ps->ps_onsite_type) && trim($ps->ps_onsite_type) != '') {
                        $onsite->ps_on_onsite_type = intVal($ps->ps_onsite_type);
                        echo ' — adding extracting to ' . $ps->ps_id . ': ' 
                            . $onsite->ps_on_onsite_type . ' — ';
                    }
                    if (isset($ps->ps_extracting_onsite) && trim($ps->ps_extracting_onsite) != '') {
                        $onsite->ps_on_extracting = intVal($ps->ps_extracting_onsite);
                        echo ' — adding extracting to ' . $ps->ps_id . ': ' 
                            . $onsite->ps_on_extracting . ' — ';
                    }
                    if (isset($ps->ps_processing_onsite) && trim($ps->ps_processing_onsite) != '') {
                        $onsite->ps_on_processing = intVal($ps->ps_processing_onsite);
                            echo ' — adding processing to ' . $ps->ps_id . ': ' 
                                . $onsite->ps_on_processing . ' — ';
                    }
                    if (isset($ps->ps_cures_offsite) && trim($ps->ps_cures_offsite) != '') {
                        $onsite->ps_on_cures_offsite = intVal($ps->ps_cures_offsite);
                        echo ' — adding cures offsite to ' . $ps->ps_id . ': ' 
                            . $onsite->ps_on_cures_offsite . ' — ';
                    }
                    if (isset($ps->ps_cures_outdoor) && trim($ps->ps_cures_outdoor) != '') {
                        $onsite->ps_on_cures_outdoor = intVal($ps->ps_cures_outdoor);
                        echo ' — adding cures offsite to ' . $ps->ps_id . ': ' 
                            . $onsite->ps_on_cures_outdoor . ' — ';
                    }
                    if (isset($ps->ps_cures_indoor) && trim($ps->ps_cures_indoor) != '') {
                        $onsite->ps_on_cures_indoor = intVal($ps->ps_cures_indoor);
                        echo ' — adding cures offsite to ' . $ps->ps_id . ': ' 
                            . $onsite->ps_on_cures_indoor . ' — ';
                    }
                    $onsite->save();
                }
                $nextUrl = '?import=1&onsite=1&offset=' . ($offset+$limit);
                echo 'Onsite relocating... ' . $nextUrl;
                echo $this->redir($nextUrl, true);
                exit;
            } else {
                return '<br />Onsite relocated! <a href="?done=1">Back</a><br />';
            }
        }
/*
Verify Transfer:

SELECT ps_pag_feed_psid, COUNT(*)
FROM rii_ps_page_feedback
GROUP BY ps_pag_feed_psid
HAVING COUNT(*) > 1;

SELECT a.*
FROM rii_ps_page_feedback a
JOIN (SELECT ps_pag_feed_psid, COUNT(*)
FROM rii_ps_page_feedback 
GROUP BY ps_pag_feed_psid
HAVING count(*) > 1 ) b
ON a.ps_pag_feed_psid = b.ps_pag_feed_psid
ORDER BY a.ps_pag_feed_psid;

SELECT rii_powerscore.ps_id, 
rii_powerscore.ps_onsite_type, rii_ps_onsite.ps_on_onsite_type, 
rii_powerscore.ps_processing_onsite, rii_ps_onsite.ps_on_processing, 
rii_powerscore.ps_extracting_onsite, rii_ps_onsite.ps_on_extracting 
FROM rii_powerscore JOIN rii_ps_onsite 
ON rii_powerscore.ps_id=rii_ps_onsite.ps_on_psid 
WHERE (rii_powerscore.ps_onsite_type NOT LIKE rii_ps_onsite.ps_on_onsite_type)
OR (rii_powerscore.ps_processing_onsite NOT LIKE rii_ps_onsite.ps_on_processing)
OR (rii_powerscore.ps_extracting_onsite NOT LIKE rii_ps_onsite.ps_on_extracting)
ORDER BY rii_powerscore.ps_id DESC;

SELECT rii_powerscore.ps_id, 
rii_powerscore.ps_newsletter, rii_ps_page_feedback.ps_pag_feed_newsletter, 
rii_powerscore.ps_member_interest, rii_ps_page_feedback.ps_pag_feed_member_interest, 
rii_powerscore.ps_incentive_used, rii_ps_page_feedback.ps_pag_feed_incentive_used, 
rii_powerscore.ps_incentive_wants, rii_ps_page_feedback.ps_pag_feed_incentive_wants, 
rii_powerscore.ps_consider_upgrade, rii_ps_page_feedback.ps_pag_feed_consider_upgrade, 
rii_powerscore.ps_feedback1, rii_ps_page_feedback.ps_pag_feed_feedback1, 
rii_powerscore.ps_feedback2, rii_ps_page_feedback.ps_pag_feed_feedback2, 
rii_powerscore.ps_feedback3, rii_ps_page_feedback.ps_pag_feed_feedback3, 
rii_powerscore.ps_feedback4, rii_ps_page_feedback.ps_pag_feed_feedback4, 
rii_powerscore.ps_feedback5, rii_ps_page_feedback.ps_pag_feed_feedback5, 
rii_powerscore.ps_feedback6, rii_ps_page_feedback.ps_pag_feed_feedback6, 
rii_powerscore.ps_feedback7, rii_ps_page_feedback.ps_pag_feed_feedback7, 
rii_powerscore.ps_feedback8, rii_ps_page_feedback.ps_pag_feed_feedback8, 
rii_powerscore.ps_uniqueness1, rii_ps_page_feedback.ps_pag_feed_uniqueness1, 
rii_powerscore.ps_uniqueness2, rii_ps_page_feedback.ps_pag_feed_uniqueness2, 
rii_powerscore.ps_uniqueness3, rii_ps_page_feedback.ps_pag_feed_uniqueness3, 
rii_powerscore.ps_uniqueness4, rii_ps_page_feedback.ps_pag_feed_uniqueness4, 
rii_powerscore.ps_uniqueness5, rii_ps_page_feedback.ps_pag_feed_uniqueness5, 
rii_powerscore.ps_uniqueness6, rii_ps_page_feedback.ps_pag_feed_uniqueness6, 
rii_powerscore.ps_uniqueness7, rii_ps_page_feedback.ps_pag_feed_uniqueness7, 
rii_powerscore.ps_uniqueness8, rii_ps_page_feedback.ps_pag_feed_uniqueness8, 
rii_powerscore.ps_water_innovation, rii_ps_page_feedback.ps_pag_feed_water_innovation
FROM rii_powerscore JOIN rii_ps_page_feedback 
ON rii_powerscore.ps_id=rii_ps_page_feedback.ps_pag_feed_psid 
WHERE (rii_powerscore.ps_newsletter NOT LIKE rii_ps_page_feedback.ps_pag_feed_newsletter)
OR (rii_powerscore.ps_member_interest NOT LIKE rii_ps_page_feedback.ps_pag_feed_member_interest)
OR (rii_powerscore.ps_incentive_used NOT LIKE rii_ps_page_feedback.ps_pag_feed_incentive_used)
OR (rii_powerscore.ps_incentive_wants NOT LIKE rii_ps_page_feedback.ps_pag_feed_incentive_wants)
OR (rii_powerscore.ps_consider_upgrade NOT LIKE rii_ps_page_feedback.ps_pag_feed_consider_upgrade)
OR (rii_powerscore.ps_feedback1 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback1)
OR (rii_powerscore.ps_feedback2 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback2)
OR (rii_powerscore.ps_feedback3 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback3)
OR (rii_powerscore.ps_feedback4 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback4)
OR (rii_powerscore.ps_feedback5 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback5)
OR (rii_powerscore.ps_feedback6 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback6)
OR (rii_powerscore.ps_feedback7 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback7)
OR (rii_powerscore.ps_feedback8 NOT LIKE rii_ps_page_feedback.ps_pag_feed_feedback8)
OR (rii_powerscore.ps_uniqueness1 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness1)
OR (rii_powerscore.ps_uniqueness2 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness2)
OR (rii_powerscore.ps_uniqueness3 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness3)
OR (rii_powerscore.ps_uniqueness4 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness4)
OR (rii_powerscore.ps_uniqueness5 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness5)
OR (rii_powerscore.ps_uniqueness6 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness6)
OR (rii_powerscore.ps_uniqueness7 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness7)
OR (rii_powerscore.ps_uniqueness8 NOT LIKE rii_ps_page_feedback.ps_pag_feed_uniqueness8)
OR (rii_powerscore.ps_water_innovation NOT LIKE rii_ps_page_feedback.ps_pag_feed_water_innovation)
ORDER BY rii_powerscore.ps_id DESC;


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
                echo 'line count: ' . sizeof($lines) . ' — ';
                foreach ($lines as $i => $l) {
                if ($i > 16000) {
                    $row = $GLOBALS["SL"]->mexplode(',', $l);
                    if (substr($row[2], 0, 1) == '"' && isset($row[9])) {
                        $row[2] = substr($row[2], 1) . substr($row[3], 0, strlen($row[3])-1);
                        $row[3] = $row[4]; $row[4] = $row[5]; $row[5] = $row[6]; 
                        $row[6] = $row[7]; $row[7] = $row[8]; $row[8] = $row[9];
                    }
                    $ut = RIIPsUtilities::where('ps_ut_name', trim($row[2]))
                        ->first();
                    if (!$ut || !isset($ut->ps_ut_id)) {
                        $ut = new RIIPsUtilities;
                        $ut->ps_ut_name = trim($row[2]);
                        $ut->ps_ut_type = $GLOBALS["SL"]->def->getID('Utility Company Type', 'Non-Investor Owned Utilities');
                        $ut->save();
                    }
                    $utzip = RIIPsUtiliZips::where('ps_ut_zp_util_id', $ut->ps_ut_id)
                        ->where('ps_ut_zp_zip_code', trim($row[0]))
                        ->first();
                    if (!$utzip || !isset($utzip->ps_ut_zp_id)) {
                        $utzip = new RIIPsUtiliZips;
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
                            $this->v["nwpcc"][$id]["PowerScore"] = new RIIPowerscore;
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
                            $this->v["nwpcc"][$id]["ps_for_cup"] = new RIIPsForCup;
                            $this->v["nwpcc"][$id]["ps_for_cup"]->ps_cup_cup_id = 369;
                            $this->v["nwpcc"][$id]["ps_for_cup"]->save();
                            if ($row[2] == 'Recreational') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPsLicenses;
                                $this->v["nwpcc"][$id]["PSLicenses"]->ps_lic_license = 142;
                                $this->v["nwpcc"][$id]["PSLicenses"]->save();
                            } elseif ($row[2] == 'Medical') {
                                $this->v["nwpcc"][$id]["PSLicenses"] = new RIIPsLicenses;
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
                                $chk = RIIPsUtilities::where('ps_ut_name', 'LIKE', $row[25])
                                    ->first();
                                if ($chk) {
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"] = new RIIPsUtiliLinks;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->ps_ut_lnk_utility_id = $chk->ps_ut_id;
                                    $this->v["nwpcc"][$id]["PSUtiliLinks"]->save();
                                } else {
                                    $this->v["nwpcc"][$id]["PowerScore"]->update([
                                        'ps_source_utility_other' => $row[25]
                                    ]);
                                }
                            }
                            if (intVal($row[27]) > 0 || intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPsRenewables;
                                $this->v["nwpcc"][$id]["PSRenewables"]->ps_rnw_renewable = 153;
                                $this->v["nwpcc"][$id]["PSRenewables"]->save();
                            }
                            if (intVal($row[28]) > 0) {
                                $this->v["nwpcc"][$id]["PSRenewables"] = new RIIPsRenewables;
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
                            $this->v["nwpcc"][$id]["PSLightTypes"][$area][$lgtInd] = new RIIPsLightTypes;
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
        $this->searcher->v["allscores"] = RIIPowerscore::where('ps_name', 'LIKE', 'NWPCC%')
            ->where('ps_effic_facility', '>', 0)
            ->where('ps_effic_production', '>', 0)
            ->where('ps_effic_lighting', '>', 0)
            ->where('ps_effic_hvac', '>', 0)
            ->orderBy($this->searcher->v["sort"][0], $this->searcher->v["sort"][1])
            ->get();
        if ($GLOBALS["SL"]->REQ->has('showEmpty')) {
            $this->searcher->v["allscores"] = RIIPowerscore::where('ps_name', 'LIKE', 'NWPCC%')
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
                $chk = RIIPsForCup::where('ps_cup_psid', $ps->ps_id)
                    ->where('ps_cup_cup_id', 369)
                    ->first();
                if (!$chk) {
                    $chk = new RIIPsForCup;
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
            $this->v["nwpcc"][$id]["PSAreas"][0] = new RIIPsAreas;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_type = 237;
            $this->v["nwpcc"][$id]["PSAreas"][0]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][0]->save();
            $this->v["nwpcc"][$id]["PSAreas"][1] = new RIIPsAreas;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_type = 160;
            $this->v["nwpcc"][$id]["PSAreas"][1]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][1]->save();
            $this->v["nwpcc"][$id]["PSAreas"][2] = new RIIPsAreas;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_type = 161;
            $this->v["nwpcc"][$id]["PSAreas"][2]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][2]->save();
            $this->v["nwpcc"][$id]["PSAreas"][3] = new RIIPsAreas;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_type = 162;
            $this->v["nwpcc"][$id]["PSAreas"][3]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][3]->save();
            $this->v["nwpcc"][$id]["PSAreas"][4] = new RIIPsAreas;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_psid = $this->v["nwpcc"][$id]["PowerScore"]->ps_id;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_type = 163;
            $this->v["nwpcc"][$id]["PSAreas"][4]->ps_area_has_stage = 1;
            $this->v["nwpcc"][$id]["PSAreas"][4]->save();
        }
        return true;
    }
    
}