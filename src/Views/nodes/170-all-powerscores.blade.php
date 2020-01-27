<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores.blade.php -->
<div class="slCard nodeWrap">
<div class="row">
    <div class="col-8">
        <a href="?refresh=1"><h2 class="slBlueDark">
    @if (isset($GLOBALS["SL"]->x["partnerVersion"])
        && $GLOBALS["SL"]->x["partnerVersion"])
        @if (isset($usrInfo) && isset($usrInfo->company))
            {{ $usrInfo->company }}
        @else Largely Lumens, Inc. PowerScores
        @endif
    @else   
        @if ($nID == 808) NWPCC Data Import 
        @else Compare All PowerScores 
        @endif
    @endif
        </h2></a>
    <?php /* <pre>{!! print_r($usrInfo) !!}</pre> */ ?>
    </div><div class="col-4 taR"><div class="mTn10 pB10">
    @if (Auth::user()->hasRole('administrator|staff'))
        @if (!$GLOBALS["SL"]->REQ->has('review'))
            <a class="btn btn-secondary mT20 mR5" 
                href="/dash/compare-powerscores?review=1"
                >Under Review
            </a>
        @else 
            <a class="btn btn-secondary mT20 mR5" 
                href="/dash/compare-powerscores"
                >All Complete
            </a>
        @endif
        <a class="btn btn-secondary mT20 mR5" target="_blank"
            href="/dash/compare-powerscores?random=1" 
            >Get Random
        </a>
        <a class="btn btn-secondary mT20" 
            href="/dash/compare-powerscores?srt={{ $sort[0] 
            }}&srta={{ $sort[1] }}{{ $urlFlts }}&excel=1"
            ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
            Excel
        </a>
    @endif
    </div></div>
</div>
@if (isset($psFilters))
    @if (!$GLOBALS["SL"]->REQ->has('review')) 
        <div class="round20 row2 mB20 p15">
            {!! $psFilters !!}
        </div>
    @else <div></div>
    @endif
@elseif (isset($psFilter))
    <div class="mB5">
        <b class="mR20">{{ $allscores->count() }} Found</b>
        {!! $psFilter !!}
    </div>
@endif

{!! $allListings !!}

</div>

@if (isset($reportExtras))
    <div class="slCard nodeWrap">{!! $reportExtras !!}</div>
@endif


<style>
@if ($nID == 170) 
     #updateScoreFiltsBtn2, #updateScoreFiltsBtn3 { display: none; } 
@endif
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

