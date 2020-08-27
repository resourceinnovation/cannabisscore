<?php
/**
  * ComplyPrintReport is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class handles the custom needs of printing an individual compliance report.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since v0.2.18
  */
namespace CannabisScore\Controllers;

use CannabisScore\Controllers\ScoreCalcsPrint;

class ComplyPrintReport extends ScoreCalcsPrint
{

    protected function reportMaMonths($nID)
    {
        if (!isset($this->sessData->dataSets["compliance_ma"])
            || !isset($this->sessData->dataSets["compliance_ma_months"])) {
            return '<!-- no months -->';
        }
        return view(
            'vendor.cannabisscore.nodes.1420-ma-compliance-monthly', 
            [
                "rec"    => $this->sessData->dataSets["compliance_ma"][0],
                "months" => $this->sessData->dataSets["compliance_ma_months"]
            ]
        )->render();
    }

    protected function reportMaID($nID)
    {
        return [
            'PowerScore Comply ID#',
            $this->coreID
        ];
    }

    protected function reportMaNextPro($nID)
    {
        return view(
            'vendor.cannabisscore.nodes.1436-ma-compliance-next-go-pro', 
            [ "rec" => $this->sessData->dataSets["compliance_ma"][0] ]
        )->render();
    }

    protected function reportMaProPdf($nID)
    {
        $ret = '<style> #node1448, #blockWrap945 { display: none; } </style>';
        if ($GLOBALS["SL"]->REQ->has('isPreview')) {
            return $ret;
        }
        if (isset($this->sessData->dataSets["powerscore"])
            && isset($this->sessData->dataSets["powerscore"][0]->ps_com_ma_id)
            && intVal($this->sessData->dataSets["powerscore"][0]->ps_com_ma_id) > 0) {
            return view(
                'vendor.cannabisscore.nodes.1449-ma-compliance-above-powerscore', 
                [ "id" => $this->sessData->dataSets["powerscore"][0]->ps_com_ma_id ]
            )->render();
        }
        return $ret;
    }

    protected function reportMaPSID($nID)
    {
        $deetLabel = 'PowerScore ID#';
        $deetVal = '';
        if (isset($this->sessData->dataSets["compliance_ma"])) {
            $com = $this->sessData->dataSets["compliance_ma"][0];
            if (isset($com->com_ma_ps_id)
                && intVal($com->com_ma_ps_id) > 0) {
                $deetVal = '<a href="/calculated/read-' . $com->com_ma_ps_id 
                    . '" target="_blank">#' . $com->com_ma_ps_id . '</a>';
            }
        }
        return [ $deetLabel, $deetVal, $nID ];
    }

    protected function reportMaEfficProd($nID)
    {
        $deetLabel = 'Electric Production Efficiency (grams/kWh)';
        $deetVal = 0;
        if (isset($this->sessData->dataSets["compliance_ma"])) {
            $com = $this->sessData->dataSets["compliance_ma"][0];
            if (isset($com->com_ma_grams)
                && intVal($com->com_ma_grams) > 0
                && isset($com->com_ma_tot_kwh)
                && intVal($com->com_ma_tot_kwh) > 0) {
                $deetVal = $com->com_ma_grams/$com->com_ma_tot_kwh;
                $deetVal = $GLOBALS["SL"]->sigFigs($deetVal, 3);
            }
        }
        return [ $deetLabel, $deetVal, $nID ];
    }

    protected function reportMaWood($nID)
    {
        if (isset($this->sessData->dataSets["compliance_ma"])
            && isset($this->sessData->dataSets["compliance_ma"][0]->com_ma_tot_biofuel)) {
            $deetLabel = 'Annual Total Wood';
            if (isset($this->sessData->dataSets["compliance_ma"][0]->com_ma_unit_wood)) {
                $def = $this->sessData->dataSets["compliance_ma"][0]->com_ma_unit_wood;
                $def = $GLOBALS["SL"]->def->getVal('Biofuel Wood Units', $def);
                $deetLabel .= ' (' . $def . ')';
            }
            $deetVal = $this->sessData->dataSets["compliance_ma"][0]->com_ma_tot_biofuel;
            return [ $deetLabel, $deetVal, $nID ];
        }
        return [];
    }

    protected function reportMaYear($nID)
    {
        $deetLabel = 'Reporting Year';
        $deetVal = intVal(date("Y"))-1;
        if (isset($this->sessData->dataSets["compliance_ma"])) {
            $com = $this->sessData->dataSets["compliance_ma"][0];
            if (isset($com->com_ma_year) && intVal($com->com_ma_year) > 0) {
                $deetVal = $com->com_ma_year;
            }
        }
        return [ $deetLabel, $deetVal, $nID ];
    }

}
