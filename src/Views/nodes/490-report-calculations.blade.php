<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

<div id="efficBlockOver" class="row">
    <div class="col-lg-6 efficHeads">
        <h1 class="m0 scoreBig">
        @if ($isPast) PowerScore Report #{{ $psid }} @else PowerScore Estimate #{{ $psid }} @endif
        </h1>
        <div class="slGrey">
        @if (isset($sessData["PowerScore"][0]->PsCharacterize)) 
            <nobr>{{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $sessData["PowerScore"][0]->PsCharacterize) 
                }}</nobr> —
        @endif
        <nobr>
        @if (isset($sessData["PowerScore"][0]->PsZipCode)) 
            {{ ucwords(strtolower($GLOBALS["SL"]->states->getZipProperty($sessData["PowerScore"][0]->PsZipCode))) }},
        @endif
        @if (isset($sessData["PowerScore"][0]->PsState)) 
            {{ $GLOBALS["SL"]->states->getState($sessData["PowerScore"][0]->PsState) }}
        @endif
        </nobr> —
        @if (isset($sessData["PowerScore"][0]->PsAshrae)) 
            <nobr> @if ($sessData["PowerScore"][0]->PsAshrae != 'Canada') Climate Zone @endif 
                {{ $sessData["PowerScore"][0]->PsAshrae }}</nobr>
        @endif
        </div>
    </div>
    <div class="col-lg-6 p0" id="psScoreOverall"></div>
</div>

@if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads"><h3 class="m0 scoreBig">Facility Efficiency:</h3></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>kWh / sq ft</nobr>
            </h3></div>
            <div class="col-lg-5 p0" id="psScoreFacility"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads"><h3 class="m0 scoreBig">Production Efficiency:</h3></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>g / kWh</nobr>
            </h3></div>
            <div class="col-lg-5 p0" id="psScoreProduction"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads"><h3 class="m0 scoreBig">HVAC Efficiency:</h3></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficHvac)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>kWh / sq ft</nobr>
            </h3></div>
            <div class="col-lg-5 p0" id="psScoreHvac"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads"><h3 class="m0 scoreBig">Lighting Efficiency:</h3></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficLighting))
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }} 
                @else 0 @endif &nbsp;&nbsp;<nobr>W / sq ft</nobr>
            </h3></div>
            <div class="col-lg-5 p0" id="psScoreLighting"></div>
        </div>
    </div>
@endif

@if (!$GLOBALS["SL"]->REQ->has('print'))
    <div class="row">
        <div class=" @if ($isPast) col-md-8 @else col-md-7 @endif ">
@endif
    
        @if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
            <div class="row p0 mB20">
                <div class=" @if ($GLOBALS['SL']->REQ->has('print')) col-md-4 @else col-md-7 @endif ">
                <div class="pL10 slGrey fPerc80">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                <div class=" @if ($GLOBALS['SL']->REQ->has('print')) col-md-3 @else col-md-5 @endif ">
                <div class="slGrey fPerc80">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                @if ($GLOBALS["SL"]->REQ->has('print'))
                    <div class="col-md-5"><div class="slGrey fPerc80">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow) {!! $calcRow["lgt"] !!}<br /> @endforeach
                    @endif
                    </div></div>
                @endif
            </div>
            @if (!$GLOBALS["SL"]->REQ->has('print'))
                <div class="pL10 slGrey fPerc80">
                @if (sizeof($printEfficLgt) > 0)
                    @foreach ($printEfficLgt as $i => $calcRow) {!! $calcRow["lgt"] !!}<br /> @endforeach
                @endif
                </div>
            @endif
        @endif
        <div class="p10 m5"></div>
    
        @if (isset($noprints) && trim($noprints) != '')
            <p><i>Not enough data provided to calculate {{ $noprints }} efficiency.</i></p>
        @endif
        <div id="cmtLnkSpacer"></div>
        @if ($isPast)
            @if (!$GLOBALS["SL"]->REQ->has('print'))
                <p>Come back to check your PowerScore again soon, 
                because your rank will change as more farms see how they stack up!</p>
            @endif
            <p><sup>*</sup> {!! view('vendor.cannabisscore.nodes.490-report-calculations-rank-about')->render() !!}</p>
            <div class="p10"></div>
        @endif
@if (!$GLOBALS["SL"]->REQ->has('print'))
        </div>
        <div class=" @if ($isPast) col-md-4 @else col-md-5 @endif ">
        @if ($isPast)
            <div id="farmFilts" class="round20 brdDshGry p20 mT20">
                <h3 class="mT0 mB5"><span class="wht">Compare to other farms</span></h3>
                {!! $psFilters !!}
            </div>
        @else
            <p class="fPerc80"><br /><sup>*</sup>
            {!! view('vendor.cannabisscore.nodes.490-report-calculations-rank-about')->render() !!}</p>
            <p class="fPerc80">
            For future-looking PowerScore estimates, Facility and Production are estimates purely based on
            the average results of real grow years using similar combinations of technologies and strageties.
            </p>
        @endif
        </div>
    </div>
@endif
@if (!$isPast && !$GLOBALS["SL"]->REQ->has('print'))
    <div class="bgGry w100 mB20" style="height: 4px;"></div>
    <h3 class="mT0 mB5"><span class="wht">Test farm design changes</span></h3>
    <div id="futureForm" class="w100"></div>
