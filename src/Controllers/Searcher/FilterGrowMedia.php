<?php
/**
  * FilterGrowMedia extends the SurvLoop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace CannabisScore\Controllers\Searcher;

use DB;
use App\Models\RIIPsGrowMedia;
use SurvLoop\Controllers\SearcherFilter;

class FilterGrowMedia extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Growing Media';
        $this->abbr  = 'fltGrowMedia';
        $this->setHideIfEmpty();
        $this->loadOptsFromDefSet('Growing Media');
        return true;
    }

    public function printEval()
    {
        if (!$this->isEmpty()) {
            $psids = [];
            $chk = DB::table('rii_ps_areas')
                ->join('rii_ps_grow_media_area', 'rii_ps_areas.ps_area_id',
                    '=', 'rii_ps_grow_media_area.ps_ar_grw_med_area_id')
                ->where('rii_ps_grow_media_area.ps_ar_grw_med_media', $this->selected)
                ->where('rii_ps_areas.ps_area_psid', '>', 0)
                ->select('rii_ps_areas.ps_area_psid')
                ->get();
            $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_area_psid');
            $chk = RIIPsGrowMedia::where('ps_grw_med_growing', $this->selected)
                ->where('ps_grw_med_psid', '>', 0)
                ->select('ps_grw_med_psid')
                ->get();
            $GLOBALS["SL"]->mergeResultIds($psids, $chk, 'ps_grw_med_psid');
            return "->whereIn('ps_id', [" . ((sizeof($psids) > 0) 
                    ? implode(', ', $psids) : 0) . "])";
        }
        return '';
    }
}
