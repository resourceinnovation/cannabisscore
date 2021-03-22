<!-- resources/views/vendor/cannabisscore/nodes/1040-partner-dashboard.blade.php -->

<div class="slCard nodeWrap">

    <h3>{{ $title }}</h3>

    <div class="row mT30">
        <div class="col-lg-6 pB10">
            <a href="/dash/partner-compare-powerscores" 
                class="btn btn-lg btn-primary btn-block"
                >Your Individual Scores</a>
        </div>
        <div class="col-lg-6 pB10">
            <p>
                List <b>all of your</b> individual PowerScores with 
                KPI averages, plus dozens of different filter options.
            </p>
        </div>
    </div>

    <div class="row mT30">
        <div class="col-lg-6 pB10">
            <a href="/dash/partner-compare-powerscore-averages" 
                class="btn btn-lg btn-secondary btn-block"
                >Your Score Averages</a>
        </div>
        <div class="col-lg-6 pB10">
            <p>
                Some basic aggregate calculations of 
                <b>all of your</b> completed PowerScores.
            </p>
        </div>
    </div>

    @if (isset($GLOBALS['SL']->x['usrInfo']))
        @if (isset($GLOBALS['SL']->x['usrInfo']->companies[0]->manus)
            && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->manus) > 0)
            @foreach ($GLOBALS['SL']->x['usrInfo']->companies[0]->manus as $manu)
                <div class="row mT30">
                    <div class="col-lg-6 pB10">
                        <a href="/dash/lighting-manufacturer-report" 
                        <?php /* /dash/competitive-performance?manu={{ 
                            urlencode($manu->name) }} */ ?>
                            class="btn btn-lg btn-secondary btn-block"
                            >{{ $manu->name }} Competitive Performance</a>
                    </div>
                    <div class="col-lg-6 pB10">
                        <p>
                            Compare the competitive advantage of growers 
                            who use <b>{{ $manu->name }}</b> 
                            during at least one growth stage.
                        </p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row mT30">
                <div class="col-lg-6 pB10">
                    <a href="/dash/competitive-performance"
                        class="btn btn-lg btn-secondary btn-block"
                        >Competitive Performance Report</a>
                </div>
                <div class="col-lg-6 pB10">
                    <p>
                        The competitive advantage of your facilities compared 
                        to the rest of the PowerScore Ranked Data Set.
                    </p>
                </div>
            </div>
        @endif
    @endif

    <!--
    <div class="row mT20">
        <div class="col-lg-6">
            <a href="/dash/powerscore-final-report" 
                class="btn btn-lg btn-primary btn-block mT10"
                >Written Report</a>
        </div>
        <div class="col-lg-6">
            <p>
                This report highlights some specific calculations and 
                tables in a written format using live data.
            </p>
        </div>
    </div>
    -->

</div>





<div class="slCard nodeWrap">
    <h3>Official <nobr>Data Set</nobr> of <nobr>Completed PowerScores</nobr></h3>
    <div class="row mT30">
        <div class="col-lg-6 pB10">
            <a href="/dash/partner-compare-ranked-powerscores" 
                class="btn btn-lg btn-primary btn-block"
                >Compare Individual Scores</a>
        </div>
        <div class="col-lg-6 pB10">
            <p>
                List all the individual PowerScores within the
                ranked data set used for analysis.
            </p>
        </div>
    </div>
