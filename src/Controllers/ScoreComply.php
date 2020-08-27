<?php
/**
  * ScoreComply is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the processes for managing compliance submissions.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2.4
  */
namespace CannabisScore\Controllers;

use DB;
use App\Models\RIIPowerscore;
use App\Models\RIIComplianceMa;
use CannabisScore\Controllers\ScorePartners;

class ScoreComply extends ScorePartners
{
    protected function reportMaListing($nID)
    {
        $recs = DB::table('rii_compliance_ma')
            ->leftJoin('rii_powerscore', 'rii_compliance_ma.com_ma_ps_id', 
                '=', 'rii_powerscore.ps_id')
            ->whereNotNull('rii_compliance_ma.com_ma_name')
            ->where('rii_compliance_ma.com_ma_name', 'NOT LIKE', '')
            ->select('rii_compliance_ma.*', 'rii_powerscore.ps_email')
            ->orderBy('rii_compliance_ma.com_ma_id', 'desc')
            ->get();
        $users = [];
        if ($recs->isNotEmpty()) {
            foreach ($recs as $rec) {
                if (isset($rec->com_ma_user_id) 
                    && intVal($rec->com_ma_user_id) > 0) {
                    $users[$rec->com_ma_user_id] = $this->printUserLnk($rec->com_ma_user_id);
                }
            }
        }
        return view(
            'vendor.cannabisscore.nodes.1543-ma-comply-listing', 
            [
                "nID"   => $nID,
                "recs"  => $recs,
                "users" => $users
            ]
        )->render();
    }

    protected function calcMaCompliance($nID)
    {
        $tbl = 'compliance_ma';
        if (isset($this->sessData->dataSets[$tbl])) {
            $com = $this->sessData->dataSets[$tbl][0];
            $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh 
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_diesel
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_propane
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_fuel_oil
                = $this->sessData->dataSets[$tbl][0]->com_ma_tot_water
                = $this->sessData->dataSets[$tbl][0]->com_ma_effic_production
                = 0;
            $this->calcConvertGramsDry('compliance_ma', 'com_ma_');

            if (isset($this->sessData->dataSets["compliance_ma_months"])
                && sizeof($this->sessData->dataSets["compliance_ma_months"]) > 0) {
                foreach ($this->sessData->dataSets["compliance_ma_months"] as $mon) {
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh
                        += $mon->com_ma_month_kwh;
                    if (isset($mon->com_ma_month_renew_kwh)) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew
                            += $mon->com_ma_month_renew_kwh;
                    }
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_diesel
                        += $mon->com_ma_month_diesel_gallons;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel
                        += $mon->com_ma_month_biofuel_wood_tons;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_fuel_oil
                        += $mon->com_ma_month_fuel_oil;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_propane
                        += $mon->com_ma_month_propane;
                    $this->sessData->dataSets[$tbl][0]->com_ma_tot_water
                        += $mon->com_ma_month_water;
                    if ($this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                        < $mon->com_ma_month_kw) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_kw
                            = $mon->com_ma_month_kw;
                    }
                    if ($mon->com_ma_month_natural_gas_therms > 0) {
                        $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                            += $mon->com_ma_month_natural_gas_therms;
                //} elseif ($mon->com_ma_month_natural_gas_gallons > 0) {
                //    $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas
                //        += $mon->com_ma_month_natural_gas_gallons;
                    }
                }
            }

            $defCcf = $GLOBALS["SL"]->def->getID('Natural Gas Units', 'CCF');
            if (isset($com->com_ma_unit_natural_gas)
                && $com->com_ma_unit_natural_gas == $defCcf) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_natural_gas 
                    *= 0.0103412;
            // https://sciencing.com/convert-cubic-feet-therms-8374234.html
            }
            /*
            if (isset($this->sessData->dataSets[$tbl][0]->com_ma_unit_wood)
                && $this->sessData->dataSets[$tbl][0]->com_ma_unit_wood
                    == $GLOBALS["SL"]->def->getID('Biofuel Wood Units', 'Cords')) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_biofuel *= 2.6;
            // http://extension.msstate.edu/sites/default/files/publications/publications/P2244_web.pdf
            }
            */
            if (isset($com->com_ma_include_renewables)
                && intVal($com->com_ma_include_renewables) == 0) {
                $this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh
                    += $this->sessData->dataSets[$tbl][0]->com_ma_tot_renew;
            }
            $btu = 3.412*$this->sessData->dataSets[$tbl][0]->com_ma_tot_kwh;
            if ($btu > 0) {
                $this->sessData->dataSets[$tbl][0]->com_ma_effic_production
                    = $this->sessData->dataSets[$tbl][0]->com_ma_grams_dry/$btu;
            }
//echo '<pre>'; print_r($this->sessData->dataSets[$tbl][0]); echo '</pre>'; exit;
            $this->sessData->dataSets[$tbl][0]->save();
        }
        return true;
    }

    protected function completeMaCompliance($nID)
    {
        if (isset($this->sessData->dataSets["compliance_ma"])) {
            $def = $GLOBALS["SL"]->def->getID('Compliance Status', 'Ranked Data Set');
            $this->sessData->dataSets["compliance_ma"][0]->com_ma_status = $def;
            $this->sessData->dataSets["compliance_ma"][0]->save();
            $this->calcMaCompliance($nID);
        }
        return true;
    }

}

