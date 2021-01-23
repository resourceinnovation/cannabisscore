<!-- generated from resources/views/vendor/cannabisscore/nodes/170-powerscore-listing-first-cols.blade.php -->

<td>
@if (Auth::user()->hasRole('administrator|staff')
    && $ps->ps_status == 556)
    <div class="relDiv">
        <div class="absDiv slRedDark" 
            style="left: -20px; top: 2px;">
            <i class="fa fa-star-half-o" aria-hidden="true"></i>
        </div>
    </div>
@endif
@if ($nID == 1373 
    && $GLOBALS["SL"]->x["partnerLevel"] < 5)
    {{ (1+$i) }})
@else
    <a href="/calculated/read-{{ $ps->ps_id }}" target="_blank"
        @if (Auth::user()->hasRole('administrator|staff')
            && $ps->ps_status == 556)
            class="slRedDark"
        @endif >#{{ 
            $ps->ps_id 
                . ((isset($ps->ps_is_flow) && intVal($ps->ps_is_flow) == 1) ? 'F' 
                    : ((!isset($ps->ps_is_pro) || intVal($ps->ps_is_pro) != 1) ? 'G' 
                        : 'P'))
        }}</a>
@endif
</td>
<td>
    <nobr>{{ $ps->ps_state }}
    {{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
        $GLOBALS["SL"]->def->getVal(
            'PowerScore Farm Types', 
            $ps->ps_characterize
        )) 
    }}</nobr>
</td>
@if ($nID != 1807) 
    <td>
    @if (isset($ps->ps_start_month) 
        && intVal($ps->ps_start_month) > 0
        && $scoreYearMonths[$ps->ps_id]["has"])
        <nobr>{{ 
        date("n/y", mktime(0, 0, 0, 
            $scoreYearMonths[$ps->ps_id]["endMonth"], 1, 
            $scoreYearMonths[$ps->ps_id]["endYear"])) }}-{{ 
        date("n/y", mktime(0, 0, 0, 
            $scoreYearMonths[$ps->ps_id]["startMonth"], 1, 
            $scoreYearMonths[$ps->ps_id]["startYear"])) 
        }}</nobr> 
    @endif
    </td>
@endif
