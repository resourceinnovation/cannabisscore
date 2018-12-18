<!-- generated from resources/views/vendor/cannabisscore/inc-score-avgs-report-table.blade.php -->
<table border=0 class="table table-striped w100">
@forelse ($tbl->rows as $i => $row)
    <tr @if (in_array($i, $tbl->lineRows)) class="brdBot" @endif >
    @forelse ($row as $j => $cell)
        @if ($i == 0 || $j == 0) <th @else <td @endif @if (in_array($j, $tbl->lineCols)) class="brdRgt" @endif >
        {!! $cell !!}
        @if ($i == 0 || $j == 0) </th> @else </td> @endif
    @empty
    @endforelse
    </tr>
@empty
@endforelse
</table>

<?php /*
<tr>
    <th>&nbsp;</th>
    <th class="brdRgt">Average <sub class="slGrey">{{ $statVrt[0]["ovr"][0] }}</sub></th>
    <th>Average Without Vertical <sub class="slGrey">{{ $statVrt[0]["tot"] }}</sub></th>
    <th>Average With Vertical <sub class="slGrey">{{ $statVrt[1]["tot"] }}</sub></th>
</tr>
<tr class="brdTop">
    <th>Facility Score <span class="mL5 slBlueDark">(kWh/SqFt)</span></th>
    <td class="brdRgt">{{ 
        $GLOBALS["SL"]->sigFigs(($statVrt[0]["fac"][0]+$statVrt[1]["fac"][0])/$statVrt[0]["ovr"][0], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[0]["fac"][0]/$statVrt[0]["tot"], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[1]["fac"][0]/$statVrt[1]["tot"], 3) }}</td>
</tr>
<tr>
    <th>Production Score <span class="mL5 slBlueDark">(g/kWh)</span></th>
    <td class="brdRgt">{{ 
        $GLOBALS["SL"]->sigFigs(($statVrt[0]["pro"][0]+$statVrt[1]["pro"][0])/$statVrt[0]["ovr"][0], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[0]["pro"][0]/$statVrt[0]["tot"], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[1]["pro"][0]/$statVrt[1]["tot"], 3) }}</td>
</tr>
<tr>
    <th>HVAC Score <span class="mL5 slBlueDark">(kWh/SqFt)</span></th>
    <td class="brdRgt">{{ 
        $GLOBALS["SL"]->sigFigs(($statVrt[0]["hvc"][0]+$statVrt[1]["hvc"][0])/$statVrt[0]["ovr"][0], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[0]["hvc"][0]/$statVrt[0]["tot"], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[1]["hvc"][0]/$statVrt[1]["tot"], 3) }}</td>
</tr>
<tr class="brdTop">
    <th><b>Lighting Score</b> <span class="mL5 slBlueDark">(W/SqFt)</span></th>
    <td class="brdRgt">{{ 
        $GLOBALS["SL"]->sigFigs(($statVrt[0]["lgt"][0]+$statVrt[1]["lgt"][0])/$statVrt[0]["ovr"][0], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[0]["lgt"][0]/$statVrt[0]["tot"], 3) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($statVrt[1]["lgt"][0]/$statVrt[1]["tot"], 3) }}</td>
</tr>
@foreach ($areaTypesFilt as $area => $id)
    @if (($statVrt[0]["lgtAr"][$id][0]+$statVrt[1]["lgtAr"][$id][0]) > 0)
        <tr @if ($area == 'Flower') class="brdTop" @endif >
            <th><span class="pL20">{{ $area }} Lighting W/SqFt</span></th>
            <td class="brdRgt">{{ $GLOBALS["SL"]->sigFigs(($statVrt[0]["lgtAr"][$id][1]+$statVrt[1]["lgtAr"][$id][1])
                /($statVrt[0]["lgtAr"][$id][0]+$statVrt[1]["lgtAr"][$id][0]), 3) }}
                <sub class="slGrey">{{ ($statVrt[0]["lgtAr"][$id][0]+$statVrt[1]["lgtAr"][$id][0]) }}</sub></td>
            <td> @if ($statVrt[0]["lgtAr"][$id][1] > 0)
                {{ $GLOBALS["SL"]->sigFigs($statVrt[0]["lgtAr"][$id][1]/$statVrt[0]["lgtAr"][$id][0], 3) }}
                <sub class="slGrey">{{ $statVrt[0]["lgtAr"][$id][0] }}</sub>
            @else <span class="slGrey">0</span> @endif </td>
            <td> @if ($statVrt[1]["lgtAr"][$id][1] > 0)
                {{ $GLOBALS["SL"]->sigFigs($statVrt[1]["lgtAr"][$id][1]/$statVrt[1]["lgtAr"][$id][0], 3) }}
                <sub class="slGrey">{{ $statVrt[1]["lgtAr"][$id][0] }}</sub>
            @else <span class="slGrey">0</span> @endif </td>
        </tr>
    @endif
@endforeach
*/ ?>