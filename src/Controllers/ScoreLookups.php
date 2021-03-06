<?php
/**
  * ScoreLookups is a small side-class to load the same set of variable lookups to a few different spots.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

class ScoreLookups
{
    public $v = [];

    public function __construct()
    {
        $this->v["defNew"]   = $GLOBALS["SL"]->def->getID(
            'PowerScore Status', 
            'New / Unreviewed'
        );
        $this->v["defCmplt"] = $GLOBALS["SL"]->def->getID(
            'PowerScore Status', 
            'Cannabis Ranked Data Set'
        );
        $this->v["farmTypes"] = [
            'Indoor'           => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Indoor'),
            'Greenhouse/Mixed' => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Greenhouse/Hybrid/Mixed Light'),
            'Outdoor'          => $GLOBALS["SL"]->def
                ->getID('PowerScore Farm Types', 'Outdoor')
        ];
        $this->v["areaTypes"] = [
            'Mother' => $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Mother Plants'),
            'Clone'  => $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Clone or Mother Plants'),
            'Veg'    => $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Vegetating Plants'),
            'Flower' => $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Flowering Plants'),
            'Dry'    => $GLOBALS["SL"]->def
                ->getID('PowerScore Growth Stages', 'Drying/Curing')
        ];
        $this->v["areaTypesFilt"] = [
            'Flower' => $this->v["areaTypes"]["Flower"],
            'Veg'    => $this->v["areaTypes"]["Veg"],
            'Clone'  => $this->v["areaTypes"]["Clone"],
            'Mother' => $this->v["areaTypes"]["Mother"]
        ];
    }
}