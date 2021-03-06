<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores-lighting.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('excel'))
    <div class="slCard nodeWrap">
    <div class="row">
        <div class="col-8">
            <a href="/dash/compare-powerscores"
                ><h1 class="slBlueDark">Compare All Lighting</h1></a>
        </div><div class="col-4 taR"><div class="mTn10 pB10">
        @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) 
            || !$GLOBALS["SL"]->x["partnerVersion"])
            <a class="btn btn-secondary mT20" 
                href="/dash/compare-powerscores?lighting=1&srt={{ 
                $sort[0] }}&srta={{ $sort[1] }}{{ $urlFlts }}&excel=1"
                ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
                Excel</a>
        @endif
        </div></div>
    </div>
    @if (isset($psFilters))
        <div class="round20 row2 mB20 p15">{!! $psFilters !!}</div>
    @elseif (isset($psFilter))
        <div class="mB5">{!! $psFilter !!}</div>
    @endif
    </div>
@endif

@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Growth Stages') as $i => $area)
    @if (!$GLOBALS["SL"]->REQ->has('excel'))
        <div class="slCard nodeWrap">
        <h2 class="slBlueDark">{{ $area->def_value }} ({{ sizeof($allights[$area->def_id]) }})</h2>
        <table border=0 class="table w100 bgWht">
    @else <tr><td colspan=5>{{ $area->def_value }} ({{ sizeof($allights[$area->def_id]) }})</td></tr>
    @endif
    <tr>
    <th>
        {!! view(
            'vendor.survloop.reports.inc-tbl-head-sort', 
            [
                "eng"    => 'Score ID#',
                "srtVal" => 'psid',
                "sort"   => $sort
            ]
        )->render() !!}
    </th>
    <th>
        {!! view(
            'vendor.survloop.reports.inc-tbl-head-sort', 
            [
            "eng"    => 'Light Types',
            "srtVal" => 'type',
            "sort"   => $sort
            ]
        )->render() !!}
    </th>
    <th>
        {!! view(
            'vendor.survloop.reports.inc-tbl-head-sort', 
            [
                "eng"    => 'Watts / SF',
                "srtVal" => 'wsft',
                "sort"   => $sort
            ]
        )->render() !!}
    </th>
    <th>
        {!! view(
            'vendor.survloop.reports.inc-tbl-head-sort', 
            [
                "eng"    => 'Days',
                "srtVal" => 'days',
                "sort"   => $sort
            ]
        )->render() !!}
    </th>
    <th>
        {!! view(
            'vendor.survloop.reports.inc-tbl-head-sort', 
            [
                "eng"    => 'Hours',
                "srtVal" => 'hour',
                "sort"   => $sort
            ]
        )->render() !!}
    </th>
    </tr>
    <!---
    <tr>
        <th>Averages</th>
        <td>&nbsp;</td>
        <th>{{ round($psAvg->ps_effic_overall) }}%</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_facility, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_production, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_lighting, 3) }}</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_hvac, 3) }}</th>
        @if (!$isExcel) <th colspan=2 ><span class="mR10"><nbor> @else <th> @endif
            {{ number_format($psAvg->ps_grams) }} g
            @if (!$isExcel) </nobr></span><span class="mR10"><nbor> @else </th><th> @endif
            {{ number_format($psAvg->ps_kwh_tot_calc) }} kWh
            @if (!$isExcel) </nbor></span><nbor> @else </th><th> @endif
            {{ number_format($psAvg->ps_flower_canopy_size) }} sq ft
            @if (!$isExcel) </nbor> @endif
        </th>
    </tr>
    --->
    @if (isset($allights[$area->def_id]) && sizeof($allights[$area->def_id]) > 0)
        <?php $cnt = 0; ?>
        @forelse ($allights[$area->def_id] as $psid => $ps)
            <tr @if ($cnt%2 == 0) class="row2" @endif >
            <td><a href="/calculated/u-{{ $psid }}" target="_blank">#{{ $psid }}</a></td>
            <td>{{ $ps["type"] }}</td>
            <td>{{ $ps["wsft"] }}</td>
            <td>{{ $ps["days"] }}</td>
            <td>{{ $ps["hour"] }}</td>
            </tr>
            <?php $cnt++; ?>
        @empty
            <tr><td colspan=5 class="slGrey" ><i>No PowerScores found.</i></td></tr>
        @endforelse
        @if (!$GLOBALS["SL"]->REQ->has('excel'))
            </table></div>
        @endif
    @endif
@endforeach

@if (!$GLOBALS["SL"]->REQ->has('excel') && isset($reportExtras))
    <div class="slCard nodeWrap">{!! $reportExtras !!}</div>
@endif