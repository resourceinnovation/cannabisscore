<!-- generated from resources/views/vendor/cannabisscore/nodes/878-lighting-audit-tots.blade.php -->

<h4>Lighting Summary</h4>
<div class="row mB10">
    <div class="col-3"><b>Growing Area</b></div>
    <div class="col-3"><b>Average Canopy Size</b></div>
    <div class="col-3"><b>Light Fixture Count</b></div>
    <div class="col-3"><b><nobr>Sq Ft</nobr> per Fixture</b></div>
</div>

@forelse ($areas as $a => $area)
    @if (isset($area->ps_area_has_stage) 
        && intVal($area->ps_area_has_stage) == 1
        && isset($area->ps_area_size) 
        && intVal($area->ps_area_size) > 0
        && isset($area->ps_area_lgt_artif) 
        && intVal($area->ps_area_lgt_artif) == 1)
        <div class="row mB10">
            <div class="col-3">{!! $GLOBALS["SL"]->def->getVal(
                'PowerScore Growth Stages', 
                $area->ps_area_type
            ) !!}</div>
            <div class="col-3"><nobr>{{ 
                number_format($area->ps_area_size)
            }} Sq Ft</nobr></div>
            <div class="col-3">
                @if (isset($areaCnts[$area->ps_area_id]))
                    {!! number_format($areaCnts[$area->ps_area_id]) !!}
                @endif
            </div>
            @if (isset($areaCnts[$area->ps_area_id]) 
                && $areaCnts[$area->ps_area_id] > 0 
                && isset($area->ps_area_sq_ft_per_fix2))
                @if ($area->ps_area_sq_ft_per_fix2 < 4
                    || $area->ps_area_sq_ft_per_fix2 > 81) 
                    <div class="col-3 red bld">
                        <nobr>
                        <i class="fa fa-exclamation-triangle mR5"
                            aria-hidden="true"></i>
                        {{ round($area->ps_area_sq_ft_per_fix2) }}
                        Sq Ft</nobr>
                    </div>
                @else
                    <div class="col-3"><nobr>{{ 
                        round($area->ps_area_sq_ft_per_fix2) 
                    }} Sq Ft</nobr></div>
                @endif
            @else
                <div class="col-3 red bld">
                    <i class="fa fa-exclamation-triangle mR5"
                        aria-hidden="true"></i> -
                </div>
            @endif
        </div>
    @endif
@empty
@endforelse

<div class="pB20 mB20">&nbsp;</div>
