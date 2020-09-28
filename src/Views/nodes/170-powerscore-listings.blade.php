<!-- generated from resources/views/vendor/cannabisscore/nodes/170-powerscore-listings.blade.php -->
<table border=0 class="table w100 bgWht">

{!! view(
    'vendor.cannabisscore.nodes.170-powerscore-listings-header',
    [
        "nID"     => $nID,
        "fixed"   => '',
        "sort"    => $sort,
        "fltCmpl" => $fltCmpl,
        "dataSet" => $dataSet,
        "psSum"   => $psSum,
        "isExcel" => $isExcel
    ]
)->render() !!}

@if (!isset($fltPartner) || $fltPartner <= 0)
    <tr>
        <th><b>Found</b></th>
        <th></th>
        <th></th>
        <th><b>{{ number_format($allscores->count()) }}</b></th>

@if (!isset($dataSet) || in_array($dataSet, ['', 'kpi']))

        <td class="slGrey">{{ number_format($psCnt->ps_effic_fac_all) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_facility) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_fac_non) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_prod_all) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_production) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_prod_non) }}</td>

@elseif ($dataSet == 'lighting')

        <td class="slGrey">{{ number_format($psCnt->ps_effic_lighting) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_lighting_power_density) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_lpd_flower) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_lpd_veg) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_lpd_clone) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_hlpd_ma) }}</td>

@elseif ($dataSet == 'others')

        <td class="slGrey">{{ number_format($psCnt->ps_effic_hvac) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_water) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_waste) }}</td>

@elseif ($dataSet == 'totals')

        <th></td>
        <th></td>
        <th></td>
        <th></td>
        @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_waste_lbs) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
            <th></td>
        @endif
        @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
            <th></td>
        @endif

@endif
        


    </tr>
@endif

@if ($allscores && $allscores->isNotEmpty())

    <tr>
        <th><b>Averages</b></th>
        <th></th>
        <th></th>
        <th><b>{{ round($psAvg->ps_effic_over_similar) }}%</b></th>

@if (!isset($dataSet) || in_array($dataSet, ['', 'kpi']))

        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_fac_all, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_facility, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_fac_non, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_prod_all, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_production, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_prod_non, 3) }}</b></th>

@elseif ($dataSet == 'lighting')

        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_lighting, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lighting_power_density, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_flower, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_veg, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_clone, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_hlpd_ma, 3) }}</b></th>

@elseif ($dataSet == 'others')

        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_hvac, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_water, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_waste, 3) }}</b></th>

@elseif ($dataSet == 'totals')

    @if (!$isExcel)
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_flower_canopy_size) }}</td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_total_canopy_size) }}</td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_grams_dry) }}</td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_kwh) }}</td>
        @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_kw_peak) }}</td>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_water) }}</td>
        @endif
        @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_waste) }}</td>
        @endif
        @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_natural_gas) }}</td>
        @endif
        @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_generator) }}</td>
        @endif
        @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_biofuel_wood) }}</td>
        @endif
        @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_propane) }}</td>
        @endif
        @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
            <td>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_fuel_oil) }}</td>
        @endif
    @else
        <td>{{ number_format($psAvg->ps_grams) }}</td>
        <td>{{ number_format($psAvg->ps_kwh_tot_calc) }}</td>
        <td>{{ number_format($psAvg->ps_flower_canopy_size) }}</td>
        @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
            <td>{{ number_format($psAvg->ps_tot_kw_peak) }}</td>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
            <td>{{ number_format($psAvg->ps_tot_water) }}</td>
        @endif
        @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
            <td>{{ number_format($psAvg->ps_tot_waste) }}</td>
        @endif
        @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
            <td>{{ number_format($psAvg->ps_tot_natural_gas) }}</td>
        @endif
        @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
            <td>{{ number_format($psAvg->ps_tot_generator) }}</td>
        @endif
        @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
            <td>{{ number_format($psAvg->ps_tot_biofuel_wood) }}</td>
        @endif
        @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
            <td>{{ number_format($psAvg->ps_tot_propane) }}</td>
        @endif
        @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
            <td>{{ number_format($psAvg->ps_tot_fuel_oil) }}</td>
        @endif
    @endif

