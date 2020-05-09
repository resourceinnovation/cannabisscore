<!-- resources/views/vendor/cannabisscore/nodes/1420-ma-compliance-monthly-fuels-headers.blade.php -->

@if ($rec->com_ma_tot_natural_gas > 0)
    <th>
        Natural Gas 
    @if (isset($rec->com_ma_unit_generator)
        && $GLOBALS["SL"]->def->getVal(
            'Natural Gas Units', 
            $rec->com_ma_unit_natural_gas
            ) == 'CCF')
        (CCF)
    @else
        (Therms)
    @endif
    </th>
@endif
@if ($rec->com_ma_tot_diesel > 0)
    <th>
    @if (isset($rec->com_ma_unit_generator)
        && $GLOBALS["SL"]->def->getVal(
            'Compliance MA Generator Units', 
            $rec->com_ma_unit_generator
            ) == 'Gasoline (Gallons)')
        Gasoline
    @else
        Diesel 
    @endif
        Generator (Gallons)
    </th>
@endif
@if ($rec->com_ma_tot_propane > 0)
    <th>Propane (Gallons)</th>
@endif
@if ($rec->com_ma_tot_fuel_oil > 0)
    <th>Fuel Oil (Gallons)</th>
@endif
@if ($rec->com_ma_tot_biofuel > 0)
    <th>
        Wood 
    @if (isset($rec->com_ma_unit_wood)
        && $GLOBALS["SL"]->def->getVal(
            'Biofuel Wood Units', 
            $rec->com_ma_unit_wood
            ) == 'Cords')
        (Cords)
    @else
        (Tons)
    @endif
    </th>
@endif
