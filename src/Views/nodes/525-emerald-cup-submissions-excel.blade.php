<!-- generated from resources/views/vendor/cannabisscore/nodes/525-emerald-cup-submissions-excel.blade.php -->
<tr>
<th>Score ID#</th>
<th>Farm Name</th>
<th>Email</th>
<th>County</th>
<th>Date Submitted</th>
<th>Completed</th>
<th>Cult Classic</th>
<th>Emerald Cup</th>
<th>Overall Rank</th>
<th>Facility Rank</th>
<th>Production Rank</th>
<th>Lighting Rank</th>
<th>HVAC Rank</th>
<th>Facility Score kWh/SqFt</th>
<th>Production Score g/kWh</th>
<th>Lighting Score kWh/SqFt</th>
<th>HVAC Score kWh/SqFt</th>
<th>Grams</th>
<th>kWh</th>
<th>Sq Ft</th>
<th>URL</th>
</tr>
@forelse ($cupScores as $i => $s)
    <tr>
    <td>#{{ $s->PsID }}</td>
    <td> @if (isset($s->PsName) && trim($s->PsName) != '') {{ $s->PsName }} @endif </td>
    <td> @if (isset($s->PsEmail) && trim($s->PsEmail) != '') {{ $s->PsEmail }} @endif </td>
    <td>{{ $s->PsCounty }} {{ $s->PsState }}</td>
    <td>{{ date("n/j/Y", strtotime($s->created_at)) }}</td>
    <td> @if ($s->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete')) Yes @else No @endif </td>
    <td> @if (in_array($s->PsID, $cultClassicIds)) Yes @endif </td>
    <td> @if (in_array($s->PsID, $emeraldIds)) Yes @endif </td>
    @if ($s->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'))
        <td>{{ round($s->PsEfficOverall) }}%</td>
        <td>{{ round($s->PsRnkFacility) }}%</td>
        <td>{{ round($s->PsRnkProduction) }}%</td>
        <td>{{ round($s->PsRnkLighting) }}%</td>
        <td>{{ round($s->PsRnkHVAC) }}%</td>
        <td>{{ $s->PsEfficFacility }}</td>
        <td>{{ $s->PsEfficProduction }}</td>
        <td>{{ $s->PsEfficLighting }}</td>
        <td>{{ $s->PsEfficHvac }}</td>
        <td>{{ number_format($s->PsGrams) }}</td>
        <td>{{ number_format($s->PsKWH) }}</td>
        <td>{{ number_format($s->PsTotalSize) }}</td>
    @else 
        <td colspan=12 >{!! $GLOBALS["SL"]->getNodePageName($s->PsSubmissionProgress) !!}</td>
    @endif
    <td>https://cannabispowerscore.org/calculated/u-{{ $s->PsID }}</td>
    </tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif