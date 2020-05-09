<!-- generated from resources/views/vendor/cannabisscore/nodes/775-powerscore-publishing.blade.php -->
<h1 class="slBlueDark">Un-Publish These PowerScores?</h1>
<p>Hi Derek!</p>
<p>Here's the list of completed PowerScores which were not included in you cleaned up Excel sheet. 
Should all of these be removed from the list of 'published' scores which others are compared to?</p>
<p>Just wanted to double-check. Thanks!-)</p>
<table border=0 class="table table-striped w100"><tr>
<th>Score ID#</th>
<th>Farm Name</th>
<th>Overall</th>
<th class="taR">Facility Score <sup class="slBlueDark">kBtu/SqFt</sup></th>
<th class="taR">Production Score <sup class="slBlueDark">g/kBtu</sup></th>
<th class="taR">Lighting Score <sup class="slBlueDark">kWh/SqFt</sup></th>
<th class="taR">HVAC Score <sup class="slBlueDark">kWh/SqFt</sup></th>
<th class="taR">Grams</th>
<th class="taR">kWh</th>
<th class="taR">Sq Ft</th>
<th>Type</th>
<th>County</th>
</tr>
@forelse ($allscores as $i => $ps)
    <tr>
    <td class="taR">
        <a href="/calculated/u-{{ $ps->ps_id }}" target="_blank">#{{ $ps->ps_id }}</a>
    </td>
    <td> @if (isset($ps->ps_name)) {{ $ps->ps_name }} @endif </td>
    <td class="taR">{{ round($ps->ps_effic_overall) }}%</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}
        @if (isset($ps->ps_rnk_facility) && $ps->ps_rnk_facility > 0) 
            <span class="slGrey">{{ round($ps->ps_rnk_facility) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}
        @if (isset($ps->ps_rnk_production) && $ps->ps_rnk_production > 0) 
            <span class="slGrey">{{ round($ps->ps_rnk_production) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}
        @if (isset($ps->ps_rnk_lighting) && $ps->ps_rnk_lighting > 0) 
            <span class="slGrey">{{ round($ps->ps_rnk_lighting) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}
        @if (isset($ps->ps_rnk_hvac) && $ps->ps_rnk_hvac > 0) 
            <span class="slGrey">{{ round($ps->ps_rnk_hvac) }}%</span> @endif </td>
    <td class="taR">{{ number_format($ps->ps_grams) }}</td>
    <td class="taR">{{ number_format($ps->ps_kwh_tot_calc) }}</td>
    <td class="taR">{{ number_format($ps->ps_flower_canopy_size) }}</td>
    <td>{{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
        $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->ps_characterize)) }}</td>
    <td>{{ $ps->ps_county }} {{ $ps->ps_state }}</td>
    </tr>
@empty
@endforelse
</table>
