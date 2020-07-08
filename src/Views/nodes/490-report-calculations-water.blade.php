<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-water.blade.php -->

@if (isset($ps->ps_tot_water) && isset($totFlwrSqFt) && $totFlwrSqFt > 0)
    <div class="pL10 pT15 slGrey">
        = {{ number_format($ps->ps_tot_water) }} 
            Total Annual Gallons &nbsp;&nbsp;/&nbsp;&nbsp;
            {{ number_format($totFlwrSqFt) }} 
            Square Feet of Flowering Canopy<br />
    </div>
@endif

<div class="pL10 mT15 fPerc80">
    <i class="slGrey">(Water score is not yet being factored into each Overal PowerScore.)</i>
</div>