<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))
<style>
{!! view('vendor.cannabisscore.nodes.490-report-calculations-css')->render() !!}
</style>

<div id="bigScoreWrap">

<div id="efficBlockOver" class="row">
    <div id="efficBlockOverLeft" class="col-lg-6 col-md-12 efficHeads">
        <div id="efficBlockOverLeftInner">
            <h2 id="efficBlockOverTitle" class="m0 scoreBig">
            @if ($isPast) Calculated PowerScore @else PowerScore Estimate @endif </h2>
            <h5>@if (isset($sessData["PowerScore"][0]->PsCharacterize))
                {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $sessData["PowerScore"][0]->PsCharacterize) }}
            @endif #{{ $psid }} </h5>
            <nobr>
            @if (isset($sessData["PowerScore"][0]->PsZipCode)) 
                {{ ucwords(strtolower($GLOBALS["SL"]->states->getZipProperty($sessData["PowerScore"][0]->PsZipCode))) }},
            @endif
            @if (isset($sessData["PowerScore"][0]->PsState)) 
                {{ $GLOBALS["SL"]->states->getState($sessData["PowerScore"][0]->PsState) }}
            @endif
            </nobr><br />
            @if (isset($sessData["PowerScore"][0]->PsAshrae)) 
                <nobr> @if ($sessData["PowerScore"][0]->PsAshrae != 'Canada') Climate Zone @endif 
                {{ $sessData["PowerScore"][0]->PsAshrae }}</nobr>
            @endif
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-5" id="psScoreOverall">
        <iframe id="guageFrameOverall" class="guageFrame" src="" frameborder="0" width="180" height="120" ></iframe>
    </div>
    <div class="col-lg-4 col-md-8 col-sm-7" id="psScoreOverTxt"><div id="efficGuageTxtOverall" class="efficGuageTxt"></div></div>
</div>

@if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>Facility Efficiency
                <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
            </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>kWh / sq ft</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreFacility">
                <iframe id="guageFrameFacility" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtFacility" class="efficGuageTxt"></div></div>
        </div>
        <div id="hidivCalcsFac" class="scoreCalcs">
        @if (isset($sessData["PowerScore"][0]->PsKWH) && isset($totFlwrSqFt) && $totFlwrSqFt > 0)
            <div class="pL10 slGrey">
                = {{ number_format($sessData["PowerScore"][0]->PsKWH) }} Total Annual Kilowatt Hours
                    &nbsp;&nbsp;/&nbsp;&nbsp;
                    {{ number_format($totFlwrSqFt) }} Square Feet of Flowering Canopy
            </div>
        @endif
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>Production Efficiency
                <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
            </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>g / kWh</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreProduction">
                <iframe id="guageFrameProduction" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtProduction" class="efficGuageTxt"></div></div>
        </div>
        <div id="hidivCalcsProd" class="scoreCalcs">
        @if (isset($sessData["PowerScore"][0]->PsGrams) && isset($sessData["PowerScore"][0]->PsKWH))
            <div class="pL10 slGrey">
                = {{ number_format($sessData["PowerScore"][0]->PsGrams) }} Annual Grams of Flower & Byproduct
                    &nbsp;&nbsp;/&nbsp;&nbsp;
                    {{ number_format($sessData["PowerScore"][0]->PsKWH) }} Total Annual Kilowatt Hours
            </div>
        @endif
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>HVAC Efficiency
                <a id="hidivBtnCalcsHvac" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
            </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficHvac)
                    && $sessData["PowerScore"][0]->PsEfficHvac > 0.000001) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>kWh / sq ft</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreHvac">
                <iframe id="guageFrameHvac" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtHvac" class="efficGuageTxt"></div></div>
        </div>
        <div id="hidivCalcsHvac" class="scoreCalcs">
            <div class="row">
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficHvac) > 0)
                        @foreach ($printEfficHvac as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficHvac)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                @if (sizeof($printEfficHvac) > 0)
                    @foreach ($printEfficHvac as $i => $calcRow)
                        @if ($i == 0) = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                        @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficHvac)-1) <br /> @endif @endif
                    @endforeach </div>
                @endif
                </div></div>
            </div>
            <div class="pT20 fPerc66"><i> These are the proposed formulas for a more accurate HVAC score, to match lighting and water.</i></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>Lighting Efficiency
                @if ($sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                    <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                @endif
                </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficLighting) 
                    && $sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>W / sq ft</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreLighting">
                <iframe id="guageFrameLighting" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtLighting" class="efficGuageTxt"></div></div>
        </div>
    @if ($sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
        <div id="hidivCalcsLgt" class="scoreCalcs">
            <div class="row">
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficLgt) > 0)
                        @foreach ($printEfficLgt as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficLgt)-1) <br /> @endif @endif
                        @endforeach </div>
                    @endif
                </div></div>
                @if ($GLOBALS["SL"]->REQ->has('print'))
                    <div class="col-md-6"><div class="slGrey">
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

