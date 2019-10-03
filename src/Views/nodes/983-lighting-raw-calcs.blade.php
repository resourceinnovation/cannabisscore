<!-- generated from resources/views/vendor/cannabisscore/nodes/983-lighting-report.blade.php -->

<div class="slCard greenline nodeWrap">
    <input type="hidden" name="toExcel" id="toExcelID" value="0" >
    <!--- <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        href="javascript:;" onClick="return loadExcel();"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
    <h1 class="slBlueDark">Lighting: Raw Calculations</h1>
    <div class="row" style="max-width: 720px;">
        <div class="col-8">
            <p>
            Only showing PowerScores with Lighting Sub-Scores greater than zero, 
            and not archived.
            <!--- <nobr><a href="javascript:;" onClick="return loadFullReport();">Lighting Report</a></nobr> --->
            <input type="hidden" name="rawCalcs" id="rawCalcsID"
                @if ($GLOBALS["SL"]->REQ->has('rawCalcs')) value="1"
                @else value="0" @endif >
            <br /><b>Found {{ number_format($totCnt) }} PowerScores</b>
            </p>
        </div>
        <div class="col-4">
            <select name="fltStateClim" id="fltStateClimID" class="form-control" style="width: 300px;"
                onChange="window.location='?fltStateClim='+this.value;" autocomplete="off">
                <option value="" @if (trim($fltStateClim) == '') SELECTED @endif
                    >All Climates and States</option>
                <option disabled ></option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
            </select>
            <label class="disBlo mT10">
                <input type="checkbox" autocomplete="off" class="mR5"
                    name="fltNoNWPCC" id="fltNoNWPCCID" value="1" onClick="return gatherFilts();"
                    @if ($GLOBALS["SL"]->REQ->has('fltNoNWPCC') 
                        && intVal($GLOBALS["SL"]->REQ->fltNoNWPCC) == 1) CHECKED @endif
                    > Exclude NWPCC Imports
            </label>
            <label class="disBlo mT10">
                <input type="checkbox" autocomplete="off" class="mR5"
                    name="fltNoLgtError" id="fltNoLgtErrorID" value="1" onClick="return gatherFilts();"
                    @if ($GLOBALS["SL"]->REQ->has('fltNoLgtError') 
                        && intVal($GLOBALS["SL"]->REQ->fltNoLgtError) == 1) CHECKED @endif
                    > Exclude Lighting Errors
            </label>
        </div>
    </div>
@if (sizeof($tbl) > 0)
    <table class="table table-striped">
    @foreach ($tbl as $r => $row)
        @if (sizeof($row) > 0)
            @if ($r%11 == 0)
                <tr class="slGrey">
                <th>ID#</th>
                <th>State</th>
                <th>Type</th>
            @if (!$GLOBALS["SL"]->REQ->has('fltNoLgtError'))
                <th>Lighting Stage Errors</th>
            @endif
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
                    <th>{{ $sfAreasGrow[1][$a] }} <nobr>Sq Ft</nobr> / Fixture</th>
                    <th>{{ $sfAreasGrow[1][$a] }} System #1</th>
                    <th>{{ $sfAreasGrow[1][$a] }} System #2</th>
                    <th>{{ $sfAreasGrow[1][$a] }} System #3</th>
                    <th>{{ $sfAreasGrow[1][$a] }} System #4</th>
                @endforeach
                </tr>
            @endif
            <tr>
            @foreach ($row as $c => $col)
                @if ($c != 3 || !$GLOBALS["SL"]->REQ->has('fltNoLgtError'))
                    <?php $cls = ((in_array($c, [4, 6, 10, 14, 18, 27, 36, 45, 56])) 
                        ? 'class="brdLft"' : ''); ?>
                    @if ($c == 0) <th {!! $cls !!} >{!! $col !!}</th>
                    @else <td {!! $cls !!} >{!! $col !!}</td> @endif
                @endif
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

<script type="text/javascript">
function loadExcel() {
    if (document.getElementById("toExcelID")) {
        document.getElementById("toExcelID").value = 1;
        gatherFilts();
    }
    return false;
}
function loadFullReport() {
    if (document.getElementById("rawCalcsID")) {
        document.getElementById("rawCalcsID").value = 0;
        gatherFilts();
    }
    return false;
}
function gatherFilts() {
    var baseUrl = "?filt=1";
    if (document.getElementById("toExcelID") && parseInt(document.getElementById("toExcelID").value) == 1) {
        baseUrl = "?excel=1";
    } else if (document.getElementById("rawCalcsID") && parseInt(document.getElementById("rawCalcsID").value) == 1) {
        baseUrl = "?rawCalcs=1";
    }
    if (document.getElementById("fltStateClimID") && document.getElementById("fltStateClimID").value.trim() != '') {
        baseUrl += "&fltStateClim="+document.getElementById("fltStateClimID").value.trim();
    }
    if (document.getElementById("fltNoNWPCCID") && document.getElementById("fltNoNWPCCID").checked) {
        baseUrl += "&fltNoNWPCC=1";
    }
    if (document.getElementById("fltNoLgtErrorID") && document.getElementById("fltNoLgtErrorID").checked) {
        baseUrl += "&fltNoLgtError=1";
    }
    window.location = baseUrl;
    return false;
}

</script>