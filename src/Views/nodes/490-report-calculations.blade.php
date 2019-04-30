<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

<div id="bigScoreWrap">

<div id="efficBlockOver" class="row">
    <div class="col-lg-6 efficHeads">
        <h2 id="efficBlockOverTitle" class="m0 scoreBig pL20">
        @if ($isPast) Calculated PowerScore<br />#{{ $psid }} @else PowerScore Estimate<br />#{{ $psid }} @endif
        </h2>
        <div class="pL20">
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
            <div class="col-lg-4 efficHeads"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                Facility Efficiency</h5></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>kWh / sq ft</nobr>
            </h5></div>
            <div class="col-lg-5 p0" id="psScoreFacility"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads pL20"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                Production Efficiency</h5></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>g / kWh</nobr>
            </h5></div>
            <div class="col-lg-5 p0" id="psScoreProduction"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads pL20"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                HVAC Efficiency</h5></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficHvac)
                    && $sessData["PowerScore"][0]->PsEfficHvac > 0.000001) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>kWh / sq ft</nobr>
            </h5></div>
            <div class="col-lg-5 p0" id="psScoreHvac"></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-lg-4 efficHeads pL20"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                Lighting Efficiency
                @if ($sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                    <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a>
                @endif
                </h5></div>
            <div class="col-lg-3 efficHeads efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficLighting) 
                    && $sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }}
                @else 0 @endif &nbsp;&nbsp;<nobr>W / sq ft</nobr>
            </h5></div>
            <div class="col-lg-5 p0" id="psScoreLighting"></div>
        </div>
    @if ($sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
        <div id="hidivCalcsLgt">
            <div class="row">
                <div class=" @if ($GLOBALS['SL']->REQ->has('print')) col-md-4 @else col-md-7 @endif ">
                <div class="pL10 slGrey">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                <div class=" @if ($GLOBALS['SL']->REQ->has('print')) col-md-3 @else col-md-5 @endif ">
                <div class="slGrey">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach </div>
                    @endif
                </div></div>
                @if ($GLOBALS["SL"]->REQ->has('print'))
                    <div class="col-md-5"><div class="slGrey">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow) {!! $calcRow["lgt"] !!}<br /> @endforeach
                    @endif
                    </div></div>
                @endif
            </div>
        @if (!$GLOBALS["SL"]->REQ->has('print'))
            <div class="pL10 pT15 slGrey">
            @if (sizeof($printEfficLgt) > 0)
                @foreach ($printEfficLgt as $i => $calcRow) {!! $calcRow["lgt"] !!}<br /> @endforeach
            @endif
            </div>
        @endif
        </div>
    @endif
    </div>
@endif
    
@if (isset($noprints) && trim($noprints) != '')
    <p><i>Not enough data provided to calculate {{ $noprints }} efficiency.</i></p>
@endif
        
@if (!$GLOBALS["SL"]->REQ->has('print') && !$isPast)
    <p><br /><sup>*</sup>
    {!! view('vendor.cannabisscore.nodes.490-report-calculations-rank-about')->render() !!}</p>
    <p>
    For future-looking PowerScore estimates, Facility and Production are estimates purely based on
    the average results of real grow years using similar combinations of technologies and strageties.
    </p>
@endif

</div> <!-- end bigScoreWrap -->

<?php $sizeBig = 180; $sizeSmall = 90; ?>
<style>
#scoreRankFiltWrap {
    width: 100%;
    background: #8dc63f;
    color: #FFF;
    padding: 30px;
}
#node945 { 
    margin-top: 8px;
    padding-left: 13px;
}
#node501 {
    margin-top: -60px;
    padding-left: 13px;
}

#hidivBtnFiltsAdv { color: #FFF; }

#bigScoreWrap { margin: 30px 0px 0px 0px; box-shadow: 0px 20px 60px #DEDEDE; border-left: 20px solid #8dc63f; }
#blockWrap151 { margin-top: 40px; }
#reportTitleWrap { margin: 20px 0px 15px 0px; }

