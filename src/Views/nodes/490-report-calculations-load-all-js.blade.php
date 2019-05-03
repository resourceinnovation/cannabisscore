<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-load-all-js.blade.php -->
<script type="text/javascript">

function updateRanks() {
@foreach (['Overall', 'Facility', 'Production', 'HVAC', 'Lighting', 'Water', 'Waste'] as $i => $eff)
    @if (isset($currRanks->{ 'PsRnk' . $eff . '' }))
    <?php $perc = round($currRanks->{ 'PsRnk' . $eff . '' }); ?>
        guageList[{{ $i }}][2] = {!! $perc !!};
        @if ($eff != 'Overall')
            guageList[{{ $i }}][3] = '<h5 class="slBlueDark">{!! $perc . $GLOBALS["SL"]->numSupscript($perc) !!} percentile</h5>';
        @else
            guageList[{{ $i }}][3] = {!! json_encode($withinFilters) !!};
        @endif
    @else
        guageList[{{ $i }}][2] = 0;
        @if ($eff != 'Overall')
            guageList[{{ $i }}][3] = '';
        @else
            guageList[{{ $i }}][3] = {!! json_encode("We did not have enough information to calculate this farm's Overall PowerScore " . $withinFilters) !!};
        @endif
    @endif
@endforeach
    reloadComplete = true;
    return true;
}
setTimeout("updateRanks()", 1);

</script>