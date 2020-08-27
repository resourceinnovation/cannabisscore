<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers-cell.blade.php -->

<?php
$currVal = -1; 
if (isset($ps->{ $scr[1] }) && $ps->{ $scr[1] } > 0) {
    $currVal = $ps->{ $scr[1] };
} elseif ($scr[0] == 'Flow SqFt/Fix') {
    $currVal = $ps->ps_area_sq_ft_per_fix2;
} elseif ($scr[0] == 'Veg SqFt/Fix' && isset($scoresVegSqFtFix[$ps->ps_id])) {
    $currVal = $scoresVegSqFtFix[$ps->ps_id];
}
?>

@if (!in_array($scr[0], $showStats[$ps->ps_id]))

    <span class="slGrey">-</span>

@elseif ($currVal >= 0)

    @if (isset($ps->{ $scr[1] }) && $ps->{ $scr[1] } > 0)
        <label><nobr>
        <input type="checkbox" name="goodScores[]" class="mLn10 mR0" 
            value="P{{ $ps->ps_id }}K{{ $scr[1] }}" autocomplete="off" 
            @if (isset($ps->{ $scr[1] . '_status' }) 
                && intVal($ps->{ $scr[1] . '_status' }) == 243) 
                CHECKED
            @endif >
        {{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}
        </nobr></label>
    @elseif ($currVal < 2 || $currVal > 64)
        <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}</b>
    @else
        {{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}
    @endif

    <div class="pL5 fPerc80">
    @if ($stats[$type][$size][$scr[0]]["sd"] > 0)
        @if ((($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]) <= -2)
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]), 3) }}</b><br />
        @elseif ((($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]) >= 2)
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]), 3) }}</b><br />
        @elseif ($currVal < $stats[$type][$size][$scr[0]]["avg"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]), 3) }}</span><br />
        @elseif ($currVal >= $stats[$type][$size][$scr[0]]["avg"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["avg"])
            /$stats[$type][$size][$scr[0]]["sd"]), 3) }}</span><br />
        @else <br />
        @endif
    @endif
    
    @if ($stats[$type][$size][$scr[0]]["iqr"] > 0)
        @if ($currVal < $stats[$type][$size][$scr[0]]["q1"])
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["med"])
            /$stats[$type][$size][$scr[0]]["iqr"]), 3) }}</b><br />
        @elseif ($currVal > $stats[$type][$size][$scr[0]]["q3"])
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["med"])
            /$stats[$type][$size][$scr[0]]["iqr"]), 3) }}</b><br />
        @elseif ($currVal < $stats[$type][$size][$scr[0]]["med"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["med"])
            /$stats[$type][$size][$scr[0]]["iqr"]), 3) }}</span><br />
        @elseif ($currVal >= $stats[$type][$size][$scr[0]]["med"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr[0]]["med"])
            /$stats[$type][$size][$scr[0]]["iqr"]), 3) }}</span><br />
        @else <br />
        @endif
    @endif
    </div>

@else

    <span class="slGrey">-</span>

@endif