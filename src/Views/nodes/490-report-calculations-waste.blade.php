<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-waste.blade.php -->

@if (isset($ps->ps_green_waste_lbs) && isset($ps->ps_flower_canopy_size))
<div class="row">
    <div class="col-12">
        <div class="slGrey">
        <div class="pL10 pT15 slGrey">
            = {{ number_format($ps->ps_green_waste_lbs) }} Annual Pounds of Green/Plant Waste
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>
    </div>
</div>
@endif
