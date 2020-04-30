<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly.blade.php -->

<div class="pT30 mT30 mB15">
    <h4>Monthly Breakdowns</h4>
</div>

<table class="table slSpreadTbl">
    <tr>
        <th>Month</th>
        <th>Electricity Usage (kWh)</th>
        <th>Peak Electric Demand (kW)</th>
    @if ($rec->com_ma_tot_renew > 0)
        <th>Renewable Energy Generated (kWh)</th>
    @endif
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

@foreach ($months as $m => $month)
    <tr @if ($m%2 == 1) class="rw2" @endif >
        <td>{{ date("M", mktime(0, 0, 0, 
            $month->com_ma_month_month, 1, 2000)) }}</td>
        <td>{{ number_format($month->com_ma_month_kwh) }}</td>
        <td>{{ number_format($month->com_ma_month_kw) }}</td>
    @if ($rec->com_ma_tot_renew > 0)
        <td>
        @if (isset($month->com_ma_month_renew_kwh)
            && $month->com_ma_month_renew_kwh > 0)
            {{ number_format($month->com_ma_month_renew_kwh) }}
        @endif
        </td>
    @endif
    @if ($rec->com_ma_tot_water > 0)
        <td>
        @if (isset($month->com_ma_month_water)
            && $month->com_ma_month_water > 0)
            {{ number_format($month->com_ma_month_water) }}
        @endif
        </td>
    @endif

    @if (!$GLOBALS["SL"]->REQ->has('print') && !$GLOBALS["SL"]->REQ->has('ajax'))
        {!! view(
            'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels', 
            [
                "rec" => $rec,
                "month" => $month
            ]
        )->render() !!}
    @endif
    </tr>
@endforeach

</table>

@if ($GLOBALS["SL"]->REQ->has('print') || $GLOBALS["SL"]->REQ->has('ajax'))
    @if ($rec->com_ma_tot_natural_gas > 0 
        || $rec->com_ma_tot_diesel > 0 
        || $rec->com_ma_tot_propane > 0 
        || $rec->com_ma_tot_biofuel > 0
        || $rec->com_ma_tot_biofuel_pellets > 0)
        <div class="mT30 mB30"></div>
        <table class="table slSpreadTbl">
            <tr>
                <th>Month</th>
            {!! view(
                'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels-headers', 
                [ "rec" => $rec ]
            )->render() !!}
            </tr>
        @foreach ($months as $m => $month)
            <tr @if ($m%2 == 1) class="rw2" @endif >
                <td>{{ date("M", mktime(0, 0, 0, 
                    $month->com_ma_month_month, 1, 2000)) }}</td>
                {!! view(
                    'vendor.cannabisscore.nodes.1420-ma-compliance-monthly-fuels', 
                    [
                        "rec"   => $rec,
                        "month" => $month
                    ]
                )->render() !!}
            </tr>
        @endforeach
        </table>
    @endif
@endif
