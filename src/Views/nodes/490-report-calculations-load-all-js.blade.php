<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-load-all-js.blade.php -->
<script type="text/javascript">

function updateRanks() {
@foreach (['Overall', 'FacAll', 'Facility', 'FacNon', 'ProdAll', 'Production', 'ProdNon', 
    'HVAC', 'Lighting', 'Water', 'Waste'] as $i => $eff)
<?php
$fld = 'ps_rnk_' . strtolower($eff);
if ($eff == 'FacAll') {
    $fld = 'ps_rnk_fac_all';
} elseif ($eff == 'FacNon') {
    $fld = 'ps_rnk_fac_non';
} elseif ($eff == 'ProdAll') {
    $fld = 'ps_rnk_prod_all';
} elseif ($eff == 'ProdNon') {
    $fld = 'ps_rnk_prod_non';
}
?>
    @if (isset($currRanks->{ $fld }))
    <?php $perc = round($currRanks->{ $fld }); ?>
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
            guageList[{{ $i }}][3] = {!! json_encode(
                "<span class='red'>We did not have enough information to calculate this "
                    . "farm's PowerScore rankings with the current filters.</span>"
            ) !!};
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