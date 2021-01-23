<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-emis.blade.php -->

@if (isset($ps->ps_tot_btu_non_electric) && $ps->ps_tot_kgco2e > 0)

<div class="row">
    <div class="col-12">
        <div class="slGrey">
    @foreach ($addLines[2] as $l => $line)
        @if ($l == 0)
            = ( {!! $line !!}
        @else
            <div class="pL30">
                + {!! $line !!}
            @if ($l == (sizeof($addLines[2])-1))
                )
            @endif
            </div>
        @endif
    @endforeach
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>

        <div class="pT15 slGrey">
    @foreach ($addLines[3] as $l => $line)
        @if ($l == 0)
            = ( {!! $line !!}
        @else
            <div class="pL30">
                + {!! $line !!}
            @if ($l == (sizeof($addLines[3])-1))
                )
            @endif
            </div>
        @endif
    @endforeach
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>

        <div class="pT15 slGrey">
            = {!! number_format(round($ps->ps_tot_kgco2e)) !!} kg CO<sub>2</sub>e
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>

    </div>
</div>

@endif