<!-- generated from resources/views/vendor/cannabisscore/nodes/1008-powerscore-calculations-mockup.blade.php -->

<div id="efficScoreMainTitle" class="row">
    <div class="col-12 mLn15">
        <h3 id="efficBlockOverTitle" class="m0 scoreBig">
            Calculated PowerScore
        </h3> Indoor #3737,
        <nobr>Mockup, MI, </nobr>
        <nobr>Climate Zone 5A</nobr>
    </div>
</div>

<div id="bigScoreWrap">

<div id="scoreCalcsWrap" class="row">
    <div id="scoreCalcsWrapLeft" class="col-lg-9 col-md-12">
        <table border="0" cellpadding="0" cellspacing="0" class="table tableScore w100 m0">
        <tbody>

        <tr class="scoreRowHeader"><td>
            <h3 style="margin: 0px 0px -10px -10px;">
            @if ($GLOBALS["SL"]->REQ->has('v'))
                ENERGY<sup>*</sup>
            @else
                ENERGY & CARBON
            @endif
            </h3>
        </td></tr>
        
        <tr id="scoreRowFac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Facility
                    <a id="hidivBtnCalcsFac" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    236 <nobr>kBtu / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreFacility">
                    <iframe id="guageFrameFacility" class="guageFrame" src="/frame/animate/meter/95/1?bg=" frameborder="0" width="190" height="30" style=""></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtFacility" class="efficGuageTxt" style="">95<sup>th</sup> percentile</div></div>
            </div>
            <div id="hidivCalcsFac" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = 125,000 Total Annual kWh
                    <div class="pL10">/ 530 Square Feet of Flowering Canopy</div>
                </div>
            </div>
        </div></td></tr>
    
        <tr id="scoreRowProd"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Production
                    <a id="hidivBtnCalcsProd" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    54.4
                    <nobr>g / kBtu</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreProduction">
                    <iframe id="guageFrameProduction" class="guageFrame" src="/frame/animate/meter/98/2?bg=" frameborder="0" width="190" height="30" style=""></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtProduction" class="efficGuageTxt" style="">98<sup>th</sup> percentile</div></div>
            </div>
            <div id="hidivCalcsProd" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = 6,804 Annual Grams of Dried Flower Produced
                    <div class="pL10">/ 125,000 Total Annual Btu</div>
                </div>
            </div>
        </div></td></tr>
    
    @if (!$GLOBALS["SL"]->REQ->has('v'))

        <tr id="scoreRowLight"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Lighting
                        <a id="hidivBtnCalcsLgt" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    15.8 <nobr>W / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreLighting">
                    <iframe id="guageFrameLighting" class="guageFrame" src="/frame/animate/meter/55/4?bg=" frameborder="0" width="190" height="30" style=""></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtLighting" class="efficGuageTxt" style="">55<sup>th</sup> percentile</div></div>
            </div>
            <div id="hidivCalcsLgt" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = ( (Mother & Clones <nobr>630 W</nobr> / <nobr>98 sq ft )</nobr> 
                         <nobr>x 12% grow area</nobr> 
                         <div class="pL10 slGrey">
                         + ( (Veg <nobr>3,150 W</nobr> / <nobr>220 sq ft )</nobr> 
                         <nobr>x 26% grow area</nobr>  <br>
                         + ( (Flower <nobr>9,600 W</nobr> / <nobr>530 sq ft )</nobr> 
                         <nobr>x 63% grow area</nobr>
                         </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = <nobr>Mother & Clones 0.743 W / sq ft</nobr> 
                         <div class="pL10 slGrey">
                         + <nobr>Veg 3.71 W / sq ft</nobr>  <br>
                         + <nobr>Flower 11.3 W / sq ft</nobr>
                         </div>
                    </div></div>
                </div>
                <div class="pL10 pT15 slGrey">
                    Mother & Clones: <nobr> 2 fixtures x 315 W </nobr><br>  
                    Veg: <nobr> 10 fixtures x 315 W </nobr><br>  
                    Flower: <nobr> 16 fixtures x 600 W </nobr><br>
                </div>
            </div>
        </div></td></tr>
    
        <tr id="scoreRowHvac"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>HVAC
                    <a id="hidivBtnCalcsHvac" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    91.3
                    <nobr>kBtu / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreHvac">
                    <iframe id="guageFrameHvac" class="guageFrame" src="/frame/animate/meter/47/3?bg=" frameborder="0" width="190" height="30" style=""></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel"><div id="efficGuageTxtHvac" class="efficGuageTxt" style="">47<sup>th</sup> percentile</div></div>
            </div>
            <div id="hidivCalcsHvac" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = ( (Mother & Clones <nobr>115 kWh / sq ft</nobr> ) 
                         <nobr>x 12% grow area</nobr> 
                         <div class="pL10 slGrey">
                         + ( (Veg <nobr>115 kWh / sq ft</nobr> ) 
                         <nobr>x 26% grow area</nobr>  <br>
                         + ( (Flower <nobr>77 kWh / sq ft</nobr> ) 
                         <nobr>x 63% grow area</nobr>
                         </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = <nobr>Mother & Clones 13.3 kWh / sq ft</nobr> <div class="pL10 slGrey">
                        + <nobr>Veg 29.8 kWh / sq ft</nobr>  <br>
                        + <nobr>Flower 48.1 kWh / sq ft</nobr>
                    </div>
                    </div></div>
                </div>
            </div>
        </div></td></tr>

        <tr class="scoreRowHeader"><td>
            <h5 class="slBlueDark">Non-grid Power</h5>
        </td></tr>
    
        <tr id="scoreRowPropane"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Propane Use
                    <a id="hidivBtnCalcsPropane" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    20 <nobr>% of BTUs</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScorePropane">
                    <iframe id="guageFramePropane" class="guageFrame" 
                        src="/frame/animate/meter/23/2?bg=" frameborder="0" 
                        width="190" height="30"></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtPropane" class="efficGuageTxt">
                        23<sup>rd</sup> percentile
                    </div>
                </div>
            </div>
            <div id="hidivCalcsPropane" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = 25 Total Annual Propane BTUs (from gallons)
                    <div class="pL10">/ 125 Total Annual BTUs</div>
                </div>
            </div>
        </div></td></tr>
    
        <tr id="scoreRowSolar"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Solar Use
                    <a id="hidivBtnCalcsSolar" class="hidivBtn fPerc66" href="javascript:;"
                        ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    70 <nobr>% of BTUs</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreSolar">
                    <iframe id="guageFrameSolar" class="guageFrame" 
                        src="/frame/animate/meter/97/2?bg=" frameborder="0" 
                        width="190" height="30"></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtSolar" class="efficGuageTxt">
                        97<sup>th</sup> percentile
                    </div>
                </div>
            </div>
            <div id="hidivCalcsSolar" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = 87.5 Total Annual Solar BTUs (from kWh)
                    <div class="pL10">/ 125 Total Annual BTUs</div>
                </div>
            </div>
        </div></td></tr>
        
        <tr id="scoreRowCarbon"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Carbon
                    <a id="hidivBtnCalcsCarbon" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    1.17 <nobr>lbs / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreCarbon">
                    <iframe id="guageFrameCarbon" class="guageFrame" 
                    src="/frame/animate/meter/65/2?bg=ebeee7" frameborder="0" 
                    width="190" height="30"></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtCarbon" class="efficGuageTxt">
                        65<sup>th</sup> percentile
                    </div>
                </div>
            </div>
            <div id="hidivCalcsCarbon" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = (410 lbs Electricity Carbon
                    <div class="pL10 slGrey">
                    + 100 lbs Propane Carbon<br />
                    + 110 lbs Other Carbon Directly Added)<br />
                    / 530 Square Feet of Flowering Canopy
                    </div>
                </div>
            </div>
        </div></td></tr>

    @endif
    
        <tr class="scoreRowHeader"><td>
            <h3 style="margin: 10px 0px -10px -10px;">WATER</h3>
        </td></tr>
        
        <tr id="scoreRowWater"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Water Efficiency
                    <a id="hidivBtnCalcsWater" class="hidivBtn fPerc66" href="javascript:;"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    2.65
                    <nobr>gallons / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWater">
                    <iframe id="guageFrameWater" class="guageFrame" src="/frame/animate/meter/52/2?bg=ebeee7" frameborder="0" width="190" height="30" style=""></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtWater" class="efficGuageTxt">
                        52<sup>nd</sup> percentile
                    </div>
                </div>
            </div>
            <div id="hidivCalcsWater" class="scoreCalcs">
                <div class="row">
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = ( (Mother & Clones <nobr>50 Gallons</nobr> / <nobr>98 sq ft )</nobr> 
                        <nobr>x 12% grow area</nobr> 
                        <div class="pL10 slGrey">
                         + ( (Veg <nobr>700 Gallons</nobr> / <nobr>220 sq ft )</nobr> 
                         <nobr>x 26% grow area</nobr>  <br>
                         + ( (Flower <nobr>1500 Gallons</nobr> / <nobr>530 sq ft )</nobr> 
                         <nobr>x 63% grow area</nobr>
                         </div>
                    </div></div>
                    <div class="col-md-6 col-sm-12"><div class="pL10 slGrey">
                         = <nobr>Mother & Clones 0.059 Gallons / sq ft</nobr> 
                         <div class="pL10 slGrey">
                         + <nobr>Veg 0.825 Gallons / sq ft</nobr>  <br>
                         + <nobr>Flower 1.77 Gallons / sq ft</nobr>
                         </div>
                    </div></div>
                </div>
            </div>
        </div></td></tr>
    
        <tr class="scoreRowHeader"><td>
            <h3 style="margin: 10px 0px -10px -10px;">WASTE</h3>
        </td></tr>
        
        <tr id="scoreRowWaste"><td><div class="efficBlock">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6 efficHeadLabel">
                    <nobr>Waste Efficiency
                    <a id="hidivBtnCalcsWaste" class="hidivBtn fPerc66" href="javascript:;"
                    ><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadScore">
                    0.113 <nobr>lbs / sq ft</nobr>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuage" id="psScoreWaste">
                    <iframe id="guageFrameWaste" class="guageFrame" 
                    src="/frame/animate/meter/6/2?bg=f5f5f3" frameborder="0" width="190" height="30"></iframe>
                </div>
                <div class="col-lg-3 col-md-3 col-6 efficHeadGuageLabel">
                    <div id="efficGuageTxtWaste" class="efficGuageTxt">
                        6<sup>nd</sup> percentile
                    </div>
                </div>
            </div>
            <div id="hidivCalcsWaste" class="scoreCalcs">
                <div class="pL10 slGrey">
                    = 60 Annual Pounds of Green/Plant Waste
                    <div class="pL10">/ 530 Square Feet of Flowering Canopy</div>
                </div>
            </div>
        </div></td></tr>
        
        </tbody></table>

    @if ($GLOBALS["SL"]->REQ->has('v'))
        <div class="bgWht" style="padding: 10px 5px; z-index: 100;">
        * Electricity only. <br />
        For a complete analysis of your energy and carbon footprint, 
        <nobr>upgrade to PowerScore Pro.</nobr>
        </div>
    @endif
    
    </div>
    <div class="col-lg-3 col-md-12" id="psScoreOverall">
        
        <div id="efficGuageTxtOverall" class="efficGuageTxt" style="">
            <div id="efficBlockOverGuageTitle"><h5>Overall: Leader</h5></div>
            <div class="efficGuageTxtOverall4">
            Your farm's overall performance within the data set of greenhouse/ hybrid/ mixed light farms in the U.S. and Canada:
            </div>
        </div>
        <center><iframe id="guageFrameOverall" class="guageFrame" src="/frame/animate/guage/82?size=180" frameborder="0" width="180" height="120" style=""></iframe></center>
        <div id="efficGuageTxtOverall2"><center>
            <h1 class="m0 scoreBig">82<sup>nd</sup></h1><b>percentile</b>
        </center></div>

        @if (!$GLOBALS["SL"]->REQ->has('v'))
            <div class="mT20 mB20"></div>
            <div class="row">
                <div class="col-3">
                    <i class="fa fa-certificate" aria-hidden="true" 
                        style="font-size: 50px;"></i>
                </div>
                <div class="col-9">
                    <h5>DLC Approved Lighting</h5>
                </div>
            </div>
        @endif

        <div id="efficGuageTxtOverall3"><br />
            Come back to check your PowerScore again soon, because 
            your rank will change as more farms see how they stack up!
        </div>
        
    </div>
</div>

</div> <!-- end bigScoreWrap -->
<div id="guageReloader" class="disNon"> </div>


<div id="treeWrap973" class="container"
    style="background: #00763c; position: relative; z-index: 100;">
    <div class="fC"></div>
    <div id="node973" class="nodeWrap w100">
        <div class="nodeHalfGap"></div>
        <div id="node973kids" class="disBlo">
            <div id="blockWrap974" class="w100">
                <div id="node974" class="nodeWrap w100">
                    <div class="nodeHalfGap"></div>
                    <div id="nLabel974" class="nPrompt" style="color: #FFF;">
                        <h5 class="mT0">Become an RII member and empower yourself with the ability to compare your farm to:</h5>
                        <ul>
                        <li>Farms near you</li>
                        <li>Farms with similar environmental conditions</li>
                        <li>Farms with specific lighting types</li>
                        <li>Farms with certain attributes like vertical farming, mobile racking and automatic irrigation</li>
                        <li>Farms designated as Leaders</li>
                        </ul>
                    </div>
                    <div class="nodeHalfGap"></div>
                </div> <!-- end #node974 -->
            </div>
        </div> <!-- end #node973kids -->
    </div> <!-- end #node973 -->
</div>

<div id="node945" class="nodeWrap w100">
    <div class="nodeHalfGap"></div>
    <div id="nLabel945" class="nPrompt">
        <p><a href="https://powerscore.resourceinnovation.org/about-powerscore-calculations"
        >Read more about these PowerScore Calculations</a>, and please 
        <a href="https://resourceinnovation.org/contact" target="_blank">Contact Us</a> 
        with any feedback.</p>
    </div>
    <div class="nodeHalfGap"></div>
</div>

<div class="nodeHalfGap"></div>
<div class="nodeHalfGap"></div>
<div class="nodeHalfGap"></div>

<style>
#efficScoreMainTitle{margin:50px 0 20px -15px;color:#726658}
#scoreCalcsWrapLeft{background:#f5f5f3;padding:0;z-index:10}
table.tableScore tr,table.tableScore tr td{border:0 none}
table tr#scoreRowFac,table tr#scoreRowLight,table tr#scoreRowPropane,table tr#scoreRowCarbon,table tr#scoreRowWater,table tr#scoreRowWaste{background:#ebeee7}
table tr#scoreRowProd,table tr#scoreRowHvac,table tr#scoreRowSolar{background:#f5f5f3}
table tr.scoreRowHeader{background:#FFF}
table tr.scoreRowHeader h5{margin:10px 0 10px -10px}
.efficBlock{color:#636564;font-size:110%}
.efficHeadLabel{padding:15px 0 15px 30px}
.efficHeadScore{padding:15px 15px}
.efficHeadGuage{padding-top:13px}
.efficHeadGuageLabel{padding:15px 15px 15px 30px}
#psScoreOverall{background:#FFF;box-shadow:0 10px 15px #DEDEDE;position:relative;z-index:1}
.scoreCalcs{display:none;padding:10px 0 20px 30px}
#efficGuageTxtOverall{margin:0 0 15px 0}
#efficBlockOverGuageTitle{margin:15px 15px 5px 0;color:#726658}
#efficGuageTxtOverall2 h1{color:#9AC356;font-size:3rem;margin:0 0 -5px 0}
#efficGuageTxtOverall2 b{color:#726658;font-size:1.13rem}
#efficGuageTxtOverall3,#efficGuageTxtOverall4{font-size:80%}
#scoreRankFiltWrap{background:#01743d;color:#FFF;padding:20px 30px 30px 30px;margin:0 -15px;position:relative;z-index:10}
@media screen and (max-width:1200px {
#treeWrap492{max-width:96%}
.efficHeadScore{padding-left:30px}
}
@media screen and (max-width:992px){
#efficGuageTxtOverall2,#efficGuageTxtOverall3,#efficGuageTxtOverall4{font-size:100%}
#efficGuageTxtOverall3{margin-top:25px;margin-bottom:15px}
}
@media screen and (max-width:768px){
.efficHeadGuage{padding:0 0 10px 30px}
.efficHeadGuageLabel{padding:0 0 0 30px}
}

#node973kids { margin-top: -50px; }
#nLabel974 { color: #FFF; padding: 0px 30px; }

</style>