@endif
    </tr>

    @foreach ($allscores as $i => $ps)

        @if ($GLOBALS["SL"]->REQ->has('test') 
            && isset($GLOBALS["SL"]->x["fakeSites"])
            && isset($GLOBALS["SL"]->x["fakeSites"][$i])
            && trim($GLOBALS["SL"]->x["fakeSites"][$i]) != '')
            <tr><td colspan=12 >
                <h4>{!! $GLOBALS["SL"]->x["fakeSites"][$i] !!}</h4>
            </td></tr>
        @endif

        @if (!$GLOBALS["SL"]->REQ->has('test') || $i < 25)

            <tr @if ($i%2 == 0) class="row2" @endif >

            <td>
            @if (Auth::user()->hasRole('administrator|staff')
                && $ps->ps_status == 556)
                <div class="relDiv">
                    <div class="absDiv slRedDark" 
                        style="left: -20px; top: 2px;">
                        <i class="fa fa-star-half-o" aria-hidden="true"></i>
                    </div>
                </div>
            @endif
            @if ($nID == 1373 && $GLOBALS["SL"]->x["partnerLevel"] < 5)
                {{ (1+$i) }})
            @else
                <a href="/calculated/read-{{ $ps->ps_id }}" 
                    @if (Auth::user()->hasRole('administrator|staff')
                        && $ps->ps_status == 556)
                        class="slRedDark"
                    @endif target="_blank">
                    @if ($nID == 808) {{ $ps->ps_name }} 
                    @else #{{ $ps->ps_id }} 
                    @endif </a>
            @endif
            </td>
            <td>
                <nobr>{{ $ps->ps_state }}
                {{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
                    $GLOBALS["SL"]->def->getVal(
                        'PowerScore Farm Types', 
                        $ps->ps_characterize
                    )) 
                }}</nobr>
            </td>
            <td>
            @if (isset($ps->ps_start_month) 
                && intVal($ps->ps_start_month) > 0
                && $scoreYearMonths[$ps->ps_id]["has"])
                <nobr>{{ 
                date("n/y", mktime(0, 0, 0, 
                    $scoreYearMonths[$ps->ps_id]["endMonth"], 1, 
                    $scoreYearMonths[$ps->ps_id]["endYear"])) }}-{{ 
                date("n/y", mktime(0, 0, 0, 
                    $scoreYearMonths[$ps->ps_id]["startMonth"], 1, 
                    $scoreYearMonths[$ps->ps_id]["startYear"])) 
                }}</nobr> 
            @endif
        @if (isset($fltCmpl) 
            && $fltCmpl == 0 
            && Auth::user()->hasRole('administrator|staff'))
            </td>
            <td>
            @if ($ps->ps_status == 243)
                <span class="slBlueDark">Complete</span>
            @elseif ($ps->ps_status == 364)
                <span class="slGrey">Archived</span>
            @elseif ($ps->ps_status == 556)
                <span class="slRedDark">
                <i class="fa fa-star-half-o mR3" aria-hidden="true"></i>
                New</span>
            @else 
                {{ $GLOBALS["SL"]->def->getVal(
                    'PowerScore Status', 
                    $ps->ps_status
                ) }}
            @endif
        @endif
            </td>
            <td>
                {{ round($ps->ps_effic_over_similar) }}%
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_overall_avg))
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_overall_avg
                    ) }}%
                    </div>
                @endif
            </td>

@if (!isset($dataSet) || in_array($dataSet, ['', 'kpi']))

            <td>
            @if ((isset($ps->ps_effic_fac_all_status) 
                && intVal($ps->ps_effic_fac_all_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_fac_all < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_fac_all, 3) }}
                @endif
                @if (!$isExcel && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_fac_all))
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_fac_all
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_facility_status) 
                && intVal($ps->ps_effic_facility_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_facility < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}
                @endif
                @if (!$isExcel && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_facility))
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_facility
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_fac_non_status) 
                    && intVal($ps->ps_effic_fac_non_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_fac_non < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_fac_non, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_fac_non))
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_fac_non
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_prod_all_status) 
                && intVal($ps->ps_effic_prod_all_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_prod_all < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_prod_all, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_prod_all)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_prod_all
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_production_status) 
                && intVal($ps->ps_effic_production_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_production < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_production)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_production
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_prod_non_status) 
                && intVal($ps->ps_effic_prod_non_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_prod_non < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_prod_non, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_prod_non)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_prod_non
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>

