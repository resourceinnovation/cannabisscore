<?php
/**
  * ScoreReports is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which crunch heavier PowerScore
  * aggregation calculations to be printed into reports generated live.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since v0.2.3
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerScore;
use CannabisScore\Controllers\ScoreListings;

class ScoreReportLightingManu extends ScoreListings
{
    public function printCompareLightManu($nID = -3)
    {
        //  prod, fac, hvac, light, water, waste // number of records analyzed
        $this->v["dataLegend"] = [
            ['Facility Efficiency',   'kWh / sq ft',     95, 0],
            ['Production Efficiency', 'g / kWh',         94, 0],
            ['HVAC Efficiency',       'kWh / sq ft',     68, 0],
            ['Lighting Efficiency',   'W / sq ft',       68, 0],
            ['Water Efficiency',      'gallons / sq ft', 24, 0],
            ['Waste Efficiency',      'g / kWh',         25, 0]
        ];
        $this->v["competitionData"] = [
            ['Your Customers', [ // in order of legend array
                [25.7, 4.46, 50.9, 15.2, 14.2, 34.0],
                [  80,   93,   70,   78,   60,   42],
                []
              ]
            ],
            ['Average', [
                [35.7, 1.46, 80.9, 25.2, 20.4, 31.2],
                [  50,   50,   50,   50,   50,   50],
                []
              ]
            ],
            ['Customers of Competitor A', [
                [29.2, 3.26, 64.3, 19.3, 18.9, 25.7],
                [  70,   77,   60,   61,   55,   62],
                []
              ]
            ],
            ['Customers of Competitor B', [
                [43.2, 0.96, 83.4, 28.2, 22.2, 37.7],
                [  33,   14,   45,   42,   37,   36],
                []
              ]
            ],
            ['Customers of Competitor C', [
                [32.5, 2.43, 77.9, 23.8, 19.3, 33.1],
                [  55,   57,   54,   67,   54,   43],
                []
              ]
            ],
        ];
        $this->v["competitionGraphs"] = [];
        foreach ($this->v["dataLegend"] as $i => $leg) {
            foreach ($this->v["competitionData"] as $dat) {
                if ($this->v["dataLegend"][$i][3] < $dat[1][0][$i]) {
                    $this->v["dataLegend"][$i][3] = $dat[1][0][$i];
                }
            }
            $this->v["dataLegend"][$i][3] = ceil($this->v["dataLegend"][$i][3]);
        }

        $GLOBALS["SL"]->x["needsCharts"] = true;
        return view('vendor.cannabisscore.nodes.979-compare-lighting-manufacturers', $this->v)->render();
	  }
    
}