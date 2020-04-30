<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly-fuels.blade.php -->

@if ($rec->com_ma_tot_natural_gas > 0)
    <td>
    @if (isset($month->com_ma_month_natural_gas_therms)
        && $month->com_ma_month_natural_gas_therms > 0)
        {{ number_format($month->com_ma_month_natural_gas_therms) }}
    @endif
    </td>
@endif
@if ($rec->com_ma_tot_diesel > 0)
    <td>
    @if (isset($month->com_ma_month_diesel_gallons)
        && $month->com_ma_month_diesel_gallons > 0)
        {{ number_format($month->com_ma_month_diesel_gallons) }}
    @endif
    </td>
@endif
@if ($rec->com_ma_tot_propane > 0)
    <td>
    @if (isset($month->com_ma_month_propane)
        && $month->com_ma_month_propane > 0)
        {{ number_format($month->com_ma_month_propane) }}
    @endif
    </td>
@endif
@if ($rec->com_ma_tot_fuel_oil > 0)
    <td>
    @if (isset($month->com_ma_month_fuel_oil)
        && $month->com_ma_month_fuel_oil > 0)
        {{ number_format($month->com_ma_month_fuel_oil) }}
    @endif
    </td>
@endif
@if ($rec->com_ma_tot_biofuel > 0)
    <td>
    @if (isset($month->com_ma_month_biofuel_wood_tons)
        && $month->com_ma_month_biofuel_wood_tons > 0)
        {{ number_format($month->com_ma_month_biofuel_wood_tons) }}
    @endif
    </td>
@endif
