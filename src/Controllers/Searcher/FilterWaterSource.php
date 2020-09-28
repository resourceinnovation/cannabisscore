<?php
/**
  * FilterWaterSource extends the Survloop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace ResourceInnovation\CannabisScore\Controllers\Searcher;

use RockHopSoft\Survloop\Controllers\SearcherFilter;

class FilterWaterSource extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Water Sources';
        $this->abbr  = 'fltWaterSource';
        $this->setHideIfEmpty();
        $this->loadOptsFromDefSet('Water Sources');
        return true;
    }

    public function printEval()
    {
        return $this->printEvalBasic(
            'rii_ps_water_sources', 
            'ps_wtr_src_source', 
            'ps_wtr_src_psid', 
            'ps_id'
        );
    }
}
