<!-- generated from resources/views/vendor/cannabisscore/nodes/170-avg-powerscores-innertable.blade.php -->
<tr>
    <th>Averages</th>
    <th class="taR">Count</th>
    <th class="taR">Facility Score <span class="fPerc66 slGrey">kWh/SqFt</span></th>
    <th class="taR">Production Score <span class="fPerc66 slGrey">g/kWh</span></th>
    <th class="taR">Lighting Score <span class="fPerc66 slGrey">kWh/SqFt</span></th>
    <th class="taR">HVAC Score <span class="fPerc66 slGrey">kWh/SqFt</span></th>
</tr>
<tr>
    <th class="fPerc125">All Scores</th>
    <td class="fPerc125 taR">{{ number_format($allAvgs["all"]["tot"]) }}</td>
    <td class="fPerc125 taR">{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["fac"][1]/$allAvgs["all"]["fac"][0], 3) }}</td>
    <td class="fPerc125 taR">{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["pro"][1]/$allAvgs["all"]["pro"][0], 3) }}</td>
    <td class="fPerc125 taR">{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["lgt"][1]/$allAvgs["all"]["lgt"][0], 3) }}</td>
    <td class="fPerc125 taR">{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["hvc"][1]/$allAvgs["all"]["hvc"][0], 3) }}</td>
</tr>
@foreach ($allAvgs["types"] as $defID => $avgs)
    <tr @if ($defID == 143) class="brdTop" @endif >
        <th>{{ $GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $defID) }}</th>
        <td class="taR">{{ number_format($avgs["tot"]) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
    </tr>
@endforeach
@foreach ($allAvgs["cups"] as $cupID => $avgs)
    <tr @if ($cupID == 230) class="brdTop" @endif >
        <th> @if ($cupID == 230) Cultivation Classic @else Emerald Cup @endif </th>
        <td class="taR">{{ number_format($avgs["tot"]) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
        <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
    </tr>
@endforeach
<?php $firstRow = true; ?>
@forelse ($allAvgs["states"] as $state => $avgs)
    @if ($avgs["tot"] > 4)
        <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
            <th>{{ $GLOBALS["SL"]->getState($state) }}</th>
            <td class="taR">{{ number_format($avgs["tot"]) }}</td>
            @if ($avgs["fac"][0] > 0)
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["pro"][0] > 0)
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["lgt"][0] > 0)
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["hvc"][0] > 0)
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
            @else <td> </td> @endif
        </tr>
    @endif
@empty
@endforelse
<?php $firstRow = true; ?>
@forelse ($allAvgs["zones"] as $zone => $avgs)
    @if ($avgs["tot"] > 4)
        <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
            <th>Climate Zone {{ $zone }}</th>
            <td class="taR">{{ number_format($avgs["tot"]) }}</td>
            @if ($avgs["fac"][0] > 0) 
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["pro"][0] > 0) 
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["lgt"][0] > 0) 
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
            @else <td> </td> @endif
            @if ($avgs["hvc"][0] > 0) 
                <td class="taR">{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
            @else <td> </td> @endif
        </tr>
    @endif
@empty
@endforelse