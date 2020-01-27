<!-- generated from resources/views/vendor/cannabisscore/nodes/170-avg-powerscores-innertable.blade.php -->
{!! view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable-headers', [ ])->render() !!}
<tr>
    <th class="fPerc125">All Scores</th>
    <td class="fPerc125 brdRgt"> @if ($allAvgs["all"]["ovr"][0] > 0)
        {{ round($allAvgs["all"]["ovr"][1]/$allAvgs["all"]["ovr"][0]) }}%
    @endif </td>
    <td class="fPerc125"> @if ($allAvgs["all"]["fac"][0] > 0)
        {{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["fac"][1]/$allAvgs["all"]["fac"][0], 3) }}
    @endif </td>
    <td class="fPerc125"> @if ($allAvgs["all"]["pro"][0] > 0)
        {{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["pro"][1]/$allAvgs["all"]["pro"][0], 3) }}
    @endif </td>
    <td class="fPerc125"> @if ($allAvgs["all"]["lgt"][0] > 0)
        {{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["lgt"][1]/$allAvgs["all"]["lgt"][0], 3) }}
    @endif </td>
    <td class="fPerc125"> @if ($allAvgs["all"]["hvc"][0] > 0)
        {{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["hvc"][1]/$allAvgs["all"]["hvc"][0], 3) }}
    @endif </td>
    <td class="fPerc125 slGrey brdLft">{{ number_format($allAvgs["all"]["tot"]) }}</td>
</tr>
<tr><td colspan=7 > </td></tr>
<?php $firstRow = true; ?>
@foreach ($allAvgs["types"] as $defID => $avgs)
    <tr @if ($defID == 144) class="brdTop" @endif >
        <th>{{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $defID) }}</th>
        <td class="brdRgt"> @if ($avgs["ovr"][0] > 0)
            {{ round($avgs["ovr"][1]/$avgs["ovr"][0]) }}%
        @endif </td>
        <td> @if ($avgs["fac"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }} 
        @endif </td>
        <td> @if ($avgs["pro"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }} 
        @endif </td>
        <td> @if ($avgs["lgt"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }} 
        @endif </td>
        <td> @if ($avgs["hvc"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }} 
        @endif </td>
        <td class="slGrey brdLft">{{ number_format($avgs["tot"]) }}</td>
    </tr>
@endforeach
<tr><td colspan=7 > </td></tr>
<?php $firstRow = true; ?>
@forelse ($allAvgs["states"] as $state => $avgs)
    @if ($avgs["tot"] > 1)
        <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
            <th>{{ $GLOBALS["SL"]->getState($state) }}</th>
            <td class="brdRgt">
                @if ($avgs["ovr"][0] > 0)
                    {{ round($avgs["ovr"][1]/$avgs["ovr"][0]) }}%
                @endif
            </td>
            @if ($avgs["fac"][0] > 0.000001) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
            @else <td>0</td> 
            @endif
            @if ($avgs["pro"][0] > 0.000001) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
            @else <td>0</td> 
            @endif
            @if ($avgs["lgt"][0] > 0.000001) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
            @else <td>0</td> 
            @endif
            @if ($avgs["hvc"][0] > 0.000001) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
            @else <td>0</td> 
            @endif
            <td class="slGrey brdLft">{{ number_format($avgs["tot"]) }}</td>
        </tr>
    @endif
@empty
@endforelse
<tr><td colspan=7 > </td></tr>
<?php $firstRow = true; ?>
@forelse ($allAvgs["zones"] as $zone => $avgs)
    @if ($avgs["tot"] > 1)
        <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
            <th><nobr>Climate Zone {{ $zone }}</nobr></th>
            @if ($avgs["ovr"][0] > 0) 
                <td class="brdRgt">{{ round($avgs["ovr"][1]/$avgs["ovr"][0]) }}%</td>
            @else <td class="brdRgt"> </td> 
            @endif
            @if ($avgs["fac"][0] > 0) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}</td>
            @else <td> </td> 
            @endif
            @if ($avgs["pro"][0] > 0) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}</td>
            @else <td> </td> 
            @endif
            @if ($avgs["lgt"][0] > 0) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }}</td>
            @else <td> </td> 
            @endif
            @if ($avgs["hvc"][0] > 0) 
                <td>{{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }}</td>
            @else <td> </td> 
            @endif
            <td class="slGrey brdLft">{{ number_format($avgs["tot"]) }}</td>
        </tr>
    @endif
@empty
@endforelse
<tr><td colspan=7 > </td></tr>
@foreach ($allAvgs["cups"] as $cupID => $avgs)
    <tr @if ($cupID == 230) class="brdTop" @endif >
        <th> @if ($cupID == 230) Cultivation Classic @else Emerald Cup @endif </th>
        <td class="brdRgt"> @if ($avgs["ovr"][0] > 0) 
            {{ round($avgs["ovr"][1]/$avgs["ovr"][0]) }}%
        @endif </td>
        <td> @if ($avgs["fac"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["fac"][1]/$avgs["fac"][0], 3) }}
        @endif </td>
        <td> @if ($avgs["pro"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["pro"][1]/$avgs["pro"][0], 3) }}
        @endif </td>
        <td> @if ($avgs["lgt"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["lgt"][1]/$avgs["lgt"][0], 3) }} 
        @endif </td>
        <td> @if ($avgs["hvc"][0] > 0) 
            {{ $GLOBALS["SL"]->sigFigs($avgs["hvc"][1]/$avgs["hvc"][0], 3) }} 
        @endif </td>
        <td class="slGrey brdLft">{{ number_format($avgs["tot"]) }}</td>
    </tr>
@endforeach


