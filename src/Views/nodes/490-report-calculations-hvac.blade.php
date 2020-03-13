<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-hvac.blade.php -->

<div class="row">
    <div class="col-12 pB15">
        <div class="pL10 slBlueDark">
            = 
            @if (isset($ps->ps_effic_hvac_orig) 
                && $ps->ps_effic_hvac_orig > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac_orig, 3) }}
            @else 0 
            @endif
            <nobr>kWh / sq ft</nobr>
        </div>
    </div>
</div>


@if ($hasRooms)
    
    <div class="row">
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($rooms as $r => $room)
                @if ($r == 0) = @else + @endif
                (
                @if (isset($room->ps_room_hvac_effic) && $room->ps_room_hvac_effic > 0.00001)
                    {{ number_format($room->ps_room_hvac_effic) }} 
                @else 0 @endif
                <nobr>kWh / sq ft</nobr>
                @if ($ps->ps_total_canopy_size > 0)
                    &nbsp;&nbsp;x&nbsp;&nbsp; {{ 
                    number_format(100*($room->ps_room_canopy_sqft/$ps->ps_total_canopy_size)) 
                    }}% grow area
                @endif
                )
                @if ($r == 0) <div class="pL10 slGrey"> 
                @elseif ($r < sizeof($rooms)-1) <br />
                @endif
            @empty
            @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
                @forelse ($rooms as $r => $room)
                    ( {!! $roomNicks[$r] !!} )
                    @if ($r < sizeof($rooms)-1) <br /> @endif
                @empty
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($rooms as $r => $room)
                @if ($r == 0) = @else + @endif
                @if ($ps->ps_total_canopy_size > 0)
                    {{ number_format(
                        $room->ps_room_hvac_effic
                            *($room->ps_room_canopy_sqft/$ps->ps_total_canopy_size)
                    ) }}
                @endif
                kWh / sq ft
                @if ($r == 0) <div class="pL10 slGrey"> 
                @elseif ($r < sizeof($rooms)-1) <br />
                @endif
            @empty
            @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($rooms as $r => $room)
                @if ($r == 0) = @else + @endif
                @if ($ps->ps_total_canopy_size > 0)
                    {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu(
                        $room->ps_room_hvac_effic
                            *($room->ps_room_canopy_sqft/$ps->ps_total_canopy_size)
                    )) }}
                @endif
                kBtu / sq ft
                @if ($r == 0) <div class="pL10 slGrey"> 
                @elseif ($r < sizeof($rooms)-1) <br />
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

@else

    <div class="row">
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($areas as $a => $area)
                @if ($a == 0) = @else + @endif
                (
                {{ number_format($area->ps_area_hvac_effic) }} kWh / sq ft 
                &nbsp;&nbsp;x&nbsp;&nbsp;
                {{ round(100*($area->ps_area_size/$ps->ps_total_canopy_size)) }}% grow area
                )
                @if ($a == 0) <div class="pL10 slGrey"> 
                @elseif ($a < sizeof($areas)-1) <br />
                @endif
            @empty
            @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
                @forelse ($areas as $a => $area)
                    ( {!! $areaNicks[$a] !!} )
                    @if ($a < sizeof($areas)-1) <br /> @endif
                @empty
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($areas as $a => $area)
                @if ($a == 0) = @else + @endif
                {{ number_format(
                    $area->ps_area_hvac_effic
                        *($area->ps_area_size/$ps->ps_total_canopy_size)
                )}} kWh / sq ft
                @if ($a == 0) <div class="pL10 slGrey"> 
                @elseif ($a < sizeof($areas)-1) <br /> 
                @endif
            @empty
            @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 pB15">
            <div class="pL10 slGrey">
            @forelse ($areas as $a => $area)
                @if ($a == 0) = @else + @endif
                {{ number_format($GLOBALS["SL"]->cnvrtKwh2Kbtu(
                    $area->ps_area_hvac_effic
                        *($area->ps_area_size/$ps->ps_total_canopy_size)
                ), 3) }} kBtu / sq ft
                @if ($a == 0) <div class="pL10 slGrey"> 
                @elseif ($a < sizeof($areas)-1) <br />
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

@endif
