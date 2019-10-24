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
        return '<div id="scoreRankFiltWrap">'
            . '<h5 class="mT0">Compare to other farms</h5>'
            . '<div class="pT5"></div>' . view(
                'vendor.cannabisscore.inc-filter-powerscores', 
                $this->searcher->v
            )->render() . '</div>';
    }

    protected function printReportForCompetition($nID)
    {
        if (isset($this->sessData->dataSets["PSForCup"])) {
            $deetVal = '';
            foreach ($this->sessData->dataSets["PSForCup"] 
                as $i => $cup) {
                if (isset($cup->PsCupCupID) 
                    && intVal($cup->PsCupCupID) > 0) {
                    $deetVal .= (($i > 0) ? ', ' : '') 
                        . $GLOBALS["SL"]->def->getVal(
                            'PowerScore Competitions', 
                            $cup->PsCupCupID
                        );
                }
            }
            return [ 'Competition', $deetVal, $nID ];
        }
        return [];
    }

    protected function printReportGrowingYear($nID)
    {
        if (isset($this->sessData->dataSets["PowerScore"])) {
            $ps = $this->sessData->dataSets["PowerScore"][0];
            if (isset($ps->PsYear)) {
                return [ 'Growing Year', $ps->PsYear, $nID ];
            }
        }
        return [];
    }

    protected function printReportUtilRef($nID)
    {
        $this->prepUtilityRefTitle();
        return view(
            'vendor.cannabisscore.nodes.508-utility-referral-title', 
            $this->v
        )->render();
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
        return $ret;
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
                ->dataSets["PowerScore"][0]->PsCharacterize;
            $this->searcher->searchFiltsURLXtra();
        }
        $this->searcher->v["nID"] = 490;
        $this->v["isPast"] = ($this->sessData->dataSets["PowerScore"][0]->PsTimeType 
            == $GLOBALS["SL"]->def->getID('PowerScore Submission Type', 'Past'));
        return view(
            'vendor.cannabisscore.nodes.490-report-calculations', 
            $this->v
        )->render();
    }



}