@if (isset($sessData["PowerScore"][0]->PsEfficWater) && $sessData["PowerScore"][0]->PsEfficWater > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>Water Efficiency
                <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
            </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficWater)
                    && $sessData["PowerScore"][0]->PsEfficWater > 0.000001) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficWater, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>gallons / sq ft</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreWater">
                <iframe id="guageFrameWater" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtWater" class="efficGuageTxt"></div></div>
        </div>
        <div id="hidivCalcsWater" class="scoreCalcs">
            <div class="row">
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficWtr) > 0)
                        @foreach ($printEfficWtr as $i => $calcRow)
                            @if ($i == 0) = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                            @else + {!! $calcRow["eng"] !!} @if ($i < sizeof($printEfficWtr)-1) <br /> @endif @endif
                        @endforeach
                    @endif </div>
                </div></div>
                <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                @if (sizeof($printEfficWtr) > 0)
                    @foreach ($printEfficWtr as $i => $calcRow)
                        @if ($i == 0) = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                        @else + {!! $calcRow["num"] !!} @if ($i < sizeof($printEfficWtr)-1) <br /> @endif @endif
                    @endforeach </div>
                @endif
                </div></div>
            </div>
            <div class="pT20 fPerc66"><i> Water score is not yet being factored into each Overal PowerScore.</i></div>
        </div>
    </div>
@endif

@if (isset($sessData["PowerScore"][0]->PsEfficWaste) && $sessData["PowerScore"][0]->PsEfficWaste > 0)
    <div class="efficBlock">
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-12 efficHeadLabel"><h5 class="m0 scoreBig" style="margin-left: 30px;">
                <nobr>Waste Efficiency
                <a id="hidivBtnCalcsWaste" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
            </h5></div>
            <div class="col-lg-1 col-md-12 efficHeadScore"><h5 class="m0 scoreBig">
                @if (isset($sessData["PowerScore"][0]->PsEfficWaste)) 
                    {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficWaste, 3) }}
                @else 0 @endif
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadLabel2"><h5 class="m0 scoreBig">
                <nobr>g / kWh</nobr>
            </h5></div>
            <div class="col-lg-2 col-md-12 efficHeadGuage" id="psScoreWaste">
                <iframe id="guageFrameWaste" class="guageFrame" src="" frameborder="0" width="100" height="70" ></iframe>
            </div>
            <div class="col-xl-4 col-lg-3 col-md-12 efficHeadGuageLabel"><div id="efficGuageTxtWaste" class="efficGuageTxt"></div></div>
        </div>
        <div id="hidivCalcsWaste" class="scoreCalcs">
        @if (isset($sessData["PowerScore"][0]->PsGreenWasteLbs))
            <div class="pL10 slGrey">
                = {{ number_format($sessData["PowerScore"][0]->PsGreenWasteLbs) }} Annual Pounds of Green/Plant Waste
                    &nbsp;&nbsp;/&nbsp;&nbsp;
                    {{ number_format($totFlwrSqFt) }} Square Feet of Flowering Canopy
            </div>
            <div class="pT20 fPerc66"><i> Waste score is not yet being factored into each Overal PowerScore.</i></div>
        @endif
        </div>
    </div>
@endif
    
@if (isset($noprints) && trim($noprints) != '')
    <div class="efficBlock" style="min-height: 30px; padding: 15px;">
        <i>Not enough data provided to calculate {{ $noprints }} efficiency.</i>
    </div>
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
<div id="guageReloader" class="disNon"></div>

<script type="text/javascript">
    
setTimeout("document.getElementById('col944').className = 'col-xl-4 col-lg-6 col-md-8'", 100);
setTimeout("document.getElementById('col943').className = 'col-xl-8 col-lg-6 col-md-4'", 100);

var spn = '<i class="fa-li fa fa-spinner fa-spin mL20 mT15"></i>';
var guageList = new Array();
guageList[guageList.length] = new Array('Overall',    2800, 0, '');
guageList[guageList.length] = new Array('Facility',   2400, 0, '');
guageList[guageList.length] = new Array('Production', 2000, 0, '');
guageList[guageList.length] = new Array('Hvac',       1600, 0, '');
guageList[guageList.length] = new Array('Lighting',   1200, 0, '');
guageList[guageList.length] = new Array('Water',      800,  0, '');
guageList[guageList.length] = new Array('Waste',      400,  0, '');
var reloadComplete = false;
var g = 0;

$(document).ready(function() {
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    
    function guageLoad(g) {
        var guageUrl = "/frame/animate/guage/"+guageList[g][2]+"";
        if (guageList[g][0] == 'Overall') guageUrl += "?size=180";
        if (document.getElementById("guageFrame"+guageList[g][0]+"") && document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
            document.getElementById("guageFrame"+guageList[g][0]+"").style.display='none';
            document.getElementById("guageFrame"+guageList[g][0]+"").src=guageUrl;
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").style.display='none';
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML=guageList[g][3];
            $("#guageFrame"+guageList[g][0]+"").fadeIn(3000);
            $("#efficGuageTxt"+guageList[g][0]+"").fadeIn(3000);
        }
        return true;
    }
    
    function chkGuageReload(baseUrl) {
        if (reloadComplete) {
            for (g = 0; g < guageList.length; g++) {
                if (document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
                    document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML = spn;
                    setTimeout(guageLoad, guageList[g][1], g);
                }
            }
            return true;
        }
        setTimeout(function() { chkGuageReload(baseUrl); }, 400);
        return false;
    }
    
    function reloadGuages() {
        reloadComplete = false;
        var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}"+gatherFilts();
        $("#guageReloader").load(""+baseUrl+"&eff=Overall&loadAll=1");
        setTimeout(function() { chkGuageReload(baseUrl); }, 400);
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
@if (!$isPast) setTimeout(function() { $("#futureForm").load("/ajax/future-look?ps={{ $psid }}"); }, 3000); @endif
    
});

</script>

@if ($GLOBALS["SL"]->REQ->has('print'))
    <script type="text/javascript"> setTimeout("window.print()", 3000); </script>
@endif

@endif