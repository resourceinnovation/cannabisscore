<?php
/**
  * ScoreListingsCore is a mid-level extension of the Survloop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.3
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIPsRanks;
use App\Models\RIIPsAreas;
use App\Models\RIIPsLightTypes;
use App\Models\RIIManufacturers;

class ScoreListingsCore
{
    public $v        = [];
    public $searcher = null;

    public function __construct($uID = 0, $user = null, $usrInfo = null)
    {
        $this->v["uID"]     = $uID;
        $this->v["user"]    = $user;
        $this->v["usrInfo"] = $usrInfo;
        $this->v["isAdmin"] = ($this->v["user"]
            && $this->v["user"]->hasRole('administrator|staff'));

        $this->v["defNew"]   = 556;
        $this->v["defCmplt"] = 243;
        $this->v["defArch"]  = 364;
        $this->v["defInc"]   = 242;

        $defSet = 'PowerScore Farm Types';
        $this->v["frmTypOut"] = $GLOBALS["SL"]->def->getID($defSet, 'Outdoor');
        $this->v["frmTypIn"]  = $GLOBALS["SL"]->def->getID($defSet, 'Indoor');
        $this->v["frmTypGrn"] = $GLOBALS["SL"]->def->getID(
            $defSet,
            'Greenhouse/Hybrid/Mixed Light'
        );
        $this->loadSearcher();
        $this->copyUserToSearcher();
    }

    protected function loadSearcher()
    {
        $this->searcher = new CannabisScoreSearcher;
    }

    public function copyUserToSearcher()
    {
        if (isset($this->v["uID"])) {
            $this->searcher->v["uID"] = $this->v["uID"];
            $this->searcher->v["user"] = $this->v["user"];
            if (isset($this->v["usrInfo"])) {
                $this->searcher->v["usrInfo"] = $this->v["usrInfo"];
            }
        }
        return true;
    }

}
