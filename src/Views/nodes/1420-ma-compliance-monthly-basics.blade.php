<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly-basics.blade.php -->

<tr @if ($month->com_ma_month_month%2 == 1) class="rw2" @endif >
    <td style="padding-right: 30px;"><nobr>
        {{ date("M", mktime(0, 0, 0, $month->com_ma_month_month, 1, 2000)) }} 
        '{{ $year }}
    </nobr></td>
    <td>{{ number_format($month->com_ma_month_kwh) }}</td>
@if ($rec->com_ma_tot_renew > 0)
    <td>
    @if (isset($month->com_ma_month_renew_kwh)
        && $month->com_ma_month_renew_kwh > 0)
        {{ number_format($month->com_ma_month_renew_kwh) }}
    @endif
    </td>
@endif
    <td>{{ number_format($month->com_ma_month_kw) }}</td>
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
            "rec"   => $rec,
            "month" => $month
        ]
    )->render() !!}
@endif
</tr>
