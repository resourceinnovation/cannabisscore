<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-water-prod.blade.php -->

@if (isset($ps->ps_tot_water) && isset($ps->ps_grams_dry))
<div class="row">
    <div class="col-12">
        <div class="pL10 pT15 slGrey">
            = {{ number_format($ps->ps_grams_dry) }} 
            Annual Grams of Dried Flower Produced
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_tot_water) }} 
                Total Annual Gallons 
            </div>
        </div>
    </div>
</div>
@endif
