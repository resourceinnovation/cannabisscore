<!-- generated from resources/views/vendor/cannabisscore/nodes/1276-all-my-powerscores-excel.blade.php -->
<tr>
<th>Score ID#</th>
@if (isset($showFarmNames) && $showFarmNames) <th>Farm Name</th> @endif
<th>Overall</th>
<th>Facility Score (kBtu / sq ft)</th>
<th>Facility Score (kWh / sq ft)</th>
<th>Production Score (g / kBtu)</th>
<th>Production Score (g / kWh)</th>
<th>Lighting Score (kWh / day)</th>
<th>HVAC Score (kBtu / sq ft)</th>
<th>Grams</th>
<th>kBtu</th>
<th>kWh</th>
<th>Sq Ft</th>
<th>Type</th>
<th>Vertical Stacking</th>
<th>Harvests Per Year</th>
<?php /*
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
*/ ?>
<th>Url</th>
</tr>
@forelse ($allscores as $i => $ps)
    @if (in_array($ps->ps_id, $allPublicFiltIDs))
        <tr>
        <td>#{{ 
            $ps->ps_id
            . ((isset($ps->ps_is_flow) && intVal($ps->ps_is_flow) == 1) ? 'F' 
                : ((!isset($ps->ps_is_pro) || intVal($ps->ps_is_pro) != 1) ? 'G' 
                    : 'P'))
        }}</td>
        @if (isset($showFarmNames) && $showFarmNames) <td> @if (isset($ps->ps_name)) {{ $ps->ps_name }} @endif </td> @endif
        <td>{{ round($ps->ps_effic_overall) }}%</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility/3.412, 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production*3.412, 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}</td>
        <td>{{ number_format($ps->ps_grams) }}</td>
        <td>{{ number_format($ps->ps_kwh_tot_calc*3.412) }}</td>
        <td>{{ number_format($ps->ps_kwh_tot_calc) }}</td>
        <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
        <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
            $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->ps_characterize)) }}</td>
        <td>
            @if (isset($ps->ps_vertical_stack) && intVal($ps->ps_vertical_stack) == 1) Y 
            @else N
            @endif 
        </td>
        <td>
            @if (isset($ps->ps_harvests_per_year) && intVal($ps->ps_harvests_per_year) > 0) 
                {{ $ps->ps_harvests_per_year }} 
            @endif
        </td>
    <?php /*
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
    */ ?>
        <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] }}/calculated/u-{{ $ps->ps_id }}</td>
        </tr>
    @endif
@empty
    <tr><td @if (isset($showFarmNames) && $showFarmNames) colspan=14 @else colspan=13 @endif ><i>No PowerScores found.</i></td></tr>
@endforelse