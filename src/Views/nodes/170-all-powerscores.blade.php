<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores.blade.php -->

<div class="slCard nodeWrap">
@if ($GLOBALS["SL"]->x["partnerLevel"] > 4
    || !isset($GLOBALS["SL"]->x["officialSet"]) 
    || !$GLOBALS["SL"]->x["officialSet"])
    <a class="btn btn-secondary pull-right" 
        href="?srt={{ $sort[0] }}&srta={{ 
        $sort[1] }}{{ $urlFlts }}&excel=1"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
        Excel
    </a>
@endif
    <a href="?refresh=1"><h2 class="slBlueDark">
    @if (isset($GLOBALS["SL"]->x["partnerVersion"])
        && $GLOBALS["SL"]->x["partnerVersion"])
        @if (isset($GLOBALS["SL"]->x["officialSet"]) 
            && $GLOBALS["SL"]->x["officialSet"])
            Ranked Data Set of PowerScores
        @elseif (isset($usrInfo) && isset($usrInfo->company))
            {{ $usrInfo->company }}
        @else Largely Lumens, Inc. PowerScores
        @endif
    @else   
        @if ($nID == 808) NWPCC Data Import 
        @else Ranked Data Set Individual Rankings
        @endif
    @endif
    </h2></a>

@if (isset($GLOBALS["SL"]->x["partnerVersion"])
    && $GLOBALS["SL"]->x["partnerVersion"])
    @if (isset($GLOBALS["SL"]->x["officialSet"]) 
        && $GLOBALS["SL"]->x["officialSet"])
        <p>
            This report lists the completed PowerScores used for
            analysis by RII. PowerScores completed through your 
            custom referral link may or may not appear here.
        </p>
    @else 
        {!! view('vendor.cannabisscore.inc-partner-ref-disclaim')->render() !!}
    @endif
@endif

    <?php /* <pre>{!! print_r($usrInfo) !!}</pre> */ ?>

    <div id="filtWrap">
    @if (isset($psFilters))
        @if (!$GLOBALS["SL"]->REQ->has('review')) 
            <div class="mT15 mB15">
                {!! $psFilters !!}
            </div>
        @endif
    @elseif (isset($psFilter))
        <div class="mT15 mB15">
            <b class="mR20">{{ $allscores->count() }} Found</b>
            {!! $psFilter !!}
        </div>
    @endif
    </div>

    <div class="row" id="dataSetWrap">
        <div class="col-lg-9 pT10">
    @if (Auth::user()->hasRole('administrator|staff'))
            @if (isset($unreviewedCnt) && $unreviewedCnt > 0)
                <a class="btn btn-danger mR5" href="?fltCmpl=556"
                    ><i class="fa fa-star-half-o mR3" aria-hidden="true"></i> 
                    New ({{ number_format($unreviewedCnt) }})
                </a>
            @endif
            @if (!$GLOBALS["SL"]->REQ->has('review'))
                <a class="btn btn-secondary mR5" 
                    href="/dash/compare-powerscores?review=1"
                    >Under Review
                </a>
            @else 
                <a class="btn btn-secondary mR5" 
                    href="/dash/compare-powerscores"
                    >All Complete
                </a>
            @endif
            <a class="btn btn-secondary mR5" target="_blank"
                href="/dash/compare-powerscores?random=1" 
                >Get Random
            </a>
    @else
        <style>
            #dataSetWrap { margin-top: -45px; }
        </style>
    @endif
        </div>
        <div class="col-lg-3 pT10">
            <select name="dataSet" id="dataSetID" 
                class="form-control psChageFilter ntrStp slTab"
                autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                <option DISABLED >Select columns to show
                    @if ($GLOBALS["SL"]->x["partnerLevel"] > 4)
                        (all in Excel export)
                    @endif </option>
                <option value="kpi" 
                    @if (!isset($dataSet) 
                        || in_array(trim($dataSet), ['', 'kpi'])) 
                        SELECTED
                    @endif >Facility & Production Indicators</option>
                <option value="lighting"
                    @if (isset($dataSet) && trim($dataSet) == 'lighting') 
                        SELECTED
                    @endif >Lighting Indicators</option>
                <option value="others"
                    @if (isset($dataSet) && trim($dataSet) == 'others') 
                        SELECTED
                    @endif >HVAC, Water, & Waste Indicators</option>
                <option value="totals"
                    @if (isset($dataSet) && trim($dataSet) == 'totals') 
                        SELECTED
                    @endif >Annual Totals</option>
            </select>
        </div>
    </div>
</div>

<div class="slCard nodeWrap">
    {!! $allListings !!}
    @if (isset($dataSet) && trim($dataSet) == 'lighting') 
        <b>HLPD<sub>MA</sub></b> equals all lighting wattage divided 
        by the sum of the flowering, veg, and mother canopy area.
    @endif
</div>

@if (isset($reportExtras))
    <div class="slCard nodeWrap">{!! $reportExtras !!}</div>
@endif

<style>
body { overflow-x: visible; }
#filtWrap { width: 100%; max-width: 100%; }
@media screen and (max-width: 768px) {
    #filtWrap { width: 100%; max-width: 370px; }
}
</style>


@if (isset($lgtCompetData))
<script type="text/javascript">

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 18;

var chartOptions = {
  scales: {
    yAxes: [{
      barPercentage: 0.8
    }],
    xAxes: [{
      ticks: {
          min: 0,
          max: 100
      }
    }]
  },
  elements: {
    rectangle: {
      borderSkipped: 'left'
    }
  },
  legend: {
    display: false
  }
};

@foreach ($lgtCompetData->dataLegend as $l => $leg)
    @if ($l < 4)

var chartData{{ $l }} = {
  label: '{{ $leg[1] }} ({{ $leg[2] }})',
  data: [ @foreach ($lgtCompetData->dataLines as $j => $dat) @if ($j > 0) , @endif {{ $dat->scores[$l] }} @endforeach ],
  backgroundColor: [
    'rgba(240, 123, 58, 0.8)', 'rgba(141, 198, 63, 0.8)' @for ($j = 2; $j < sizeof($lgtCompetData->dataLines); $j++) , 'rgba(130, 142, 89, 0.8)' @endfor
  ],
  borderColor: [
    'rgba(240, 123, 58, 1)', 'rgba(141, 198, 63, 1)' @for ($j = 2; $j < sizeof($lgtCompetData->dataLines); $j++) , 'rgba(130, 142, 89, 1)' @endfor
  ],
  borderWidth: 2,
  hoverBorderWidth: 0
};

var chartDiv{{ $l }} = document.getElementById("chartDiv{{ $l }}");
var barChart{{ $l }} = new Chart(chartDiv{{ $l }}, {
  type: 'horizontalBar',
  data: {
    labels: [ @foreach ($lgtCompetData->dataLines as $j => $dat) @if ($j > 0) , @endif "{{ str_replace('Customers of ', '', $dat->title) }}" @endforeach ],
    datasets: [chartData{{ $l }}]
  },
  options: {
    scales: {
      yAxes: [{
        barPercentage: 0.8
      }],
      xAxes: [{
        ticks: {
            autoSkip: true, 
            maxTicksLimit: 6
        }
      }]
    },
    elements: {
      rectangle: {
        borderSkipped: 'left'
      }
    },
    legend: {
      display: false
    },
    responsive: true,
    maintainAspectRatio: false
  }
});

    @endif
@endforeach

</script>
@endif

