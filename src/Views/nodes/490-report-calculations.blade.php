<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))
<style>
{!! view('vendor.cannabisscore.nodes.490-report-calculations-css')->render() !!}
</style>

<div id="efficScoreMainTitle" class="row">
    <div class="col-12 mLn15">
        <h3 id="efficBlockOverTitle" class="m0 scoreBig">
        @if ($isPast) Calculated PowerScore @else PowerScore Estimate @endif </h3>
        @if (isset($sessData["PowerScore"][0]->PsCharacterize))
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $sessData["PowerScore"][0]->PsCharacterize) }}
            <input type="hidden" id="filtFarmID" name="filtFarm" value="{{ 
                $sessData['PowerScore'][0]->PsCharacterize }}">
        @endif #{{ $psid }},
        <nobr>
        @if (isset($sessData["PowerScore"][0]->PsZipCode)) 
            {{ ucwords(strtolower($GLOBALS["SL"]->states->getZipProperty($sessData["PowerScore"][0]->PsZipCode))) }},
        @endif
        @if (isset($sessData["PowerScore"][0]->PsState)) 
            {{ $sessData["PowerScore"][0]->PsState }}, 
        @endif
        </nobr>
        @if (isset($sessData["PowerScore"][0]->PsAshrae)) 
            <nobr> @if ($sessData["PowerScore"][0]->PsAshrae != 'Canada') Climate Zone @endif 
            {{ $sessData["PowerScore"][0]->PsAshrae }}</nobr>
        @endif
    </div>
</div>

<div id="bigScoreWrap">

<div id="scoreCalcsWrap" class="row">
    <div id="scoreCalcsWrapLeft" class="col-lg-9 col-md-12">
        <table border=0 cellpadding="0" cellspacing="0" 
            class="table tableScore w100 m0">
        
        <tr class="scoreRowHeader"><td>
            <h5 class="slBlueDark">Energy</h5>
        </td></tr>
        
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
        <tr id="scoreRowFac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Facility Efficiency
                    <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
                    @else 0 @endif
                    <nobr>kWh / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacility">
                    <iframe id="guageFrameFacility" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtFacility" class="efficGuageTxt"></div></div>
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
        </div></td></tr>
    @endif

    @if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
        <tr id="scoreRowProd"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Production Efficiency
                    <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
                    @else 0 @endif
                    <nobr>g / kWh</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreProduction">
                    <iframe id="guageFrameProduction" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtProduction" class="efficGuageTxt"></div></div>
            </div>
            <div id="hidivCalcsProd" class="scoreCalcs">
            @if (isset($sessData["PowerScore"][0]->PsGrams) && isset($sessData["PowerScore"][0]->PsKWH))
                <div class="pL10 slGrey">
                    = {{ number_format($sessData["PowerScore"][0]->PsGrams) }} Annual Grams of Dried Flower Produced
                        &nbsp;&nbsp;/&nbsp;&nbsp;
                        {{ number_format($sessData["PowerScore"][0]->PsKWH) }} Total Annual Kilowatt Hours
                </div>
            @endif
            </div>
        </div></td></tr>
    @endif

    @if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
        <tr id="scoreRowHvac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>HVAC Efficiency
                    <a id="hidivBtnCalcsHvac" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficHvac)
                        && $sessData["PowerScore"][0]->PsEfficHvac > 0.000001) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) }}
                    @else 0 @endif
                    <nobr>kWh / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreHvac">
                    <iframe id="guageFrameHvac" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtHvac" class="efficGuageTxt"></div></div>
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
            </div>
        </div></td></tr>
    @endif

    @if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
        <tr id="scoreRowLight"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Lighting Efficiency
                    @if ($sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                        <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    @endif
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficLighting) 
                        && $sessData["PowerScore"][0]->PsEfficLighting > 0.000001)
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }}
                    @else 0 @endif
                    <nobr>W / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreLighting">
                    <iframe id="guageFrameLighting" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtLighting" class="efficGuageTxt"></div></div>
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
        </div></td></tr>
    @endif

        <tr class="scoreRowHeader"><td>
            <h5 class="slBlueDark">Water</h5>
        </td></tr>
        
    @if (isset($sessData["PowerScore"][0]->PsEfficWater) && $sessData["PowerScore"][0]->PsEfficWater > 0)
        <tr id="scoreRowWater"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Water Efficiency
                    <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficWater)
                        && $sessData["PowerScore"][0]->PsEfficWater > 0.000001) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficWater, 3) }}
                    @else 0 @endif
                    <nobr>gallons / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWater">
                    <iframe id="guageFrameWater" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtWater" class="efficGuageTxt"></div></div>
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
        </div></td></tr>
    @endif

        <tr class="scoreRowHeader"><td>
            <h5 class="slBlueDark">Waste</h5>
        </td></tr>
        
    @if (isset($sessData["PowerScore"][0]->PsEfficWaste) && $sessData["PowerScore"][0]->PsEfficWaste > 0)
        <tr id="scoreRowWaste"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Waste Efficiency
                    <a id="hidivBtnCalcsWaste" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["PowerScore"][0]->PsEfficWaste)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficWaste, 3) }}
                    @else 0 @endif
                    <nobr>g / kWh</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWaste">
                    <iframe id="guageFrameWaste" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtWaste" class="efficGuageTxt"></div></div>
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
        </div></td></tr>
    @endif
        
    @if (isset($noprints) && trim($noprints) != '')
        <tr><td><div class="efficBlock" style="min-height: 30px; padding: 15px;">
            <i>Not enough data provided to calculate {{ $noprints }} efficiency.</i>
        </div></td></tr>
    @endif
    
        </table>
    
    </div>
    <div class="col-lg-3 col-md-12" id="psScoreOverall">
        
        <div id="efficGuageTxtOverall" class="efficGuageTxt"></div>
        <center><iframe id="guageFrameOverall" class="guageFrame" src="" frameborder="0" width="180" height="120" ></iframe></center>
        <div id="efficGuageTxtOverall2"></div>
        <div id="efficGuageTxtOverall3">
        Come back to check your PowerScore again soon, because your rank will change as more farms see how they stack up!
        </div>
        
    </div>
</div>


        
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
    
var spn = '<i class="fa-li fa fa-spinner fa-spin mL20 mT15"></i>';
var guageList = new Array();
guageList[guageList.length] = new Array('Overall',    2800, 0, '', '', '');
guageList[guageList.length] = new Array('Facility',   2400, 0, '', '', '');
guageList[guageList.length] = new Array('Production', 2000, 0, '', '', '');
guageList[guageList.length] = new Array('Hvac',       1600, 0, '', '', '');
guageList[guageList.length] = new Array('Lighting',   1200, 0, '', '', '');
guageList[guageList.length] = new Array('Water',      800,  0, '', '', '');
guageList[guageList.length] = new Array('Waste',      400,  0, '', '', '');
var reloadComplete = false;
var g = 0;

$(document).ready(function() {
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    
    function guageLoad(g) {
        var guageUrl = "/frame/animate/meter/"+guageList[g][2]+"/"+g+"?bg="+guageList[g][5]+"";
        if (guageList[g][0] == 'Overall') {
            guageUrl = "/frame/animate/guage/"+guageList[g][2]+"?size=180";
            if (document.getElementById("efficGuageTxt"+guageList[g][0]+"2")) {
                document.getElementById("efficGuageTxt"+guageList[g][0]+"2").innerHTML=guageList[g][4];
            }
        }
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