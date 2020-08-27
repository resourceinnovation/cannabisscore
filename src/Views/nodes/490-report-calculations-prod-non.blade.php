<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-prod-non.blade.php -->

@if (isset($ps->ps_grams_dry) && isset($ps->ps_kwh_tot_calc))
<div class="row">
    <div class="col-12">
        <div class="slGrey">
            = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
            <div class="pL15">
                /&nbsp;&nbsp;(
            @foreach ($addLines as $l => $line)
                @if ($l > 0) + @endif
                {!! $line !!}
                @if ($l == 0) <div class="pL15">
                @elseif ($l != (sizeof($addLines)-1)) <br />
                @endif
            @endforeach
                ) </div>
            </div>

            <div class="pT15">
                = {{ number_format($ps->ps_grams_dry) }} 
                    Annual Grams of Dried Flower Produced
            </div>
            <div class="pL15">
                /&nbsp;&nbsp;(
            @foreach ($addLines2 as $l => $line)
                @if ($l > 0) + @endif
                {!! $line !!}
                @if ($l == 0) <div class="pL15">
                @elseif ($l != (sizeof($addLines)-1)) <br />
                @endif
            @endforeach
                ) </div>
            </div>

            <div class="pT15">
                = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
                <div class="pL15">
                    /&nbsp;&nbsp;{!! number_format($ps->ps_tot_btu_non_electric) !!} 
                    Total Annual Non-Electric kBtu 
                </div>
            </div>
        </div>
    </div>
</div>
@endif
