<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores.blade.php -->
    <div class="row" ><div class="col-2">
        <select name="fltFarm" id="filtFarmID" class="form-control @if ($nID == 946) form-control-sm @endif ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0"   @if (!isset($fltFarm) || $fltFarm == 0)  SELECTED @endif >All Farm Types</option>
            <option value="143" @if (isset($fltFarm) && $fltFarm == 143) SELECTED @endif >Outdoor</option>
            <option value="144" @if (isset($fltFarm) && $fltFarm == 144) SELECTED @endif >Indoor</option>
            <option value="145" @if (isset($fltFarm) && $fltFarm == 145) SELECTED @endif 
                >Greenhouse/Hybrid/Mixed Light</option>
        </select>
@if ($nID != 946) 
    </div><div class="col-2">
        <select name="fltClimate" id="filtClimateID" class="form-control ntrStp slTab mT5" 
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            {!! $GLOBALS["SL"]->states->climateZoneDrop($fltClimate) !!}
        </select>
@endif
    </div><div class="col-2">
        <select name="fltState" id="fltStateID" class="form-control @if ($nID == 946) form-control-sm @endif ntrStp slTab mT5" 
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            {!! $GLOBALS["SL"]->states->stateDrop($fltState, true) !!}
        </select>
    </div><div class="col-2">
        <select name="fltLght" id="fltLghtID" class="form-control @if ($nID == 946) form-control-sm @endif ntrStp slTab mT5" autocomplete="off" 
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
    </div><div class="col-2">
        <select name="fltHvac" id="fltHvacID" class="form-control @if ($nID == 946) form-control-sm @endif ntrStp slTab mT5" autocomplete="off" 
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
@if ($nID != 946)
    </div><div class="col-2"> 
        <a class="btn btn-lg btn-primary updateScoreFilts float-right" href="javascript:;"
            ><i class="fa fa-filter mR5" aria-hidden="true"></i> Filter</a>
        <div class="mT10"><a id="btnFiltsAdv" class="hidivBtn" href="javascript:;"
            ><i class="fa fa-cogs"></i></a></div>
@else
    </div><div class="col-2"> 
        <a class="btn btn-sm btn-primary updateScoreFilts mT5" href="javascript:;">Apply Filters</a>
@endif
    </div>
</div>
    <?php /* @if (isset($psid) && $psid > 0)
        <label><input type="checkbox" name="psid" id="psidID" value=""></label>
    @endif */ ?>
<div id="filtsAdv" class="
    @if ((isset($fltSize) && intVal($fltSize) > 0) || (isset($fltPerp) && intVal($fltPerp) == 1) 
        || (isset($fltPump) && intVal($fltPump) == 1) || (isset($fltWtrh) && intVal($fltWtrh) == 1) 
        || (isset($fltManu) && intVal($fltManu) == 1) || (isset($fltAuto) && intVal($fltAuto) == 1) 
        || (isset($fltVert) && intVal($fltVert) == 1) || (isset($fltRenew) && sizeof($fltRenew) > 0) 
        || (isset($fltCmpl) && $fltCmpl != 243) || (isset($fltCup) && intVal($fltCup) > 0))
        disBlo
    @else disNon @endif ">
@if ($nID == 946)
    @if (isset($psFiltChks)) <div class="w100 pT20 taL">{!! $psFiltChks !!}</div> @endif
