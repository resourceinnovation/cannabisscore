<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores-excel.blade.php -->
<tr>
<th>Score ID#</th>
@if ($showFarmNames) <th>Farm Name</th> @endif
<th>Overall</th>
<th>Facility Score (kWh/SqFt)</th>
<th>Production Score (g/kWh)</th>
<th>Lighting Score (kWh/SqFt)</th>
<th>HVAC Score (kWh/SqFt)</th>
<th>Grams</th>
<th>kWh</th>
<th>Sq Ft</th>
<th>Type</th>







<th>County</th>
<th>Zip</th>
<th>Email</th>
<th>Url</th>
</tr>
<tr>
    <th>Averages</th>
    @if ($showFarmNames) <th> </th> @endif
    <th>{{ round($psAvg->PsEfficOverall) }}%</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficFacility, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficProduction, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficLighting, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficHvac, 3) }}</th>
    <th>{{ number_format($psAvg->PsGrams) }}</th>
    <th>{{ number_format($psAvg->PsKWH) }}</th>
    <th>{{ number_format($psAvg->PsTotalSize) }}</th>
    
    
    
    
    
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
@forelse ($allscores as $i => $ps)
    <tr>
    <td>#{{ $ps->PsID }}</td>
    @if ($showFarmNames) <td> @if (isset($ps->PsName)) {{ $ps->PsName }} @endif </td> @endif
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
    
    
    
    
    
    
    
    <td>{{ $ps->PsCounty }} {{ $ps->PsState }}</td>
    <td>{{ $ps->PsZipCode }}</td>
    <td>{{ $ps->PsEmail }}</td>
    <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] }}/calculated/u-{{ $ps->PsID }}</td>
    </tr>
@empty
    <tr><td @if ($showFarmNames) colspan=14 @else colspan=13 @endif ><i>No PowerScores found.</i></td></tr>
@endforelse