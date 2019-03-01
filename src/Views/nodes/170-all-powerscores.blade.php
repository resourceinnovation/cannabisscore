<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores.blade.php -->
<div class="slCard nodeWrap">
<div class="row">
    <div class="col-8">
        <a href="/dash/compare-powerscores"><h1 class="slBlueDark"> 
        @if ($nID == 808) NWPCC Data Import @else Compare All PowerScores @endif </h1></a>
    </div><div class="col-4 taR"><div class="mTn10 pB10">
    @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) || !$GLOBALS["SL"]->x["partnerVersion"])
        @if (!$GLOBALS["SL"]->REQ->has('review'))
            <a class="btn btn-secondary mT20 mR5" href="/dash/compare-powerscores?review=1">Under Review</a>
        @else <a class="btn btn-secondary mT20 mR5" href="/dash/compare-powerscores">All Complete</a> @endif
        <a class="btn btn-secondary mT20 mR5" href="/dash/compare-powerscores?random=1" target="_blank">Get Random</a>
        <a class="btn btn-secondary mT20" href="/dash/compare-powerscores?srt={{ $sort[0] }}&srta={{ $sort[1] }}{{ 
            $urlFlts }}&excel=1"><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
    @endif
    </div></div>
</div>
@if (isset($psFilters))
    @if (!$GLOBALS["SL"]->REQ->has('review')) <div class="round20 row2 mB20 p15">{!! $psFilters !!}</div>
    @else <div></div> @endif
    <div class="mTn15"><b>{{ $allscores->count() }} Found</b></div>
@elseif (isset($psFilter))
    <div class="mB5"><b class="mR20">{{ $allscores->count() }} Found</b> {!! $psFilter !!}</div>
@endif
</div>

<div class="slCard nodeWrap">
<table border=0 class="table w100 bgWht">
<tr>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Score ID#',
        "srtVal" => 'PsID',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Type',
        "srtVal" => 'PsCharacterize',
        "sort"   => $sort
        ])->render() !!}
    @if (isset($fltCmpl) && $fltCmpl == 0 && $isAdmin)
        <br />{!! view('vendor.survloop.inc-tbl-head-sort', [
            "eng"    => 'Status',
            "srtVal" => 'PsStatus',
            "sort"   => $sort
            ])->render() !!}
    @endif
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Overall',
        "srtVal" => 'PsEfficOverall',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Facility Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
        "srtVal" => 'PsEfficFacility',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Production Score <div class="fPerc66 slGrey">g/kWh</div>',
        "srtVal" => 'PsEfficProduction',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Lighting Score <div class="fPerc66 slGrey">W/SqFt</div>',
        "srtVal" => 'PsEfficLighting',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'HVAC Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
        "srtVal" => 'PsEfficHvac',
        "sort"   => $sort
        ])->render() !!}
</th>
<th>
    @if (!$isExcel) <span class="fPerc80"><span class="mR10"> @endif
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Grams',
        "srtVal" => 'PsGrams',
        "sort"   => $sort
        ])->render() !!}
    @if (!$isExcel) </span><span class="mR10"> @else </th><th> @endif
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'kWh',
        "srtVal" => 'PsKWH',
        "sort"   => $sort
        ])->render() !!}
    @if (!$isExcel) </span> @else </th><th> @endif
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Sq Ft',
        "srtVal" => 'PsTotalSize',
        "sort"   => $sort
        ])->render() !!}
    @if (!$isExcel) </span> @endif
</th>
<th>
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'County State',
        "srtVal" => 'PsCounty',
        "sort"   => $sort
        ])->render() !!}
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Zip',
        "srtVal" => 'PsZipCode',
        "sort"   => $sort
        ])->render() !!}
    @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) || !$GLOBALS["SL"]->x["partnerVersion"])
        <br />{!! view('vendor.survloop.inc-tbl-head-sort', [
            "eng"    => 'Email',
            "srtVal" => 'PsEmail',
            "sort"   => $sort
            ])->render() !!}
    @endif
</th>
</tr>
<tr>
    <th>Averages</th>
    <td>&nbsp;</td>
    <th>{{ round($psAvg->PsEfficOverall) }}%</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficFacility, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficProduction, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficLighting, 3) }}</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficHvac, 3) }}</th>
    @if (!$isExcel) <th colspan=2 ><span class="mR10"><nbor> @else <th> @endif
        {{ number_format($psAvg->PsGrams) }} g
        @if (!$isExcel) </nobr></span><span class="mR10"><nbor> @else </th><th> @endif
        {{ number_format($psAvg->PsKWH) }} kWh
        @if (!$isExcel) </nbor></span><nbor> @else </th><th> @endif
        {{ number_format($psAvg->PsTotalSize) }} sq ft
        @if (!$isExcel) </nbor> @endif
    </th>
