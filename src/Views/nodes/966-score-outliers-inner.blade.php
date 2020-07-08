<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers-inner.blade.php -->

<table border=0 class="table w100 bgWht">
    @if ($stats[$type][$size]["Facility"]["cnt"] == 0)
        <tr><th colspan=8 ><i>None found.</i></th></tr>
    @else
        <tr class="slGrey">
            <th class="brdRgtGrey"><b>Average</b>, SD</th>
        @foreach ($outlierCols as $scr)
            <th @if ($scr == 'Lighting') class="brdLftGrey" @endif d>
            <b>{{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["avg"], 3) }}</b>, 
            {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["sd"], 3) }}
            </th>
        @endforeach
        </tr>
        <tr class="slGrey">
            <th class="brdRgtGrey">Median, IQR</th>
        @foreach ($outlierCols as $scr)
            <th @if ($scr == 'Lighting') class="brdLftGrey" @endif >
            {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["med"], 3) }}, 
            {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["iqr"], 3) }}
            </th>
        @endforeach
        </tr>
        <tr>
            <th class="brdRgtGrey">Score #, Location, Status</th>
            <th>Facility
                ({{ number_format($stats[$type][$size]["Facility"]["cnt"]) }})</th>
            <th>Production
                ({{ number_format($stats[$type][$size]["Facility"]["cnt"]) }})</th>
            <th>HVAC
                ({{ number_format($stats[$type][$size]["Hvac"]["cnt"]) }})</th>
            <th class="brdLftGrey">Lighting
                ({{ number_format($stats[$type][$size]["Lighting"]["cnt"]) }})</th>
            <th>Flow SqFt/Fix</th>
            <th>Veg SqFt/Fix</th>
        </tr>
        @foreach ($scores as $ps)
            @if ($ps->ps_characterize == $type 
                && ($size == 0 
                    || $GLOBALS["CUST"]->getSizeDefID($ps->ps_area_size) == $size))
                <tr>
                <td class="brdRgtGrey 
                    @if ($ps->ps_status == 364) slRedDark bld @endif " >
                    <a href="/calculated/read-{{ $ps->ps_id }}" target="_blank" 
                        @if ($ps->ps_status == 364) class="slRedDark" @endif 
                        >#{{ $ps->ps_id }}</a><div class="pL5 fPerc80">
                    {{ $ps->ps_county }} {{ $ps->ps_state }}<br />
                    {{ $GLOBALS["SL"]->def->getVal(
                        'PowerScore Status', 
                        $ps->ps_status
                    ) }}
                    @if (isset($ps->ps_notes) && trim($ps->ps_notes) != '')
                        <a id="hidivBtnPsComment{{ $ps->ps_id }}" 
                            class="hidivBtn" href="javascript:;"
                            ><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                        <div id="hidivPsComment{{ $ps->ps_id }}" class="disNon">
                            {{ $ps->ps_notes }}
                        </div>
                    @endif
                </div></td>
                @foreach ($outlierCols as $scr)
                    <td @if ($scr == 'Lighting') class="brdLftGrey" @endif >
                    {!! view(
                        'vendor.cannabisscore.nodes.966-score-outliers-cell', 
                        [
                            "defCmplt"         => $defCmplt,
                            "stats"            => $stats,
                            "type"             => $type,
                            "size"             => $size,
                            "scr"              => $scr,
                            "scrL"             => strtolower($scr),
                            "ps"               => $ps,
                            "showStats"        => $showStats,
                            "scoresVegSqFtFix" => $scoresVegSqFtFix
                        ]
                    )->render() !!}
                    </td>
                @endforeach
                </tr>
            @endif
        @endforeach
    @endif
</table>