<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly.blade.php -->

<div style="padding: 30px;">&nbsp;</div>
<div class="page-break-avoid">
    <h5 style="margin-bottom: 15px;">Monthly Breakdowns</h5>

    <table class="table slSpreadTbl">
        <tr>
            <th>Month</th>
            <th>Electricity Usage (kWh)</th>
        @if ($rec->com_ma_tot_renew > 0)
            <th>Renewable Energy Generated (kWh)</th>
        @endif
            <th>Peak Electric Demand (kW)</th>
        @if ($rec->com_ma_tot_water > 0)
            <th>Water (Gallons)</th>
        @endif
    @if (!$GLOBALS["SL"]->REQ->has('print') && !$GLOBALS["SL"]->REQ->has('ajax'))
        {!! view(
            'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels-headers', 
            [ "rec" => $rec ]
        )->render() !!}
    @endif
        </tr>

@for ($i = ($rec->com_ma_start_month+1); $i <= 12; $i++)
    {!! view(
        'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-basics', 
        [
            "rec"   => $rec,
            "month" => $months[($i-1)],
            "year"  => (intVal(date("y"))-1)
        ]
    )->render() !!}
@endfor
@for ($i = 1; $i <= $rec->com_ma_start_month; $i++)
    {!! view(
        'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-basics', 
        [
            "rec"   => $rec,
            "month" => $months[($i-1)],
            "year"  => date("y")
        ]
    )->render() !!}
@endfor
    </table>
</div>

@if ($GLOBALS["SL"]->REQ->has('print') || $GLOBALS["SL"]->REQ->has('ajax'))
    @if ($rec->com_ma_tot_natural_gas > 0 
        || $rec->com_ma_tot_diesel > 0 
        || $rec->com_ma_tot_propane > 0 
        || $rec->com_ma_tot_biofuel > 0
        || $rec->com_ma_tot_biofuel_pellets > 0)
        <div style="padding: 15px;">&nbsp;</div>
        <div class="page-break-avoid">
            <table class="table slSpreadTbl">
                <tr>
                    <th>Month</th>
            {!! view(
                'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels-headers', 
                [ "rec" => $rec ]
            )->render() !!}
                </tr>
            @for ($i = ($rec->com_ma_start_month+1); $i <= 12; $i++)
                <tr @if ($months[($i-1)]->com_ma_month_month%2 == 1) class="rw2" @endif >
                    <td style="padding-right: 30px;"><nobr>
                        {{ date("M", mktime(0, 0, 0, $months[($i-1)]->com_ma_month_month, 1, 2000)) }} 
                        '{{ (intVal(date("y"))-1) }}
                    </nobr></td>
                {!! view(
                    'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels', 
                    [
                        "rec"   => $rec,
                        "month" => $months[($i-1)]
                    ]
                )->render() !!}
                </tr>
            @endfor
            @for ($i = 1; $i <= $rec->com_ma_start_month; $i++)
                <tr @if ($months[($i-1)]->com_ma_month_month%2 == 1) class="rw2" @endif >
                    <td style="padding-right: 30px;"><nobr>
                        {{ date("M", mktime(0, 0, 0, $months[($i-1)]->com_ma_month_month, 1, 2000)) }} 
                        '{{ date("y") }}
                    </nobr></td>
                {!! view(
                    'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels', 
                    [
                        "rec"   => $rec,
                        "month" => $months[($i-1)]
                    ]
                )->render() !!}
                </tr>
            @endfor
            </table>
        @if ($rec->com_ma_tot_natural_gas > 0
            && $rec->com_ma_unit_natural_gas == $GLOBALS["SL"]->def->getID('Natural Gas Units', 'CCF'))
            <p class="slGrey">1 Therm is around 100 CCF</p>
        @endif
        </div>
    @endif
@endif


<?php /*
<br /><br />Starting Month: {{ $rec->com_ma_start_month }} <pre>{!! print_r($months) !!}</pre>
*/ ?>

