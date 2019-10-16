<!-- generated from resources/views/vendor/cannabisscore/nodes/878-lighting-audit-tots.blade.php -->

<h4>Lighting Summary</h4>
<div class="row mB10">
    <div class="col-3"><b>Growing Area</b></div>
    <div class="col-3"><b>Average Canopy Size</b></div>
    <div class="col-3"><b>Light Fixture Count</b></div>
    <div class="col-3"><b><nobr>Sq Ft</nobr> per Fixture</b></div>
</div>

@forelse ($areas as $a => $area)
    @if (isset($area->PsAreaHasStage) 
        && intVal($area->PsAreaHasStage) == 1)
        <div class="row mB10">
            <div class="col-3">{!! $GLOBALS["SL"]->def->getVal(
                'PowerScore Growth Stages', 
                $area->PsAreaType
            ) !!}</div>
            <div class="col-3"><nobr>{{ 
                number_format($area->PsAreaSize)
            }} Sq Ft</nobr></div>
            <div class="col-3">{!!
                number_format($areaCnts[$area->PsAreaID])
            !!}</div>
            @if ($areaCnts[$area->PsAreaID] > 0 
                && isset($area->PsAreaSqFtPerFix2))
                @if ($area->PsAreaSqFtPerFix2 < 9
                    || $area->PsAreaSqFtPerFix2 > 49) 
                    <div class="col-3 red bld">
                        <nobr>
                        <i class="fa fa-exclamation-triangle mR5"
                            aria-hidden="true"></i>
                        {{ round($area->PsAreaSqFtPerFix2) }}
                        Sq Ft</nobr>
                    </div>
                @else
                    <div class="col-3"><nobr>{{ 
                        round($area->PsAreaSqFtPerFix2) 
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
