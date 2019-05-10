<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers.blade.php -->
<div class="slCard nodeWrap">
<div class="row">
    <div class="col-8">
        <a href="/dash/powerscore-outliers"><h2 class="slBlueDark">PowerScore Outliers</h2></a>
        <p>
            Below each sub-score (Facility, Production, Lighting, and HVAC) are that sub-score's 
            Standard Deviations (SD) from the group's average, then Interquartile Range (IQR) from the median.
            These are flagged as <span class="slRedDark">red</span> when at least 2 SDs from mean, 
            lower than (Quartile 1 ​− 1.5*IQR), or higher than (Quartile 3 + 1.5*IQR).
        </p>
    </div><div class="col-4 taR"><div class="mTn10 pB10">
        <select name="status" id="statusID" class="form-control w100 filterOutliers">
            <option value="all" 
            @if (!$GLOBALS["SL"]->REQ->has('status') || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') SELECTED @endif
            >All Complete & Archived</option>
            <option value="complete" 
            @if ($GLOBALS["SL"]->REQ->has('status') && trim($GLOBALS["SL"]->REQ->get('status')) == 'complete') SELECTED @endif
            >Ranked Dataset Only</option>
        </select>
        <select name="sizes" id="sizesID" class="form-control w100 filterOutliers">
            <option value="yes" 
            @if (!$GLOBALS["SL"]->REQ->has('sizes') || trim($GLOBALS["SL"]->REQ->get('sizes')) == 'yes') SELECTED @endif
            >Break Down By Flowing Sizes</option>
            <option value="no" 
            @if ($GLOBALS["SL"]->REQ->has('sizes') && trim($GLOBALS["SL"]->REQ->get('sizes')) == 'no') SELECTED @endif
            >Skip Size Break Down</option>
        </select>
    </div></div>
</div>

<table border=0 class="table w100 bgWht">
@foreach ([144, 145, 143] as $type) <?php /* Indoor, Hybrid, Outdoor */ ?>
    <tr><th colspan=8 ><h2>{{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $type) }}</h2></th></tr>
    @foreach ($sizes as $size) <?php /* <5,000 sf, 5,000-10,000 sf, 10,000-50,000 sf, 50,000+ sf */ ?>
        <tr><th colspan=8 ><h4 class="slBlueDark">
            {{ number_format($stats[$type][$size]["Facility"]["cnt"]) }}
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $type) }}
            {{ $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $size) }}</h4></th></tr>
<?php /* <tr><td colspan=8 ><pre>{!! print_r($stats[$type][$size]) !!}</pre></td></tr> */ ?>
        @if ($stats[$type][$size]["Facility"]["cnt"] == 0)
            <tr><th colspan=8 ><i>None found.</i></th></tr>
        @else
            <tr class="slGrey"><th><b>Average</b>, SD</th>
                @foreach (['Facility', 'Production', 'Lighting', 'Hvac'] as $scr)
                    <th>
                        <b>{{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["avg"], 3) }}</b>, 
                        {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["sd"], 3) }}
                    </th>
                @endforeach
            </tr>
            <tr class="slGrey"><th>Median, IQR</th>
                @foreach (['Facility', 'Production', 'Lighting', 'Hvac'] as $scr)
                    <th>
                        {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["med"], 3) }}, 
                        {{ $GLOBALS["SL"]->sigFigs($stats[$type][$size][$scr]["iqr"], 3) }}
                    </th>
                @endforeach
            </tr>
            <tr><th>Score #, Location, Status</th><th>Facility</th><th>Production</th><th>Lighting</th><th>HVAC</th></tr>
            @foreach ($scores as $ps)
                @if ($ps->PsCharacterize == $type && ($size == 0 || $GLOBALS["CUST"]->getSizeDefID($ps->PsAreaSize) == $size))
                    <tr>
                    <td @if ($ps->PsStatus == 364) class="slRedDark" bld @endif >
                        <a href="/calculated/read-{{ $ps->PsID }}" target="_blank" 
                            @if ($ps->PsStatus == 364) class="slRedDark" @endif 
                            >#{{ $ps->PsID }}</a><div class="pL5 fPerc80">
                        {{ $ps->PsCounty }} {{ $ps->PsState }}<br />
                        {{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->PsStatus) }}
                    </div></td>
                    @foreach (['Facility', 'Production', 'Lighting', 'Hvac'] as $scr)
                        <td>
                        @if (isset($ps->{ 'PsEffic' . $scr }) && $ps->{ 'PsEffic' . $scr } > 0)
                            {{ $GLOBALS["SL"]->sigFigs($ps->{ 'PsEffic' . $scr }, 3) }}
                            <div class="pL5 fPerc80">
                            @if ($stats[$type][$size][$scr]["sd"] > 0)
                                @if ((($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]) <= -2)
                                    <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]),
                                    3) }}</b><br />
                                @elseif ((($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]) >= 2)
                                    <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]),
                                    3) }}</b><br />
                                @elseif ($ps->{ 'PsEffic' . $scr } < $stats[$type][$size][$scr]["avg"])
                                    <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]),
                                    3) }}</span><br />
                                @elseif ($ps->{ 'PsEffic' . $scr } >= $stats[$type][$size][$scr]["avg"])
                                    <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["avg"])/$stats[$type][$size][$scr]["sd"]),
                                    3) }}</span><br />
                                @else <br />
                                @endif
                            @endif
                            
                            @if ($stats[$type][$size][$scr]["iqr"] > 0)
                                @if ($ps->{ 'PsEffic' . $scr } < $stats[$type][$size][$scr]["q1"])
                                    <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["med"])/$stats[$type][$size][$scr]["iqr"]),
                                    3) }}</b><br />
                                @elseif ($ps->{ 'PsEffic' . $scr } > $stats[$type][$size][$scr]["q3"])
                                    <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["med"])/$stats[$type][$size][$scr]["iqr"]),
                                    3) }}</b><br />
                                @elseif ($ps->{ 'PsEffic' . $scr } < $stats[$type][$size][$scr]["med"])
                                    <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["med"])/$stats[$type][$size][$scr]["iqr"]),
                                    3) }}</span><br />
                                @elseif ($ps->{ 'PsEffic' . $scr } >= $stats[$type][$size][$scr]["med"])
                                    <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
                                    (($ps->{ 'PsEffic' . $scr }-$stats[$type][$size][$scr]["med"])/$stats[$type][$size][$scr]["iqr"]),
                                    3) }}</span><br />
                                @else <br />
                                @endif
                            @endif
                            </div>
                        @endif
                        </td>
                    @endforeach
                    </tr>
                @endif
            @endforeach
        @endif
    @endforeach
@endforeach
</table>

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
$(document).on("change", "select.filterOutliers", function() { filterOutliers(); });
}); </script>