@endif

<div class="p10"></div>

<style>
#blockWrap151 { margin-top: 40px; }
#blockWrap492 .slGrey { color: #B5CE96; }
#reportTitleWrap { margin: 20px 0px 15px 0px; }
.efficGuageWrap img, .efficGuageWrap .mpt { height: 45px; margin: -10px 0px -1px 32px; opacity:0.90; filter:alpha(opacity=90); }
.efficGuageWrapBig img { height: 100px; margin: 2px 0px -13px 80px; }
.efficGuageTxt, .efficGuageTxtOver { text-align: left; width: 250px; }
.efficGuageTxt { margin: -5px 0px 0px -9px; }
.efficGuageTxt .slGrey, .efficGuageTxtOver .slGrey { font-size: 12px; line-height: 16px; }
.scoreBig { color: #FFF; text-shadow: -1px 1px 2px #000; }
#efficBlockOver { min-height: 100px; margin-top: 30px; }
.efficBlock { width: 100%; min-height: 54px; padding: 15px 0px 5px 0px; border-top: 1px #DDD solid; }
@media screen and (max-width: 1200px) {
    .efficBlock { min-height: 55px; }
    #efficBlockOver { min-height: 100px; }
    .efficGuageWrapBig img { margin: -8px 0px -13px 22px; }
    .efficGuageTxtOver { margin: -8px 0px 0px -30px; }
}
@media screen and (max-width: 992px) {
    .efficBlock { min-height: 96px; }
    #efficBlockOver { min-height: 170px; }
    .efficHeads { padding-left: 20px; }
    .efficHeadScore { padding-left: 30px; }
    .efficGuageTxtOver { width: 365px; margin: 24px 0px 35px -347px; }
    .efficGuageWrapBig { padding-top: 20px; }
    .efficGuageWrapBig img { height: 120px; margin: -14px 0px 10px 472px; padding-top: 20px; }
    .efficGuageWrap img { height: 70px; margin: -54px 0px 26px 496px; }
    .efficGuageTxt { margin: -58px 0px 21px 36px; }
    #farmFilts { width: 90%; margin: -10px 15px 20px 15px; }
    #cmtLnkSpacer { margin-top: -10px; }
}
@media screen and (max-width: 768px) {
    .efficBlock { min-height: 84px; }
    #efficBlockOver { min-height: 195px; }
    .efficGuageTxtOver { width: 265px; margin: -115px 0px 35px 15px; }
    .efficGuageWrapBig img { margin: -5px 0px 10px 325px; }
    .efficGuageWrap img { margin: -60px 0px 19px 377px; }
    .efficGuageTxt { margin: -84px 0px 21px 265px; }
    .efficGuageTxt h4, #blockWrap492 .efficGuageTxt h4 { font-size: 1.2rem; color: #B5CE96; }
}
@media screen and (max-width: 600px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: -165px 0px 0px 15px; }
    .efficGuageWrapBig img { height: 130px; margin: -15px 0px 50px 305px; }
    .efficGuageWrap img { height: 85px; margin: -44px 0px 0px 281px; }
    .efficGuageTxt { margin: -35px 0px 15px 15px; }
}                                              
@media screen and (max-width: 480px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: 0px 0px 0px 15px; }
    .efficGuageWrapBig { text-align: center; }
    .efficGuageWrapBig img { height: 160px; margin: -30px 0px 0px 0px; }
    .efficGuageWrap img { height: 70px; margin: -50px 0px 0px 272px; }
    .efficGuageTxt { margin: -15px 0px 40px 15px; }
    #efficBlockOver .efficHeads h2 { font-size: 28px; }
}
</style>
<script type="text/javascript"> $(document).ready(function() {
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    var spn = '<i class="fa-li fa fa-spinner fa-spin"></i>';
    function reloadGuages() {
        if (document.getElementById('psScoreOverall')) document.getElementById('psScoreOverall').innerHTML = spn;
        if (document.getElementById('psScoreFacility')) document.getElementById('psScoreFacility').innerHTML = spn;
        if (document.getElementById('psScoreProduction')) document.getElementById('psScoreProduction').innerHTML = spn;
        if (document.getElementById('psScoreHvac')) document.getElementById('psScoreHvac').innerHTML = spn;
        if (document.getElementById('psScoreLighting')) document.getElementById('psScoreLighting').innerHTML = spn;
        var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}"+gatherFilts();
        setTimeout(function() { $("#psScoreOverall").load(   ""+baseUrl+"&eff=Overall"); },    2400);
        setTimeout(function() { $("#psScoreFacility").load(  ""+baseUrl+"&eff=Facility"); },   2000);
        setTimeout(function() { $("#psScoreProduction").load(""+baseUrl+"&eff=Production"); }, 1600);
        setTimeout(function() { $("#psScoreHvac").load(      ""+baseUrl+"&eff=HVAC"); },       1200);
        setTimeout(function() { $("#psScoreLighting").load(  ""+baseUrl+"&eff=Lighting"); },      1);
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
    @if (!$isPast) setTimeout(function() { $("#futureForm").load("/ajax/future-look?ps={{ $psid }}"); }, 3000); @endif
    
});

@if ($GLOBALS["SL"]->REQ->has('print')) setTimeout("window.print()", 3000); @endif

</script>

@endif