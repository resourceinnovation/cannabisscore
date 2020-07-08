<?php
/**
  * ScoreComply is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the processes for managing compliance submissions.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.4
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIComplianceMa;
use CannabisScore\Controllers\ScorePartners;

class ScoreComply extends ScorePartners
{
    protected function reportMaListing($nID)
    {
        $recs = DB::table('rii_compliance_ma')
            ->leftJoin('rii_powerscore', 'rii_compliance_ma.com_ma_ps_id', 
                '=', 'rii_powerscore.ps_id')
            ->whereNotNull('rii_compliance_ma.com_ma_name')
            ->where('rii_compliance_ma.com_ma_name', 'NOT LIKE', '')
            ->select('rii_compliance_ma.*', 'rii_powerscore.ps_email')
            ->orderBy('rii_compliance_ma.com_ma_id', 'desc')
            ->get();
        $users = [];
        if ($recs->isNotEmpty()) {
            foreach ($recs as $rec) {
                if (isset($rec->com_ma_user_id) 
                    && intVal($rec->com_ma_user_id) > 0) {
                    $users[$rec->com_ma_user_id] = $this->printUserLnk($rec->com_ma_user_id);
                }
            }
        }
        return view(
            'vendor.cannabisscore.nodes.1543-ma-comply-listing', 
            [
                "nID"   => $nID,
                "recs"  => $recs,
                "users" => $users
            ]
        )->render();
    }

}

