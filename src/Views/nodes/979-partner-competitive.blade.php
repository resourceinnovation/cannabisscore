<!-- generated from resources/views/vendor/cannabisscore/nodes/979-partner-competitive.blade.php -->
<div class="nodeAnchor"><a name="n979"></a></div>
<div id="node979">

<div class="slCard nodeWrap">
    <h3 class="slBlueDark">
        Competitive Performance
        @if (isset($GLOBALS["SL"]->x["partnerCompany"])
            && trim($GLOBALS["SL"]->x["partnerCompany"]) != '')
            for {{ $GLOBALS["SL"]->x["partnerCompany"] }}
        @endif
    </h3>
    <div class="row">
        <div class="col-md-4 pB15">
            <select name="fltClimate" id="fltClimateID"
                class="form-control" autocomplete="off"
                onchange="alert('Coming Soon');">
                <option value="0" 
                    @if (!isset($fltClimate) || intVal($fltClimate) <= 0) 
                        SELECTED 
                    @endif >All climate zones</option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltClimate) !!}
            </select>
        </div>

    @if ($GLOBALS["SL"]->x["partnerLevel"] >= 4)
        <div class="col-md-4 pB15">
            <select name="fltSize" id="filtSizeID" autocomplete="off" 
                class="form-control" autocomplete="off"
                onchange="alert('Coming Soon');">
                <option value="0" 
                    @if (!isset($fltSize) || intVal($fltSize) <= 0)
                        SELECTED 
                    @endif
                    >All farm sizes (sq ft of flowering canopy, annual average)
                </option>
            @foreach ($GLOBALS["SL"]->def->getSet('Indoor Size Groups') as $size)
                <option value="{{ $size->def_id }}"
                    @if ($fltSize == $size->def_id) SELECTED @endif
                    >{{ $size->def_value }}</option>
            @endforeach
            </select>
        </div>
    @endif

    </div>
</div>

@foreach ($dataLegend as $l => $leg)
    <div class="row">
        <div class="col-lg-8 pB20">
            <div class="slCard nodeWrap mB20">
                <h5>{{ $leg[2] }} ( {{ $leg[3] }} )</h5>
                <canvas id="chartDiv{{ $l }}" class="dataChart" width="100%"></canvas>
            </div>
            <div class="col-lg-4 pB20">


            </div>
        </div>
    </div>
@endforeach


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

@foreach ($graphData as $l => $dat)

    var chartData{{ $l }} = {
      label: '{{ $dataLegend[$l][2] }} ({{ $dataLegend[$l][3] }})',
      data: [ @foreach ($farmTypes as $label => $farm) @foreach ($dat[1][$farm] as $v => $vals) @if ($v > 0 || $label != 'Indoor') , @endif {{ printf("%f", $vals[0]) }} @endforeach @endforeach ],
      backgroundColor: [
        @foreach ($farmTypes as $label => $farm) @foreach ($dat[1][$farm] as $v => $vals) @if ($v > 0 || $label != 'Indoor') , @endif @if (stripos($vals[2], 'Your') !== false) 'rgba(240, 123, 58, 0.8)' @else 'rgba(141, 198, 63, 0.8)' @endif @endforeach @endforeach
      ],
      borderColor: [
        @foreach ($farmTypes as $label => $farm) @foreach ($dat[1][$farm] as $v => $vals) @if ($v > 0 || $label != 'Indoor') , @endif @if (stripos($vals[2], 'Your') !== false) 'rgba(240, 123, 58, 1)' @else 'rgba(141, 198, 63, 1)' @endif @endforeach @endforeach
      ],
      borderWidth: 2,
      hoverBorderWidth: 0
    };

    var chartDiv{{ $l }} = document.getElementById("chartDiv{{ $l }}");
    var barChart{{ $l }} = new Chart(chartDiv{{ $l }}, {
      type: 'horizontalBar',
      data: {
        labels: [ @foreach ($farmTypes as $label => $farm) @foreach ($dat[1][$farm] as $v => $vals) @if ($v > 0 || $label != 'Indoor') , @endif "{{ $vals[2] }} ({{ number_format($vals[1]) }})" @endforeach @endforeach ],
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