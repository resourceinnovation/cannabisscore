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
    <td>#{{ $s->ps_id }}</td>
    <td> @if (isset($s->ps_name) && trim($s->ps_name) != '') {{ $s->ps_name }} @endif </td>
    <td> @if (isset($s->ps_email) && trim($s->ps_email) != '') {{ $s->ps_email }} @endif </td>
    <td>{{ $s->ps_county }} {{ $s->ps_state }}</td>
    <td>{{ date("n/j/Y", strtotime($s->created_at)) }}</td>
    <td> @if ($s->ps_status == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete')) Yes 
        @else No @endif </td>
    <td> @if (in_array($s->ps_id, $cultClassicIds)) Yes @endif </td>
    <td> @if (in_array($s->ps_id, $emeraldIds)) Yes @endif </td>
    @if ($s->ps_status == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'))
        <td>{{ round($s->ps_effic_overall) }}%</td>
        <td>{{ round($s->ps_rnk_facility) }}%</td>
        <td>{{ round($s->ps_rnk_production) }}%</td>
        <td>{{ round($s->ps_rnk_lighting) }}%</td>
        <td>{{ round($s->ps_rnk_hvac) }}%</td>
        <td>{{ $s->ps_effic_facility }}</td>
        <td>{{ $s->ps_effic_production }}</td>
        <td>{{ $s->ps_effic_lighting }}</td>
        <td>{{ $s->ps_effic_hvac }}</td>
        <td>{{ number_format($s->ps_grams) }}</td>
        <td>{{ number_format($s->ps_kwh) }}</td>
        <td>{{ number_format($s->ps_total_size) }}</td>
    @else 
        <td colspan=12 >{!! $GLOBALS["SL"]->getNodePageName($s->ps_submission_progress) !!}</td>
    @endif
    <td>http://cannabispowerscore.org/calculated/u-{{ $s->ps_id }}</td>
    </tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif