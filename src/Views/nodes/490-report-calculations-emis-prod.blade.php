<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-emis-prod.blade.php -->

@if (isset($ps->ps_grams_dry) && isset($ps->ps_tot_kgco2e))
<div class="row">
    <div class="col-12">
        <div class="slGrey">
            = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
            <div class="pL15">
                /&nbsp;&nbsp;(
            @foreach ($addLines[2] as $l => $line)
                @if ($l > 0) + @endif
                {!! $line !!}
                @if ($l == 0) <div class="pL15">
                @elseif ($l < (sizeof($addLines[2])-1)) <br />
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
            @foreach ($addLines[3] as $l => $line)
                @if ($l > 0) + @endif
                {!! $line !!}
                @if ($l == 0) <div class="pL15">
                @elseif ($l < (sizeof($addLines[3])-1)) <br />
                @endif
            @endforeach
                ) </div>
            </div>

            <div class="pT15">
                = {{ number_format($ps->ps_grams_dry) }} 
                Annual Grams of Dried Flower Produced
                <div class="pL15">
                    /&nbsp;&nbsp;{!! number_format($ps->ps_tot_kgco2e) !!} 
                    kg CO<sub>2</sub>e
                </div>
            </div>
        </div>
    </div>
</div>
@endif
