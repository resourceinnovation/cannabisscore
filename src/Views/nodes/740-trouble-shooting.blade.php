<!-- generated from resources/views/vendor/cannabisscore/nodes/740-trouble-shooting.blade.php -->
@if (isset($importResult)) {!! $importResult !!} @endif
<div class="pull-right mT5 mB5"><a href="?refresh=1" class="btn btn-default btn-sm">Re-Calculate All PowerScores</a></div>
<a href="#lgtNew" class="pull-right">New Lighting Sub-Score Calculations &darr;</a>
<h2>Troubleshooting</h2>

@if (isset($hvcChk) && sizeof($hvcChk) > 0)
    <h3 class="slBlueDark">HVAC Efficiency Score</h3>
    <p class="slGrey"><b>New Formula #2:</b> multiplying each growth stage's HVAC kWh/score estimate
    by the stage's canopy square feet divided by total canopies square feet (including drying), 
    and taking the sum of all stages.</p>
    <p class="slGrey"><b>New Formula #1:</b> multiplying each growth stage's HVAC kWh/score estimate
    by the stage's canopy square feet, and taking the sum of all stages.</p>
    <table class="table table-striped mT10">
    <tr>
        <th>Score ID# - <i>ALL COMPLETE INDOOR PowerScores</i></th>
        <th>Current (Simple)<div class="slGrey fPerc66">kWh/sqft</div></th>
        <th>Weighted Stages <nobr>(Formula #2)</nobr><div class="slGrey fPerc66">kWh/sqft</div></th>
        <th colspan=2 >Sum of Stages <nobr>(Formula #1)</nobr>
            <div class="slGrey fPerc66"><span class="pull-right">MWh</span> kWh</div></th>
    </tr>
    <tr class="slBlueDark">
        <th>Calculation Average</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($hvcAvg[0], 3) }} kWh/sqft</th>
        <th>{{ number_format($hvcAvg[2]) }} kWh/sqft</th>
        <th>{{ number_format($hvcAvg[1]) }} kWh</th>
        <th>{{ $GLOBALS["SL"]->sigFigs($hvcAvg[1]/1000, 3) }} MWh</th>
    </tr>
    @foreach ($hvcChk as $i => $ps)
        <tr>
        <td><a href="/calculated/u-{{ $lgtChk[$i][0]->PsID }}" target="_blank">#{{ $lgtChk[$i][0]->PsID }}</a> 
            @if (isset($lgtChk[$i][0]->PsName)) <span class="slGrey">{{ $lgtChk[$i][0]->PsName }}</span> @endif</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[0], 3) }} <span class="slGrey fPerc66">kWh/sqft</span></td>
        <td>{{ number_format($ps[2]) }} <span class="slGrey fPerc66">kWh/sqft</span></td>
        <td>{{ number_format($ps[1]) }} <span class="slGrey fPerc66">kWh</span></td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[1]/1000, 3) }} <span class="slGrey fPerc66">MWh</span></td>
        </tr>
    @endforeach
    </table>
@endif

@if (isset($lgtChk) && sizeof($lgtChk) > 0)
    <div class="nodeAnchor"><a name="lgtNew"></a></div>
    <h3 class="slBlueDark">Lighting Efficiency Score</h3>
    <i class="slGrey">Evan and I were talking about how we take the average of the 1-4 growing stages for which we have
    lighting info.</i>
    <table class="table table-striped mT10">
    <tr>
    <th>Score ID# - <i>ALL COMPLETE INDOOR PowerScores</i></th>
    <th>Average (Current)<div class="slGrey fPerc66">of growth stages' kWh/sqft</div></th>
    <th>Average W/sqft<div class="slGrey fPerc66">of growth stages' W/sqft</div></th>
    <th>Raw kWh/sqft<div class="slGrey fPerc66">kWh added up over sqft added up</div></th>
    <th>Raw W/sqft<div class="slGrey fPerc66">W added up over sqft added up</div></th>
    </tr>
    <tr class="slBlueDark">
    <th>Calculation Average</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($lgtAvg[0], 3) }} kWh/sqft</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($lgtAvg[3], 3) }} W/sqft</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($lgtAvg[1], 3) }} kWh/sqft</th>
    <th>{{ $GLOBALS["SL"]->sigFigs($lgtAvg[2], 3) }} W/sqft</th>
    </tr>
    @foreach ($lgtChk as $i => $ps)
        <tr>
        <td><a href="/calculated/u-{{ $lgtChk[$i][0]->PsID }}" target="_blank">#{{ $ps[0]->PsID }}</a> 
            @if (isset($ps[0]->PsName)) <span class="slGrey">{{ $ps[0]->PsName }}</span> @endif</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[0]->PsEfficLighting, 3) }} <span class="slGrey fPerc66">kWh/sqft</span></td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[7], 3) }} <span class="slGrey fPerc66">W/sqft</span></td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[4], 3) }} <span class="slGrey fPerc66">kWh/sqft</span></td>
        <td>{{ $GLOBALS["SL"]->sigFigs($ps[5], 3) }} <span class="slGrey fPerc66">W/sqft</span></td>
        </tr>
    @endforeach
    </table>
@endif

@if (isset($logOne) && trim($logOne) != '')
    <div class="jumbotron">{!! $logOne !!}</div>
@endif

@if (isset($chk1) && $chk1->isNotEmpty())
    <h3 class="slBlueDark fC">Found {{ sizeof($chk1) }} Wiped Records!</h3>
    <table class="table table-striped">
    @foreach ($chk1 as $i => $chk)
        <tr>
        <td><pre style="width: 300px;">{!! print_r($chk) !!}</pre></td>
        <td> @if (isset($chks2[$i])) <pre style="width: 500px;">{!! print_r($chks2[$i]) !!}</pre> @endif </td>
        </tr>
    @endforeach
    </table>
@endif

<?php /*
<iframe id="calcRefresh" src="" class="w100 h50"></iframe>
<script type="text/javascript">
@forelse ($allScoreIDs as $i => $id)
    setTimeout("document.getElementById('calcRefresh').src='/calculated/u-{{ $id->PsID }}?refresh=1'", {{ (1+($i*7000)) }});
@empty
@endforelse
</script>
*/ ?>