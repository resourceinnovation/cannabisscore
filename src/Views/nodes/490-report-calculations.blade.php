<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

<div id="efficScoreMainTitle" class="row">
    <div class="col-12 mLn15">
        <h3 id="efficBlockOverTitle" class="m0 scoreBig">
            @if ($isPast) Calculated PowerScore 
            @else PowerScore Estimate 
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
            <table border=0 cellpadding="0" cellspacing="0" 
                id="scoreCalcsTbl" class="table tableScore w100 m0">
            
<?php $cnt = 0; ?>
            <tr class="scoreRowHeader"><td>
                <h5 class="slBlueDark pL15">Energy Efficiency</h5>
            </td></tr>

        @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
            && $sessData["powerscore"][0]->ps_effic_facility > 0
            && isset($sessData["powerscore"][0]->ps_effic_non_electric) 
            && $sessData["powerscore"][0]->ps_effic_non_electric > 0)
            <?php $cnt++; ?>
            <tr id="scoreRowFacAll" @if ($cnt%2 == 0) class="rw2" @endif >
            <td><div class="efficBlock">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                        <nobr>Facility
                        <a id="hidivBtnCalcsFacAll" class="hidivBtn fPerc66" 
                            href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_fac_all)
                            && $sessData["powerscore"][0]->ps_effic_fac_all > 0.00001) 
                            {{ $GLOBALS["SL"]->sigFigs(
                                $sessData["powerscore"][0]->ps_effic_fac_all
                            , 3) }}
                        @else 0 @endif
                        <nobr>kBtu / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacAll">
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
                        <nobr>Electric Facility
                        <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_facility)) 
                            {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_facility, 3) }}
                        @else 0 @endif
                        <nobr>kBtu / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacility">
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

        @if (isset($sessData["powerscore"][0]->ps_effic_non_electric) 
            && $sessData["powerscore"][0]->ps_effic_non_electric > 0)
            <?php $cnt++; ?>
            <tr id="scoreRowFacNon" @if ($cnt%2 == 0) class="rw2" @endif >
            <td><div class="efficBlock">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                        <nobr>Non-Electric Facility
                        <a id="hidivBtnCalcsFacNon" class="hidivBtn fPerc66" 
                            href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_non_electric)
                            && $sessData["powerscore"][0]->ps_effic_non_electric > 0.00001) 
                            {{ $GLOBALS["SL"]->sigFigs(
                                $sessData["powerscore"][0]->ps_effic_non_electric
                            , 3) }}
                        @else 0 @endif
                        <nobr>kBtu / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacNon">
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
                        <nobr>Production
                        <a id="hidivBtnCalcsProdAll" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_prod_all)) 
                            {{ $GLOBALS["SL"]->sigFigs(
                                $sessData["powerscore"][0]->ps_effic_prod_all, 
                                3
                            ) }}
                        @else 0 @endif
                        <nobr>g / kBtu</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" 
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
                        <nobr>Electric Production
                        <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_production)) 
                            {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_production, 3) }}
                        @else 0 @endif
                        <nobr>g / kBtu</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreProduction">
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
                        <nobr>Non-Electric Production
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
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreProdNon">
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

        @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
            && $sessData["powerscore"][0]->ps_effic_lighting > 0)
            <?php $cnt++; ?>
            <tr id="scoreRowLight" @if ($cnt%2 == 0) class="rw2" @endif >
            <td><div class="efficBlock">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                        <nobr>Lighting Efficiency
                        @if ($sessData["powerscore"][0]->ps_effic_lighting > 0.00001)
                            <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                                ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                        @endif
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
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreLighting">
                        <iframe id="guageFrameLighting" class="guageFrame" src="" 
                            frameborder="0" width="190" height="30" ></iframe>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                        <div id="efficGuageTxtLighting" class="efficGuageTxt"></div>
                    </div>
                </div>
            @if ($sessData["powerscore"][0]->ps_effic_lighting > 0.00001)
                <div id="hidivCalcsLgt" class="scoreCalcs">
                    {!! $printEfficLgt !!}
                </div>
            @endif
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
                            {{ $GLOBALS["SL"]->sigFigs($GLOBALS["SL"]->cnvrtKwh2Kbtu(
                                $sessData["powerscore"][0]->ps_effic_hvac_orig
                            ), 3) }}
                        @else 0 @endif
                        <nobr>kBtu / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreHvac">
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

