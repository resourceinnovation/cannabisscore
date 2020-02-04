<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers-cell.blade.php -->

<?php
$currVal = -1; 
if (isset($ps->{ 'ps_effic_' . $scrL }) && $ps->{ 'ps_effic_' . $scrL } > 0) {
    $currVal = $ps->{ 'ps_effic_' . $scrL };
} elseif ($scr == 'Flow SqFt/Fix') {
    $currVal = $ps->ps_area_sq_ft_per_fix2;
} elseif ($scr == 'Veg SqFt/Fix' && isset($scoresVegSqFtFix[$ps->ps_id])) {
    $currVal = $scoresVegSqFtFix[$ps->ps_id];
}
?>

@if (!in_array($scr, $showStats[$ps->ps_id]))

    <span class="slGrey">-</span>

@elseif ($currVal >= 0)

    @if (isset($ps->{ 'ps_effic_' . $scrL }) && $ps->{ 'ps_effic_' . $scrL } > 0)
        <label>
        <input type="checkbox" name="goodScores[]" class="mLn10 mR0" autocomplete="off" 
            value="p{{ $ps->ps_id }}s{{ str_replace(' SqFt/Fix', 'SqFix', $scr) }}"
            @if (isset($ps->{ 'ps_effic_' . $scrL . '_status' }) 
                && intVal($ps->{ 'ps_effic_' . $scrL . '_status' }) == 243) 
                CHECKED
            @endif >
        {{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}
        </label>
    @elseif ($currVal < 2 || $currVal > 64)
        <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}</b>
    @else
        {{ $GLOBALS["SL"]->sigFigs($currVal, 3) }}
    @endif

    <div class="pL5 fPerc80">
    @if ($stats[$type][$size][$scr]["sd"] > 0)
        @if ((($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]) <= -2)
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]), 3) }}</b><br />
        @elseif ((($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]) >= 2)
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]), 3) }}</b><br />
        @elseif ($currVal < $stats[$type][$size][$scr]["avg"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]), 3) }}</span><br />
        @elseif ($currVal >= $stats[$type][$size][$scr]["avg"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["avg"])
            /$stats[$type][$size][$scr]["sd"]), 3) }}</span><br />
        @else <br />
        @endif
    @endif
    
    @if ($stats[$type][$size][$scr]["iqr"] > 0)
        @if ($currVal < $stats[$type][$size][$scr]["q1"])
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["med"])
            /$stats[$type][$size][$scr]["iqr"]), 3) }}</b><br />
        @elseif ($currVal > $stats[$type][$size][$scr]["q3"])
            <b class="slRedDark">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["med"])
            /$stats[$type][$size][$scr]["iqr"]), 3) }}</b><br />
        @elseif ($currVal < $stats[$type][$size][$scr]["med"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["med"])
            /$stats[$type][$size][$scr]["iqr"]), 3) }}</span><br />
        @elseif ($currVal >= $stats[$type][$size][$scr]["med"])
            <span class="slGrey">{{ $GLOBALS["SL"]->sigFigs(
            (($currVal-$stats[$type][$size][$scr]["med"])
            /$stats[$type][$size][$scr]["iqr"]), 3) }}</span><br />
        @else <br />
        @endif
    @endif
    </div>
@else
    <span class="slGrey">-</span>
@endif