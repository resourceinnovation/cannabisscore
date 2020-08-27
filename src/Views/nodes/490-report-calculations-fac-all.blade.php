<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-fac-all.blade.php -->

<div class="row">
    <div class="col-12">
        <div class="slGrey">
            = ( 
                {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)) }} 
                Total Annual Electric kBtu
            <div class="pL30">
                + {!! number_format(round($ps->ps_tot_btu_non_electric)) !!} 
                Total Annual Non-Electric kBtu )
            </div>
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>

        <div class="pT15 slGrey">
            = {!! number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu($ps->ps_kwh_tot_calc)
                +round($ps->ps_tot_btu_non_electric)) !!} 
            Total Annual kBtu 
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>
    </div>
</div>