@else
    <div class="row"><div class="col-2 pT10"> 
        <select name="fltSize" id="fltSizeID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0" @if (!isset($fltSize) || intVal($fltSize) == 0) SELECTED @endif 
                >All Farm Sizes (Square feet of flower canopy, annual average)</option>
            <option value="375" @if (isset($fltSize) && $fltSize == 375) SELECTED @endif 
                >&lt; 5,000 square feet</option>
            <option value="376" @if (isset($fltSize) && $fltSize == 376) SELECTED @endif 
                >5,000-10,000 sf</option>
            <option value="377" @if (isset($fltSize) && $fltSize == 377) SELECTED @endif 
                >10,000-50,000 sf</option>
            <option value="378" @if (isset($fltSize) && $fltSize == 378) SELECTED @endif 
                >50,000+ sf</option>
        </select>
        <select name="fltCup" id="fltCupID" class="form-control ntrStp slTab mT20" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0" @if (!isset($fltCup) || intVal($fltCup) == 0) SELECTED @endif 
                >No Competition/Set Filter</option>
            <option value="230" @if (isset($fltCup) && $fltCup == 230) SELECTED @endif 
                >Cultivation Classic</option>
            <option value="231" @if (isset($fltCup) && $fltCup == 231) SELECTED @endif 
                >Emerald Cup Regenerative Award</option>
            <option value="369" @if (isset($fltCup) && $fltCup == 369) SELECTED @endif 
                >NWPCC Import</option>
        </select>
    </div><div class="col-4 pT10">
        @if (isset($psFiltChks)) {!! $psFiltChks !!} @endif
    </div><div class="col-2 pT10">
        <select name="fltCmpl" id="fltCmplID" class="form-control ntrStp slTab mT5" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="242" @if (isset($fltCmpl) && $fltCmpl == 242) SELECTED @endif >Incomplete Scores</option>
            <option value="243" @if (!isset($fltCmpl) || $fltCmpl == 243) SELECTED @endif >Completed Scores</option>
            <option value="364" @if (isset($fltCmpl) && $fltCmpl == 364) SELECTED @endif >Archived Scores</option>
            <option value="0" @if (isset($fltCmpl) && intVal($fltCmpl) == 0) SELECTED @endif >All</option>
        </select>
        <select name="fltFut" id="fltFutID" class="form-control ntrStp slTab mT20" autocomplete="off" 
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="232" @if (!isset($fltFut) || $fltFut == 232) SELECTED @endif >Past-Looking Scores</option>
            <option value="233" @if (isset($fltFut) && $fltFut == 233) SELECTED @endif >Future-Looking Scores</option>
        </select>    
    </div></div>
@endif
</div>
@if ($nID != 946)
    <div class="pT20 pB20">
        <a id="btnFiltsAdv" class="disBlo pull-left" href="javascript:;" style="color: #FFF;">Show More<br />Filters</a>
        <a id="btnFiltsAdvHide" class="disNon pull-left" href="javascript:;" style="color: #FFF;">Show Less<br />Filters</a>
        <a class="pull-right btn btn-info updateScoreFilts" href="javascript:;">Apply Filters</a><br />
    </div>
@endif

<input type="hidden" name="tblBaseUrl" id="tblBaseUrlID" 
    @if (isset($GLOBALS["SL"]->x["partnerVersion"]) && $GLOBALS["SL"]->x["partnerVersion"])
        @if ($GLOBALS["SL"]->REQ->has('all')) value="/dash/partner-compare-powerscores?all=1"
        @else value="/dash/partner-compare-powerscores?z=z"
        @endif
    @else value="/dash/compare-powerscores?z=z"
    @endif >

<script type="text/javascript"> $(document).ready(function() {
    
    $(document).on("click", "#btnFiltsAdv", function() {
@if ($nID == 946)
        setTimeout(function() { $("#btnFiltsAdvHide").slideDown("fast"); }, 350);
        $("#btnFiltsAdv").slideUp("fast");
        $("#filtsAdv").slideDown("fast");
@else
        if (document.getElementById('filtsAdv').style.display != 'block') {
            $("#filtsAdv").slideDown("fast");
        } else {
            $("#filtsAdv").slideUp("fast");
        }
@endif
    });
    $(document).on("click", "#btnFiltsAdvHide", function() {
        setTimeout(function() { $("#btnFiltsAdv").slideDown("fast"); }, 350);
        $("#btnFiltsAdvHide").slideUp("fast");
        $("#filtsAdv").slideUp("fast");
    });
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    function applySort(srtType) {
        var currDir = '{{ $sort[1] }}';
        var baseUrl = document.getElementById('tblBaseUrlID').value;
        if (srtType == '{{ $sort[0] }}') {
            if (currDir == 'asc') baseUrl += '&srta=desc';
            else baseUrl += '&srta=asc';
        }
        baseUrl += gatherFilts();
        console.log(baseUrl);
	    window.location=baseUrl;
	    return true;
    }
    $(document).on("click", ".sortScoresBtn", function() { applySort($(this).attr("data-sort-type")); });
    
@if ($nID != 946)
    function applyFilts() {
        var newUrl = document.getElementById('tblBaseUrlID').value+"&ps={{ $psid }}"+gatherFilts();
        console.log(newUrl);
        window.location=newUrl;
        return true;
    }
    $(document).on("click", ".updateScoreFilts", function() { applyFilts(); });
@endif
    
}); </script>