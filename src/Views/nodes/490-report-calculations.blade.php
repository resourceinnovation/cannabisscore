<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->

<div id="efficScoreMainTitle" class="row">
    <div class="col-12 mLn15">
        <h3 id="efficBlockOverTitle" class="m0 scoreBig">
        @if ($isPast) 
            @if ($isFlow)
                Calculated PowerScore Flow
            @elseif ($isPro)
                Calculated PowerScore
            @else
                Calculated PowerScore Grow 
            @endif
        @else 
            PowerScore Estimate 
        @endif
        </h3>
    @if (isset($sessData["powerscore"][0]->ps_characterize))
        {{ $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', 
            $sessData["powerscore"][0]->ps_characterize) }}
        @if (!isset($GLOBALS["SL"]->x["indivFilters"])
            || !$GLOBALS["SL"]->x["indivFilters"])
            <input type="hidden" id="filtFarmID" name="filtFarm" value="{{ 
                $sessData['powerscore'][0]->ps_characterize }}">
        @endif
    @endif
        #{{ $psid }},
        
        <nobr>
    @if (isset($sessData["powerscore"][0]->ps_zip_code)
        && $usr 
        && ($usr->hasRole('administrator|staff')
            || ($usr->hasRole('partner') 
                && isset($GLOBALS["SL"]->x["partnerPSIDs"])
                && in_array($sessData["powerscore"][0]->ps_id, 
                    $GLOBALS["SL"]->x["partnerPSIDs"]))))
        {{ ucwords(strtolower($GLOBALS["SL"]->states->getZipProperty(
            $sessData["powerscore"][0]->ps_zip_code
        ))) }},
    @endif
    @if (isset($sessData["powerscore"][0]->ps_state)) 
        {{ $sessData["powerscore"][0]->ps_state }}, 
    @endif
        </nobr>
    @if (isset($sessData["powerscore"][0]->ps_ashrae)) 
        <nobr> 
        @if ($sessData["powerscore"][0]->ps_ashrae != 'Canada') Climate Zone @endif 
        {{ $sessData["powerscore"][0]->ps_ashrae }},</nobr>
    @endif
    @if (isset($sessData["powerscore"][0]->ps_start_month)
        && intVal($sessData["powerscore"][0]->ps_start_month) > 0
        && isset($scoreYearMonths[$sessData["powerscore"][0]->ps_id])
        && $scoreYearMonths[$sessData["powerscore"][0]->ps_id]["has"])
        <nobr>
        {{ date("F Y", mktime(0, 0, 0, 
            $scoreYearMonths[$sessData["powerscore"][0]->ps_id]["endMonth"], 1, 
            $scoreYearMonths[$sessData["powerscore"][0]->ps_id]["endYear"])) }}
        - 
        {{ date("F Y", mktime(0, 0, 0, 
            $scoreYearMonths[$sessData["powerscore"][0]->ps_id]["startMonth"], 1, 
            $scoreYearMonths[$sessData["powerscore"][0]->ps_id]["startYear"])) }}
        </nobr> 
    @endif
    </div>
</div>

<div class="nodeAnchor">
    <a name="calculations" id="calculations"></a>
