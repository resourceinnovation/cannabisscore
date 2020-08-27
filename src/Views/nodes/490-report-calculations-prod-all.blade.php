<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-prod-all.blade.php -->

@if (isset($ps->ps_grams_dry))
<div class="row">
    <div class="col-12">
        <div class="slGrey">
            = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
            <div class="pL15">
                /&nbsp;&nbsp;(
                {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)) }} 
                Total Annual Electric kBtu
            </div>
            <div class="pL30">
                + {{ number_format($ps->ps_tot_btu_non_electric) }} 
                Total Annual Non-Electric kBtu )
            </div>

            <div class="pT15">
                = {{ number_format($ps->ps_grams_dry) }} 
                    Annual Grams of Dried Flower Produced
            </div>
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_tot_btu_non_electric
                    +$GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)) }} 
                Total Annual kBtu
            </div>
        </div>
    </div>
</div>
@endif
