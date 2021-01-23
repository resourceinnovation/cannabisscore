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

@elseif ($dataSet == 'emissions')

        <td class="slGrey">{{ number_format($psCnt->ps_effic_emis) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_emis_prod) }}</td>

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
        <td class="slGrey">{{ number_format($psCnt->ps_effic_water_prod) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_waste) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_waste_prod) }}</td>

@elseif ($dataSet == 'totals')

        <th></th>
        <th></th>
        <th></th>
        <th></th>
        @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste_lbs) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
            <th></th>
        @endif
        @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
            <th></th>
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

@elseif ($dataSet == 'emissions')

        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_emis, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_emis_prod, 3) }}</b></th>

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
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_water_prod, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_waste, 3) }}</b></th>
        <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_waste_prod, 3) }}</b></th>

@elseif ($dataSet == 'totals')

    @if (!$isExcel)
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_flower_canopy_size) }}</b></td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_total_canopy_size) }}</b></td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_grams_dry) }}</b></td>
        <th><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_kwh) }}</b></td>
        @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_kw_peak) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_water) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_waste) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_natural_gas) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_generator) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_biofuel_wood) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_propane) }}</b></td>
        @endif
        @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
            <td><b>{{ $GLOBALS["SL"]->numKMBT($psAvg->ps_tot_fuel_oil) }}</b></td>
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
            {!! view(
                'vendor.cannabisscore.nodes.170-powerscore-listing-first-cols',
                [
                    "nID"             => $nID,
                    "ps"              => $ps,
                    "i"               => $i,
                    "scoreYearMonths" => $scoreYearMonths
                ]
            )->render() !!}

        @if (isset($fltCmpl) 
            && $fltCmpl == 0 
            && Auth::user()->hasRole('administrator|staff'))
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
            </td>
        @endif
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

@elseif ($dataSet == 'emissions')

            <td>
            @if ((isset($ps->ps_effic_emis_status) 
                && intVal($ps->ps_effic_emis_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_emis < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_emis, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_emis)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_emis
                    ) }}%
                    </div>
                @endif
            @else -
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_emis_prod_status) 
                && intVal($ps->ps_effic_emis_prod_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_emis_prod < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_emis_prod, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_emis_prod)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_emis_prod
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
            @if ((isset($ps->ps_effic_water_prod_status) 
                && intVal($ps->ps_effic_water_prod_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_water_prod < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_water_prod, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_water_prod)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_water_prod
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
            <td>
            @if ((isset($ps->ps_effic_waste_prod_status) 
                && intVal($ps->ps_effic_waste_prod_status) == $defCmplt)
                    || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_waste_prod < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_waste_prod, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_waste_prod)) 
                    <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs(
                        $allranks[$ps->ps_id]->ps_rnk_waste_prod
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
            <td class="slGrey">
                @if (isset($ps->ps_tot_kw_peak) && intVal($ps->ps_tot_kw_peak) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_kw_peak) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_water) && intVal($ps->ps_tot_water) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_water) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_waste) && intVal($ps->ps_tot_waste) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_waste) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_natural_gas) && intVal($ps->ps_tot_natural_gas) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_natural_gas) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_generator) && intVal($ps->ps_tot_generator) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_generator) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_biofuel_wood) && intVal($ps->ps_tot_biofuel_wood) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_biofuel_wood) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_propane) && intVal($ps->ps_tot_propane) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_propane) }}
                @endif
            </td>
            <td class="slGrey">
                @if (isset($ps->ps_tot_fuel_oil) && intVal($ps->ps_tot_fuel_oil) > 0)
                    {{ $GLOBALS["SL"]->numKMBT($ps->ps_tot_fuel_oil) }}
                @endif
            </td>
        @else
            <td>{{ number_format($ps->ps_grams) }}</td>
            <td>{{ number_format($ps->ps_kwh_tot_calc) }}</td>
            <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
            <td>
                @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
                    {{ number_format($ps->ps_tot_kw_peak) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
                    {{ number_format($ps->ps_tot_water) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
                    {{ number_format($ps->ps_tot_waste) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
                    {{ number_format($ps->ps_tot_natural_gas) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
                    {{ number_format($ps->ps_tot_generator) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
                    {{ number_format($ps->ps_tot_biofuel_wood) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
                    {{ number_format($ps->ps_tot_propane) }}
                @endif
            </td>
            <td>
                @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
                    {{ number_format($ps->ps_tot_fuel_oil) }}
                @endif
            </td>
        @endif 

@elseif ($dataSet == 'names' && Auth::user() && Auth::user()->hasRole('administrator|staff'))
    
    <td>
        @if (isset($ps->ps_name) && trim($ps->ps_name) != '')
            {{ trim($ps->ps_name) }}
        @endif
    </td>

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