<tr>
<th>Score ID#</th>
@if (Auth::user()->hasRole('administrator|staff')
    && isset($fltCmpl) 
    && $fltCmpl != 243)
    <th>Status</th>
@endif
@if (isset($showFarmNames) && $showFarmNames)
    <th>Farm Name</th>
@endif
<th>Overall</th>
<th>Facility KPI (kBtu / sq ft)</th>
<th>Electric Facility KPI (kBtu / sq ft)</th>
<th>Non-Electric Facility KPI (kBtu / sq ft)</th>
<th>Production KPI (g / kBtu)</th>
<th>Electric Production KPI (g / kBtu)</th>
<th>Non-Electric Production KPI (g / kBtu)</th>
<th>Water KPI (gallons / sq ft)</th>
<th>Waste KPI (lbs / sq ft)</th>
<th>HVAC KPI (kBtu / sq ft)</th>
<th>Lighting KPI (kWh / day)</th>
<th>LPD: Overall (W / sq ft)</th>
<th>LPD: Flower (W / sq ft)</th>
<th>LPD: Veg (W / sq ft)</th>
<th>LPD: Clone (W / sq ft)</th>
<?php /*
<th>HLPD MA (W / sq ft)</th>
*/ ?>
<th>Grams Dry Flower</th>
<th>Total kWh</th>
<th>Flower Canopy Sq Ft</th>
<th>Type</th>
<th>Mechanical Water Heating</th>
<th>Manual Environmental Controls</th>
<th>Automatic Environmental Controls</th>
<th>Vertical Stacking</th>
<th>Harvests Per Year</th>
@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $def)
    <th>Has {{ $def->def_value }}?</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Sunlight</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Light Dep</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Artificial Light</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} HVAC Type</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} HVAC Other</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Days In Cycle</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Canopy Sqft</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Total Light Watts</th>
    <th>{{ str_replace(' Plants', '', $def->def_value) }} Light Counts</th>
@endforeach
<th>County</th>
<th>State</th>
<th>Zip</th>
@if (Auth::user()->hasRole('administrator|staff'))
    <th>Email</th>
    <th>Url</th>
@endif
</tr>

@if (!isset($fltPartner) || $fltPartner <= 0)
    <tr>
        <th><b>Found</b></th>
    @if (Auth::user()->hasRole('administrator|staff')
        && isset($fltCmpl) 
        && $fltCmpl != 243)
        <th></th>
    @endif
    @if (isset($showFarmNames) && $showFarmNames)
        <th></th>
    @endif
        <th><b>{{ number_format($allscores->count()) }}</b></th>
        <td>{{ number_format($psCnt->ps_effic_fac_all) }}</td>
        <td>{{ number_format($psCnt->ps_effic_facility) }}</td>
        <td>{{ number_format($psCnt->ps_effic_non_electric) }}</td>
        <td>{{ number_format($psCnt->ps_effic_prod_all) }}</td>
        <td>{{ number_format($psCnt->ps_effic_production) }}</td>
        <td>{{ number_format($psCnt->ps_effic_prod_non) }}</td>
        <td>{{ number_format($psCnt->ps_effic_water) }}</td>
        <td>{{ number_format($psCnt->ps_effic_waste) }}</td>
        <td>{{ number_format($psCnt->ps_effic_hvac) }}</td>
        <td>{{ number_format($psCnt->ps_effic_lighting) }}</td>
        <td>{{ number_format($psCnt->ps_lighting_power_density) }}</td>
        <td>{{ number_format($psCnt->ps_lpd_flower) }}</td>
        <td>{{ number_format($psCnt->ps_lpd_veg) }}</td>
        <td>{{ number_format($psCnt->ps_lpd_clone) }}</td>
        <th colspan=5 >
    </tr>
@endif

