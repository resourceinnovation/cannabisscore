<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-facility.blade.php -->

@if (isset($ps->ps_kwh_tot_calc) && isset($totFlwrSqFt) && $totFlwrSqFt > 0)
<div class="row">
    <div class="col-12">
        <div class="slBlueDark">
            = {{ 
            $GLOBALS["SL"]->sigFigs($GLOBALS["SL"]->cnvrtKbtu2Kwh($ps->ps_effic_facility), 3)
            }} <nobr>kWh / sq ft</nobr>
        </div>
        <div class="pT15 slGrey">
            = {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)) }} 
                Total Annual Electric kBtu 
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($totFlwrSqFt) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>
        <div class="pL10 pT15 slGrey">
            Total Annual Electric kBtu = 
            <a target="_blank"
            href="https://www.eia.gov/energyexplained/units-and-calculators/energy-conversion-calculators.php" 
            >3.412</a> x 
            ( {{ number_format($ps->ps_kwh_tot_calc) }} Total Electric Kilowatt Hours )
        </div>
    </div>
</div>
@endif
