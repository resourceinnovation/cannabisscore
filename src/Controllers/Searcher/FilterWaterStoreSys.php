<?php
/**
  * FilterWaterStoreSys extends the Survloop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace ResourceInnovation\CannabisScore\Controllers\Searcher;

use App\Models\RIIPsOnsite;
use RockHopSoft\Survloop\Controllers\SearcherFilter;

class FilterWaterStoreSys extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Water Storage Systems';
        $this->abbr  = 'fltWaterStoreSys';
        $this->setHideIfEmpty();
        $this->loadOptsFromArr([
            1 => 'Any Water in Centralized Storage System',
            2 => 'Any Water in Decentralized Storage System',
            3 => 'Source Water in Centralized Storage System',
            4 => 'Source Water in Decentralized Storage System',
            5 => 'Recirculated Water in Centralized Storage System',
            6 => 'Recirculated Water in Decentralized Storage System',
            7 => 'Source & Recirculated in Centralized Storage Systems',
            8 => 'Source & Recirculated in Decentralized Storage Systems'
        ]);
        return true;
    }

    public function printEval()
    {
        $eval = "";
        if (!$this->isEmpty()) {
            $psids = [];
            $chk = null;
            if ($this->selected == 1) { // Any Water in Centralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 1)
                    ->orWhere('ps_on_water_recirc_centralized', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 2) { // Any Water in Decentralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 0)
                    ->orWhere('ps_on_water_recirc_centralized', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 3) { // Source Water in Centralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 4) { // Source Water in Decentralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 5) { // Recirculated Water in Centralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_recirc_centralized', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 6) { // Recirculated Water in Decentralized Storage System
                $chk = RIIPsOnsite::where('ps_on_water_recirc_centralized', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 7) { // All Water in Centralized Storage Systems
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 1)
                    ->where('ps_on_water_recirc_centralized', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 8) { // All Water in Decentralized Storage Systems
                $chk = RIIPsOnsite::where('ps_on_water_source_centralized', 'LIKE', 0)
                    ->where('ps_on_water_recirc_centralized', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            }
            $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_on_psid');
            $eval = "->whereIn('ps_id', [" 
                . ((sizeof($psids) > 0) ? implode(', ', $psids) : 0)
                . "])";
        }
        return $eval;
    }



}