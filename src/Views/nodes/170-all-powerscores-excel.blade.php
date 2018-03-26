<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores-excel.blade.php -->
<tr>
<th>Score ID#</th>
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
<th>Url</th>
</tr>
@forelse ($allscores as $i => $ps)
    <tr>
    <td>#{{ $ps->PsID }}</td>
    <td>{{ round($ps->PsEfficOverall) }}%</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}</td>
    <td>{{ number_format($ps->PsGrams) }}</td>
    <td>{{ number_format($ps->PsKWH) }}</td>
    <td>{{ number_format($ps->PsTotalSize) }}</td>
    <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
        $GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $ps->PsCharacterize)) }}</td>
    <td>{{ $ps->PsCounty }} {{ $ps->PsState }}</td>
    <td>{{ $GLOBALS["SL"]->sysOpts["app-url"] }}/calculated/u-{{ $ps->PsID }}</td>
    </tr>
@empty
    <tr><td colspan=12 ><i>No PowerScores found.</i></td></tr>
@endforelse