<?php $cnt = 0; ?>
            <tr class="scoreRowHeader"><td>
                <h5 class="slBlueDark pL15">Water Efficiency</h5>
            </td></tr>
            
        @if (isset($sessData["powerscore"][0]->ps_effic_water) 
            && $sessData["powerscore"][0]->ps_effic_water > 0)
            <?php $cnt++; ?>
            <tr id="scoreRowWater" @if ($cnt%2 == 0) class="rw2" @endif >
            <td><div class="efficBlock">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-6 efficHeadLabel">
                        <nobr>Water Efficiency
                        <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"
                            ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                    </div>
                    <div class="col-lg-2 col-md-2 col-6"><div class="efficHeadScore"><nobr>
                        @if (isset($sessData["powerscore"][0]->ps_effic_water)
                            && $sessData["powerscore"][0]->ps_effic_water > 0.000001) 
                            {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_water, 3) }}
                        @else 0 @endif
                        <nobr>gal / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWater">
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
        @endif

<?php $cnt = 0; ?>
            <tr class="scoreRowHeader"><td>
                <h5 class="slBlueDark pL15">Waste Efficiency</h5>
            </td></tr>
            
        @if (isset($sessData["powerscore"][0]->ps_effic_waste) 
            && $sessData["powerscore"][0]->ps_effic_waste > 0)
            <?php $cnt++; ?>
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
                            {{ $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_waste, 3) }}
                        @else 0 @endif
                        <nobr>lbs / sq ft</nobr>
                    </nobr></div></div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWaste">
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
        @endif
            
        @if (isset($noprints) && trim($noprints) != '')
            <tr><td>
                <div class="efficBlock" style="min-height: 30px; padding: 15px;">
                    <i>Not enough data provided to calculate {{ $noprints }} efficiency.</i>
                </div>
            </td></tr>
        @endif


        @if ($GLOBALS["SL"]->REQ->has('bonus'))

            <tr class="scoreRowHeader"><td>
                <h5 class="slBlueDark pL15">Bonus Points Earned</h5>
            </td></tr>
            <tr id="scoreRowBonusDLC"><td><div class="efficBlock">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-6">
                        <div class="pT5 pB15 pL15">
                            <img border="0" height="150" 
                                src="/cannabisscore/uploads/dlc-bonus-points.png">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="p20"></div>
                        <h5>Bringing Efficiency to Light&trade;</h5>
                        <p>
                            This facility flowers with <a target="_blank" 
                            href="https://www.designlights.org/">DLC lights</a>!
                        </p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                        <div class="p15"></div>
                        <div class="efficGuageTxt">+5</div>
                    </div>
                </div>
            </div></td></tr>
            <style>
                #nLabel974 .p30 { padding: 10px; }
            </style>

        @endif
        

            </table>
        
        </div>
        <div class="col-lg-3 col-md-12" id="psScoreOverall">
            
            <div id="efficGuageTxtOverall" class="efficGuageTxt"></div>
            <center><iframe id="guageFrameOverall" class="guageFrame" src="" 
                frameborder="0" width="180" height="120" ></iframe></center>
            <div id="efficGuageTxtOverall2"></div>
            <div id="efficGuageTxtOverall3" class="pT15">
                Come back to update and check your PowerScore 
                regularly to see how your rank changes as more 
                facilities benchmark their performance!
            </div>
            
        </div>
    </div>
        
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

@else
    <!-- Has Preview Param -->
@endif

<style>
table#scoreCalcsTbl tr {
    background: #ebeee7;
}
table#scoreCalcsTbl tr.rw2, table#scoreCalcsTbl tr.scoreRowHeader {
    background: #f5f5f3;
}
</style>