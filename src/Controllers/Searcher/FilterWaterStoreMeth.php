<?php
/**
  * FilterWaterStoreMeth extends the Survloop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace ResourceInnovation\CannabisScore\Controllers\Searcher;

use App\Models\RIIPsWaterHolding;
use App\Models\RIIPsWaterHoldingRecirc;
use RockHopSoft\Survloop\Controllers\SearcherFilter;

class FilterWaterStoreMeth extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Water Storage Methods';
        $this->abbr  = 'fltWaterStoreMeth';
        $this->setHideIfEmpty();
        $this->loadOptsFromArr([
            1  => 'Any Water in Open Tank(s)',
            2  => 'Any Water in Covered Tank(s)',
            3  => 'Any Water in Open Reservoir(s)',
            4  => 'Any Water in Covered Reservoir(s)',
            5  => 'Source Water in Open Tank(s)',
            6  => 'Source Water in Covered Tank(s)',
            7  => 'Source Water in Open Reservoir(s)',
            8  => 'Source Water in Covered Reservoir(s)',
            9  => 'Recirculated Water in Open Tank(s)',
            10 => 'Recirculated Water in Covered Tank(s)',
            11 => 'Recirculated Water in Open Reservoir(s)',
            12 => 'Recirculated Water in Covered Reservoir(s)'
        ]);
        return true;
    }

    public function printEval()
    {
        $eval = "";
        $tankOpen  = 631;
        $tankClose = 632;
        $resOpen   = 633;
        $resClose  = 634;
        if (!$this->isEmpty()) {
            $psids = [];
            $chk = null;
            if ($this->selected == 1) { // Any Water in Open Tank(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $tankOpen)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $tankOpen)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 2) { // Any Water in Covered Tank(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $tankClose)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $tankClose)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 3) { // Any Water in Open Reservoir(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $resOpen)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $resOpen)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 4) { // Any Water in Covered Reservoir(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $resClose)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $resClose)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 5) { // Source Water in Open Tank(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $tankOpen)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
            } elseif ($this->selected == 6) { // Source Water in Covered Tank(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $tankClose)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
            } elseif ($this->selected == 7) { // Source Water in Open Reservoir(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $resOpen)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
            } elseif ($this->selected == 8) { // Source Water in Covered Reservoir(s)
                $chk = RIIPsWaterHolding::where('ps_wtr_hld_holding', 'LIKE', $resClose)
                    ->select('ps_wtr_hld_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_psid');
            } elseif ($this->selected == 9) { // Recirculated Water in Open Tank(s)
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $tankOpen)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 10) { // Recirculated Water in Covered Tank(s)
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $tankClose)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 11) { // Recirculated Water in Open Reservoir(s)
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $resOpen)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            } elseif ($this->selected == 12) { // Recirculated Water in Covered Reservoir(s)
                $chk = RIIPsWaterHoldingRecirc::where('ps_wtr_hld_rcr_holding', 'LIKE', $resClose)
                    ->select('ps_wtr_hld_rcr_ps_id')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_wtr_hld_rcr_ps_id');
            }
            $eval = "->whereIn('ps_id', [" 
                . ((sizeof($psids) > 0) ? implode(', ', $psids) : 0)
                . "])";
        }
        return $eval;
    }


}