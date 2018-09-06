<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
<div id="efficBlockOver" class="row">
    <div class="col-md-6 efficHeads">
        <h1 class="m0 scoreBig">PowerScore Report #{{ $psid }}</h1>
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
    <div class="col-md-6 p0" id="psScoreOverall"></div>
</div>

@if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-md-4 efficHeads"><h3 class="m0 scoreBig">Facility Efficiency:</h3></div>
            <div class="col-md-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </h3></div>
            <div class="col-md-5 p0" id="psScoreFacility"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-md-4 efficHeads"><h3 class="m0 scoreBig">Production Efficiency:</h3></div>
            <div class="col-md-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
                @else 0 @endif &nbsp;&nbsp;grams / kWh
            </h3></div>
            <div class="col-md-5 p0" id="psScoreProduction"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-md-4 efficHeads"><h3 class="m0 scoreBig">HVAC Efficiency:</h3></div>
            <div class="col-md-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficHvac)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) }}
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </h3></div>
            <div class="col-md-5 p0" id="psScoreHvac"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-md-4 efficHeads"><h3 class="m0 scoreBig">Lighting Efficiency:</h3></div>
            <div class="col-md-3 efficHeads efficHeadScore"><h3 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficLighting))
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }} 
                @else 0 @endif &nbsp;&nbsp;kWh / sq ft
            </h3></div>
            <div class="col-md-5 p0" id="psScoreLighting"></div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
    
        @if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
            <div class="row p0 mB20">
                <div class="col-md-6"><div class="pL10 slGrey fPerc80">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = @if ($efficAvgCnt > 1) ( @endif
                                {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif @if ($efficAvgCnt > 1) ) / {{ $efficAvgCnt }} @endif </div>
                </div></div>
                <div class="col-md-6"><div class="pL10 slGrey fPerc80">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = @if ($efficAvgCnt > 1) ( @endif
                                {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif @if ($efficAvgCnt > 1) ) / {{ $efficAvgCnt }} @endif </div>
                </div></div>
            </div>
            <div class="pL10 slGrey fPerc80">
            @if (sizeof($printEfficLgt) > 0)
                @foreach ($printEfficLgt as $i => $calcRow) {!! $calcRow["lgt"] !!}<br /> @endforeach
            @endif
            </div>
        @endif
        <div class="p10 m5"></div>
    
        @if (isset($noprints) && trim($noprints) != '')
            <p><i>Not enough data provided to calculate {{ $noprints }} efficiency.</i></p>
        @endif
        <div id="cmtLnkSpacer"></div>
        <p>
        Come back to check your PowerScore again soon, 
        because your rank will change as more farms see how they stack up!
        </p><p>
        <sup>*</sup> Overall rankings are determined by an equally weighted average among the categories applied: 
        Facility Efficiency, Production Efficiency, Lighting, HVAC. 
        Please explore the <a href="/public-comments-stakeholder-feedback">Public Commenting</a> 
        page to share more insight and stakeholder feedback.
        </p>
        <div class="p10"></div>
    </div>
    <div class="col-md-4">
        <div id="farmFilts" class="round20 brdDshGry p20 mT20">
            <h3 class="mT0 mB5"><span class="wht">Compare to other farms...</span></h3>
            {!! $psFilters !!}
        </div>
    </div>
</div>

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
    .efficGuageTxtOver { width: 365px; margin: -115px 0px 35px 15px; }
    .efficGuageWrapBig { padding-top: 20px; }
    .efficGuageWrapBig img { height: 120px; margin: -14px 0px 10px 472px; padding-top: 20px; }
    .efficGuageWrap img { height: 70px; margin: -54px 0px 26px 496px; }
    .efficGuageTxt { margin: -79px 0px 21px 321px; }
    #farmFilts { width: 90%; margin: -10px 15px 20px 15px; }
    #cmtLnkSpacer { margin-top: -10px; }
}
@media screen and (max-width: 768px) {
    .efficBlock { min-height: 84px; }
    #efficBlockOver { min-height: 195px; }
    .efficGuageTxtOver { width: 265px; margin: -115px 0px 35px 15px; }
    .efficGuageWrapBig img { margin: -5px 0px 10px 440px; }
    .efficGuageWrap img { margin: -60px 0px 19px 463px; }
    .efficGuageTxt { margin: -78px 0px 21px 320px; }
}
@media screen and (max-width: 600px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: -225px 0px 25px 15px; }
    .efficGuageWrapBig img { height: 130px; margin: -15px 0px 110px 313px; }
    .efficGuageWrap img { height: 85px; margin: -44px 0px 25px 330px; }
    .efficGuageTxt { margin: -55px 0px 15px 15px; }
}                                              
@media screen and (max-width: 480px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: 0px 0px 0px 15px; }
    .efficGuageWrapBig { text-align: center; }
    .efficGuageWrapBig img { height: 160px; margin: -30px 0px 0px 0px; }
    .efficGuageWrap img { height: 70px; margin: 0px 0px 0px 230px; }
    .efficGuageTxt { margin: -55px 0px 0px 15px; }
    #efficBlockOver .efficHeads h2 { font-size: 28px; }
}
</style>
<script type="text/javascript"> $(document).ready(function() {
	
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
	function reloadGuages() {
	    var spn = '<i class="fa-li fa fa-spinner fa-spin"></i>';
	    if (document.getElementById('psScoreOverall')) document.getElementById('psScoreOverall').innerHTML = spn;
	    if (document.getElementById('psScoreFacility')) document.getElementById('psScoreFacility').innerHTML = spn;
	    if (document.getElementById('psScoreProduction')) document.getElementById('psScoreProduction').innerHTML = spn;
	    if (document.getElementById('psScoreHvac')) document.getElementById('psScoreHvac').innerHTML = spn;
	    if (document.getElementById('psScoreLighting')) document.getElementById('psScoreLighting').innerHTML = spn;
	    var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}{!! $hasRefresh !!}"+gatherFilts();
        setTimeout(function() { $("#psScoreOverall").load(   ""+baseUrl+"&eff=Overall"); },    2400);
        setTimeout(function() { $("#psScoreFacility").load(  ""+baseUrl+"&eff=Facility"); },   2000);
        setTimeout(function() { $("#psScoreProduction").load(""+baseUrl+"&eff=Production"); }, 1600);
        setTimeout(function() { $("#psScoreHvac").load(      ""+baseUrl+"&eff=HVAC"); },       1200);
        setTimeout(function() { $("#psScoreLighting").load(  ""+baseUrl+"&eff=Lighting"); },      1);
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
}); </script>