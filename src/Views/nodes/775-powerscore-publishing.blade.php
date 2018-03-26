<!-- generated from resources/views/vendor/cannabisscore/nodes/775-powerscore-publishing.blade.php -->
<h1 class="mT0 slBlueDark">Un-Publish These PowerScores?</h1>
<p>Hi Derek!</p>
<p>Here's the list of completed PowerScores which were not included in you cleaned up Excel sheet. 
Should all of these be removed from the list of 'published' scores which others are compared to? 
The scores with ID's below 1241 are a very safe bet to remove from this set, but are the all rest really outliers?</p>
<p>Just wanted to double-check. Thanks!-)</p>
<table border=0 class="table table-striped w100"><tr>
<th>Score ID#</th>
<th>Farm Name</th>
<th>Overall</th>
<th class="taR">Facility Score <div class="slGrey fPerc66">kWh/SqFt</div></th>
<th class="taR">Production Score <div class="slGrey fPerc66">g/kWh</div></th>
<th class="taR">Lighting Score <div class="slGrey fPerc66">kWh/SqFt</div></th>
<th class="taR">HVAC Score <div class="slGrey fPerc66">kWh/SqFt</div></th>
<th class="taR">Grams</th>
<th class="taR">kWh</th>
<th class="taR">Sq Ft</th>
<th>Type</th>
<th>County</th>
</tr>
@forelse ($allscores as $i => $ps)
    <tr>
    <td class="taR"><a href="/calculated/u-{{ $ps->PsID }}" target="_blank">#{{ $ps->PsID }}</a></td>
    <td> @if (isset($ps->PsName)) {{ $ps->PsName }} @endif </td>
    <td class="taR">{{ round($ps->PsEfficOverall) }}%</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}
        @if (isset($ps->PsRnkFacility) && $ps->PsRnkFacility > 0) 
            <span class="slGrey">{{ round($ps->PsRnkFacility) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}
        @if (isset($ps->PsRnkProduction) && $ps->PsRnkProduction > 0) 
            <span class="slGrey">{{ round($ps->PsRnkProduction) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}
        @if (isset($ps->PsRnkLighting) && $ps->PsRnkLighting > 0) 
            <span class="slGrey">{{ round($ps->PsRnkLighting) }}%</span> @endif </td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}
        @if (isset($ps->PsRnkHVAC) && $ps->PsRnkHVAC > 0) 
            <span class="slGrey">{{ round($ps->PsRnkHVAC) }}%</span> @endif </td>
    <td class="taR">{{ number_format($ps->PsGrams) }}</td>
    <td class="taR">{{ number_format($ps->PsKWH) }}</td>
    <td class="taR">{{ number_format($ps->PsTotalSize) }}</td>
    <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
        $GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $ps->PsCharacterize)) }}</td>
    <td>{{ $ps->PsCounty }} {{ $ps->PsState }}</td>
    </tr>
@empty
@endforelse
</table>
