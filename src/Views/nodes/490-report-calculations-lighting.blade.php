<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-lighting.blade.php -->

<div class="row">
    <div class="col-12 pB15">
        <div class="pL10 slBlueDark">
            = 
            @if (isset($ps->ps_effic_lighting) 
                && $ps->ps_effic_lighting > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($GLOBALS["SL"]->cnvrtKwh2Kbtu(
                    $ps->ps_effic_lighting
                ), 3) }}
            @else 0 @endif
            <nobr>kBtu / day</nobr>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12 pB15">
        <div class="pL10 slGrey">
        @forelse ($lgts as $l => $lgt)
            @if ($l == 0) = @else + @endif
            (
            @if (isset($lgt->ps_lg_typ_count))
                {{ number_format($lgt->ps_lg_typ_count) }} x
            @endif
            @if (isset($lgt->ps_lg_typ_wattage))
                {{ number_format($lgt->ps_lg_typ_wattage) }} W x
            @endif
            @if (isset($lgtHours[$lgt->ps_lg_typ_id]))
                {{ $lgtHours[$lgt->ps_lg_typ_id] }} hours / day
            @endif
            )
            @if ($l == 0) <div class="pL10 slGrey"> 
            @elseif ($l < sizeof($lgts)-1) <br />
            @endif
        @empty
        @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 pB15">
        <div class="pL10 slGrey">
            @forelse ($lgts as $l => $lgt)
                @if (isset($lgtNicks[$lgt->ps_lg_typ_id]))
                    ( {!! $lgtNicks[$lgt->ps_lg_typ_id] !!} )
                    @if ($l < sizeof($lgts)-1) <br /> @endif
                @endif
            @empty
            @endforelse
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12 pB15">
        <div class="pL10 slGrey">
        @forelse ($lgts as $l => $lgt)
            @if ($l == 0) = @else + @endif
            @if (isset($lgtTotKwh[$lgt->ps_lg_typ_id]))
                {{ number_format($lgtTotKwh[$lgt->ps_lg_typ_id]) }} kWh / day
            @endif
            @if ($l == 0) <div class="pL10 slGrey"> 
            @elseif ($l < sizeof($lgts)-1) <br />
            @endif
        @empty
        @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 pB15">
        <div class="pL10 slGrey">
        @forelse ($lgts as $l => $lgt)
            @if ($l == 0) = @else + @endif
            @if (isset($lgtTotKwh[$lgt->ps_lg_typ_id]))
                {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu(
                    $lgtTotKwh[$lgt->ps_lg_typ_id])) }} kBtu / day
            @endif
            @if ($l == 0) <div class="pL10 slGrey"> 
            @elseif ($l < sizeof($lgts)-1) <br />
            @endif
        @empty
        @endforelse
            </div>
        </div>
    </div>
</div>
<div class="pL10 fPerc80">
    <i class="slGrey">(1 kWh converts to 3.412 kBtu)</i>
</div>
