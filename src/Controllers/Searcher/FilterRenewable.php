<?php
/**
  * FilterRenewable extends the Survloop SearcherFilter for some hard-coded overrides.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.18
  */
namespace ResourceInnovation\CannabisScore\Controllers\Searcher;

use RockHopSoft\Survloop\Controllers\SearcherFilter;

class FilterRenewable extends SearcherFilter
{
    protected function initExtend()
    {
        $this->title = 'Onsite Energy Sources';
        $this->abbr  = 'fltRenew';
        $this->setHideIfEmpty();
        $this->loadOptsFromDefSet('PowerScore Onsite Power Sources');
        return true;
    }

    public function printEval()
    {
        return $this->printEvalBasic(
            'rii_ps_renewables', 
            'ps_rnw_renewable', 
            'ps_rnw_psid', 
            'ps_id'
        );
    }
}
