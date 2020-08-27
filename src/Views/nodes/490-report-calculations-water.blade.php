<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-water.blade.php -->

@if (isset($ps->ps_tot_water) && isset($ps->ps_flower_canopy_size))
<div class="row">
    <div class="col-12">
        <div class="pL10 pT15 slGrey">
            = {{ number_format($ps->ps_tot_water) }} Total Annual Gallons 
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>
    </div>
</div>
@endif
