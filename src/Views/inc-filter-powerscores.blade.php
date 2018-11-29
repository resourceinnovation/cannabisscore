<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores.blade.php -->
<div class="row" >
@if ($nID != 490) <div class="col-2"> @else <div class="col-6"> @endif
        <select name="fltFarm" id="filtFarmID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0"   @if (!isset($fltFarm) || $fltFarm == 0)  SELECTED @endif >All Farm Types</option>
            <option value="143" @if (isset($fltFarm) && $fltFarm == 143) SELECTED @endif >Outdoor</option>
            <option value="144" @if (isset($fltFarm) && $fltFarm == 144) SELECTED @endif >Indoor</option>
            <option value="145" @if (isset($fltFarm) && $fltFarm == 145) SELECTED @endif 
                >Greenhouse/Hybrid/Mixed Light</option>
        </select>
@if ($nID != 490) </div><div class="col-2"> @endif
        <select name="fltClimate" id="filtClimateID" class="form-control ntrStp slTab mT5" 
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            {!! $GLOBALS["SL"]->states->climateZoneDrop($fltClimate) !!}
        </select>
@if ($nID != 490) </div><div class="col-2"> @endif
        <select name="fltState" id="fltStateID" class="form-control ntrStp slTab mT5" 
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            {!! $GLOBALS["SL"]->states->stateDrop($fltState, true) !!}
        </select>
</div><div class=" @if ($nID != 490) col-2 @else col-6 @endif ">
        <select name="fltLght" id="fltLghtID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="" @if (!$GLOBALS["SL"]->REQ->has('fltLght') 
                || trim($GLOBALS["SL"]->REQ->get('fltLght')) == '') SELECTED @endif 
                >All Light Types</option>
            <option value="168" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '168') SELECTED @endif 
                >HID (double-ended HPS)</option>
            <option value="169" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '169') SELECTED @endif 
                >HID (single-ended HPS)</option>
            <option value="170" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '170') SELECTED @endif 
                >HID (double-ended MH)</option>
            <option value="171" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '171') SELECTED @endif 
                >HID (single-ended MH)</option>
            <option value="164" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '164') SELECTED @endif 
                >CMH</option>
            <option value="165" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '165') SELECTED @endif 
                >Fluorescent</option>
            <option value="203" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '203') SELECTED @endif 
                >LED</option>
        @foreach ($areaTypesFilt as $type => $defID)
            <option value="" DISABLED > </option>
            <option value="{{ $defID }}-168" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-168') SELECTED @endif 
                >{{ $type }} - HID (double-ended HPS)</option>
            <option value="{{ $defID }}-169" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-169') SELECTED @endif 
                >{{ $type }} - HID (single-ended HPS)</option>
            <option value="{{ $defID }}-170" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-170') SELECTED @endif 
                >{{ $type }} - HID (double-ended MH)</option>
            <option value="{{ $defID }}-171" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-171') SELECTED @endif 
                >{{ $type }} - HID (single-ended MH)</option>
            <option value="{{ $defID }}-164" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-164') SELECTED @endif 
                >{{ $type }} - CMH</option>
            <option value="{{ $defID }}-165" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-165') SELECTED @endif 
                >{{ $type }} - Fluorescent</option>
            <option value="{{ $defID }}-203" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
                && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-203') SELECTED @endif 
                >{{ $type }} - LED</option>
        @endforeach
        </select>
@if ($nID != 490) </div><div class="col-2"> @endif
        <select name="fltHvac" id="fltHvacID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="" @if (!$GLOBALS["SL"]->REQ->has('fltHvac') 
                || trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '') SELECTED @endif 
                >All HVAC Systems</option>
            <option value="247" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '247') SELECTED @endif 
                >System A - Conventional Air Conditioning with Supplemental Portable Dehumidification Units</option>
            <option value="248" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '248') SELECTED @endif 
                >System B - Conventional Air Conditioning with Enhanced Dehumidification</option>
            <option value="249" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '249') SELECTED @endif 
                >System C - Conventional Air Conditioning with Split Dehumidification Systems</option>
            <option value="250" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '250') SELECTED @endif 
                >System D - Fully Integrated Cooling and Dehumidification System</option>
            <option value="356" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '356') SELECTED @endif 
                >System E - Chilled Water Dehumidification System</option>
            <option value="357" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '357') SELECTED @endif 
                >System F - Greenhouse HVAC Systems</option>
            <option value="251" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '251') SELECTED @endif 
                >Other System</option>
            <option value="360" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '360') SELECTED @endif 
                >None</option>
        @foreach ($areaTypesFilt as $type => $defID)
            <option value="" DISABLED > </option>
            <option value="{{ $defID }}-247" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-247') SELECTED @endif 
                >{{ $type }} - System A - Conventional Air Conditioning with Supplemental Portable Dehumidification 
                Units</option>
            <option value="{{ $defID }}-248" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-248') SELECTED @endif 
                >{{ $type }} - System B - Conventional Air Conditioning with Enhanced Dehumidification</option>
            <option value="{{ $defID }}-249" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-249') SELECTED @endif 
                >{{ $type }} - System C - Conventional Air Conditioning with Split Dehumidification Systems</option>
            <option value="{{ $defID }}-250" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-250') SELECTED @endif 
                >{{ $type }} - System D - Fully Integrated Cooling and Dehumidification System</option>
            <option value="{{ $defID }}-356" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-356') SELECTED @endif 
                >{{ $type }} - System E - Chilled Water Dehumidification System</option>
            <option value="{{ $defID }}-357" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-357') SELECTED @endif 
                >{{ $type }} - System F - Greenhouse HVAC Systems</option>
            <option value="{{ $defID }}-251" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-251') SELECTED @endif 
                >{{ $type }} - Other System</option>
            <option value="{{ $defID }}-360" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
                && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-360') SELECTED @endif 
                >{{ $type }} - None</option>
        @endforeach
        </select>
