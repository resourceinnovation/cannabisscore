<!-- generated from resources/views/vendor/cannabisscore/nodes/170-powerscore-listings-header.blade.php -->
<tr>
    <th>
    <?php /* @if (isset($GLOBALS["SL"]->x["partnerVersion"]) 
        && $GLOBALS["SL"]->x["partnerVersion"])
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Client Name',
            "srtVal" => 'PsOwnClientUser',
            "sort"   => $sort
        ])->render() !!}<br />
    @endif */ ?>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Score <br />ID#',
            "srtVal" => 'ps_id',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Overall',
            "srtVal" => 'ps_effic_overall',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Facility <br />Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
            "srtVal" => 'ps_effic_facility',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Production <br />Score <div class="fPerc66 slGrey">g/kWh</div>',
            "srtVal" => 'ps_effic_production',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Lighting <br />Score <div class="fPerc66 slGrey">W/SqFt</div>',
            "srtVal" => 'ps_effic_lighting',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'HVAC <br />Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
            "srtVal" => 'ps_effic_hvac',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Grams',
            "srtVal" => 'ps_grams',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'kWh',
            "srtVal" => 'ps_kwh',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Sq Ft',
            "srtVal" => 'ps_total_size',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Type',
            "srtVal" => 'ps_characterize',
            "sort"   => $sort
        ])->render() !!}
    @if (isset($fltCmpl) && $fltCmpl == 0 
        && Auth::user()->hasRole('administrator|staff'))
        <br />{!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Status',
            "srtVal" => 'ps_status',
            "sort"   => $sort
        ])->render() !!}
    @endif
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'County State',
            "srtVal" => 'ps_county',
            "sort"   => $sort
        ])->render() !!}
    @if (Auth::user()->hasRole('administrator|staff'))
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Zip',
            "srtVal" => 'ps_zip_code',
            "sort"   => $sort
        ])->render() !!}
    @endif
    </th>
    <?php /* <th>
        Year
    </th>
    */ ?>
</tr>
