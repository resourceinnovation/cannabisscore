<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-fac-non.blade.php -->

@if (isset($ps->ps_tot_btu_non_electric) && $ps->ps_tot_btu_non_electric > 0)

<div class="row">
    <div class="col-12">
        <div class="slGrey">
    @foreach ($addLines[0] as $l => $line)
        @if ($l == 0)
            = ( {!! $line !!}
        @else
            <div class="pL30">
                + {!! $line !!}
            @if ($l == (sizeof($addLines[0])-1))
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
    @foreach ($addLines[1] as $l => $line)
        @if ($l == 0)
            = ( {!! $line !!}
        @else
            <div class="pL30">
                + {!! $line !!}
            @if ($l == (sizeof($addLines[1])-1))
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
            = {!! number_format(round($ps->ps_tot_btu_non_electric)) !!} 
            Total Annual Non-Electric kBtu 
            <div class="pL15">
                /&nbsp;&nbsp;{{ number_format($ps->ps_flower_canopy_size) }} 
                Square Feet of Flowering Canopy
            </div>
        </div>

    </div>
</div>

@endif