@if ($nID != 490) </div><div class="col-2"> @else </div></div> @endif
        <a href="javascript:;" class="btn btn-lg btn-primary updateScoreFilts float-right"
            ><i class="fa fa-filter mR5" aria-hidden="true"></i> Filter</a>
        <div class="mT10"><a id="hidivBtnFiltsAdv" class="hidivBtn" href="javascript:;"
            ><i class="fa fa-cogs"></i></a></div>
@if ($nID != 490) </div></div> @endif
    <?php /* @if (isset($psid) && $psid > 0)
        <label><input type="checkbox" name="psid" id="psidID" value=""></label>
    @endif */ ?>
<div id="hidivFiltsAdv" class="
    @if ((isset($fltPerp) && intVal($fltPerp) == 1) || (isset($fltPump) && intVal($fltPump) == 1) 
        || (isset($fltWtrh) && intVal($fltWtrh) == 1) || (isset($fltManu) && intVal($fltManu) == 1) 
        || (isset($fltAuto) && intVal($fltAuto) == 1) || (isset($fltVert) && intVal($fltVert) == 1)
        || (isset($fltRenew) && sizeof($fltRenew) > 0) || (isset($fltCmpl) && $fltCmpl != 243)
        || (isset($fltCup) && intVal($fltCup) > 0)) disBlo
    @else disNon @endif ">
    <div class="row">
@if ($nID != 490) <div class="col-2 pT10"> 
        <select name="fltCmpl" id="fltCmplID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="242" @if (isset($fltCmpl) && $fltCmpl == 242) SELECTED @endif >Incomplete Scores</option>
            <option value="243" @if (!isset($fltCmpl) || $fltCmpl == 243) SELECTED @endif >Completed Scores</option>
            <option value="364" @if (isset($fltCmpl) && $fltCmpl == 364) SELECTED @endif >Archived Scores</option>
            <option value="0" @if (isset($fltCmpl) && intVal($fltCmpl) == 0) SELECTED @endif >All</option>
        </select>
        <select name="fltCup" id="fltCupID" class="form-control ntrStp slTab mT20" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0" @if (!isset($fltCup) || intVal($fltCup) == 0) SELECTED @endif 
                >No Competition Filter</option>
            <option value="230" @if (isset($fltCup) && $fltCup == 230) SELECTED @endif 
                >Cultivation Classic</option>
            <option value="231" @if (isset($fltCup) && $fltCup == 231) SELECTED @endif 
                >Emerald Cup Regenerative Award</option>
        </select>
        
    </div><div class="col-4 pT10">
@else <div class="col-12 pT20"> @endif
        @if (isset($psFiltChks)) {!! $psFiltChks !!} @endif
    </div>
    </div>
</div>

<input type="hidden" name="tblBaseUrl" id="tblBaseUrlID" value="/dash/compare-powerscores">
<script type="text/javascript"> $(document).ready(function() {
        
        
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    function applySort(srtType) {
        var currDir = '{{ $sort[1] }}';
        @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) || !$GLOBALS["SL"]->x["partnerVersion"])
            var baseUrl = '/dash/compare-powerscores?srt='+srtType;
        @else
            var baseUrl = '/compare-powerscores?srt='+srtType;
        @endif
        if (srtType == '{{ $sort[0] }}') {
            if (currDir == 'asc') baseUrl += '&srta=desc';
            else baseUrl += '&srta=asc';
        }
	    window.location=baseUrl+gatherFilts();
	    return true;
    }
    $(document).on("click", ".sortScoresBtn", function() { applySort($(this).attr("data-sort-type")); });
    
    @if ($nID != 490)
        function applyFilts() {
            @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) || !$GLOBALS["SL"]->x["partnerVersion"])
                window.location="/dash/compare-powerscores?ps={{ $psid }}"+gatherFilts();
            @else
                window.location="/compare-powerscores?ps={{ $psid }}"+gatherFilts();
            @endif
            return true;
        }
        $(document).on("click", ".updateScoreFilts", function() { applyFilts(); });
    @endif
}); </script>