#guageRowOverall {
    background: #FFF;
    border-top: 20px solid #8dc63f;
    border-bottom: 20px solid #8dc63f;
    border-right: 20px solid #8dc63f;
    min-height: 178px;
}
.guageWrap, .guageWrapOver {
    position: relative;
    width: 100%;
    height: 120px;
    overflow: hidden;
    margin: 16px 0px -12px 30px;
}
.guageImgMeter, .guageWrap .guageImgMeter, .guageWrapOver .guageImgMeter {
    position: absolute;
    z-index: 98;
    top: 0px;
    left: 0px;
    height: 180px;
    width: 180px;
}
.guageImgDial, .guageWrap .guageImgDial, .guageWrapOver .guageImgDial {
    position: absolute;
    z-index: 99;
    top: 50%;
    left: 50%;
    margin-top: -57px;
    margin-left: -120px;
    height: 180px;
    width: 180px;

    -webkit-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
    -moz-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
    -o-transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
    transition: all 2000ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
}
.guageWrap {
    height: 65px;
    margin: -8px 0 -20px -23px;
}
.guageWrap .guageImgMeter {
    height: 100px;
    width: 100px;
}
.guageWrap .guageImgDial {
    margin-top: -31px;
    margin-left: -81px;
    height: 100px;
    width: 100px;
}

.efficGuageTxt, .efficGuageTxtOver { text-align: left; width: 250px; }
.efficGuageTxt { margin: 12px 0 0 -16px; }
.efficGuageTxt .slGrey, .efficGuageTxtOver .slGrey { font-size: 12px; line-height: 16px; }
#efficBlockOver { background: #8dc63f; color: #FFF; min-height: 178px; }
#efficBlockOverTitle { margin-top: 20px; }
#efficBlockOverGuageTitle { margin-top: 15px; }
.efficBlock { width: 100%; min-height: 75px; padding: 15px 0px 5px 0px; border-top: 1px solid #f1f1f1; }
.efficHeads { padding: 10px 15px 0px 15px; }
#hidivCalcsLgt { display: none; padding: 15px 0px 15px 30px; width: 75%; }

#guageOverallTxt { color: #444; }


@media screen and (max-width: 1200px) {
    .efficBlock { min-height: 75px; }
    #efficBlockOver { min-height: 100px; }
    .efficGuageTxtOver { margin: -8px 0px 0px -30px; }
}
@media screen and (max-width: 992px) {
    .efficBlock { min-height: 96px; }
    #efficBlockOver { min-height: 170px; }
    .efficHeads { padding-left: 20px; }
    .efficHeadScore { padding-left: 30px; }
    .efficGuageTxtOver { width: 365px; margin: 24px 0px 35px -347px; }
    .efficGuageWrapBig { padding-top: 20px; }
    .efficGuageTxt { margin: -58px 0px 21px 36px; }
    #cmtLnkSpacer { margin-top: -10px; }
}
@media screen and (max-width: 768px) {
    .efficBlock { min-height: 84px; }
    #efficBlockOver { min-height: 195px; }
    .efficGuageTxtOver { width: 265px; margin: -115px 0px 35px 15px; }
    .efficGuageTxt { margin: -84px 0px 21px 265px; }
    .efficGuageTxt h4, #blockWrap492 .efficGuageTxt h4 { font-size: 1.2rem; color: #B5CE96; }
}
@media screen and (max-width: 600px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: -165px 0px 0px 15px; }
    .efficGuageTxt { margin: -35px 0px 15px 15px; }
}                                              
@media screen and (max-width: 480px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: 0px 0px 0px 15px; }
    .efficGuageWrapBig { text-align: center; }
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
        console.log(baseUrl+"&eff=Overall");
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
    @if (!$isPast) setTimeout(function() { $("#futureForm").load("/ajax/future-look?ps={{ $psid }}"); }, 3000); @endif
    
});

@if ($GLOBALS["SL"]->REQ->has('print')) setTimeout("window.print()", 3000); @endif

</script>

@endif