<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers-cell.blade.php -->

<?php
$currVal = -1; 
if (isset($ps->{ 'PsEffic' . $scr }) && $ps->{ 'PsEffic' . $scr } > 0) {
    $currVal = $ps->{ 'PsEffic' . $scr };
} elseif ($scr == 'Flow SqFt/Fix') {
    $currVal = $ps->PsAreaSqFtPerFix2;
} elseif ($scr == 'Veg SqFt/Fix' && isset($scoresVegSqFtFix[$ps->PsID])) {
    $currVal = $scoresVegSqFtFix[$ps->PsID];
}
?>

@if (!in_array($scr, $showStats[$ps->PsID]))

    <span class="slGrey">-</span>

@elseif ($currVal >= 0)

    @if (isset($ps->{ 'PsEffic' . $scr }) && $ps->{ 'PsEffic' . $scr } > 0)
        <label>
        <input type="checkbox" name="goodScores[]" class="mLn10 mR0" autocomplete="off" 
            value="p{{ $ps->PsID }}s{{ str_replace(' SqFt/Fix', 'SqFix', $scr) }}"
            @if (isset($ps->{ 'PsEffic' . $scr . 'Status' }) 
                && intVal($ps->{ 'PsEffic' . $scr . 'Status' }) == 243) 
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