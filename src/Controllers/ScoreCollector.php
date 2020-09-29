<?php
/**
  * ScoreCollector is a helper class stacking PSIDs.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.7
  */
namespace ResourceInnovation\CannabisScore\Controllers;

class ScoreCollector
{
    public $psids = [];

    protected function addPSID($psid)
    {
        $psid = intVal($psid);
        if ($psid > 0 && !in_array($psid, $this->psids)) {
            $this->psids[] = $psid;
        }
        return true;
    }
    
    protected function addPSIDs($psids)
    {
        if (sizeof($psids) > 0) {
            foreach ($psids as $ps) {
                $this->addPSID($ps);
            }
        }
        return true;
    }

}
