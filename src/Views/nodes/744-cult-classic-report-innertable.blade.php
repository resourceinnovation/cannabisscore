<!-- generated from resources/views/vendor/cannabisscore/nodes/744-cult-classic-report-innertable.blade.php -->
<tr><b>
<th>Farm Name</th>
<th>Points</th>
<th>Complete?</th>
<th>Score ID#</th>
<th>Overall</th>
<th>Facility Rank</th>
<th>Production Rank</th>
<th>Lighting Rank</th>
<th>HVAC Rank</th>
<th>Facility Score <div class="fPerc66 slGrey">kWh/SqFt</div></th>
<th>Production Score <div class="fPerc66 slGrey">g/kWh</div></th>
<th>Lighting Score <div class="fPerc66 slGrey">kWh/SqFt</div></th>
<th>HVAC Score <div class="fPerc66 slGrey">kWh/SqFt</div></th>
<th>Grams</th>
<th>kWh</th>
<th>Sq Ft</th>
<th>County</th>
<th>Email</th>
<th>Submitted</th>
<th>URL</th>
</b></tr>
@forelse ($farms as $i => $f)
    <tr>
    <td class=" @if ($f['ps'] && isset($f['ps']->PsStatus) && in_array($f['ps']->PsStatus, [ 
        $GLOBALS['SL']->def->getID('PowerScore Status', 'Complete'),
        $GLOBALS['SL']->def->getID('PowerScore Status', 'Archived') ])) slGreenDark @else slRedLight @endif " >
        @if (isset($f["name"]) && trim($f["name"]) != '') {{ $f["name"] }} @endif </td>
    @if (!isset($f["ps"]) || !isset($f["ps"]->PsID))
        <td class="slRedLight"><b>0</b></td>
        <td colspan=14 >No @if ($GLOBALS["SL"]->REQ->has("search") && sizeof($f["srch"]) > 0)
            <span class="slGrey fPerc80">
            @foreach ($f["srch"] as $psID => $psName)
                , <a href="/calculated/u-{{ $psID }}" target="_blank">{{ $psName }}</a>
            @endforeach </span>
        @endif </td><td colspan=4 >&nbsp;</td>
    @else
        <td @if (in_array($f["ps"]->PsStatus, [ $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived') ])) class="slGreenDark"
            @else class="slRedLight" @endif ><b>
            @if ($f["ps"]->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete')) 0
            @elseif ($f["ps"]->PsEfficOverall > 66) 2
            @elseif ($f["ps"]->PsEfficOverall > 33) 1.5
            @else 1
            @endif
        </b></td>
        <td>
            @if (in_array($f["ps"]->PsStatus, [ $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived') ])) Yes @else No @endif
        </td>
        @if ($GLOBALS["SL"]->REQ->has('excel')) <td>#{{ $f["ps"]->PsID }}</td>
        @else <td><a href="/calculated/u-{{ $f['ps']->PsID }}" target="_blank"
            @if ($f["ps"]->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete')) class="slRedLight"
            @endif >#{{ $f["ps"]->PsID }}</a></td> @endif
        @if (in_array($f["ps"]->PsStatus, [ $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived') ]))
            <td>{{ round($f["ps"]->PsEfficOverall) }}%</td>
            <td>{{ round($f["ps"]->PsRnkFacility) }}%</td>
            <td>{{ round($f["ps"]->PsRnkProduction) }}%</td>
            <td>{{ round($f["ps"]->PsRnkLighting) }}%</td>
            <td>{{ round($f["ps"]->PsRnkHVAC) }}%</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($f["ps"]->PsEfficFacility, 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($f["ps"]->PsEfficProduction, 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($f["ps"]->PsEfficLighting, 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($f["ps"]->PsEfficHvac, 3) }}</td>
            <td>{{ number_format($f["ps"]->PsGrams) }}</td>
            <td>{{ number_format($f["ps"]->PsKWH) }}</td>
            <td>{{ number_format($f["ps"]->PsTotalSize) }}</td>
        @else 
            <td colspan=12 class="slRedLight" >
                <i>Page: {!! $GLOBALS["SL"]->getNodePageName($f["ps"]->PsSubmissionProgress) !!}</i>
            </td>
        @endif
        <td>{{ $f["ps"]->PsCounty }} {{ $f["ps"]->PsState }}</td>
        <td> @if (isset($f["ps"]->PsEmail) && trim($f["ps"]->PsEmail) != '') {{ $f["ps"]->PsEmail }} @endif </td>
        <td>{{ date("n/j/Y", strtotime($f["ps"]->created_at)) }}</td>
        <td>
        @if ($GLOBALS["SL"]->REQ->has('excel')) http://cannabispowerscore.org/calculated/u-{{ $f["ps"]->PsID }}
        @else <a href="/calculated/u-{{ $f['ps']->PsID }}" target="_blank"
            >http://cannabispowerscore.org/calculated/u-{{ $f["ps"]->PsID }}</a>
        @endif
        </td>
    @endif
    </tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif