<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores.blade.php -->
<?php if (!isset($psid)) $psid = 0; ?>

@if ($GLOBALS["SL"]->x["partnerLevel"] >= 2)

<?php /* @if ($psid > 0)
    <label><input type="checkbox" name="psid" id="psidID" value=""></label>
@endif */ ?>

<div class="row">
    <div class="col-md-9">

        <div class="row">

        @if ($GLOBALS["SL"]->x["partnerLevel"] >= 9)
            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-cmpl', 
                [ "fltCmpl" => $fltCmpl ]
            )->render() !!}
        @endif

        @if ($nID != 979 
            && Auth::user()
            && Auth::user()->hasRole('partner')
            && isset($GLOBALS['SL']->x['usrInfo'])
            && sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
            && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-facilities',
                [ "fltFacility" => $fltFacility ]
            )->render() !!}
        @endif

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

    @if ($GLOBALS["SL"]->x["partnerLevel"] >= 4
        || !isset($GLOBALS["SL"]->x["partnerVersion"]) 
        || !$GLOBALS["SL"]->x["partnerVersion"])

            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-size', 
                [ "fltSize" => $fltSize ]
            )->render() !!}

            {!! $flts["fltRenew"]->printDropdownColWrap('psChageFilter') !!}

            {!! $flts["fltWaterSource"]->printDropdownColWrap('psChageFilter') !!}

            {!! $flts["fltWaterStore"]->printDropdownColWrap('psChageFilter') !!}

            {!! $flts["fltWaterStoreSys"]->printDropdownColWrap('psChageFilter') !!}

            {!! $flts["fltWaterStoreMeth"]->printDropdownColWrap('psChageFilter') !!}

            {!! $flts["fltGrowMedia"]->printDropdownColWrap('psChageFilter') !!}

            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-flt-techniques', 
                [ "fltTechniques" => $fltTechniques ]
            )->render() !!}

        @if ($nID != 946)
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

            @if (isset($usrCompanies))
                {!! view(
                    'vendor.cannabisscore.inc-filter-powerscores-flt-partners', 
                    [
                        "fltPartner"   => $fltPartner,
                        "usrCompanies" => $usrCompanies
                    ]
                )->render() !!}
            @endif
        @endif

        @if ($GLOBALS["SL"]->x["partnerLevel"] >= 9)
            {!! view(
                'vendor.cannabisscore.inc-filter-powerscores-admin-extras', 
                [
                    "fltFut" => $fltFut,
                    "fltCup" => $fltCup
                ]
            )->render() !!}
        @endif

        </div>
        <input id="fltAdvShowing" name="fltAdvShow" type="hidden" value="0">

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
            value="/dash/partner-compare-ranked-powerscores?z=z"
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

    function showAdvFilt(item, index) {
        if (document.getElementById(""+item+"Wrap")) {
            document.getElementById(""+item+"Wrap").style.display='block';
        }
    } 
    function hideAdvFilt(item, index) {
        if (document.getElementById(""+item+"Wrap") && document.getElementById(""+item+"ID")) {
            var e = document.getElementById(""+item+"ID");
            var currVal = parseInt(e.options[e.selectedIndex].value);
            if (currVal <= 0) {
                document.getElementById(""+item+"Wrap").style.display='none';
            } else if (item == "fltFut" && currVal == 232) {
                document.getElementById(""+item+"Wrap").style.display='none';
            }
        }
    } 
    function toggleAdvFilts() {
        if (document.getElementById("fltAdvShowing")) {
            var fltList = new Array('fltSize', 'fltRenew', 'fltWaterSource', 'fltWaterStore', 'fltWaterStoreSys', 'fltWaterStoreMeth', 'fltGrowMedia', 'fltTechniques', 'fltManuLgt', 'fltPartner', 'fltFut', 'fltCup');
            if (parseInt(document.getElementById("fltAdvShowing").value) == 0) {
                fltList.forEach(showAdvFilt);
                document.getElementById("fltAdvShowing").value = 1;
            } else {
                fltList.forEach(hideAdvFilt);
                document.getElementById("fltAdvShowing").value = 0;
            }
        }
    }
    $(document).on("click", "#filtsAdvBtn", function() { toggleAdvFilts(); });
    
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