</tr>

@if ($allscores && $allscores->isNotEmpty())
    @foreach ($allscores as $i => $ps)
        <tr @if ($i%2 == 0) class="row2" @endif >
        <td><a href="/calculated/u-{{ $ps->PsID }}" target="_blank">
            @if ($nID == 808) {{ $ps->PsName }} @else #{{ $ps->PsID }} @endif</a>
        @if (!$isExcel && in_array($ps->PsID, $cultClassicIds))
            <div class="mTn5 mBn5 slGrey fPerc66"><i class="fa fa-certificate" aria-hidden="true"></i> CC</div>
        @endif
        @if (!$isExcel && in_array($ps->PsID, $emeraldIds))
            <div class="mTn5 mBn5 slGrey fPerc66"><i class="fa fa-certificate" aria-hidden="true"></i> EC</div>
        @endif
        </td>
        <td>
            {{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
                $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->PsCharacterize)) }}
            @if (isset($fltCmpl) && $fltCmpl == 0 && $isAdmin)
                <br /> @if ($ps->PsStatus == 243) <span class="slBlueDark">Complete</span>
                @elseif ($ps->PsStatus == 364) <span class="slRedDark">Archived</span>
                @else {{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->PsStatus) }}
                @endif
            @endif
        </td>
        <td>{{ round($ps->PsEfficOverall) }}%
            @if (!$isExcel && isset($allranks) && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkOverallAvg)) <div class="slGrey fPerc66">{{ 
                $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkOverallAvg) }}%</div> @endif
        </td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}
            @if (!$isExcel && isset($allranks) && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkFacility)) <div class="slGrey fPerc66">{{ 
                $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkFacility) }}%</div> @endif
        </td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}
            @if (!$isExcel && isset($allranks) && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkProduction)) <div class="slGrey fPerc66">{{ 
                $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkProduction) }}%</div> @endif
        </td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}
            @if (!$isExcel && isset($allranks) && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkHVAC)) <div class="slGrey fPerc66">{{ 
                $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkHVAC) }}%</div> @endif
        </td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}
            @if (!$isExcel && isset($allranks) && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkLighting)) <div class="slGrey fPerc66">{{ 
                $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkLighting) }}%</div> @endif
        </td>
        @if (!$isExcel)
            <td class="fPerc66">
                @if ($ps->PsGrams > 0)     {{ number_format($ps->PsGrams) }} g<br /> @endif
                @if ($ps->PsKWH > 0)       {{ number_format($ps->PsKWH) }} kWh<br /> @endif
                @if ($ps->PsTotalSize > 0) {{ number_format($ps->PsTotalSize) }} sq ft @endif
        @else
            <td>{{ $ps->PsGrams }}</td>
            <td>{{ $ps->PsKWH }}</td>
            <td>{{ $ps->PsTotalSize }}
        @endif </td>
        <td>{{ $ps->PsCounty }} {{ $ps->PsState }} {{ $ps->PsZipCode }}
        @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) || !$GLOBALS["SL"]->x["partnerVersion"])
            <a id="hidivBtn{{ $ps->PsID }}Ema" class="hidivBtnSelf disBlo mTn5 fPerc66 slGrey" href="javascript:;">@</a>
            <div id="hidiv{{ $ps->PsID }}Ema" class="disNon mTn5"><a href="mailto:{{ $ps->PsEmail }}" class="slGrey"
                >{{ $ps->PsEmail }}</a></div></td>
        @endif
        </tr>
        @if ($GLOBALS["SL"]->REQ->has('review'))
            <tr class="brdTopNon @if ($i%2 == 0) row2 @endif " >
                <td class="taR"><div class="mTn10 slGrey fPerc66">Review Notes:</div></td>
                <td colspan=9 ><div class="mTn15"><i>{!! $ps->PsNotes !!}</i></div></td>
            </tr>
        @endif
    @endforeach
@else
    <tr><td colspan=11 class="slGrey"><i>No PowerScores found.</i></td></tr>
@endif
</table>
</div>

@if (isset($reportExtras)) <div class="slCard nodeWrap">{!! $reportExtras !!}</div> @endif