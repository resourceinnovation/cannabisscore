<!-- generated from resources/views/vendor/cannabisscore/nodes/983-lighting-report.blade.php -->

<div class="slCard nodeWrap">
    <input type="hidden" name="toExcel" id="toExcelID" value="0" >
@if ($GLOBALS["SL"]->x["partnerLevel"] > 4)
    <a class="float-right btn btn-secondary btn-sm mT5 mB15"
        href="javascript:;" id="reportToExcel"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
@endif
    <h1 class="slBlueDark">Lighting Report</h1>
    <div class="row">
        <div class="col-8">
            <p>
            Many columns are clickable to load the report listing all individual reports
            matching the filter (when possible). Small subscript counts are the number
            of growing areas (reported in powerscores) upon which each calculated average
            is based. This report only shows PowerScores with Lighting KPIs greater than
            zero (AKA use any artifical lighting), and have not been archived.
            <input type="hidden" name="rawCalcs" id="rawCalcsID"
                @if ($GLOBALS["SL"]->REQ->has('rawCalcs')) value="1"
                @else value="0"
                @endif >
            <br /><b>Found {{ number_format($totCnt) }} PowerScores</b>
            </p>
        </div>
        <div class="col-4">
            {!! $GLOBALS["SL"]->states->stateClimateTagsSelect($fltStateClimTag, 983, 'psChangeFilterDelay') !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsList($fltStateClimTag, 983) !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsJS($fltStateClimTag, 983, 'psClickFilterDelay') !!}

        @if ($GLOBALS["SL"]->x["partnerLevel"] > 6)
            <label class="disBlo mT10">
                <input type="checkbox" autocomplete="off" class="mR5"
                    name="fltNoNWPCC" id="fltNoNWPCCID"
                    value="1" onClick="return gatherFilts();"
                    @if ($GLOBALS["SL"]->REQ->has('fltNoNWPCC')
                        && intVal($GLOBALS["SL"]->REQ->fltNoNWPCC) == 1)
                        CHECKED @endif
                    > Exclude NWPCC Imports
            </label>
        @endif
            <!---
            <label class="disBlo mT10">
                <input type="checkbox" autocomplete="off" class="mR5"
                    name="fltNoLgtError" id="fltNoLgtErrorID"
                    value="1" onClick="return gatherFilts();"
                    @if ($GLOBALS["SL"]->REQ->has('fltNoLgtError')
                        && intVal($GLOBALS["SL"]->REQ->fltNoLgtError) == 1)
                        CHECKED @endif
                    > Exclude Lighting Errors
            </label>
            --->
        </div>
    </div>
</div>

<div class="nodeAnchor"><a name="flowers"></a></div>
@foreach ($reportTitles as $typeID => $typeName)
    @if ($typeName != 'c. Outdoor')
        <div class="slCard nodeWrap">
        <h3>
            1{{ $typeName }} Scores by Type of Flowering Lighting
        </h3>
        {!! str_replace('fltFarm=144', 'fltFarm=' . $typeID,
            $scoreSets["statScorLgtF" . $typeID]->printScoreAvgsTbl2()
        ) !!}
        </div>
    @endif
@endforeach

<div class="nodeAnchor"><a name="veg"></a></div>
@foreach ($reportTitles as $typeID => $typeName)
    @if ($typeName != 'c. Outdoor')
        <div class="slCard nodeWrap">
        <h3>
            2{{ $typeName }} Scores by Type of Vegetative Lighting
        </h3>
        {!! str_replace('fltFarm=144', 'fltFarm=' . $typeID,
            $scoreSets["statScorLgtV" . $typeID]->printScoreAvgsTbl2()
        ) !!}
        </div>
    @endif
@endforeach

<div class="nodeAnchor"><a name="clones"></a></div>
@foreach ($reportTitles as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h3>
        3{{ $typeName }} Scores by Type of Clone Lighting
    </h3>
    {!! str_replace('fltFarm=144', 'fltFarm=' . $typeID,
        $scoreSets["statScorLgtC" . $typeID]->printScoreAvgsTbl2()
    ) !!}
    </div>
@endforeach

<div class="nodeAnchor"><a name="mothers"></a></div>
@foreach ($reportTitles as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h3>
        4{{ $typeName }} Scores by Type of Mother Lighting
    </h3>
    {!! str_replace('fltFarm=144', 'fltFarm=' . $typeID,
        $scoreSets["statScorLgtM" . $typeID]->printScoreAvgsTbl2()
    ) !!}
    </div>
@endforeach

<script type="text/javascript"> $(document).ready(function(){

function loadExcel() {
    if (document.getElementById("toExcelID")) {
        document.getElementById("toExcelID").value = 1;
        applyFilts();
    }
    return false;
}
$(document).on("click", "#reportToExcel", function() {
    loadExcel();
    return false;
});

function loadRawCalcs() {
    if (document.getElementById("rawCalcsID")) {
        document.getElementById("rawCalcsID").value = 1;
        applyFilts();
    }
    return false;
}
function applyFilts() {
    var baseUrl = "?filt=1";
    if (document.getElementById("toExcelID") && parseInt(document.getElementById("toExcelID").value) == 1) {
        baseUrl = "?excel=1&refresh=1";
    } else if (document.getElementById("rawCalcsID") && parseInt(document.getElementById("rawCalcsID").value) == 1) {
        baseUrl = "?rawCalcs=1";
    }
    if (document.getElementById("n983tagIDsID") && document.getElementById("n983tagIDsID").value.trim() != '') {
        baseUrl += "&fltStateClimTag="+document.getElementById("n983tagIDsID").value.trim();
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

$(document).on("change", ".psChangeFilterDelay", function() {
    setTimeout(function() { applyFilts(); }, 200);
    return true;
});
$(document).on("click", ".psClickFilterDelay", function() {
    setTimeout(function() { applyFilts(); }, 200);
    return true;
});


}); </script>


<style>
body { overflow-x: visible; }
</style>