</div>
<div id="bigScoreWrap">

    <div id="scoreCalcsWrap" class="row">
        <div id="scoreCalcsWrapLeft" class="col-lg-9 col-md-12">

            <table id="scoreCalcsTbl" class="table tableScore w100 m0">
            
        @if (!$isFlow)
            <?php $cnt = 0; ?>

                <tr class="scoreRowHeader"><td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <h5 class="slBlueDark pL10">Energy</h5>
                        </div>
                        <div class="col-lg-5 col-md-5 col-12"></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtCatEnr" class="efficGuageTxt pT15"></div>
                        </div>
                    </div>
                </div></td></tr>

            @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
                && $sessData["powerscore"][0]->ps_effic_facility > 0
                && isset($sessData["powerscore"][0]->ps_effic_fac_non) 
                && $sessData["powerscore"][0]->ps_effic_fac_non > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowFacAll" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Energy Efficiency
                            <a id="hidivBtnCalcsFacAll" class="hidivBtn fPerc66" 
                                href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_fac_all)
                                && $sessData["powerscore"][0]->ps_effic_fac_all > 0.00001) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_fac_all, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>kBtu / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreFacAll">
                            <iframe id="guageFrameFacAll" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtFacAll" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsFacAll" class="scoreCalcs">
                        {!! $printEfficFacAll !!}
                    </div>
                </div></td></tr>
            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
                && $sessData["powerscore"][0]->ps_effic_facility > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowFac" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Electric Efficiency
                            <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_facility)) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_facility, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>kBtu / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreFacility">
                            <iframe id="guageFrameFacility" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtFacility" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsFac" class="scoreCalcs">
                        {!! $printEfficFac !!}
                    </div>
                </div></td>
                </tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_fac_non) 
                && $sessData["powerscore"][0]->ps_effic_fac_non > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowFacNon" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Non-Electric Efficiency
                            <a id="hidivBtnCalcsFacNon" class="hidivBtn fPerc66" 
                                href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_fac_non)
                                && $sessData["powerscore"][0]->ps_effic_fac_non > 0.00001) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_fac_non, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>kBtu / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreFacNon">
                            <iframe id="guageFrameFacNon" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtFacNon" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsFacNon" class="scoreCalcs">
                        {!! $printEfficFacNon !!}
                    </div>
                </div></td></tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_production) 
                && $sessData["powerscore"][0]->ps_effic_production > 0
                && isset($sessData["powerscore"][0]->ps_effic_prod_non) 
                && $sessData["powerscore"][0]->ps_effic_prod_non > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowProdAll" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Energy Productivity
                            <a id="hidivBtnCalcsProdAll" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_prod_all)) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_prod_all, 
                                    3
                                ) }}
                            @else 0 
                            @endif
                            <nobr>g / kBtu</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" 
                            id="psScoreProdAll">
                            <iframe id="guageFrameProdAll" class="guageFrame" 
                                src="" frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtProdAll" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsProdAll" class="scoreCalcs">
                        {!! $printEfficProdAll !!}
                    </div>
                </div></td></tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_production) 
                && $sessData["powerscore"][0]->ps_effic_production > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowProd" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Electric Productivity
                            <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_production)) 
                                {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_production, 3) }}
                            @else 0 @endif
                            <nobr>g / kBtu</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreProduction">
                            <iframe id="guageFrameProduction" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtProduction" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsProd" class="scoreCalcs">
                        {!! $printEfficProd !!}
                    </div>
                </div></td></tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_prod_non) 
                && $sessData["powerscore"][0]->ps_effic_prod_non > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowProdNon" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Non-Electric Productivity
                            <a id="hidivBtnCalcsProdNon" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_prod_non)) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_prod_non, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>g / kBtu</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreProdNon">
                            <iframe id="guageFrameProdNon" class="guageFrame" 
                                src="" frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtProdNon" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsProdNon" class="scoreCalcs">
                        {!! $printEfficProdNon !!}
                    </div>
                </div></td></tr>

            @endif
        @endif
 
            @if (isset($sessData["powerscore"][0]->ps_effic_emis) 
                && $sessData["powerscore"][0]->ps_effic_emis > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowEmis" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Emissions Efficiency
                            <a id="hidivBtnCalcsEmis" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            {{ $GLOBALS["SL"]->sigFigs(
                                $sessData["powerscore"][0]->ps_effic_emis, 
                                3
                            ) }}
                            <nobr>kg CO<sub>2</sub>e / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreEmis">
                            <iframe id="guageFrameEmis" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtEmis" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsEmis" class="scoreCalcs">
                        {!! $printEfficEmis !!}
                    </div>
                </div></td></tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_emis_prod) 
                && $sessData["powerscore"][0]->ps_effic_emis_prod > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowEmisProd" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Emissions Productivity
                            <a id="hidivBtnCalcsEmisProd" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            {{ $GLOBALS["SL"]->sigFigs(
                                $sessData["powerscore"][0]->ps_effic_emis_prod, 
                                3
                            ) }}
                            <nobr>g / kg CO<sub>2</sub>e</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreEmisProd">
                            <iframe id="guageFrameEmisProd" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtEmisProd" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsEmisProd" class="scoreCalcs">
                        {!! $printEfficEmisProd !!}
                    </div>
                </div></td></tr>

            @endif

        @if ($isPro)
            @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
                && $sessData["powerscore"][0]->ps_effic_lighting > 0.00001)
                <?php $cnt++; ?>

                <tr id="scoreRowLight" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Lighting Efficiency
                            <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
                                && $sessData["powerscore"][0]->ps_effic_lighting > 0.00001)
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_lighting, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>kWh / day</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreLighting">
                            <iframe id="guageFrameLighting" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtLighting" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsLgt" class="scoreCalcs">
                        {!! $printEfficLgt !!}
                    </div>
                </div></td></tr>

            @endif

            @if (isset($sessData["powerscore"][0]->ps_effic_hvac) 
                && $sessData["powerscore"][0]->ps_effic_hvac > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowHvac" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>HVAC Efficiency
                            <a id="hidivBtnCalcsHvac" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_hvac_orig)
                                && $sessData["powerscore"][0]->ps_effic_hvac_orig > 0.00001) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $GLOBALS["SL"]->cnvrtKwh2Kbtu(
                                        $sessData["powerscore"][0]->ps_effic_hvac_orig
                                    ), 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>kBtu / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreHvac">
                            <iframe id="guageFrameHvac" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtHvac" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsHvac" class="scoreCalcs">
                        {!! $printEfficHvac !!}
                    </div>
                </div></td></tr>

            @endif
        @endif


        @if (!$isGrow
            && isset($sessData["powerscore"][0]->ps_effic_water) 
            && $sessData["powerscore"][0]->ps_effic_water > 0)
            <?php $cnt = 1; ?>

                <tr class="scoreRowHeader"><td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <h5 class="slBlueDark pL10">Water</h5>
                        </div>
                        <div class="col-lg-5 col-md-5 col-12"></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtCatWtr" class="efficGuageTxt pT15"></div>
                        </div>
                    </div>
                </div></td></tr>
                
                <tr id="scoreRowWater" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Water Efficiency
                            <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="efficHeadScore"><nobr>
                                @if (isset($sessData["powerscore"][0]->ps_effic_water)
                                    && $sessData["powerscore"][0]->ps_effic_water > 0.000001) 
                                    {{ $GLOBALS["SL"]->sigFigs(
                                        $sessData["powerscore"][0]->ps_effic_water, 
                                        3
                                    ) }}
                                @else 0 @endif
                                <nobr>gal / sq ft</nobr>
                            </nobr></div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreWater">
                            <iframe id="guageFrameWater" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtWater" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsWater" class="scoreCalcs">
                        {!! $printEfficWtr !!}
                    </div>
                </div></td></tr>

            @if (isset($sessData["powerscore"][0]->ps_effic_water_prod) 
                && $sessData["powerscore"][0]->ps_effic_water_prod > 0)
                <?php $cnt++; ?>

                <tr id="scoreRowWaterProd" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Water Productivity
                            <a id="hidivBtnCalcsWaterProd" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="efficHeadScore"><nobr>
                                @if (isset($sessData["powerscore"][0]->ps_effic_water_prod)
                                    && $sessData["powerscore"][0]->ps_effic_water_prod > 0.000001) 
                                    {{ $GLOBALS["SL"]->sigFigs(
                                        $sessData["powerscore"][0]->ps_effic_water_prod, 
                                        3
                                    ) }}
                                @else 0 @endif
                                <nobr>g / gal</nobr>
                            </nobr></div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreWaterProd">
                            <iframe id="guageFrameWaterProd" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtWaterProd" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsWaterProd" class="scoreCalcs">
                        {!! $printEfficWtrProd !!}
                    </div>
                </div></td></tr>

            @endif
        @endif

        @if (isset($sessData["powerscore"][0]->ps_effic_waste) 
            && $sessData["powerscore"][0]->ps_effic_waste > 0)
            <?php $cnt = 1; ?>

                <tr class="scoreRowHeader"><td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <h5 class="slBlueDark pL10">Waste</h5>
                        </div>
                        <div class="col-lg-5 col-md-5 col-12"></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtCatWst" class="efficGuageTxt pT15"></div>
                        </div>
                    </div>
                </div></td></tr>
                
                <tr id="scoreRowWaste" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Waste Efficiency
                            <a id="hidivBtnCalcsWaste" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_waste)) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_waste, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>lbs / sq ft</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreWaste">
                            <iframe id="guageFrameWaste" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtWaste" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsWaste" class="scoreCalcs">
                        {!! $printEfficWst !!}
                    </div>
                </div></td></tr>
                
            @if (isset($sessData["powerscore"][0]->ps_effic_waste_prod) 
                && $sessData["powerscore"][0]->ps_effic_waste_prod > 0)
                <?php $cnt++; ?>
                <tr id="scoreRowWasteProd" @if ($cnt%2 == 0) class="rw2" @endif >
                <td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                            <nobr>Waste Production
                            <a id="hidivBtnCalcsWasteProd" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        </div>
                        <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                            @if (isset($sessData["powerscore"][0]->ps_effic_waste_prod)) 
                                {{ $GLOBALS["SL"]->sigFigs(
                                    $sessData["powerscore"][0]->ps_effic_waste_prod, 
                                    3
                                ) }}
                            @else 0 @endif
                            <nobr>g / lbs</nobr>
                        </nobr></div></div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuage pL30" id="psScoreWasteProd">
                            <iframe id="guageFrameWasteProd" class="guageFrame" src="" 
                                frameborder="0" width="190" height="30" ></iframe>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div id="efficGuageTxtWasteProd" class="efficGuageTxt"></div>
                        </div>
                    </div>
                    <div id="hidivCalcsWasteProd" class="scoreCalcs">
                        {!! $printEfficWstProd !!}
                    </div>
                </div></td></tr>
            @endif
        @endif
            
            @if (isset($noprints) && trim($noprints) != '')
                <tr><td>
                    <div class="efficBlock" 
                        style="min-height: 30px; padding: 15px;">
                        <i>Not enough data provided to calculate 
                        {{ $noprints }} efficiency.</i>
                    </div>
                </td></tr>
            @endif

            @if ((isset($sessData["powerscore"][0]->ps_dlc_bonus) 
                    && intVal($sessData["powerscore"][0]->ps_dlc_bonus) > 0)
                || $GLOBALS["SL"]->REQ->has('bonus'))

                <tr class="scoreRowHeader"><td>
                    <h5 class="slBlueDark pL10">Bonus Points Earned</h5>
                </td></tr>
                <tr id="scoreRowBonusDLC"><td><div class="efficBlock">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-6">
                            <div class="pT5 pB15 pL15">
                                <img border="0" height="150" 
                                    src="/cannabisscore/uploads/dlc-bonus-points.png">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-6 pL30">
                            <h5>
                                Lighting solutions on DesignLights Consortium Qualified Products List
                            </h5>
                            <p>
                                This facility cultivates with lighting systems certified by 
                                <a target="_blank" 
                                    href="https://www.designlights.org/horticultural-lighting/search/"
                                    >DesignLights Consortium</a>, 
                                a third party standards organization.
                            </p>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                            <div class="p15"></div>
                            <div class="efficGuageTxt">
                                +{{ $sessData["powerscore"][0]->ps_dlc_bonus }}
                            </div>
                        </div>
                    </div>
                </div></td></tr>
                <style>
                    #nLabel974 .p30 { padding: 10px; }
                </style>

            @endif
        

            </table>


    @if ($canEdit)
        @if ($isFlow)
            <div class="p30">
                <a href="/start/calculator?go=pro&time=232&cpyFlow={{ 
                    $sessData['powerscore'][0]->ps_id }}-{{ 
                    $sessData['powerscore'][0]->ps_unique_str }}" 
                    class="btn btn-primary btn-xl btn-block"
                    >BENCHMARK MY PERFORMANCE</a>
            </div>
        @elseif ($isGrow)
            <div class="p30">
                <a href="/start/calculator?go=pro&time=232&cpyGrow={{ 
                    $sessData['powerscore'][0]->ps_id }}-{{ 
                    $sessData['powerscore'][0]->ps_unique_str }}" 
                    class="btn btn-primary btn-xl btn-block"
                    >BENCHMARK MY PERFORMANCE</a>
            </div>
        @endif
    @endif

        </div>
        <div class="col-lg-3 col-md-12" id="psScoreOverall">
            
        @if ($isPro)
            <div id="efficGuageTxtOverall" class="efficGuageTxt"></div>
            <center><iframe id="guageFrameOverall" class="guageFrame" src="" 
                frameborder="0" width="180" height="120" ></iframe></center>
            <div id="efficGuageTxtOverall2"></div>
            <div id="efficGuageTxtOverall3" class="pT15">
                Come back to update and check your PowerScore 
                regularly to see how your rank changes as more 
                facilities benchmark their performance!
            </div>
        @else
            <div id="efficBlockOverGuageTitle"><h5>
                Want to learn more about your facility's efficiency performance?
            </h5></div>
            <div id="efficGuageTxtOverall3" class="pT15">
            @if ($canEdit)
                @if ($isFlow)
                    Click the "Benchmark My Performance' button for a deeper 
                    understanding of how your facility's use of energy and 
                    waste compares to other facilities throughout North America.
                @else
                    Click the "Benchmark My Performance' button for a deeper 
                    understanding of how your facility's use of energy and 
                    water compares to other facilities throughout North America.
                @endif
            @else
                <center><a href="/start/calculator?new=1" class="btn btn-lg btn-primary"
                    >Calculate PowerScore</a></center>
            @endif
            </div>
        @endif
            
        </div>
    </div> <!-- scoreCalcsWrap.row -->
        
@if (!$GLOBALS["SL"]->REQ->has('print') && !$isPast)

    <p><br /><sup>*</sup>
    {!! view('vendor.cannabisscore.nodes.490-report-calculations-rank-about')->render() !!}
    </p><p>
    For future-looking PowerScore estimates, Facility and Production 
    are estimates purely based on the average results of real grow 
    years using similar combinations of technologies and strageties.
    </p>

@endif

</div> <!-- end bigScoreWrap -->
<div id="guageReloader" class="disNon"></div>


<style>
table#scoreCalcsTbl tr {
    background: #ebeee7;
}
table#scoreCalcsTbl tr.rw2, table#scoreCalcsTbl tr.scoreRowHeader {
    background: #f5f5f3;
}
</style>
