<!-- generated from resources/views/vendor/cannabisscore/nodes/979-compare-lighting-manufacturers.blade.php -->
<div class="nodeAnchor"><a name="n979"></a></div>
<div id="node979" class="nodeWrap">

<div class="slCard mT15 mB30">
    <a class="btn btn-secondary btn-sm pull-right"
        href="#raw">&darr; Raw Data</a>
    <h3 class="slBlueDark">
        Competitive Performance <nobr>Dashboard for</nobr>
        <nobr>{{ $lightManuName }}</nobr>
    </h3>
    @if ($lightManuName == 'Largely Lumens, Inc.')
        <p><b class="red">
            If we had real data to show you here, 
            it might look something like the fake report below.
        </b></p>
    @endif
    <div class="row">
        <div class="col-md-4">
            <select name="growthStage" class="form-control" autocomplete="off"
                onChange="alert('Coming Soon!');">
                <option value="" SELECTED 
                    >Using your lights during any stage</option>
                <option value="flower" >Using your lights for flowering</option>
                <option value="veg" >Using your lights for vegetative</option>
                <option value="clone" >Using your lights for clones</option>
                <option value="mother" >Using your lights for mothers</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="fltFarm" id="filtFarmID" autocomplete="off" 
                class="form-control" 
                onChange="alert('Coming Soon!');">
                <option value="0" SELECTED >All farm types</option>
                <option value="143">Outdoor</option>
                <option value="144">Indoor</option>
                <option value="145">Greenhouse/Hybrid/Mixed Light</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="climateZone" class="form-control" autocomplete="off"
                onChange="alert('Coming Soon!');">
                <option value="" SELECTED >All climate zones</option>
                <option value="" >Hot-Humid</option>
                <option value="" >Mixed-Humid</option>
                <option value="" >Cold</option>
                <option value="" >Very Cold</option>
                <option value="" >Subarctic</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
    @foreach ($lgtCompetData->dataLegend as $l => $leg)
        <div class="slCard">
            <h5>{{ $leg[1] }} ( {{ $leg[2] }} )</h5>
            <canvas id="chartDiv{{ $l }}" width="100%"></canvas>
        </div>
        <div class="mB10">&nbsp;</div>
        </div>
        @if ($l > 0 && $l%2 == 1) </div><div class="row"> @endif
        <div class="col-lg-6">
    @endforeach
    </div>
</div>

<div class="nodeAnchor"><a name="raw"></a></div>
<p>&nbsp;</p>
<div class="row">
    <div class="col-lg-6">
        <div class="slCard">
            <div style="min-height: 84px;">
                <h5>{{ $lgtCompetData->dataLines[0]->title }}</h5>
            </div>
            <div class="row">
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ str_replace('Efficiency', '', $leg[1]) }}</nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ $GLOBALS["SL"]->sigFigs(
                        $lgtCompetData->dataLines[0]->scores[$l], 3) }}
                    <span class="fPerc66 slGrey">{{ $leg[2] }}</span></nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ round($lgtCompetData->dataLines[0]->ranks[$l]) }} 
                    {!! $GLOBALS["SL"]->numSupscript(
                        round($lgtCompetData->dataLines[0]->ranks[$l])) !!}
                    <span class="fPerc66 slGrey">percentile</span></nobr></p>
                @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="slCard">
            <div style="min-height: 84px;">
                <h5>{{ $lgtCompetData->dataLines[1]->title }}</h5>
            </div>
            <div class="row">
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ str_replace('Efficiency', '', $leg[1]) }}</nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ $GLOBALS["SL"]->sigFigs(
                        $lgtCompetData->dataLines[1]->scores[$l], 3) }} 
                    <span class="fPerc66 slGrey">{{ $leg[2] }}</span></nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ round($leg[3]) }} 
                    <span class="fPerc66 slGrey">scores compared</span></nobr></p>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mB10">&nbsp;</div>

<div class="row">
@foreach ($lgtCompetData->dataLines as $d => $dat)
    @if ($d > 1)
    <div class="col-lg-4">
        <div class="slCard">
            <div style="min-height: 84px;">
                <h5>{{ $dat->title }}</h5>
            </div>
            <div class="row">
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>
                        {{ str_replace('Efficiency', '', $leg[1]) }}
                    </nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>
                    {{ $GLOBALS["SL"]->sigFigs($dat->scores[$l], 3) }} 
                    <span class="fPerc66 slGrey">{{ $leg[2] }}</span>
                    </nobr></p>
                @endforeach
                </div>
                <div class="col-4">
                @foreach ($lgtCompetData->dataLegend as $l => $leg)
                    <p><nobr>{{ round($dat->ranks[$l]) }} {!! 
                    $GLOBALS["SL"]->numSupscript(round($dat->ranks[$l]))
                    !!} <span class="fPerc66 slGrey">percentile</span>
                    </nobr></p>
                @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

</div>

<div class="mB10">&nbsp;</div>
<div class="slCard">
    <p><b>Your Individual PowerScore Records:</b><br />
    @forelse ($yourPsIDs as $l => $psid) 
        @if ($l > 0) , @endif
        <a href="/calculated/read-{{ $psid }}" 
            target="_blank">#{{ $psid }}</a>
    @empty
    @endforelse
    </p>
</div>

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

@foreach ($lgtCompetData->dataLegend as $l => $leg)

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
    }
  }
});

@endforeach
</script>