@if (sizeof($allscores) > 0)

    <tr>
        <th>Averages</th>
    @if (Auth::user()->hasRole('administrator|staff')
        && isset($fltCmpl) 
        && $fltCmpl != 243)
        <th></th>
    @endif
    @if (isset($showFarmNames) && $showFarmNames)
        <th></th>
    @endif
        <th>{{ round($psAvg->ps_effic_overall) }}%</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_facility, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_non_electric, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_production, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_water, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_waste, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_hvac, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_lighting, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lighting_power_density, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_flower, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_veg, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lpd_clone, 3) }}</th>
        <th>{{ number_format($psAvg->ps_grams_dry) }}</th>
        <th>{{ number_format($psAvg->ps_kwh) }}</th>
        <th>{{ number_format($psAvg->ps_flower_canopy_size) }}</th>
        <th>&nbsp;</th><th>&nbsp;</th>
        @foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $def)
            <th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>
            <th>&nbsp;</th><th>&nbsp;</th>
            <th>&nbsp; <!-- days in cycle --> </th>
            <th>&nbsp; <!-- average canopy square feet --> </th>
            <th>&nbsp; <!-- total stage light watts --> </th>
            <th>&nbsp;</th>
        @endforeach
        <th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>
    </tr>

    @foreach ($allscores as $i => $ps)
        <tr>
        @if ($nID == 1373 && $GLOBALS["SL"]->x["partnerLevel"] < 5)
            <td>{{ (1+$i) }})</td>
        @else
            <td>#{{ $ps->ps_id }}</td>
        @endif
        @if (isset($fltCmpl) && $fltCmpl != 243)
            <td>{{ $GLOBALS["SL"]->def->getVal(
                'PowerScore Status', 
                $ps->ps_status
            ) }}</td>
        @endif
        @if (isset($showFarmNames) && $showFarmNames) 
            <td> @if (isset($ps->ps_name)) {{ $ps->ps_name }} @endif </td>
        @endif
        <td>{{ round($ps->ps_effic_overall) }}%</td>
        <td>
        @if ((isset($ps->ps_effic_fac_all_status) 
            && intVal($ps->ps_effic_fac_all_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_fac_all < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_fac_all, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_facility_status) 
            && intVal($ps->ps_effic_facility_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_facility < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_non_electric_status) 
            && intVal($ps->ps_effic_non_electric_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_non_electric < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_non_electric, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_prod_all_status) 
            && intVal($ps->ps_effic_prod_all_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_prod_all < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_prod_all, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_production_status) 
            && intVal($ps->ps_effic_production_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_production < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_prod_non_status) 
            && intVal($ps->ps_effic_prod_non_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_prod_non < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_prod_non, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_water_status) 
            && intVal($ps->ps_effic_water_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_water < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_water, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_waste_status) 
            && intVal($ps->ps_effic_waste_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_waste < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_waste, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_hvac_status) 
            && intVal($ps->ps_effic_hvac_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_hvac < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}
            @endif
        @endif 
        </td>
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if ($ps->ps_effic_lighting < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if (isset($ps->ps_lighting_power_density) 
                && $ps->ps_lighting_power_density > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_lighting_power_density, 3) }}
            @else 
                0
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if (isset($ps->ps_lpd_flower) && $ps->ps_lpd_flower > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_flower, 3) }}
            @else 
                0
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if (isset($ps->ps_lpd_veg) && $ps->ps_lpd_veg > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_veg, 3) }}
            @else 
                0
            @endif
        @endif
        </td>
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if (isset($ps->ps_lpd_clone) && $ps->ps_lpd_clone > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_lpd_clone, 3) }}
            @else 
                0
            @endif
        @endif
        </td>
        <?php /*
        <td>
        @if ((isset($ps->ps_effic_lighting_status) 
            && intVal($ps->ps_effic_lighting_status) == $defCmplt)
            || (isset($fltPartner) && $fltPartner > 0))
            @if (isset($ps->ps_hlpd_ma) && $ps->ps_hlpd_ma > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_hlpd_ma, 3) }}
            @else 
                0
            @endif
        @endif
        </td>
        */ ?>
        <td>{{ number_format($ps->ps_grams_dry) }}</td>
        <td>{{ number_format($ps->ps_kwh_tot_calc) }}</td>
        <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
        <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
            $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', 
                $ps->ps_characterize)) 
        }}</td>
        <td>
            @if (isset($ps->ps_heat_water) 
                && intVal($ps->ps_heat_water) == 1) 
                Y 
            @else 
                N 
            @endif
        </td>
        <td>
            @if (isset($ps->ps_controls) 
                && intVal($ps->ps_controls) == 1) 
                Y 
            @else 
                N 
            @endif 
        </td>
        <td> 
            @if (isset($ps->ps_controls_auto) 
                && intVal($ps->ps_controls_auto) == 1) 
                Y 
            @else 
                N 
            @endif 
        </td>
        <td> 
            @if (isset($ps->ps_vertical_stack) 
                && intVal($ps->ps_vertical_stack) == 1) 
                Y 
            @else 
                N 
            @endif 
        </td>
        <td>
            @if (isset($ps->ps_harvests_per_year) 
                && intVal($ps->ps_harvests_per_year) > 0) 
                {{ $ps->ps_harvests_per_year }} 
            @endif
            </td>
        @foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $def)
            <?php $foundArea = false; ?>
            @if (isset($allmores[$ps->ps_id]))
                @if ($allmores[$ps->ps_id]["areas"]->isNotEmpty())
                    @foreach ($allmores[$ps->ps_id]["areas"] as $area)
                        @if (intVal($area->ps_area_type) == intVal($def->def_id))
                            <td> @if (isset($area->ps_area_has_stage) && intVal($area->ps_area_has_stage) == 1) Y @else N @endif
                                </td>
                            <td> @if (isset($area->ps_area_lgt_sun) && intVal($area->ps_area_lgt_sun) == 1) Y @else N @endif </td>
                            <td> @if (isset($area->ps_area_lgt_dep) && intVal($area->ps_area_lgt_dep) == 1) Y @else N @endif </td>
                            <td> @if (isset($area->ps_area_lgt_artif) && intVal($area->ps_area_lgt_artif) == 1) Y @else N @endif 
                                </td>
                            <td> @if (isset($area->ps_area_hvac_type) && intVal($area->ps_area_hvac_type) > 0) 
                                {{ $GLOBALS["SL"]->def->getVal('PowerScore HVAC Systems', $area->ps_area_hvac_type) }} @endif
                                </td>
                            <td> @if (isset($area->ps_area_hvac_other) && trim($area->ps_area_hvac_other) != '') 
                                {{ $area->ps_area_hvac_other }} @endif </td>
                            <td> @if (isset($area->ps_area_days_cycle)) {{ $area->ps_area_days_cycle }} @endif </td>
                            <td> @if (isset($area->ps_area_size)) {{ number_format($area->ps_area_size) }} @endif </td>
                            <td> @if (isset($area->ps_area_total_light_watts)) 
                                {{ number_format($area->ps_area_total_light_watts) }} @endif </td>
                            <td>
                            @if ($allmores[$ps->ps_id]["lights"]->isNotEmpty())
                                <?php $foundLgt = false; ?>
                                @foreach ($allmores[$ps->ps_id]["lights"] as $lgt)
                                    @if ($lgt->ps_lg_typ_area_id == $area->ps_area_id)
                                        @if ($foundLgt) , @endif
                                        {{ $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $lgt->ps_lg_typ_light) }}
                                        {{ number_format($lgt->ps_lg_typ_wattage) }}W 
                                        x{{ number_format($lgt->ps_lg_typ_count) }}
                                        <?php $foundLgt = true; ?>
                                    @endif
                                @endforeach
                            @endif
                            </td>
                            <?php $foundArea = true; ?>
                        @endif
                    @endforeach
                @endif
            @endif
            @if (!$foundArea)
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
            @endif
        @endforeach
        <td>{{ $ps->ps_county }}</td>
        <td>{{ $ps->ps_state }}</td>
        <td>{{ $ps->ps_zip_code }}</td>
        @if (Auth::user()->hasRole('administrator|staff'))
            <td>{{ $ps->ps_email }}</td>
            <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] 
                }}/calculated/u-{{ $ps->ps_id }}</td>
        @endif
        </tr>
    @endforeach

    <tr>
        <td colspan=14 >Sums</td>
        <td>{{ number_format($psSum->ps_grams_dry) }}</td>
        <td>{{ number_format($psSum->ps_kwh) }}</td>
        <td>{{ number_format($psSum->ps_flower_canopy_size) }}</td>
        <td colspan=2 >&nbsp;</td>
    </tr>

@else

    <tr><td @if (isset($showFarmNames) && $showFarmNames) colspan=14 
        @else colspan=13 @endif ><i>No PowerScores found.</i></td></tr>

@endif
