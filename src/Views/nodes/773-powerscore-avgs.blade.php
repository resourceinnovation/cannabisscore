<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs.blade.php -->

<div class="slCard nodeWrap">
@if ($GLOBALS["SL"]->x["partnerLevel"] > 4)
    <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        @if (sizeof($fltStateClimTag) > 0) 
            href="?excel=1&fltStateClimTag={{ implode(',', $fltStateClimTag) }}"
        @else 
            href="?excel=1"
        @endif
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
        Excel</a>
@endif
    <h2 class="mT0 slBlueDark">
        @if ($nID == 801
            && isset($GLOBALS["SL"]->x["partnerCompany"])
            && trim($GLOBALS["SL"]->x["partnerCompany"]) != '')
            {{ $GLOBALS["SL"]->x["partnerCompany"] }}
        @else 
            Ranked Data Set
        @endif
        Averages <nobr>by Category</nobr>
    </h2>

    <div class="mT30 mBn15">
        <div class="row">
            <div class="col-7">
                {!! view('vendor.cannabisscore.inc-partner-ref-disclaim')->render() !!}
                <hr>
                <p>
                &darr; <a href="#farm" class="mL5 mR5">Farm Types</a> - 
                <!-- <a href="#cups" class="mL5 mR5">Competitions</a> - -->
                <a href="#flw-lgty" class="mL5 mR5">Lighting by Growth Stage</a> - 
                <a href="#tech" class="mL5 mR5">Techniques</a> - 
                <a href="#powr" class="mL5 mR5">Power Sources</a>
                </p>
            </div>
            <div class="col-1">
            </div>
            <div class="col-4">

                <p><b>Filter Report:</b></p>

            @if (isset($GLOBALS['SL']->x['usrInfo'])
                && sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
                && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
                <select name="fltFacility" id="fltFacilityID" 
                    class="form-control psChangeFilter ntrStp slTab mB15"
                    onChange="window.location='?fltFacility='+this.value;"
                    autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                    <option value="0"
                        @if (!isset($fltFacility) || in_array(trim($fltFacility), ["", "0"]))
                            SELECTED
                        @endif >All Facilities</option>
                @foreach ($GLOBALS['SL']->x['usrInfo']->companies[0]->facs as $i => $fac)
                    <option value="{{ (1+$i) }}" 
                        @if (isset($fltFacility) && intVal($fltFacility) == (1+$i))
                            SELECTED
                        @endif >Facility: {{ $fac->name }}</option>
                @endforeach
                </select>
            @endif

            {!! $GLOBALS["SL"]->states->stateClimateTagsSelect($fltStateClimTag, $nID, 'psChangeFilterDelay') !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsList($fltStateClimTag, $nID) !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsJS($fltStateClimTag, $nID, 'psClickFilterDelay') !!}

            </div>
        </div>
    </div>
</div>

@foreach ($scoreSets as $i => $set)
    <a name="{{ $set[0] }}"></a>
    <div id="tblCard{{ $i }}" class="slCard nodeWrap">
    <h3 id="tblTitle{{ $i }}">{{ (1+$i) }}. {{ $set[1] }}</h3>
    @if (isset($set[2])) {!! $set[2] !!} 
    @else <i>No completed records found.</i>
    @endif
    </div>
@endforeach

<script type="text/javascript"> $(document).ready(function(){

function applyFilts() {
    baseUrl = "?filt=1";
    if (document.getElementById("n{{ $nID }}tagIDsID") && document.getElementById("n{{ $nID }}tagIDsID").value.trim() != '') {
        baseUrl += "&fltStateClimTag="+document.getElementById("n{{ $nID }}tagIDsID").value.trim();
    }
    window.location=baseUrl;
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
#tblTitle6, #tblTitle7 { display: none; }
#tblCard6, #tblCard7 { border-top: 0px none; }
</style>