@if ($GLOBALS["SL"]->x["partnerLevel"] >= 4)
    <div class="row mT30">
        <div class="col-lg-6 pB10">
            <a href="/dash/average-powerscores-lighting" 
                class="btn btn-lg btn-secondary btn-block"
                >Lighting Type Report</a>
        </div>
        <div class="col-lg-6 pB10">
            <p>
                Compare all KPI averages for different types
                of lighting within the ranked data set.
            </p>
        </div>
    </div>
    @if ($GLOBALS["SL"]->x["partnerLevel"] > 4)
        <div class="row mT30">
            <div class="col-lg-6 pB10">
                <a href="/dash/manufacturer-adoption" 
                    class="btn btn-lg btn-secondary btn-block"
                    >Lighting Manufacturer Adoption</a>
            </div>
            <div class="col-lg-6 pB10">
                <p>
                    Counts the total PowerScores within the ranked data set
                    which use specific lighting manufacturers, by growth stage.
                </p>
            </div>
        </div>
        <div class="row mT30">
            <div class="col-lg-6 pB10">
                <a href="/dash/lighting-manufacturer-report" 
                    class="btn btn-lg btn-secondary btn-block"
                    >Lighting Manufacturer Report</a>
            </div>
            <div class="col-lg-6 pB10">
                <p>
                    Compare all lighting manufacturers KPI 
                    averages within the ranked data set.
                </p>
            </div>
        </div>
    @else
        <p>
            <a href="https://resourceinnovation.org/joinwithus/" target="_blank"
                >More data analysis is available with higher membership levels.</a>
        </p>
    @endif
@else
    <p>
        <a href="https://resourceinnovation.org/joinwithus/" target="_blank"
            >More data analysis is available with higher membership levels.</a>
    </p>
@endif
</div>



@if (sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
    && trim($GLOBALS['SL']->x['usrInfo']->companies[0]->slug) != ''
    && (!$GLOBALS['SL']->x['usrInfo']->companies[0]->manus 
        || sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->manus) == 0))
    <div class="nodeAnchor"><a name="refLinks"></a></div>
    <div class="slCard nodeWrap">
        <h3>Your Company's Referral Link</h3>
        <p>
            Give this link to your employees or clients, 
            and you can track and compare their PowerScores:
        </p>
        <div class="row">
            <div class="col-lg-10 pB10">
                <input id="prtnerRefURL" type="text" class="form-control" 
                    value="https://powerscore.resourceinnovation.org/start-for-{{ 
                    $GLOBALS['SL']->x['usrInfo']->companies[0]->slug }}">
            </div>
            <div class="col-lg-2 pB10">
                <a href="javascript:;" class="btn btn-secondary btn-block"
                    onClick="copyRefLink();"
                    ><i class="fa fa-files-o mR3" aria-hidden="true"></i> Copy</a>
            </div>
        </div>
        <div id="prtnerRefURLalert" class="fR"></div>
        <div class="fC"></div>

        <p>&nbsp;</p>
        <h3>Facility Referral Links</h3>
        <p>
            You also have the option to create different referral 
            links for each facility managed by your company. 
            You can filter your reporting by facility.
        </p>
@if (isset($GLOBALS['SL']->x['usrInfo'])
    && sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
    && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
    @foreach ($GLOBALS['SL']->x['usrInfo']->companies[0]->facs as $i => $fac)
        <div class="pT20 @if ($i%2 == 0) row2 @endif ">
            <div class="row">
                <div class="col-lg-4 pT0 pB10">
                    <h4 class="mTn5 mB0 slBlueDark">{{ $fac->name }}</h4>
                </div>
                <div class="col-lg-6 pB10">
                    <input id="facility{{ $i }}URL" type="text" class="form-control" 
                        value="https://powerscore.resourceinnovation.org/start-for-{{ 
                        $fac->slug }}">
                </div>
                <div class="col-lg-2 pB10">
                    <a href="javascript:;" class="btn btn-secondary btn-block"
                        onClick="copyFacLink({{ $i }});"
                        ><i class="fa fa-files-o mR3" aria-hidden="true"></i> Copy</a>
                </div>
            </div>
            <div id="facility{{ $i }}URLalert" class="fR mB5"></div>
            <div class="fC"></div>
        </div>
    @endforeach
@else
    <p class="slGrey">No facilities created.</p>
@endif

    <?php /* check for Write or Owner permissions for the Company */ ?>
        <a href="/dash/manage-company-facilities" class="btn btn-secondary mT30"
            >Manage Company's Facilities</a>

    </div>
@endif


