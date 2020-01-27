<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-preview.blade.php -->
<div class="calcPrev">
    <table>
        <tr>
            <td colspan=2 >
                <h3 class="m0">PowerScore Report #{{ $ps->ps_id }}</h3>
                <div class="slGrey fPerc80">
                    This is how your performance compares with all other {!! $filtDesc !!} farms:
                </div>
            </td><td class="taC">
            @if (isset($rank->ps_rnk_overall))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->ps_rnk_overall) }}.gif" style="width: 100px;" />
            @endif
            </td><td>
            @if (isset($rank->ps_rnk_overall))
                <b>Overall:<br />{!! round($rank->ps_rnk_overall) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->ps_rnk_overall)) 
                !!} percentile</b>
            @endif
            </td>
        </tr>
    @if (isset($ps->ps_effic_facility) && $ps->ps_effic_facility > 0)
        <tr>
            <td>
                <b>Facility Efficiency:</b>
            </td><td>
                @if (isset($ps->ps_effic_facility)) 
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->ps_rnk_facility))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->ps_rnk_facility) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->ps_rnk_facility))
                {!! round($rank->ps_rnk_facility) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->ps_rnk_facility)) 
                !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->ps_effic_production) && $ps->ps_effic_production > 0)
        <tr>
            <td>
                <b>Production Efficiency:</b>
            </td><td>
                @if (isset($ps->ps_effic_production)) 
                    {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}
                @else 0 @endif &nbsp;&nbsp;grams / kWh
            </td><td class="taC">
            @if (isset($rank->ps_rnk_production))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->ps_rnk_production) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->ps_rnk_production))
                {!! round($rank->ps_rnk_production) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->ps_rnk_production)) 
                !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->ps_effic_hvac) && $ps->ps_effic_hvac > 0)
        <tr>
            <td>
                <b>HVAC Efficiency:</b>
            </td><td>
                @if (isset($ps->ps_effic_hvac)) {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->ps_rnk_hvac))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] 
                    }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->ps_rnk_hvac) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->ps_rnk_hvac))
                {!! round($rank->ps_rnk_hvac) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->ps_rnk_hvac)) !!} percentile
            @endif
            </td>
        </tr>
    @endif
    @if (isset($ps->ps_effic_lighting) && $ps->ps_effic_lighting > 0)
        <tr>
            <td>
                <b>Lighting Efficiency:</b>
            </td><td>
                @if (isset($ps->ps_effic_lighting)) {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }} 
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </td><td class="taC">
            @if (isset($rank->ps_rnk_lighting))
                <img src="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/cannabisscore/uploads/greenometer-anim-{{ 
                    round($rank->ps_rnk_lighting) }}.gif" style="width: 66px;" />
            @endif
            </td><td>
            @if (isset($rank->ps_rnk_lighting))
                {!! round($rank->ps_rnk_lighting) 
                    . $GLOBALS["SL"]->numSupscript(round($rank->ps_rnk_lighting)) !!} percentile
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