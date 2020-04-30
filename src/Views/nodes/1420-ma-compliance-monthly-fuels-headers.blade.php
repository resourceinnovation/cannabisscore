<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly-fuels-headers.blade.php -->

@if ($rec->com_ma_tot_natural_gas > 0)
    <th>Natural Gas (Therms)</th>
@endif
@if ($rec->com_ma_tot_diesel > 0)
    <th>Diesel Generator (Gallons)</th>
@endif
@if ($rec->com_ma_tot_propane > 0)
    <th>Propane (Gallons)</th>
@endif
@if ($rec->com_ma_tot_fuel_oil > 0)
    <th>Fuel Oil (Gallons)</th>
@endif
@if ($rec->com_ma_tot_biofuel > 0)
    <th>Wood (Tons)</th>
@endif
