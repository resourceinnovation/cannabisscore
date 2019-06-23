<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-load-all-js.blade.php -->
<script type="text/javascript">

function updateRanks() {
@foreach (['Overall', 'Facility', 'Production', 'HVAC', 'Lighting', 'Water', 'Waste'] as $i => $eff)
    @if (isset($currRanks->{ 'PsRnk' . $eff . '' }))
    <?php $perc = round($currRanks->{ 'PsRnk' . $eff . '' }); ?>
        guageList[{{ $i }}][2] = {!! $perc !!};
        @if ($eff == 'Overall')
            guageList[{{ $i }}][3] = {!! json_encode($withinFilters) !!};
            guageList[{{ $i }}][4] = {!! json_encode($overallScoreTitle) !!};
        @else
            guageList[{{ $i }}][3] = '{!! $perc . $GLOBALS["SL"]->numSupscript($perc) !!} percentile';
        @endif
    @else
        guageList[{{ $i }}][2] = 0;
        @if ($eff != 'Overall')
            guageList[{{ $i }}][3] = '';
        @else
            guageList[{{ $i }}][3] = {!! json_encode("We did not have enough information to calculate this farm's Overall PowerScore " . $withinFilters) !!};
        @endif
        @if (in_array($eff, ['Overall', 'Production', 'Lighting', 'Waste']))
            guageList[{{ $i }}][5] = 'f5f5f3';
        @else
            guageList[{{ $i }}][5] = 'ebeee7';
        @endif
    @endif
@endforeach
    reloadComplete = true;
    return true;
}
setTimeout("updateRanks()", 1);

</script>