@foreach (['PowerScore Light Types', 'PowerScore HVAC Systems'] as $set)
    <tr><td colspan=7 > </td></tr>
    {!! view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable-headers')->render() !!}
    @foreach ([ 162 => 'Flower', 163 => 'Veg', 160 => 'Clone'] as $area => $areaName)
        @if (in_array($area, [163, 160])) <tr><td colspan=7 > </td></tr> @endif
        @foreach ($GLOBALS["SL"]->def->getSet($set) as $def)
            <tr>
                <th> @if ($set == 'PowerScore HVAC Systems') <a href="javascript:;" id="hidivBtnH{{ $def->def_id 
                    }}" class="hidivBtn"> {{ $areaName }} HVAC {!! str_replace(' - ', 
                    '</a> <div id="hidivH' . $def->def_id . '" class="disNon slGrey">', $def->def_value) !!}</div> 
                    @else {{ $areaName }} {{ str_replace('double-ended ', '2x', str_replace('single-ended ', '1x', $def->def_value)) }} @endif
                    </th>
                <td class="brdRgt"> @if ($allAvgs["avgs"][$area . '-' . $def->def_id]["ovr"][0] > 0) {{ 
                    round($allAvgs["avgs"][$area . '-' . $def->def_id]["ovr"][1]/$allAvgs["avgs"][$area . '-' 
                        . $def->def_id]["ovr"][0]) }}% @else <span class="slGrey">0%</span> @endif </td>
                <td> @if ($allAvgs["avgs"][$area . '-' . $def->def_id]["fac"][0] > 0) {{ 
                    $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$area . '-' . $def->def_id]["fac"][1]/$allAvgs["avgs"][$area 
                        . '-' . $def->def_id]["fac"][0], 3) }} @else <span class="slGrey">0</span> @endif </td>
                <td> @if ($allAvgs["avgs"][$area . '-' . $def->def_id]["pro"][0] > 0) {{ 
                    $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$area . '-' . $def->def_id]["pro"][1]/$allAvgs["avgs"][$area 
                        . '-' . $def->def_id]["pro"][0], 3) }} @else <span class="slGrey">0</span> @endif </td>
                <td> @if ($allAvgs["avgs"][$area . '-' . $def->def_id]["lgt"][0] > 0) {{ 
                    $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$area . '-' . $def->def_id]["lgt"][1]/$allAvgs["avgs"][$area 
                        . '-' . $def->def_id]["lgt"][0], 3) }} @else <span class="slGrey">0</span> @endif </td>
                <td> @if ($allAvgs["avgs"][$area . '-' . $def->def_id]["hvc"][0] > 0) {{ 
                    $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$area . '-' . $def->def_id]["hvc"][1]/$allAvgs["avgs"][$area 
                        . '-' . $def->def_id]["hvc"][0], 3) }} @else <span class="slGrey">0</span> @endif </td>
                <td class="slGrey brdLft">{{ number_format($allAvgs["avgs"][$area . '-' . $def->def_id]["ovr"][0]) 
                    }}</td>
            </tr>
        @endforeach
    @endforeach
@endforeach

<tr><td colspan=7 > </td></tr>
{!! view('vendor.cannabisscore.nodes.170-avg-powerscores-innertable-headers', [ ])->render() !!}

<?php $firstRow = true; ?>
@foreach ($GLOBALS["CUST"]->psTechs() as $fld => $name)
    <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
        <th>{{ $name }}</th>
        <td class="brdRgt"> @if ($allAvgs["avgs"][$fld]["ovr"][0] > 0) {{ 
            round($allAvgs["avgs"][$fld]["ovr"][1]/$allAvgs["avgs"][$fld]["ovr"][0]) 
            }}% @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$fld]["fac"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$fld]["fac"][1]/$allAvgs["avgs"][$fld]["fac"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$fld]["pro"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$fld]["pro"][1]/$allAvgs["avgs"][$fld]["pro"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$fld]["lgt"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$fld]["lgt"][1]/$allAvgs["avgs"][$fld]["lgt"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$fld]["hvc"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$fld]["hvc"][1]/$allAvgs["avgs"][$fld]["hvc"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td class="slGrey brdLft">{{ number_format($allAvgs["avgs"][$fld]["ovr"][0]) }}</td>
    </tr>
@endforeach

<tr id="startSources"><td colspan=7 > </td></tr>
<?php $firstRow = true; ?>
@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $def)
    <tr @if ($firstRow) class="brdTop" @endif ><?php $firstRow = false; ?>
        <th><nobr>{{ $def->def_value }}</nobr></th>
        <td class="brdRgt"> @if ($allAvgs["avgs"][$def->def_id]["ovr"][0] > 0) {{ 
            round($allAvgs["avgs"][$def->def_id]["ovr"][1]
                /$allAvgs["avgs"][$def->def_id]["ovr"][0]) 
            }}% @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$def->def_id]["fac"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$def->def_id]["fac"][1]
                /$allAvgs["avgs"][$def->def_id]["fac"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$def->def_id]["pro"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$def->def_id]["pro"][1]
                /$allAvgs["avgs"][$def->def_id]["pro"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$def->def_id]["lgt"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$def->def_id]["lgt"][1]
                /$allAvgs["avgs"][$def->def_id]["lgt"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td> @if ($allAvgs["avgs"][$def->def_id]["hvc"][0] > 0) {{ 
            $GLOBALS["SL"]->sigFigs($allAvgs["avgs"][$def->def_id]["hvc"][1]
                /$allAvgs["avgs"][$def->def_id]["hvc"][0], 3) 
            }} @else <span class="slGrey">0</span> @endif </td>
        <td class="slGrey brdLft">{{ number_format($allAvgs["avgs"][$def->def_id]["ovr"][0]) }}</td>
    </tr>
@endforeach