@elseif ($dataSet == 'lighting')

            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_lighting < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_lighting))
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_lighting
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if (isset($ps->ps_lighting_power_density) 
                    && $ps->ps_lighting_power_density > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_lighting_power_density, 3) }}
                @else 0
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if (isset($ps->ps_lpd_flower) && $ps->ps_lpd_flower > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_flower, 3) }}
                @else 0
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if (isset($ps->ps_lpd_veg) && $ps->ps_lpd_veg > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_veg, 3) }}
                @else 0
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if (isset($ps->ps_lpd_clone) && $ps->ps_lpd_clone > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_clone, 3) }}
                @else 0
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if (isset($ps->ps_hlpd_ma) && $ps->ps_hlpd_ma > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_hlpd_ma, 3) }}
                @else 0
                @endif
            @else -
            @endif
            </td>

@elseif ($dataSet == 'others')

            <td>
            @if ((isset($ps->ps_effic_hvac_status) 
                && intVal($ps->ps_effic_hvac_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_hvac < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_hvac)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_hvac
                    ) }}%
                    </div>
                @endif
            @else -
            @endif 
            </td>
            <td>
            @if ((isset($ps->ps_effic_water_status) 
                && intVal($ps->ps_effic_water_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_water < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_water, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_water)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_water
                    ) }}%
                    </div>
                @endif
            @else -
            @endif 
            </td>
            <td>
            @if ((isset($ps->ps_effic_waste_status) 
                && intVal($ps->ps_effic_waste_status) == $defCmplt)
                    || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_waste < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_waste, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_waste)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_waste
                    ) }}%
                    </div>
                @endif
            @else -
            @endif 
            </td>

@elseif ($dataSet == 'totals')

        @if (!$isExcel)
            <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_flower_canopy_size) }}</td>
            <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_total_canopy_size) }}</td>
            <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_grams_dry) }}</td>
            <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_kwh) }}</td>
            @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_kw_peak) }}</td>
            @endif
            @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_water) }}</td>
            @endif
            @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_waste) }}</td>
            @endif
            @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_natural_gas) }}</td>
            @endif
            @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_generator) }}</td>
            @endif
            @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_biofuel_wood) }}</td>
            @endif
            @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_propane) }}</td>
            @endif
            @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
                <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_fuel_oil) }}</td>
            @endif
        @else
            <td>{{ number_format($ps->ps_grams) }}</td>
            <td>{{ number_format($ps->ps_kwh_tot_calc) }}</td>
            <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
            @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
                <td>{{ number_format($ps->ps_tot_kw_peak) }}</td>
            @endif
            @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
                <td>{{ number_format($ps->ps_tot_water) }}</td>
            @endif
            @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
                <td>{{ number_format($ps->ps_tot_waste) }}</td>
            @endif
            @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
                <td>{{ number_format($ps->ps_tot_natural_gas) }}</td>
            @endif
            @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
                <td>{{ number_format($ps->ps_tot_generator) }}</td>
            @endif
            @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
                <td>{{ number_format($ps->ps_tot_biofuel_wood) }}</td>
            @endif
            @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
                <td>{{ number_format($ps->ps_tot_propane) }}</td>
            @endif
            @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
                <td>{{ number_format($ps->ps_tot_fuel_oil) }}</td>
            @endif
        @endif 

@endif

            </tr>
            @if ($GLOBALS["SL"]->REQ->has('review') 
                && Auth::user()->hasRole('administrator|staff'))
                <tr class="brdTopNon @if ($i%2 == 0) row2 @endif " >
                    <td class="taR">
                        <div class="mTn10 slGrey fPerc66">Review Notes:</div>
                    </td>
                    <td colspan=9 >
                        <div class="mTn15"><i>{!! $ps->ps_notes !!}</i></div>
                    </td>
                </tr>
            @endif

        @endif

    @endforeach



@if (isset($dataSet) && $dataSet == 'totals')

    <tr>
        <td colspan=3 class="slGrey">Sums</td>
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_flower_canopy_size) }}</td>
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_total_canopy_size) }}</td>
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_grams_dry) }}</td>
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_kwh) }}</td>
    @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_kw_peak) }}</td>
    @endif
    @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_water) }}</td>
    @endif
    @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_waste) }}</td>
    @endif
    @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_natural_gas) }}</td>
    @endif
    @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_generator) }}</td>
    @endif
    @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_biofuel_wood) }}</td>
    @endif
    @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_propane) }}</td>
    @endif
    @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
        <td class="slGrey">{{ $GLOBALS["SL"]->numKMBT($psSum->ps_tot_fuel_oil) }}</td>
    @endif
    </tr>

@endif




@else
    <tr><td colspan=13 class="slGrey">
        <i>No PowerScores found.</i>
    </td></tr>
@endif

</table>