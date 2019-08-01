<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores-excel.blade.php -->
<tr>
<th>Score ID#</th>
@if (isset($fltCmpl) && $fltCmpl != 243) <th>Status</th> @endif
@if (isset($showFarmNames) && $showFarmNames) <th>Farm Name</th> @endif
<th>Overall</th>
<th>Facility Score (kWh/SqFt)</th>
<th>Production Score (g/kWh)</th>
<th>Lighting Score (W/SqFt)</th>
<th>HVAC Score (kWh/SqFt)</th>
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
    <th>Has {{ $def->DefValue }}?</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Sunlight</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Light Dep</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Artificial Light</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} HVAC Type</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} HVAC Other</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Days In Cycle</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Canopy Sqft</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Total Light Watts</th>
    <th>{{ str_replace(' Plants', '', $def->DefValue) }} Light Counts</th>
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
    <th>{{ round($psAvg->PsEfficOverall) }}%</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficFacility, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficProduction, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficLighting, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficHvac, 3) }}</th>
    <th>{{ number_format($psAvg->PsGrams) }}</th>
    <th>{{ number_format($psAvg->PsKWH) }}</th>
    <th>{{ number_format($psAvg->PsTotalSize) }}</th>
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
    <td>#{{ $ps->PsID }}</td>
    @if (isset($fltCmpl) && $fltCmpl != 243)
        <td>{{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->PsStatus) }}</td>
    @endif
    @if (isset($showFarmNames) && $showFarmNames) <td> @if (isset($ps->PsName)) {{ $ps->PsName }} @endif </td> @endif
    <td>{{ round($ps->PsEfficOverall) }}%</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}</td>
    <td>{{ number_format($ps->PsGrams) }}</td>
    <td>{{ number_format($ps->PsKWH) }}</td>
    <td>{{ number_format($ps->PsTotalSize) }}</td>
    <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
        $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->PsCharacterize)) }}</td>
    <td> @if (isset($ps->PsHeatWater) && intVal($ps->PsHeatWater) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->PsControls) && intVal($ps->PsControls) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->PsControlsAuto) && intVal($ps->PsControlsAuto) == 1) Y @else N @endif </td>
    <td> @if (isset($ps->PsVerticalStack) && intVal($ps->PsVerticalStack) == 1) Y @else N @endif 
        </td>
    <td> @if (isset($ps->PsHavestsPerYear) && intVal($ps->PsHavestsPerYear) > 0) {{ $ps->PsHavestsPerYear }} @endif
        </td>
    @foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $def)
        <?php $foundArea = false; ?>
        @if (isset($allmores[$ps->PsID]))
            @if ($allmores[$ps->PsID]["areas"]->isNotEmpty())
                @foreach ($allmores[$ps->PsID]["areas"] as $area)
                    @if (intVal($area->PsAreaType) == intVal($def->DefID))
                        <td> @if (isset($area->PsAreaHasStage) && intVal($area->PsAreaHasStage) == 1) Y @else N @endif
                            </td>
                        <td> @if (isset($area->PsAreaLgtSun) && intVal($area->PsAreaLgtSun) == 1) Y @else N @endif </td>
                        <td> @if (isset($area->PsAreaLgtDep) && intVal($area->PsAreaLgtDep) == 1) Y @else N @endif </td>
                        <td> @if (isset($area->PsAreaLgtArtif) && intVal($area->PsAreaLgtArtif) == 1) Y @else N @endif 
                            </td>
                        <td> @if (isset($area->PsAreaHvacType) && intVal($area->PsAreaHvacType) > 0) 
                            {{ $GLOBALS["SL"]->def->getVal('PowerScore HVAC Systems', $area->PsAreaHvacType) }} @endif
                            </td>
                        <td> @if (isset($area->PsAreaHvacOther) && trim($area->PsAreaHvacOther) != '') 
                            {{ $area->PsAreaHvacOther }} @endif </td>
                        <td> @if (isset($area->PsAreaDaysCycle)) {{ $area->PsAreaDaysCycle }} @endif </td>
                        <td> @if (isset($area->PsAreaSize)) {{ number_format($area->PsAreaSize) }} @endif </td>
                        <td> @if (isset($area->PsAreaTotalLightWatts)) 
                            {{ number_format($area->PsAreaTotalLightWatts) }} @endif </td>
                        <td>
                        @if ($allmores[$ps->PsID]["lights"]->isNotEmpty())
                            <?php $foundLgt = false; ?>
                            @foreach ($allmores[$ps->PsID]["lights"] as $lgt)
                                @if ($lgt->PsLgTypAreaID == $area->PsAreaID)
                                    @if ($foundLgt) , @endif
                                    {{ $GLOBALS["SL"]->def->getVal('PowerScore Light Types', $lgt->PsLgTypLight) }}
                                    {{ number_format($lgt->PsLgTypWattage) }}W x{{ number_format($lgt->PsLgTypCount) }}
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
    <td>{{ $ps->PsCounty }}</td>
    <td>{{ $ps->PsState }}</td>
    <td>{{ $ps->PsZipCode }}</td>
    <td>{{ $ps->PsEmail }}</td>
    <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] }}/calculated/u-{{ $ps->PsID }}</td>
    </tr>
@empty
    <tr><td @if (isset($showFarmNames) && $showFarmNames) colspan=14 @else colspan=13 @endif ><i>No PowerScores found.</i></td></tr>
@endforelse