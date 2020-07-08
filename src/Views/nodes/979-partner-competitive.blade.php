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

    {!! view('vendor.cannabisscore.inc-partner-ref-disclaim')->render() !!}

    @if (isset($psFilters))
        <div class="mT30 mB15">
            {!! $psFilters !!}
        </div>
    @endif
</div>

@if ($totCnt == 0)
    <div class="row">
        <div class="col-lg-2 pB20"></div>
        <div class="col-lg-8 pB20">
            <div class="slCard">
                <h5>No completed records found.</h5>
                <p>
                  Submit a record to see comparative results:<br />
                  <a href="https://powerscore.resourceinnovation.org/start-for-{{ 
                      $GLOBALS['SL']->x['usrInfo']->slug 
                      }}">https://powerscore.resourceinnovation.org/start-for-{{ 
                      $GLOBALS['SL']->x['usrInfo']->slug }}</a>
                </p>
            </div>
        </div>
        <div class="col-lg-2 pB20"></div>
    </div>
@endif

@foreach ($dataLegend as $l => $leg)
    <div class="row">
        <div class="col-lg-2 pB20"></div>
        <div class="col-lg-8 pB20">
            <div class="slCard nodeWrap mB20">
                <h5>{{ $leg[2] }} ( {{ $leg[3] }} )</h5>
                <canvas id="chartDiv{{ $l }}" class="dataChart" width="100%"></canvas>
            </div>
        </div>
        <div class="col-lg-2 pB20"></div>
    </div>
@endforeach

</div> <!-- node979 -->

<script type="text/javasript">

function loadCharts() {

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
}
setTimeout("loadCharts()", 600);

</script>