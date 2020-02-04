<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

<div id="efficScoreMainTitle" class="row">
    <div class="col-12 mLn15">
        <h3 id="efficBlockOverTitle" class="m0 scoreBig">
        @if ($isPast) Calculated PowerScore @else PowerScore Estimate @endif </h3>
        @if (isset($sessData["powerscore"][0]->ps_characterize))
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $sessData["powerscore"][0]->ps_characterize) }}
            <input type="hidden" id="filtFarmID" name="filtFarm" value="{{ 
                $sessData['powerscore'][0]->ps_characterize }}">
        @endif #{{ $psid }},
        <nobr>
        @if (isset($sessData["powerscore"][0]->ps_zip_code)) 
            {{ ucwords(strtolower($GLOBALS["SL"]->states->getZipProperty(
                $sessData["powerscore"][0]->ps_zip_code
            ))) }},
        @endif
        @if (isset($sessData["powerscore"][0]->ps_state)) 
            {{ $sessData["powerscore"][0]->ps_state }}, 
        @endif
        </nobr>
        @if (isset($sessData["powerscore"][0]->ps_ashrae)) 
            <nobr> @if ($sessData["powerscore"][0]->ps_ashrae != 'Canada') Climate Zone @endif 
            {{ $sessData["powerscore"][0]->ps_ashrae }}</nobr>
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
        
        @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
            && $sessData["powerscore"][0]->ps_effic_facility > 0)
        <tr id="scoreRowFac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Facility Efficiency
                    <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_facility)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_facility, 3) }}
                    @else 0 @endif
                    <nobr>kBtu / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacility">
                    <iframe id="guageFrameFacility" class="guageFrame" src="" 
                        frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtFacility" class="efficGuageTxt"></div>
                </div>
            </div>
            <div id="hidivCalcsFac" class="scoreCalcs">
            @if (isset($sessData["powerscore"][0]->ps_kwh) 
                && isset($totFlwrSqFt) 
                && $totFlwrSqFt > 0)
                <div class="pL10 slGrey">
                    = {{ number_format($GLOBALS["CUST"]
                        ->cnvrtKwh2Kbtu($sessData["powerscore"][0]->ps_kwh)) }} 
                        Total Annual kBtu &nbsp;&nbsp;/&nbsp;&nbsp;
                        {{ number_format($totFlwrSqFt) }} Square Feet of Flowering Canopy<br />
                </div>
                <div class="pL10 pT15 slGrey">
                    Total Annual kBtu = 3.412 x ( {{ 
                        number_format($sessData["powerscore"][0]->ps_kwh)
                        }} Total Kilowatt Hours )
                </div>
            @endif
            </div>
        </div></td></tr>
    @endif

    @if (isset($sessData["powerscore"][0]->ps_effic_production) 
        && $sessData["powerscore"][0]->ps_effic_production > 0)
        <tr id="scoreRowProd"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Production Efficiency
                    <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_production)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_production, 3) }}
                    @else 0 @endif
                    <nobr>g / kBtu</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreProduction">
                    <iframe id="guageFrameProduction" class="guageFrame" src="" frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtProduction" class="efficGuageTxt"></div>
                </div>
            </div>
            <div id="hidivCalcsProd" class="scoreCalcs">
            @if (isset($sessData["powerscore"][0]->ps_grams) 
                && isset($sessData["powerscore"][0]->ps_kwh))
                <div class="pL10 slGrey">
                    = {{ number_format($sessData["powerscore"][0]->ps_grams) }} 
                        Annual Grams of Dried Flower Produced
                        &nbsp;&nbsp;/&nbsp;&nbsp;
                        {{ number_format($GLOBALS["CUST"]->cnvrtKwh2Kbtu(
                            $sessData["powerscore"][0]->ps_kwh)) }} 
                        Total Annual kBtu
                </div>
                <div class="pL10 pT15 slGrey">
                    Total Annual kBtu = 3.412 x ( {{ 
                        number_format($sessData["powerscore"][0]->ps_kwh)
                        }} Total Kilowatt Hours )
                </div>
            @endif
            </div>
        </div></td></tr>
    @endif

    @if (isset($sessData["powerscore"][0]->ps_effic_hvac) 
        && $sessData["powerscore"][0]->ps_effic_hvac > 0)
        <tr id="scoreRowHvac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>HVAC Efficiency
                    <a id="hidivBtnCalcsHvac" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_hvac)
                        && $sessData["powerscore"][0]->ps_effic_hvac > 0.000001) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_hvac, 3) }}
                    @else 0 @endif
                    <nobr>kWh / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreHvac">
                    <iframe id="guageFrameHvac" class="guageFrame" src="" 
                        frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtHvac" class="efficGuageTxt"></div>
                </div>
            </div>
            <div id="hidivCalcsHvac" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                        @if (sizeof($printEfficHvac) > 0)
                            @foreach ($printEfficHvac as $i => $calcRow)
                                @if ($i == 0) 
                                    = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                                @else 
                                    + {!! $calcRow["eng"] !!} 
                                    @if ($i < sizeof($printEfficHvac)-1) <br /> @endif
                                @endif
                            @endforeach
                        @endif </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficHvac) > 0)
                        @foreach ($printEfficHvac as $i => $calcRow)
                            @if ($i == 0) 
                                = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else 
                                + {!! $calcRow["num"] !!} 
                                @if ($i < sizeof($printEfficHvac)-1) <br /> @endif
                            @endif
                        @endforeach </div>
                    @endif
                    </div></div>
                </div>
            </div>
        </div></td></tr>
    @endif

    @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
        && $sessData["powerscore"][0]->ps_effic_lighting > 0)
        <tr id="scoreRowLight"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Lighting Efficiency
                    @if ($sessData["powerscore"][0]->ps_effic_lighting > 0.000001)
                        <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    @endif
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
                        && $sessData["powerscore"][0]->ps_effic_lighting > 0.000001)
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_lighting, 3) }}
                    @else 0 @endif
                    <nobr>W / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreLighting">
                    <iframe id="guageFrameLighting" class="guageFrame" src="" 
                        frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtLighting" class="efficGuageTxt"></div>
                </div>
            </div>
        @if ($sessData["powerscore"][0]->ps_effic_lighting > 0.000001)
            <div id="hidivCalcsLgt" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                        @if (sizeof($printEfficLgt) > 0)
                            @foreach ($printEfficLgt as $i => $calcRow)
                                @if ($i == 0) 
                                    = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                                @else 
                                    + {!! $calcRow["eng"] !!} 
                                    @if ($i < sizeof($printEfficLgt)-1) <br /> @endif 
                                @endif
                            @endforeach
                        @endif </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                        @if (sizeof($printEfficLgt) > 0)
                            @foreach ($printEfficLgt as $i => $calcRow)
                                @if ($i == 0) 
                                    = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                                @else 
                                    + {!! $calcRow["num"] !!} 
                                    @if ($i < sizeof($printEfficLgt)-1) <br /> @endif
                                @endif
                            @endforeach </div>
                        @endif
                    </div></div>
                    @if ($GLOBALS["SL"]->REQ->has('print'))
                        <div class="col-md-6"><div class="slGrey">
                        @if (sizeof($printEfficLgt) > 0)
                            @foreach ($printEfficLgt as $i => $calcRow) 
                                {!! $calcRow["lgt"] !!}<br />
                            @endforeach
                        @endif
                        </div></div>
                    @endif
                </div>
            @if (!$GLOBALS["SL"]->REQ->has('print'))
                <div class="pL10 pT15 slGrey">
                @if (sizeof($printEfficLgt) > 0)
                    @foreach ($printEfficLgt as $i => $calcRow)
                        {!! $calcRow["lgt"] !!}<br />
                    @endforeach
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
        
    @if (isset($sessData["powerscore"][0]->ps_effic_water) 
        && $sessData["powerscore"][0]->ps_effic_water > 0)
        <tr id="scoreRowWater"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Water Efficiency
                    <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_water)
                        && $sessData["powerscore"][0]->ps_effic_water > 0.000001) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_water, 3) }}
                    @else 0 @endif
                    <nobr>gallons / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWater">
                    <iframe id="guageFrameWater" class="guageFrame" src="" 
                        frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtWater" class="efficGuageTxt"></div>
                </div>
            </div>
            <div id="hidivCalcsWater" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                        @if (sizeof($printEfficWtr) > 0)
                            @foreach ($printEfficWtr as $i => $calcRow)
                                @if ($i == 0) 
                                    = {!! $calcRow["eng"] !!} <div class="pL10 slGrey">
                                @else 
                                    + {!! $calcRow["eng"] !!} 
                                    @if ($i < sizeof($printEfficWtr)-1) <br /> @endif 
                                @endif
                            @endforeach
                        @endif </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                    @if (sizeof($printEfficWtr) > 0)
                        @foreach ($printEfficWtr as $i => $calcRow)
                            @if ($i == 0) 
                                = {!! $calcRow["num"] !!} <div class="pL10 slGrey">
                            @else 
                                + {!! $calcRow["num"] !!} 
                                @if ($i < sizeof($printEfficWtr)-1) <br /> @endif 
                            @endif
                        @endforeach </div>
                    @endif
                    </div></div>
                </div>
                <div class="pT20 fPerc66"><i>
                    Water score is not yet being factored into each Overal PowerScore.
                </i></div>
            </div>
        </div></td></tr>
    @endif

        <tr class="scoreRowHeader"><td>
            <h5 class="slBlueDark">Waste</h5>
        </td></tr>
        
    @if (isset($sessData["powerscore"][0]->ps_effic_waste) 
        && $sessData["powerscore"][0]->ps_effic_waste > 0)
        <tr id="scoreRowWaste"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Waste Efficiency
                    <a id="hidivBtnCalcsWaste" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    @if (isset($sessData["powerscore"][0]->ps_effic_waste)) 
                        {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_waste, 3) }}
                    @else 0 @endif
                    <nobr>lbs / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWaste">
                    <iframe id="guageFrameWaste" class="guageFrame" src="" 
                        frameborder="0" width="190" height="30" ></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtWaste" class="efficGuageTxt"></div></div>
            </div>
            <div id="hidivCalcsWaste" class="scoreCalcs">
            @if (isset($sessData["powerscore"][0]->ps_green_waste_lbs))
                <div class="pL10 slGrey">
                    = {{ number_format($sessData["powerscore"][0]->ps_green_waste_lbs) }} 
                        Annual Pounds of Green/Plant Waste
                        &nbsp;&nbsp;/&nbsp;&nbsp;
                        {{ number_format($totFlwrSqFt) }} Square Feet of Flowering Canopy
                </div>
                <div class="pT20 fPerc66"><i> 
                    Waste score is not yet being factored into each Overal PowerScore.
                </i></div>
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
        <center><iframe id="guageFrameOverall" class="guageFrame" src="" 
            frameborder="0" width="180" height="120" ></iframe></center>
        <div id="efficGuageTxtOverall2"></div>
        <div id="efficGuageTxtOverall3">
            Come back to check your PowerScore again soon, because 
            your rank will change as more farms see how they stack up!
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

@else
    <!-- Has Preview Param -->
@endif