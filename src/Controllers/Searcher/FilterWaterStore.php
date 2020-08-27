<?php
/**
  * FilterWaterStore extends the SurvLoop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace CannabisScore\Controllers\Searcher;

use App\Models\RIIPsOnsite;
use SurvLoop\Controllers\SearcherFilter;

class FilterWaterStore extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Water Storage Options';
        $this->abbr  = 'fltWaterStore';
        $this->setHideIfEmpty();
        $this->loadOptsFromArr([
            1  => 'No Water Stored',
            2  => 'Any Water Stored',
            3  => 'Any Water Stored Indoor',
            4  => 'Any Water Stored Outdoor',
            5  => 'Any Water Stored Outdoor, Above Ground',
            6  => 'Any Water Stored Outdoor, Underground',

            7  => 'Source Water Stored Indoor',
            8  => 'Source Water Stored Outdoor',
            9  => 'Source Water Stored Outdoor, Above Ground',
            10 => 'Source Water Stored Outdoor, Underground',

            11 => 'Recirculated Water Stored Indoor',
            12 => 'Recirculated Water Stored Outdoor',
            13 => 'Recirculated Water Stored Outdoor, Above Ground',
            14 => 'Recirculated Water Stored Outdoor, Underground',

            15 => 'Source & Recirculated Stored Indoor',
            16 => 'Source & Recirculated Stored Outdoor',
            17 => 'Source & Recirculated Stored Outdoor, Above Ground',
            18 => 'Source & Recirculated Stored Outdoor, Underground'
        ]);
        return true;
    }

    public function printEval()
    {
        $eval = "";
        if (!$this->isEmpty()) {
            $psids = [];
            $chk = null;
            if ($this->selected == 1) { // No Water Stored
                $chk = RIIPsOnsite::where('ps_on_water_store_source', 'LIKE', 0)
                    ->where('ps_on_water_recirc', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
                $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_on_psid');
                $chk = RIIPsOnsite::where('ps_on_water_store_source', 'LIKE', 0)
                    ->where('ps_on_water_recirc', 'LIKE', 1)
                    ->where('ps_on_water_store_recirc', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 2) { // Any Water Stored
                $chk = RIIPsOnsite::where('ps_on_water_store_source', 'LIKE', 1)
                    ->orWhere('ps_on_water_store_recirc', 'LIKE', 1)
                    ->orWhereNotNull('ps_on_water_source_store_indoor')
                    ->orWhereNotNull('ps_on_water_recirc_store_indoor')
                    ->orWhereNotNull('ps_on_water_source_above_ground')
                    ->orWhereNotNull('ps_on_water_recirc_above_ground')
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 3) { // Any Water Stored Indoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 1)
                    ->orWhere('ps_on_water_recirc_store_indoor', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 4) { // Any Water Stored Outdoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 0)
                    ->orWhere('ps_on_water_recirc_store_indoor', 'LIKE', 0)
                    ->orWhereNotNull('ps_on_water_source_above_ground')
                    ->orWhereNotNull('ps_on_water_recirc_above_ground')
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 5) { // Any Water Stored Outdoor, Above Ground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 1)
                    ->orWhere('ps_on_water_recirc_above_ground', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 6) { // Any Water Stored Outdoor, Underground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 0)
                    ->orWhere('ps_on_water_recirc_above_ground', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();

            } elseif ($this->selected == 7) { // Source Water Stored Indoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 8) { // Source Water Stored Outdoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 0)
                    ->orWhereNotNull('ps_on_water_source_above_ground')
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 9) { // Source Water Stored Outdoor, Above Ground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 10) { // Source Water Stored Outdoor, Underground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();

            } elseif ($this->selected == 11) { // Recirculated Water Stored Indoor
                $chk = RIIPsOnsite::where('ps_on_water_recirc_store_indoor', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 12) { // Recirculated Water Stored Outdoor
                $chk = RIIPsOnsite::where('ps_on_water_recirc_store_indoor', 'LIKE', 0)
                    ->orWhereNotNull('ps_on_water_recirc_above_ground')
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 13) { // Recirculated Water Stored Outdoor, Above Ground
                $chk = RIIPsOnsite::where('ps_on_water_recirc_above_ground', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 14) { // Recirculated Water Stored Outdoor, Underground
                $chk = RIIPsOnsite::where('ps_on_water_recirc_above_ground', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            
            } elseif ($this->selected == 15) { // Source & Recirculated Stored Indoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 1)
                    ->where('ps_on_water_recirc_store_indoor', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 16) { // Source & Recirculated Stored Outdoor
                $chk = RIIPsOnsite::where('ps_on_water_source_store_indoor', 'LIKE', 0)
                    ->where('ps_on_water_recirc_store_indoor', 'LIKE', 0)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 17) { // Source & Recirculated Stored Outdoor, Above Ground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 1)
                    ->where('ps_on_water_recirc_above_ground', 'LIKE', 1)
                    ->select('ps_on_psid')
                    ->get();
            } elseif ($this->selected == 18) { // Source & Recirculated Stored Outdoor, Underground
                $chk = RIIPsOnsite::where('ps_on_water_source_above_ground', 'LIKE', 0)
                    ->where('ps_on_water_recirc_above_ground', 'LIKE', 0)
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