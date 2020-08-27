<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-production.blade.php -->

@if (isset($ps->ps_grams_dry) && isset($ps->ps_kwh_tot_calc))
<div class="row">
    <div class="col-12">
        <div class="slBlueDark">
        @if (isset($ps->ps_kwh_tot_calc) && $ps->ps_kwh_tot_calc > 0)
            = {{ $GLOBALS["SL"]->sigFigs(($ps->ps_grams_dry/$ps->ps_kwh_tot_calc), 3) }}
        @else 0
        @endif
            <nobr>g / kWh</nobr>
        </div>
        <div class="pT15 slGrey">
            = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)) }} 
                Total Annual Electric kBtu
            </div>
        </div>
        <div class="pT15 slGrey">
            Total Annual Electric kBtu = <a target="_blank"
            href="https://www.eia.gov/energyexplained/units-and-calculators/energy-conversion-calculators.php" 
            >3.412</a> x 
            ( {{ number_format($ps->ps_kwh_tot_calc) }} Total Kilowatt Hours )
        </div>
    </div>
</div>
@endif
