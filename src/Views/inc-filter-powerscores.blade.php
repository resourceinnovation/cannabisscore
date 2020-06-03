<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores.blade.php -->
<?php if (!isset($psid)) $psid = 0; ?>

@if ($GLOBALS["SL"]->x["partnerLevel"] >= 2)

<?php /* @if ($psid > 0)
    <label><input type="checkbox" name="psid" id="psidID" value=""></label>
@endif */ ?>

<div class="row">
    <div class="col-md-9">

        <div class="row">

            @if (!isset($GLOBALS["SL"]->x["partnerVersion"]) 
                || !$GLOBALS["SL"]->x["partnerVersion"]
                || $nID != 979)
                {!! view(
                    'vendor.cannabisscore.inc-filter-powerscores-flt-farm', 
                    [ "fltFarm" => $fltFarm ]
                )->render() !!}
            @endif

            @if ($GLOBALS["SL"]->x["partnerLevel"] > 2)
                {!! view(
                    'vendor.cannabisscore.inc-filter-powerscores-flt-climate', 
                    [ "fltStateClim" => $fltStateClim ]
                )->render() !!}
            @endif

            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-lght', 
                [
                    "fltLght"       => $fltLght,
                    "areaTypesFilt" => $areaTypesFilt
                ]
            )->render() !!}

            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-hvac',
                [ "areaTypesFilt" => $areaTypesFilt ]
            )->render() !!}

            @if ($GLOBALS["SL"]->x["partnerLevel"] < 4
                && isset($GLOBALS["SL"]->x["partnerVersion"]) 
                && $GLOBALS["SL"]->x["partnerVersion"])
                {!! view(
                    'vendor.cannabisscore.inc-filter-powerscores-join'
                )->render() !!}
            @endif

        </div>

    @if ($GLOBALS["SL"]->x["partnerLevel"] >= 4
        || !isset($GLOBALS["SL"]->x["partnerVersion"]) 
        || !$GLOBALS["SL"]->x["partnerVersion"])
        <div id="hidivFiltsAdv" class="
            @if ( (isset($fltSize) && intVal($fltSize) > 0) 
                || (isset($fltPerp) && intVal($fltPerp) == 1) 
                || (isset($fltPump) && intVal($fltPump) == 1) 
                || (isset($fltWtrh) && intVal($fltWtrh) == 1) 
                || (isset($fltManu) && intVal($fltManu) == 1) 
                || (isset($fltAuto) && intVal($fltAuto) == 1) 
                || (isset($fltVert) && intVal($fltVert) == 1) 
                || (isset($fltRenew) && sizeof($fltRenew) > 0) 
                || (isset($fltCup) && intVal($fltCup) > 0) )
                || (isset($fltManuLgt) 
                    && !in_array(trim($fltManuLgt), ['', '0']))
            @elseif (!isset($GLOBALS['SL']->x['partnerVersion']) 
                || !$GLOBALS['SL']->x['partnerVersion'])
                @if ( (isset($fltCmpl) && $fltCmpl != 243)
                    || (isset($fltPartner) && $fltPartner > 0)
                    || (isset($fltManuLgt) 
                        && !in_array(trim($fltManuLgt), ['', '0'])) )
                    disBlo
                @else
                    disNon
                @endif
            @else 
                disNon 
            @endif ">

            <div class="row">

                {!! view(
                    'vendor.cannabisscore.inc-filter-powerscores-flt-size', 
                    [ "fltSize" => $fltSize ]
                )->render() !!}

            @if ($nID != 946)
                @if (isset($usrCompanies))
                    {!! view(
                        'vendor.cannabisscore.inc-filter-powerscores-flt-partners', 
                        [
                            "fltPartner"   => $fltPartner,
                            "usrCompanies" => $usrCompanies
                        ]
                    )->render() !!}
                @endif

                @if (isset($manuList) && sizeof($manuList) > 0)
                    {!! view(
                        'vendor.cannabisscore.inc-filter-powerscores-flt-manus', 
                        [
                            "fltPartner" => $fltPartner,
                            "fltManuLgt" => $fltManuLgt,
                            "manuList"   => $manuList
                        ]
                    )->render() !!}
                @endif

                @if ($GLOBALS["SL"]->x["partnerLevel"] >= 9)
                    {!! view(
                        'vendor.cannabisscore.inc-filter-powerscores-flt-cmpl', 
                        [
                            "fltCmpl" => $fltCmpl,
                            "fltFut"  => $fltFut,
                            "fltCup"  => $fltCup
                        ]
                    )->render() !!}
                @endif
            @endif

            </div>

            @if (isset($psFiltChks)) 
                <div class="w100 pB20 taL">{!! $psFiltChks !!}</div>
            @endif

        </div>

    @endif

    </div>
    <div class="col-md-3">

        {!! view(
            'vendor.cannabisscore.inc-filter-powerscores-flt-btns', 
            [ "nID" => $nID ]
        )->render() !!}

    </div>
</div>


<input type="hidden" name="tblBaseUrl" id="tblBaseUrlID" 
    @if (isset($GLOBALS["SL"]->x["partnerVersion"]) 
        && $GLOBALS["SL"]->x["partnerVersion"])
        @if ($nID == 979)
            value="/dash/competitive-performance?z=z"
        @elseif ($nID == 1373)
            value="/dash/partner-compare-official-powerscores?z=z"
        @else
            value="/dash/partner-compare-powerscores?z=z"
        @endif
    @else 
        value="/dash/compare-powerscores?z=z"
    @endif >

<script type="text/javascript"> $(document).ready(function() {

    {!! view(
        'vendor.cannabisscore.inc-filter-powerscores-js', 
        [ "psid" => $psid ]
    )->render() !!}
    function applySort(srtType) {
        var currDir = '{{ $sort[1] }}';
        var baseUrl = document.getElementById('tblBaseUrlID').value+"&sSort="+srtType;
        if (srtType == '{{ $sort[0] }}') {
            if (currDir == 'asc') baseUrl += '&sSortDir=desc';
            else baseUrl += '&sSortDir=asc';
        }
        baseUrl += gatherFilts();
        console.log(baseUrl);
	    window.location=baseUrl;
	    return true;
    }
    $(document).on("click", ".sortScoresBtn", function() {
        applySort($(this).attr("data-sort-type"));
    });
    
@if ($nID != 946)
    function applyFilts() {
        var params = "&sSort={{ $sort[0] }}&ps={{ $psid }}";
        var newUrl = document.getElementById('tblBaseUrlID').value+params+gatherFilts();
        console.log("applyFilts: "+newUrl);
        window.location=newUrl;
        return true;
    }
    $(document).on("click", ".updateScoreFilts", function() {
        if (document.getElementById("calculations")) {
            var newTop = (1+getAnchorOffset()+$("#calculations").offset().top);
            $('html, body').animate({ scrollTop: newTop }, 800, 'swing', function(){ });
        }
        applyFilts(); 
    });
    $(document).on("change", ".psChageFilter", function() {
        applyFilts(); 
        return true; 
    });
@endif
    
}); </script>

    @if ($nID == 946)
        <style>
        label.finger, label.fingerAct { color: #495057; }
        </style>
    @endif

@elseif ($GLOBALS["SL"]->x["partnerLevel"] == 1)

    <div class="row mBn20">
        {!! view(
            'vendor.cannabisscore.inc-filter-powerscores-join'
        )->render() !!}
    </div>

@endif