<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores-excel.blade.php -->
<tr>
<th>Score ID#</th>
@if (isset($fltCmpl) && $fltCmpl != 243) <th>Status</th> @endif
@if (isset($showFarmNames) && $showFarmNames) <th>Farm Name</th> @endif
<th>Overall</th>
<th>Facility Score (kBtu / sq ft)</th>
<th>Production Score (g / kBtu)</th>
<th>Lighting Score (kWh / day)</th>
<th>HVAC Score (kBtu / sq ft)</th>
<th>Grams</th>
<th>kWh</th>
<th>Sq Ft</th>
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
<th>Email</th>
<th>Url</th>
</tr>

<tr>
    <th>Averages</th>
    @if (isset($fltCmpl) && $fltCmpl != 243) <th> </th> @endif
    @if (isset($showFarmNames) && $showFarmNames) <th> </th> @endif
    <th>{{ round($psAvg->ps_effic_overall) }}%</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_facility, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_production, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_lighting, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_hvac, 3) }}</th>
    <th>{{ number_format($psAvg->ps_grams) }}</th>
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

@forelse ($allscores as $i => $ps)
    <tr>
    <td>#{{ $ps->ps_id }}</td>
    @if (isset($fltCmpl) && $fltCmpl != 243)
        <td>{{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->ps_status) }}</td>
    @endif
    @if (isset($showFarmNames) && $showFarmNames) <td> @if (isset($ps->ps_name)) {{ $ps->ps_name }} @endif </td> @endif
    <td>{{ round($ps->ps_effic_overall) }}%</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}</td>
    <td>{{ number_format($ps->ps_grams) }}</td>
    <td>{{ number_format($ps->ps_kwh) }}</td>
    <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
    <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
        $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->ps_characterize)) }}</td>
    <td> @if (isset($ps->ps_heat_water) && intVal($ps->ps_heat_water) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->ps_controls) && intVal($ps->ps_controls) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->ps_controls_auto) && intVal($ps->ps_controls_auto) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->ps_vertical_stack) && intVal($ps->ps_vertical_stack) == 1) Y @else N @endif 
        </td>
    <td> @if (isset($ps->ps_harvests_per_year) && intVal($ps->ps_harvests_per_year) > 0) {{ $ps->ps_harvests_per_year }} @endif
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
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        @endif
    @endforeach
    <td>{{ $ps->ps_county }}</td>
    <td>{{ $ps->ps_state }}</td>
    <td>{{ $ps->ps_zip_code }}</td>
    <td>{{ $ps->ps_email }}</td>
    <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] }}/calculated/u-{{ $ps->ps_id }}</td>
    </tr>
@empty
    <tr><td @if (isset($showFarmNames) && $showFarmNames) colspan=14 @else colspan=13 @endif ><i>No PowerScores found.</i></td></tr>
@endforelse