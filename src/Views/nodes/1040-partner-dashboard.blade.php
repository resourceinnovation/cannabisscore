<!-- resources/views/vendor/cannabisscore/nodes/1040-partner-dashboard.blade.php -->

<div class="slCard nodeWrap">

    <h3>{{ $title }}</h3>

    @if ($company != '')
        <div class="row mT30">
            <div class="col-lg-6 pB10">
                <a href="/dash/partner-compare-powerscores" 
                    class="btn btn-lg btn-primary btn-block"
                    >Your Individual Scores</a>
            </div>
            <div class="col-lg-6 pB10">
                <p>
                    List <b>all of your</b> individual PowerScores with 
                    sub-score averages, plus dozens of different filter options.
                </p>
            </div>
        </div>
    @endif

    <div class="row mT30">
        <div class="col-lg-6 pB10">
            <a href="/dash/partner-compare-powerscore-averages" 
                class="btn btn-lg btn-primary btn-block"
                >Your Score Averages</a>
        </div>
        <div class="col-lg-6 pB10">
            <p>
                Some basic aggregate calculations of 
                <b>all of your</b> completed PowerScores.
            </p>
        </div>
    </div>

    @if (isset($usrInfo))
        @if (isset($usrInfo->manufacturers)
            && sizeof($usrInfo->manufacturers) > 0)
            @foreach ($usrInfo->manufacturers as $manu)
                <div class="row mT30">
                    <div class="col-lg-6 pB10">
                        <a href="/dash/competitive-performance?manu={{ 
                            urlencode($manu->manu_name) }}" 
                            class="btn btn-lg btn-primary btn-block"
                            >{{ $manu->manu_name }} Competitive Performance</a>
                    </div>
                    <div class="col-lg-6 pB10">
                        <p>
                            Compare the competitive advantage of growers 
                            who use <b>{{ $manu->manu_name }}</b> 
                            during at least one growth stage.
                        </p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row mT30">
                <div class="col-lg-6 pB10">
                    <a href="/dash/competitive-performance" 
                        class="btn btn-lg btn-primary btn-block"
                        >Competitive Performance Report</a>
                </div>
                <div class="col-lg-6 pB10">
                    <p>
                        The competitive advantage your growers compared 
                        to the rest of the PowerScore data set.
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


@if (isset($usrInfo->slug) && trim($usrInfo->slug) != '')
    <div class="slCard nodeWrap">
        <h3>Your Custom Referral Link</h3>
        <p>
            Give this link to your employees or clients, 
            and you can track and compare their PowerScores:
        </p>
        <div class="row">
            <div class="col-lg-2 pB10">
                <a href="javascript:;" class="btn btn-primary btn-block"
                    onClick="copyClip('prtnerRefURL'); 
                    document.getElementById('prtnerRefURLalert').innerHTML += 'Copied. ';"
                    ><i class="fa fa-files-o mR3" aria-hidden="true"></i> Copy</a>
            </div>
            <div class="col-lg-10 pB10">
                <input id="prtnerRefURL" type="text" class="form-control" 
                    value="https://powerscore.resourceinnovation.org/start-for-{{ $usrInfo->slug }}">
            </div>
        </div>
        <div id="prtnerRefURLalert">

        </div>

    </div>
@endif