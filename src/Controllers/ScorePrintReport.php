<?php
/**
  * ScoreAdminMisc is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class handles the custom needs of printing an individual PowerScore's report.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
  * @since v0.2.4
  */
namespace CannabisScore\Controllers;

use CannabisScore\Controllers\ScoreCalcs;

class ScorePrintReport extends ScoreCalcs
{
    public function printPreviewReport($isAdmin = false)
    {
        return view(
            'vendor.cannabisscore.powerscore-report-preview', 
            [
                "uID"      => $this->v["uID"],
                "sessData" => $this->sessData->dataSets
            ]
        )->render();
    }
    
    public function printPsRankingFilters($nID)
    {
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->searcher->v["nID"] = $nID;
        $this->searcher->v["psFiltChks"] = view(
            'vendor.cannabisscore.inc-filter-powerscores-checkboxes', 
            $this->searcher->v
        )->render();
        $ret = view(
            'vendor.cannabisscore.inc-filter-powerscores', 
            $this->searcher->v
        )->render();
        return '<div id="scoreRankFiltWrap">'
            . '<h5 class="mT0">Compare to other farms</h5>'
            . '<div class="pT5"></div>' . $ret . '</div>';
    }

    protected function printReportForCompetition($nID)
    {
        if (isset($this->sessData->dataSets["PSForCup"])) {
            $deetVal = '';
            foreach ($this->sessData->dataSets["PSForCup"] 
                as $i => $cup) {
                if (isset($cup->ps_cup_cup_id) 
                    && intVal($cup->ps_cup_cup_id) > 0) {
                    $deetVal .= (($i > 0) ? ', ' : '') 
                        . $GLOBALS["SL"]->def->getVal(
                            'PowerScore Competitions', 
                            $cup->ps_cup_cup_id
                        );
                }
            }
            return [ 'Competition', $deetVal, $nID ];
        }
        return [];
    }

    protected function printReportGrowingYear($nID)
    {
        if (isset($this->sessData->dataSets["powerscore"])) {
            $ps = $this->sessData->dataSets["powerscore"][0];
            if (isset($ps->ps_year)) {
                return [ 'Growing Year', $ps->ps_year, $nID ];
            }
        }
        return [];
    }

    protected function printReportUtilRef($nID)
    {
        $this->prepUtilityRefTitle();
        $ret = view(
            'vendor.cannabisscore.nodes.508-utility-referral-title', 
            $this->v
        )->render();
        return $ret;
    }
    
    public function customPrint490($nID)
    {
        if ($GLOBALS["SL"]->REQ->has('isPreview')) {
            return '<style> #blockWrap492, #blockWrap501, #blockWrap727 '
                . '{ display: none; } </style>';
        }
        $ret = '';
        $this->v["nID"] = $nID;
        if ($GLOBALS["SL"]->REQ->has('refresh')) {
            $ret .= view(
                'vendor.cannabisscore.nodes.490-report-calculations-top-refresh', 
                [
                    "psid" => $this->coreID
                ]
            )->render();
        } else {
            $ret .= $this->printReport490();
        }
        return ' <!-- customPrint490.start --> ' . $ret . ' <!-- customPrint490.end --> ';
    }
    
    public function printReport490()
    {
        if ($GLOBALS["SL"]->REQ->has('step') 
            && $GLOBALS["SL"]->REQ->has('postAction')
            && trim($GLOBALS["SL"]->REQ->get('postAction')) != '') {
            return $this->redir($GLOBALS["SL"]->REQ->get('postAction'), true);
        }
        $this->initSearcher();
        $this->searcher->getSearchFilts();
        $this->getAllReportCalcs();
        if (!$GLOBALS["SL"]->REQ->has('fltFarm')) {
            $this->searcher->v["fltFarm"] = $this->sessData
                ->dataSets["powerscore"][0]->ps_characterize;
            $this->searcher->searchFiltsURLXtra();
        }
        $this->searcher->v["nID"] = 490;
        $this->v["isPast"] = ($this->sessData->dataSets["powerscore"][0]->ps_time_type 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));

        $GLOBALS["SL"]->pageCSS .= view(
            'vendor.cannabisscore.nodes.490-report-calculations-css'
        )->render();
        $GLOBALS["SL"]->pageJAVA .= view(
            'vendor.cannabisscore.nodes.490-report-calculations-js',
            $this->v
        )->render();
        $ret = view(
            'vendor.cannabisscore.nodes.490-report-calculations', 
            $this->v
        )->render();
        return ' <!-- printReport490.start --> ' . $ret . ' <!-- printReport490.end --> ';
    }



}
