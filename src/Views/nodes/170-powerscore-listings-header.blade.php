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
            "srtVal" => 'PsID',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Overall',
            "srtVal" => 'PsEfficOverall',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Facility <br />Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
            "srtVal" => 'PsEfficFacility',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Production <br />Score <div class="fPerc66 slGrey">g/kWh</div>',
            "srtVal" => 'PsEfficProduction',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Lighting <br />Score <div class="fPerc66 slGrey">W/SqFt</div>',
            "srtVal" => 'PsEfficLighting',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'HVAC <br />Score <div class="fPerc66 slGrey">kWh/SqFt</div>',
            "srtVal" => 'PsEfficHvac',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Grams',
            "srtVal" => 'PsGrams',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'kWh',
            "srtVal" => 'PsKWH',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Sq Ft',
            "srtVal" => 'PsTotalSize',
            "sort"   => $sort
        ])->render() !!}
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Type',
            "srtVal" => 'PsCharacterize',
            "sort"   => $sort
        ])->render() !!}
    @if (isset($fltCmpl) && $fltCmpl == 0 
        && Auth::user()->hasRole('administrator|staff'))
        <br />{!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Status',
            "srtVal" => 'PsStatus',
            "sort"   => $sort
        ])->render() !!}
    @endif
    </th>
    <th>
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'County State',
            "srtVal" => 'PsCounty',
            "sort"   => $sort
        ])->render() !!}
    @if (Auth::user()->hasRole('administrator|staff'))
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Zip',
            "srtVal" => 'PsZipCode',
            "sort"   => $sort
        ])->render() !!}
    @endif
    </th>
    <?php /* <th>
        Year
    </th>
    */ ?>
</tr>
