<!-- generated from resources/views/vendor/cannabisscore/nodes/170-powerscore-listings-header.blade.php -->
<tr>
    <th><div id="fixHead{{ $fixed }}1">
    <?php /* @if (isset($GLOBALS["SL"]->x["partnerVersion"]) 
        && $GLOBALS["SL"]->x["partnerVersion"])
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Client Name',
            "srtVal" => 'PsOwnClientUser',
            "sort"   => $sort
        ])->render() !!}<br />
    @endif */ ?>
    @if ($nID != 1373 && $GLOBALS["SL"]->x["partnerLevel"] >= 4)
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Score ID#',
            "srtVal" => 'ps_id',
            "sort"   => $sort
        ])->render() !!}
    @else
        Score
    @endif
    </div></th>
    <th><div id="fixHead{{ $fixed }}2">
        State, Type
    <th><div id="fixHead{{ $fixed }}3">
        Months
@if (isset($fltCmpl) && $fltCmpl == 0 
    && Auth::user()->hasRole('administrator|staff'))
    </th>
    <th>
    {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
        "eng"    => 'Status',
        "srtVal" => 'ps_status',
        "sort"   => $sort
    ])->render() !!}
@endif

    </div></th>
    <th><div id="fixHead{{ $fixed }}4">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Overall<br />Ranking',
            "srtVal" => 'ps_effic_over_similar',
            "sort"   => $sort
        ])->render() !!}
    </div></th>

@if (!isset($dataSet) || in_array($dataSet, ['', 'kpi']))

    <th><div id="fixHead{{ $fixed }}5">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Facility <br />KPI '
                . '<div class="fPerc66 slGrey">kBtu / sq ft</div>',
            "srtVal" => 'ps_effic_fac_all',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}6">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Electric <br />Facility KPI '
                . '<div class="fPerc66 slGrey">kBtu / sq ft</div>',
            "srtVal" => 'ps_effic_facility',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}7">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Non-Electric <br />Facility KPI '
                . '<div class="fPerc66 slGrey">kBtu / sq ft</div>',
            "srtVal" => 'ps_effic_fac_non',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}8">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Production <br />KPI '
                . '<div class="fPerc66 slGrey">g / kBtu</div>',
            "srtVal" => 'ps_effic_prod_all',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}9">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Electric <br />Production KPI '
                . '<div class="fPerc66 slGrey">g / kBtu</div>',
            "srtVal" => 'ps_effic_production',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}10">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Non-Electric <br />Production KPI '
                . '<div class="fPerc66 slGrey">g / kBtu</div>',
            "srtVal" => 'ps_effic_prod_non',
            "sort"   => $sort
        ])->render() !!}
    </div></th>

@elseif ($dataSet == 'lighting')

    <th><div id="fixHead{{ $fixed }}5">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Lighting <br />KPI <div class="fPerc66 slGrey">kWh / day</div>',
            "srtVal" => 'ps_effic_lighting',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}6">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Weighted LPD <br />(all grow areas) '
                . '<div class="fPerc66 slGrey">W / sq ft</div>',
            "srtVal" => 'ps_lighting_power_density',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}7">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'LPD Flower <div class="fPerc66 slGrey">W / sq ft</div>',
            "srtVal" => 'ps_lpd_flower',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}8">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'LPD Veg <div class="fPerc66 slGrey">W / sq ft</div>',
            "srtVal" => 'ps_lpd_veg',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}9">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'LPD Clone <br />& Mother <div class="fPerc66 slGrey">W / sq ft</div>',
            "srtVal" => 'ps_lpd_clone',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}10">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'HLPD<sub>MA</sub> <div class="fPerc66 slGrey">W / sq ft</div>',
            "srtVal" => 'ps_hlpd_ma',
            "sort"   => $sort
        ])->render() !!}
    </div></th>

@elseif ($dataSet == 'others')

    <th><div id="fixHead{{ $fixed }}5">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'HVAC <br />KPI <div class="fPerc66 slGrey">kBtu / sq ft</div>',
            "srtVal" => 'ps_effic_hvac',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}6">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Water <br />KPI <div class="fPerc66 slGrey">gallons / sq ft</div>',
            "srtVal" => 'ps_effic_water',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}7">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Waste <br />KPI <div class="fPerc66 slGrey">lbs / sq ft</div>',
            "srtVal" => 'ps_effic_waste',
            "sort"   => $sort
        ])->render() !!}
    </div></th>

@elseif ($dataSet == 'totals')

    <th><div id="fixHead{{ $fixed }}5">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Flower Canopy <div class="fPerc66 slGrey">sq ft</div>',
            "srtVal" => 'ps_flower_canopy_size',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}6">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Total Canopy <div class="fPerc66 slGrey">sq ft</div>',
            "srtVal" => 'ps_total_canopy_size',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}7">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Dry Flower <div class="fPerc66 slGrey">grams</div>',
            "srtVal" => 'ps_grams_dry',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    <th><div id="fixHead{{ $fixed }}8">
        {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
            "eng"    => 'Electric Usage <div class="fPerc66 slGrey">kWh</div>',
            "srtVal" => 'ps_kwh_tot_calc',
            "sort"   => $sort
        ])->render() !!}
    </div></th>
    @if (isset($psSum->ps_tot_kw_peak) && intVal($psSum->ps_tot_kw_peak) > 0)
        <th><div id="fixHead{{ $fixed }}9">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Peak kW',
                "srtVal" => 'ps_tot_kw_peak',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_water) && intVal($psSum->ps_tot_water) > 0)
        <th><div id="fixHead{{ $fixed }}10">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Water Usage <div class="fPerc66 slGrey">gallons</div>',
                "srtVal" => 'ps_tot_water',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_waste) && intVal($psSum->ps_tot_waste) > 0)
        <th><div id="fixHead{{ $fixed }}11">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Total Waste <div class="fPerc66 slGrey">lbs</div>',
                "srtVal" => 'ps_tot_waste',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_natural_gas) && intVal($psSum->ps_tot_natural_gas) > 0)
        <th><div id="fixHead{{ $fixed }}12">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Natural Gas <div class="fPerc66 slGrey">therms</div>',
                "srtVal" => 'ps_tot_natural_gas',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_generator) && intVal($psSum->ps_tot_generator) > 0)
        <th><div id="fixHead{{ $fixed }}13">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Back Up Generator <div class="fPerc66 slGrey">gallons</div>',
                "srtVal" => 'ps_tot_generator',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_biofuel_wood) && intVal($psSum->ps_tot_biofuel_wood) > 0)
        <th><div id="fixHead{{ $fixed }}14">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Biofuel Wood <div class="fPerc66 slGrey">tons</div>',
                "srtVal" => 'ps_tot_biofuel_wood',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_propane) && intVal($psSum->ps_tot_propane) > 0)
        <th><div id="fixHead{{ $fixed }}15">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Propane <div class="fPerc66 slGrey">gallons</div>',
                "srtVal" => 'ps_tot_propane',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif
    @if (isset($psSum->ps_tot_fuel_oil) && intVal($psSum->ps_tot_fuel_oil) > 0)
        <th><div id="fixHead{{ $fixed }}16">
            {!! view('vendor.survloop.reports.inc-tbl-head-sort', [
                "eng"    => 'Fuel Oil <div class="fPerc66 slGrey">gallons</div>',
                "srtVal" => 'ps_tot_fuel_oil',
                "sort"   => $sort
            ])->render() !!}
        </div></th>
    @endif

@endif

</tr>
