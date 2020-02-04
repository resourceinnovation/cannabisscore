<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers.blade.php -->
<div class="slCard nodeWrap">
<div class="row">
    <div class="col-8">
        <a href="/dash/powerscore-outliers"><h2 class="slBlueDark">
            PowerScore Outliers
        </h2></a>
        <p>
            Below each sub-score (Facility, Production, Lighting, and HVAC) 
            are that sub-score's Standard Deviations (SD) from the group's 
            average, then Interquartile Range (IQR) from the median.
            These are flagged as <span class="slRedDark">red</span> when at 
            least 2 SDs from mean, lower than (Quartile 1 ​− 1.5*IQR), 
            or higher than (Quartile 3 + 1.5*IQR).
        </p><p>
            In addition to the efficiency sub-scores, the final columns show the
            reported flowering canopy square feet per lighting fixture and
            vegitative canopy square feet per lighting fixture.
            These are currently set to flag <span class="slRedDark">red</span> 
            when each light covers less than 2 sq ft, or greater than 64 sq ft.
        </p>
    </div><div class="col-4 taR"><div class="mT10 pB10">
        <select name="status" id="statusID" 
            class="form-control w100 filterOutliers">
            <option value="all" 
                @if (!$GLOBALS["SL"]->REQ->has('status') 
                    || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') 
                    SELECTED 
                @endif
                >All Complete & Archived</option>
            <option value="complete" 
                @if ($GLOBALS["SL"]->REQ->has('status') 
                    && trim($GLOBALS["SL"]->REQ->get('status')) == 'complete') 
                    SELECTED 
                @endif
                >Ranked Dataset Only</option>
        </select>
        <select name="sizes" id="sizesID" 
            class="form-control w100 filterOutliers">
            <option value="yes" 
                @if (!$GLOBALS["SL"]->REQ->has('sizes') 
                    || trim($GLOBALS["SL"]->REQ->get('sizes')) == 'yes') 
                    SELECTED
                @endif
                >Break Down By Flowing Sizes</option>
            <option value="no" 
            @if ($GLOBALS["SL"]->REQ->has('sizes') 
                && trim($GLOBALS["SL"]->REQ->get('sizes')) == 'no') 
                SELECTED 
            @endif
            >Skip Size Break Down</option>
        </select>
    </div></div>
</div>

<form action="?saveArchives=1&refresh=1{{ 
    (($GLOBALS['SL']->REQ->has('status')) ? '&status=' . $GLOBALS['SL']->REQ->get('status') : '')
    . (($GLOBALS['SL']->REQ->has('sizes')) ? '&sizes=' . $GLOBALS['SL']->REQ->get('sizes') : '')
    }}" method="post">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">

<table border=0 class="table w100 bgWht">
@foreach ($farmTypesOrd as $type) <?php /* Indoor, Hybrid, Outdoor */ ?>
    <tr><th colspan=8 >
        <h2>{{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $type) }}</h2>
    </th></tr>
    @foreach ($sizes as $size) 
    <?php /* <5,000 sf, 5,000-10,000 sf, 10,000-50,000 sf, 50,000+ sf */ ?>
    <?php /* <tr><td colspan=8 ><pre>{!! print_r($stats[$type][$size]) !!}</pre></td></tr> */ ?>
        <tr><th colspan=8 ><h4 class="slBlueDark">
            {{ number_format($stats[$type][$size]["Facility"]["cnt"]) }}
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $type) }}
            {{ $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $size) }}
        </h4></th></tr>
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
                @if ($ps->ps_characterize == $type && ($size == 0 
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
    @endforeach
@endforeach
</table>
<center><input type="submit" value="Save All Archive Changes" 
    class="btn btn-xl btn-primary"></center>
</form>

</div>

<script type="text/javascript"> $(document).ready(function(){
    function filterOutliers() {
        var url = '';
        if (document.getElementById('statusID')) {
            url += '&status='+document.getElementById('statusID').value;
        }
        if (document.getElementById('sizesID')) {
            url += '&sizes='+document.getElementById('sizesID').value;
        }
        window.location='?'+url.substring(1);
        return true;
    }
    $(document).on("change", "select.filterOutliers", function() {
        filterOutliers();
    });
}); </script>
