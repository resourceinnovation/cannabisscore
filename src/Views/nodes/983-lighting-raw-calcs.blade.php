<!-- generated from resources/views/vendor/cannabisscore/nodes/983-lighting-report.blade.php -->

<!--- <a class="float-right btn btn-secondary mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
<div class="slCard greenline nodeWrap">
    <h1 class="slBlueDark">Lighting: Raw Calculations</h1>
    Only showing PowerScores with Lighting Sub-Scores greater than zero.
    Found {{ number_format($totCnt) }}
    <a href="?rawCalcs=1&fltStateClim={{ $fltStateClim }}">Lighting Report</a>
    <select name="fltStateClim" id="fltStateClimID" class="form-control mB20" style="width: 300px;"
        onChange="window.location='?fltStateClim='+this.value;" autocomplete="off">
        <option value="" @if (trim($fltStateClim) == '') SELECTED @endif
            >All Climates and States</option>
        <option disabled ></option>
        {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
    </select>
@if (sizeof($tbl) > 0)
    <table class="table table-striped">
    @foreach ($tbl as $r => $row)
        @if (sizeof($row) > 0)
            @if ($r%11 == 0)
                <tr class="slGrey">
                <th>ID#</th>
                <th>State</th>
                <th>Type</th>
                <th class="brdLft">Overall Similar Percentile</th>
                <th>Lighting Efficiency <nobr>Sub-Score</nobr></th>
                <th class="brdLft">Flower Weighted</th>
                <th>Veg Weighted</th>
                <th>Clone Weighted</th>
                <th>Mother Weighted</th>
                <th class="brdLft">Flower SqFt %</th>
                <th>Veg SqFt %</th>
                <th>Clone SqFt %</th>
                <th>Mother SqFt %</th>
                <th class="brdLft">Flower Efficiency</th>
                <th>Veg Efficiency</th>
                <th>Clone Efficiency</th>
                <th>Mother Efficiency</th>
                @foreach ($sfAreasGrow[0] as $a => $areaType)
                    <th class="brdLft">Has {{ $sfAreasGrow[1][$a] }} Stage</th>
                    <th>{{ $sfAreasGrow[1][$a] }} Has Lights</th>
                    @if ($a == 3) <th>Mother Location</th> @endif
                    <th>{{ $sfAreasGrow[1][$a] }} Size</th>
                    <th>{{ $sfAreasGrow[1][$a] }} Watts</th>
                    <th>{{ $sfAreasGrow[1][$a] }} Desc</th>
                @endforeach
                </tr>
            @endif
            <tr>
            @foreach ($row as $c => $col)
                <?php $cls = ((in_array($c, [3, 5, 9, 13, 17, 22, 27, 32, 39])) ? 'class="brdLft"' : ''); ?>
                @if ($c == 0) <th {!! $cls !!} >{!! $col !!}</th>
                @else <td {!! $cls !!} >{!! $col !!}</td> @endif
            @endforeach
            </tr>
        @endif
    @endforeach
    </table>
@endif
</div>

<style>
body { overflow-x: visible; }
</style>