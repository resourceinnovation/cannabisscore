<!-- generated from resources/views/vendor/cannabisscore/nodes/983-lighting-report.blade.php -->

<div class="slCard nodeWrap">
    <input type="hidden" name="toExcel" id="toExcelID" value="0" >
    <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        href="javascript:;" onClick="return loadExcel();"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
    <h1 class="slBlueDark">Lighting Report</h1>
    <div class="row">
        <div class="col-8">
            <p>
            Many columns are clickable to load the report listing all 
            individual reports matching the filter (when possible).
            Small subscript counts are the number of growing areas 
            (reported in powerscores) upon which each calculated 
            average is based. This report only shows PowerScores 
            with Lighting Sub-Scores greater than zero (AKA use 
            any artifical lighting), and have not been archived.
            <input type="hidden" name="rawCalcs" id="rawCalcsID"
                @if ($GLOBALS["SL"]->REQ->has('rawCalcs')) value="1"
                @else value="0" 
                @endif >
            <br /><b>Found {{ number_format($totCnt) }} PowerScores</b>
            </p>
        </div>
        <div class="col-4">
            <select name="fltStateClim" id="fltStateClimID" class="form-control"
                onChange="return gatherFilts();" autocomplete="off">
                <option value="" @if (trim($fltStateClim) == '') SELECTED @endif
                    >All Climates and States</option>
                <option disabled ></option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
            </select>
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

<script type="text/javascript">

function loadExcel() {
    if (document.getElementById("toExcelID")) {
        document.getElementById("toExcelID").value = 1;
        gatherFilts();
    }
    return false;
}
function loadRawCalcs() {
    if (document.getElementById("rawCalcsID")) {
        document.getElementById("rawCalcsID").value = 1;
        gatherFilts();
    }
    return false;
}
function gatherFilts() {
    var baseUrl = "?filt=1";
    if (document.getElementById("toExcelID") && parseInt(document.getElementById("toExcelID").value) == 1) {
        baseUrl = "?excel=1&refresh=1";
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


<?php /*
@foreach ($sfFarms[0] as $i => $farmDef)
    @if ($sfFarms[1][$i] != 'Outdoor')
        <div class="slCard nodeWrap">
            <h2 class="slBlueDark">@if ($sfFarms[0][$i] == 144) 4a. @elseif ($sfFarms[0][$i] == 145) 4b. @else 4c. @endif 
            {{ $sfFarms[1][$i] }} Square Footage</h2>

        <table class="table table-striped w100" border="0">
            <tbody><tr class="brdBot">
             <th>
         </th>              <th class="brdRgt">
        Averages<!--- <sub class="slGrey">13</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-247" target="_blank">System A</a>
        <!--- <sub class="slGrey">5</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-248" target="_blank">System B</a>
        <!--- <sub class="slGrey">1</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-249" target="_blank">System C</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-250" target="_blank">System D</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-356" target="_blank">System E</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-357" target="_blank">System F</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-251" target="_blank">Other System</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef }}&amp;fltLighting=160-360" target="_blank">None</a>
        <!--- <sub class="slGrey">2</sub> --->
         </th>         </tr>

        @foreach ($sfAreasGrow[0] as $i => $areaDef)
            <tr><th><nobr>{{ $sfAreasGrow[1][$i] }}</nobr></th>
                <td class="brdRgt">
                    {{ number_format($LightingSqft[$farmDef][$areaDef][0]) }}
                    <sub class="slGrey">{{ $LightingSqft[$farmDef][$areaDef][1] }}</sub>
                </td>
            @foreach ($sfLighting[0] as $i => $LightingDef)
                <td>
                    {{ number_format($LightingSqft[$farmDef][$areaDef][2][$LightingDef][0]) }}
                    <sub class="slGrey">{{ count($LightingSqft[$farmDef][$areaDef][2][$LightingDef][1]) }}</sub>
                </td>
            @endforeach
            </tr>
        @endforeach

        </tbody></table>
        </div>

    @endif
@endforeach
*/ ?>

<style>
body { overflow-x: visible; }
</style>