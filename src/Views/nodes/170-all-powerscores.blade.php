<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores.blade.php -->
<h1 class="mT0 slBlueDark">All Completed PowerScores</h1>

<div class="round20 row2 mB20 p15">
    <div class="row">
        <div class="col-md-3">
            <select name="fltFarm" id="filtFarmID" class="form-control ntrStp slTab mT5" autocomplete="off" 
                {!! $GLOBALS["SL"]->tabInd() !!} >
                <option value="0"   @if (!isset($fltFarm) || $fltFarm == 0)  SELECTED @endif >All Farm Types</option>
                <option value="143" @if (isset($fltFarm) && $fltFarm == 143) SELECTED @endif >Outdoor</option>
                <option value="144" @if (isset($fltFarm) && $fltFarm == 144) SELECTED @endif >Indoor</option>
                <option value="145" @if (isset($fltFarm) && $fltFarm == 145) SELECTED @endif >Greenhouse</option>
                <option value="223" @if (isset($fltFarm) && $fltFarm == 223) SELECTED @endif >Multiple Environments</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="fltClimate" id="filtClimateID" class="form-control ntrStp slTab mT5" 
                autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                {!! view('vendor.survloop.inc-drop-opts-ashrae', [ "fltClimate" => $fltClimate ])->render() !!}
            </select>
        </div>
        <div class="col-md-1">
            <a href="javascript:;" class="btn btn-lg btn-primary updateScoreFilts">Filter</a>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-2 taR">
            <a class="btn btn-default mT5" href="/dash/compare-powerscores?fltFarm={{ $fltFarm }}&fltClimate={{ 
                $fltClimate }}&srt={{ $sort[0] }}&srta={{ $sort[1] }}&excel=1"
                ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
        </div>
        <?php /* @if (isset($psid) && $psid > 0)
            <label><input type="checkbox" name="psid" id="psidID" value=""></label>
        @endif */ ?>
    </div>
</div>

<table border=0 class="table table-striped w100">
<tr>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Score ID#',
        "srtVal" => 'PsID',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Overall',
        "srtVal" => 'PsEfficOverall',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Facility Score <div class="pull-right fPerc66 slGrey">kWh/SqFt</div>',
        "srtVal" => 'PsEfficFacility',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Production Score <div class="pull-right fPerc66 slGrey">g/kWh</div>',
        "srtVal" => 'PsEfficProduction',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Lighting Score <div class="pull-right fPerc66 slGrey">kWh/SqFt</div>',
        "srtVal" => 'PsEfficLighting',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'HVAC Score <div class="pull-right fPerc66 slGrey">kWh/SqFt</div>',
        "srtVal" => 'PsEfficHvac',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Grams',
        "srtVal" => 'PsGrams',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'kWh',
        "srtVal" => 'PsKWH',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Sq Ft',
        "srtVal" => 'PsTotalSize',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'Type',
        "srtVal" => 'PsCharacterize',
        "sort"   => $sort
        ])->render() !!}
</th>
<th class="taR">
    {!! view('vendor.survloop.inc-tbl-head-sort', [
        "eng"    => 'County',
        "srtVal" => 'PsCounty',
        "sort"   => $sort
        ])->render() !!}
</th>
</tr>
@forelse ($allscores as $i => $ps)
    <tr>
    <td class="taR"><a href="/calculated/u-{{ $ps->PsID }}" target="_blank">#{{ $ps->PsID }}</a></td>
    <td class="taR">{{ round($ps->PsEfficOverall) }}%</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}</td>
    <td class="taR">{{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}</td>
    <td class="taR">{{ number_format($ps->PsGrams) }}</td>
    <td class="taR">{{ number_format($ps->PsKWH) }}</td>
    <td class="taR">{{ number_format($ps->PsTotalSize) }}</td>
    <td>{{ str_replace('Multiple Environments', 'Multiple Env', 
        $GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $ps->PsCharacterize)) }}</td>
    <td>{{ $ps->PsCounty }} {{ $ps->PsState }}</td>
    </tr>
@empty
    <tr><td colspan=11 class="slGrey" ><i>No PowerScores found.</i></td></tr>
@endforelse
</table>

<input type="hidden" name="tblBaseUrl" id="tblBaseUrlID" 
    value="/dash/compare-powerscores?fltFarm={{ $fltFarm }}&fltClimate={{ $fltClimate }}">
<script type="text/javascript"> $(document).ready(function() {
    function applySort(srtType) {
        var currDir = '{{ $sort[1] }}';
        var baseUrl = document.getElementById("tblBaseUrlID").value+'&srt='+srtType;
        if (srtType == '{{ $sort[0] }}') {
            if (currDir == 'asc') baseUrl += '&srta=desc';
            else baseUrl += '&srta=asc';
        }
	    window.location=baseUrl;
	    return true;
    }
    $(document).on("click", ".sortScoresBtn", function() { applySort($(this).attr("data-sort-type")); });
    function applyFilts() {
	    var baseUrl = "/dash/compare-powerscores?ps={{ $psid }}";
	    if (document.getElementById("filtClimateID") && parseInt(document.getElementById("filtClimateID").value) == 1) {
	        baseUrl += "&fltClimate="+document.getElementById("filtClimateID").value.trim();
	    }
	    if (document.getElementById("filtFarmID") && parseInt(document.getElementById("filtFarmID").value) > 0) {
	        baseUrl += "&fltFarm="+document.getElementById("filtFarmID").value.trim();
	    }
	    window.location=baseUrl;
        return true;
    }
    $(document).on("click", ".updateScoreFilts", function() { applyFilts(); });
}); </script>