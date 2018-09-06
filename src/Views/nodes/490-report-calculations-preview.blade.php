<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-preview.blade.php -->
<div class="calcPrev">
    <table>
        <tr>
            <td colspan=2 >
                <h3 class="m0">PowerScore Report #{{ $ps->PsID }}</h3>
                <div class="slGrey fPerc80">
                    This is how your performance compares with all other {!! $filtDesc !!} farms:
                </div>
            </td><td class="taC">
            @if (isset($rank->PsRnkOverall))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->PsRnkOverall) }}.gif" style="width: 100px;" />
            @endif
            </td><td>
            @if (isset($rank->PsRnkOverall))
                <b>Overall:<br />{!! round($rank->PsRnkOverall) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->PsRnkOverall)) !!} percentile</b>
            @endif
            </td>
        </tr>
    @if (isset($ps->PsEfficFacility) && $ps->PsEfficFacility > 0)
        <tr>
            <td>
                <b>Facility Efficiency:</b>
            </td><td>
                @if (isset($ps->PsEfficFacility)) {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->PsRnkFacility))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->PsRnkFacility) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->PsRnkFacility))
                {!! round($rank->PsRnkFacility) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->PsRnkFacility)) !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->PsEfficProduction) && $ps->PsEfficProduction > 0)
        <tr>
            <td>
                <b>Production Efficiency:</b>
            </td><td>
                @if (isset($ps->PsEfficProduction)) {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}
                @else 0 @endif &nbsp;&nbsp;grams / kWh
            </td><td class="taC">
            @if (isset($rank->PsRnkProduction))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->PsRnkProduction) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->PsRnkProduction))
                {!! round($rank->PsRnkProduction) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->PsRnkProduction)) !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->PsEfficHvac) && $ps->PsEfficHvac > 0)
        <tr>
            <td>
                <b>HVAC Efficiency:</b>
            </td><td>
                @if (isset($ps->PsEfficHvac)) {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->PsRnkHVAC))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->PsRnkHVAC) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->PsRnkHVAC))
                {!! round($rank->PsRnkHVAC) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->PsRnkHVAC)) !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->PsEfficLighting) && $ps->PsEfficLighting > 0)
        <tr>
            <td>
                <b>Lighting Efficiency:</b>
            </td><td>
                @if (isset($ps->PsEfficLighting)) {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }} 
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->PsRnkLighting))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->PsRnkLighting) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->PsRnkLighting))
                {!! round($rank->PsRnkLighting) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->PsRnkLighting)) !!} percentile
            @endif
            </td>
        </tr>
    @endif
    </table>
</div>

<style>
.calcPrev { min-width: 50%; margin: 0px 15px; padding: 15px; background: #006D36; }
.calcPrev, .calcPrev table, .calcPrev table tr td { color: #FFF; }
.taC { text-align: center; }
.m0 { margin: 0px; }
.slGrey { color: #C6BEB1; }
.fPerc80 { font-size: 80%; }
</style>