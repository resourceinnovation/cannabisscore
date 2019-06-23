<!-- generated from resources/views/vendor/cannabisscore/nodes/979-compare-lighting-manufacturers.blade.php -->
<div class="nodeAnchor"><a name="n976"></a></div>
<div id="node398" class="nodeWrap">

<div class="slCard greenline">
    <h3 class="slBlueDark">
        Competitive Performance <nobr>Dashboard for</nobr>
        <nobr>Largely Lumens, Inc.</nobr>
    </h3>
    <div class="mTn20"><a href="#raw">&darr; Raw Data</a></div>
</div>
<div class="mB10">&nbsp;</div>

<div class="row">
    <div class="col-md-6">
    @foreach ($dataLegend as $i => $leg)
        <div class="slCard greenline">
            <h5>{{ $leg[0] }} ( {{ $leg[1] }} )</h5>
            <canvas id="chartDiv{{ $i }}" width="100%"></canvas>
        </div>
        <div class="mB10">&nbsp;</div>
        </div> @if ($i > 0 && $i%2 == 1) </div><div class="row"> @endif <div class="col-md-6">
    @endforeach
    </div>
</div>


<style>
</style>
<script type="text/javasript">

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

@foreach ($dataLegend as $i => $leg)

var chartData{{ $i }} = {
  label: '{{ $leg[0] }} ({{ $leg[1] }})',
  data: [ @foreach ($competitionData as $j => $dat) @if ($j > 0) , @endif {{ $dat[1][1][$i] }} @endforeach ],
  backgroundColor: [
    'rgba(240, 123, 58, 0.8)', 'rgba(141, 198, 63, 0.8)' @for ($j = 2; $j < sizeof($competitionData); $j++) , 'rgba(130, 142, 89, 0.8)' @endfor
  ],
  borderColor: [
    'rgba(240, 123, 58, 1)', 'rgba(141, 198, 63, 1)' @for ($j = 2; $j < sizeof($competitionData); $j++) , 'rgba(130, 142, 89, 1)' @endfor
  ],
  borderWidth: 2,
  hoverBorderWidth: 0
};

var chartDiv{{ $i }} = document.getElementById("chartDiv{{ $i }}");
var barChart{{ $i }} = new Chart(chartDiv{{ $i }}, {
  type: 'horizontalBar',
  data: {
    labels: [ @foreach ($competitionData as $j => $dat) @if ($j > 0) , @endif "{{ str_replace('Customers of ', '', $dat[0]) }}" @endforeach ],
    datasets: [chartData{{ $i }}]
  },
  options: chartOptions
});

@endforeach
</script>

<div class="nodeAnchor"><a name="raw"></a></div>
<p>&nbsp;</p>
<div class="row">
@foreach ($competitionData as $dat)
    @if ($dat[0] != 'Average')
    <div class="col-md-3">
        <div class="slCard greenline">
            <div style="min-height: 84px;"><h5>{{ $dat[0] }}</h5></div>
            <div class="row">
                <div class="col-6">
                    @foreach ($dataLegend as $i => $leg)
                        <p><nobr>{{ str_replace('Efficiency', '', $leg[0]) }}</nobr></p>
                    @endforeach
                </div>
                <div class="col-6">
                    @foreach ($dataLegend as $i => $leg)
                        <p><nobr>{{ $dat[1][0][$i] }} 
                            <span class="fPerc66 slGrey">{{ $leg[1] }}</span></nobr></p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
</div>