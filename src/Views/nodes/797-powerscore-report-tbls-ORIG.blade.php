<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-report-tbls.blade.php -->
<div class="slWebReport">
<h1 class="slBlueDark">Final Report</h1>

<h4>
As of {{ date("F j, Y") }}, Resource Innovation Institute's <b class="slBlueDark">Cannabis PowerScore</b> 
has collected complete data for <b class="slBlueDark">{{ number_format(sizeof($allscores)) }} observations</b> 
of growers' annual production.
</h4>

<div class="p15">
<h3>Table 1: Lighting Power Density by Grow Type</h3>
<table border=0 class="table w100">
<tr>
    <th>&nbsp;</th>
    <th>kWh Lighting</th>
    <th>SQF in Lighting analysis</th>
    <th>Lighting kWh/SF of Canopy</th>
    <th># of Observations</th>
</tr>
<tr>
    <th>Indoor Only</th>
    <td>{{ number_format($allAvgs["typesX"][144]["sqfts"][0]*(
        $allAvgs["types"][144]["lgt"][1]/$allAvgs["types"][144]["lgt"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][144]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][144]["lgt"][1]/$allAvgs["types"][144]["lgt"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][144]["lgt"][0]) }}</td>
</tr>
<tr>
    <th>Greenhouse/Mixed</th>
    <td>{{ number_format($allAvgs["typesX"][145]["sqfts"][0]*(
        $allAvgs["types"][145]["lgt"][1]/$allAvgs["types"][145]["lgt"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][145]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][145]["lgt"][1]/$allAvgs["types"][145]["lgt"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][145]["lgt"][0]) }}</td>
</tr>
<tr>
    <th>Outdoor</th>
    <td>{{ number_format($allAvgs["typesX"][143]["sqfts"][0]*(
        $allAvgs["types"][143]["lgt"][1]/$allAvgs["types"][143]["lgt"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][143]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][143]["lgt"][1]/$allAvgs["types"][143]["lgt"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][143]["lgt"][0]) }}</td>
</tr>
<tr class="brdTop">
    <th>Aggregated</th>
    <td>{{ number_format($allAvgs["typesX"][0]["sqfts"][0]*($allAvgs["all"]["lgt"][1]/$allAvgs["all"]["lgt"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][0]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["lgt"][1]/$allAvgs["all"]["lgt"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["all"]["lgt"][0]) }}</td>
</tr>
</table>
</div>

<div class="p15">
<h3>Table 2: HVAC Power Density by Grow Type</h3>
<table border=0 class="table w100">
<tr>
    <th>&nbsp;</th>
    <th>HVAC, Pumping, etc.</th>
    <th>SQF in HVAC analysis</th>
    <th>HVAC, etc. kWh/SF of Canopy</th>
    <th># of Observations</th>
</tr>
<tr>
    <th>Indoor Only</th>
    <td>{{ number_format($allAvgs["typesX"][144]["sqfts"][0]*(
        $allAvgs["types"][144]["hvc"][1]/$allAvgs["types"][144]["hvc"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][144]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][144]["hvc"][1]/$allAvgs["types"][144]["hvc"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][144]["hvc"][0]) }}</td>
</tr>
<tr>
    <th>Greenhouse/Mixed</th>
    <td>{{ number_format($allAvgs["typesX"][145]["sqfts"][0]*(
        $allAvgs["types"][145]["hvc"][1]/$allAvgs["types"][145]["hvc"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][145]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][145]["hvc"][1]/$allAvgs["types"][145]["hvc"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][145]["hvc"][0]) }}</td>
</tr>
<tr>
    <th>Outdoor</th>
    <td>{{ number_format($allAvgs["typesX"][143]["sqfts"][0]*(
        $allAvgs["types"][143]["hvc"][1]/$allAvgs["types"][143]["hvc"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][143]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][143]["hvc"][1]/$allAvgs["types"][143]["hvc"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["types"][143]["hvc"][0]) }}</td>
</tr>
<tr class="brdTop">
    <th>Aggregated</th>
    <td>{{ number_format($allAvgs["typesX"][0]["sqfts"][0]*($allAvgs["all"]["hvc"][1]/$allAvgs["all"]["hvc"][0])) }}</td>
    <td>{{ number_format($allAvgs["typesX"][0]["sqfts"][0]) }}</td>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["hvc"][1]/$allAvgs["all"]["hvc"][0], 3) }}</td>
    <td>{{ number_format($allAvgs["all"]["hvc"][0]) }}</td>
</tr>
</table>
</div>

<div class="p15">
<h3>Table 3: Total Power Density by Grow Type</h3>
<table border=0 class="table w50">
<tr>
    <th>&nbsp;</th>
    <th>Total kWh / SF of Canopy</th>
</tr>
<tr>
    <th>Indoor Only</th>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][144]["fac"][1]/$allAvgs["types"][144]["fac"][0], 3) }}</td>
</tr>
<tr>
    <th>Greenhouse/Mixed</th>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][145]["fac"][1]/$allAvgs["types"][145]["fac"][0], 3) }}</td>
</tr>
<tr>
    <th>Outdoor</th>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["types"][143]["fac"][1]/$allAvgs["types"][143]["fac"][0], 3) }}</td>
</tr>
<tr class="brdTop">
    <th>Aggregated</th>
    <td>{{ $GLOBALS["SL"]->sigFigs($allAvgs["all"]["fac"][1]/$allAvgs["all"]["fac"][0], 3) }}</td>
</tr>
</table>
</div>

<p>
On a weighted average basis (square footage by canopy environment), demand for power was about 
{{ $GLOBALS["SL"]->sigFigs($allAvgs["nums"]["avgKwhSqft"], 3) }} kilowatt-hours per square foot. 
</p>
<p>
Overall <span class="slBlueDark">lighting represents 
{{ number_format(round(100*$allAvgs["nums"]["kwhTotPerc"])) }}
percent of the total power demand</span> in facility operations. 
The highest demand of lighting is in flowering rooms at
{{ number_format(round(100*$allAvgs["typesX"][0]["lgtkWh"][162]/$allAvgs["typesX"][0]["kWh"][0])) }}
percent. Vegetative rooms use
{{ number_format(round(100*$allAvgs["typesX"][0]["lgtkWh"][161]/$allAvgs["typesX"][0]["kWh"][0])) }}
percent of total facility electricity use. 
</p>
<p>
HVAC systems are estimated to use about 
{{ number_format(round(100*$allAvgs["nums"]["hvacTotPerc"])) }} 
percent of the total power consumption in the grow operations.
</p>

<div class="p15">
<h3>Table 6: Total annual kWh of electricity used for grow operations</h3>
<table border=0 class="table w50">
<tr>
    <th>&nbsp;</th>
    <th>KWH</th>
    <th>% of total</th>
</tr>
<tr class="slBlueDark">
    <th>Lighting, by room:</th>
    <td><b>{{ number_format($allAvgs["typesX"][0]["lgtkWh"][0]) }}</b></td>
    <td><b>{{ number_format(round(100*$allAvgs["nums"]["kwhTotPerc"])) }}%</b></td>
</tr>
@foreach ($areaTypesFilt as $nick => $area)
    <tr>
        <th><span class="mL20">{{ $nick }}</span></th>
        <td><span class="mL20">{{ number_format($allAvgs["typesX"][0]["lgtkWh"][$area]) }}</span></td>
        <td>{{ number_format(round(100*$allAvgs["typesX"][0]["lgtkWh"][$area]/$allAvgs["typesX"][0]["kWh"][0])) }}%</td>
    </tr>
@endforeach
<tr class="slBlueDark">
    <th>HVAC estimate</th>
    <td>{{ number_format(round($allAvgs["nums"]["hvacTotKwh"])) }}</td>
    <td>{{ number_format(round(100*$allAvgs["nums"]["hvacTotPerc"])) }}%</td>
</tr>
<tr class="slGrey">
    <th>Other & Unknowns</th>
    <td>{{ number_format(round($allAvgs["nums"]["othTotKwh"])) }}</td>
    <td>{{ number_format(round(100*$allAvgs["nums"]["othTotPerc"])) }}%</td>
</tr>
<tr class="brdTop slBlueDark">
    <th>Total</th>
    <td>{{ number_format($allAvgs["typesX"][0]["kWh"][0]) }}</td>
    <td>100%</td>
</tr>
</table>
</div>

<p>
For an indoor grow environment, the average lighting power density is 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["typesX"][144]["lgtkWh"][0]/$allAvgs["typesX"][144]["sqfts"][0], 3))}}
kilowatt-hours per square foot; the lowest level is 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["nums"]["144-kwh-sqf-min"], 3)) }}
kilowatt-hours per square foot; and highest level is about 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["nums"]["144-kwh-sqf-max"], 3)) }}
kilowatt-hours per square foot. For greenhouses/hybrid/mixed light, the average is 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["typesX"][145]["lgtkWh"][0]/$allAvgs["typesX"][145]["sqfts"][0], 3))}}
kilowatt-hours per square foot; the lowest is 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["nums"]["145-kwh-sqf-min"], 3)) }}
kilowatt-hour per square foot; and the highest is 
{{ number_format($GLOBALS["SL"]->sigFigs($allAvgs["nums"]["145-kwh-sqf-max"], 3)) }}
kilowatt-hours per square foot.
</p>
<p>
In general, an LED or fluorescent lamp uses about half the power as an HID. 
Flower rooms with high-efficient lamps have overall PowerScore rankings which are 
{{ round(100*abs($allAvgs["nums"]["led-ovr-perc"])) }}% 
@if ($allAvgs["nums"]["led-lgt-perc"] > 0) lower, @else higher, @endif and
lighting sub-scores (kWh/sqft) which are
{{ round(100*abs($allAvgs["nums"]["led-lgt-perc"])) }}% 
@if ($allAvgs["nums"]["led-lgt-perc"] > 0) lower, @else higher, @endif and
facility sub-scores (kWh/sqft) which are 
{{ round(100*abs($allAvgs["nums"]["led-fac-perc"])) }}% 
@if ($allAvgs["nums"]["led-fac-perc"] > 0) lower. @else higher. @endif 
</p>
<p>
Table 7 shows additional details on the type of lighting technologies in each grow environment. 
In the flowering rooms for example, 
{{ round(100*$allAvgs["nums"]["flowerHID"]) }}
percent of lighting was HID; 
{{ round(100*$allAvgs["avgs"]['162-165']["ovr"][0]/$allAvgs["has"][162]) }}
percent was fluorescent; and 
{{ round(100*$allAvgs["avgs"]['162-203']["ovr"][0]/$allAvgs["has"][162]) }}
percent LED.  Survey results show that on average, 1 LED lamp is used for every 
{{ number_format($allAvgs["lgtsq"]["l203"]/$allAvgs["lgtfx"]["l203"]) }}
square feet of grow environment. If using HID lighting, 
{{ number_format($allAvgs["lgtsq"]["l1"]/$allAvgs["lgtfx"]["l1"]) }}
square feet is used.
</p>

<div class="p15">
<h3>Table 7: Breakdown of sites and lighting technologies across the survey participants</h3>
<table class="table w100">
<tr>
    <th>&nbsp;</th>
    @foreach ($areaTypesFilt as $nick => $areaTypeID)
        <th>{{ $nick }}</th>
    @endforeach
</tr>
@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Light Types') as $def)
    <tr>
        <th>{{ $def->DefValue }}</th>
        @foreach ($areaTypesFilt as $nick => $areaTypeID)
            <td> @if ($allAvgs["avgs"][$areaTypeID . '-' . $def->DefID]["ovr"][0] > 0
                && $allAvgs["has"][$areaTypeID] > 0)
                {{ round(100*$allAvgs["avgs"][$areaTypeID . '-' 
                    . $def->DefID]["ovr"][0]/$allAvgs["has"][$areaTypeID]) }}%
                <sub class="slGrey">{{ number_format($allAvgs["avgs"][$areaTypeID . '-' 
                    . $def->DefID]["ovr"][0]) }}</sub>
            @else <span class="slGrey">0%</span> @endif </td>
        @endforeach
    </tr>
@endforeach
<tr>
    <th>No Lights</th>
    @foreach ($areaTypesFilt as $nick => $areaTypeID)
        <td> @if ($allAvgs["avgs"][$areaTypeID . '-0']["ovr"][0] > 0 && $allAvgs["has"][$areaTypeID] > 0)
            {{ round(100*$allAvgs["avgs"][$areaTypeID . '-0']["ovr"][0]/$allAvgs["has"][$areaTypeID]) }}%
                <sub class="slGrey">{{ number_format($allAvgs["avgs"][$areaTypeID . '-0']["ovr"][0]) }}</sub>
            @else <span class="slGrey">0%</span> @endif </td>
    @endforeach
</tr>
</table>
</div>

<div class="p15">
<h3>Table 10: Business Overview: Administrative Totals</h3>
<div class="row">
    <div class="col-6">
        <table class="table w100">
        <tr>
            <th> </th>
            <th>Total</th>
            <th>Percent</th>
        </tr>
        <tr>
            <th>Medical Licenses</th>
            <td>{{ $allAvgs["techs"][0][141] }}</td>
            <td>{{ round(100*$allAvgs["techs"][0][141]/sizeof($allscores)) }}%</td>
        </tr>
        <tr>
            <th>Recreational Licenses</th>
            <td>{{ $allAvgs["techs"][0][142] }}</td>
            <td>{{ round(100*$allAvgs["techs"][0][142]/sizeof($allscores)) }}%</td>
        </tr>
        </table>
    </div>
    <div class="col-6">
        <table class="table w100">
        <tr>
            <th>Total growing square feet</th>
            <th>Percent of total</th>
        </tr>
        @foreach ($allAvgs["typesX"] as $char => $typesX) @if ($char > 0)
            <tr>
                <td>{{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Greenhouse/Mixed', 
                    $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $char)) }}</td>
                <td> @if ($allAvgs["typesX"][0]["sqfts"][0] > 0) {{ 
                    number_format(round(100*$allAvgs["typesX"][$char]["sqfts"][0]/$allAvgs["typesX"][0]["sqfts"][0]))
                }}% @endif</td>
            </tr>
        @endif @endforeach
        </table>
    </div>
</div>


<div class="pT15 pB15 pL5 pR5">
<h3>Table 12: Energy Efficiency Interest</h3>
<table class="table w100">
<tr>
    <th> </th>
    <th class="brdRgt">Total</th>
    @forelse ($allAvgs["typesX"] as $char => $typesX)
        @if ($char > 0)
            <th>{{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Greenhouse/Mixed', 
                $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $char)) }}</th>
        @endif
    @empty
    @endforelse
</tr>
@forelse ($psContact as $fld => $name)
    @if ($fld != 'PsNewsletter')
    <tr>
        <th>{{ $name }}</th>
        <td class="brdRgt"> @if (isset($allAvgs["techs"][0][$fld]))
            {{ round(100*$allAvgs["techs"][0][$fld]/sizeof($allscores)) }}%
            <sub class="slGrey">{{ number_format($allAvgs["techs"][0][$fld]) }}</sub>
            @else <span class="slGrey">0%</span> @endif </td>
        @foreach ($allAvgs["typesX"] as $char => $typesX) @if ($char > 0)
            <td> @if (isset($allAvgs["techs"][$char][$fld]) && $allAvgs["types"][$char]["tot"] > 0)
                {{ round(100*$allAvgs["techs"][$char][$fld]/$allAvgs["types"][$char]["tot"]) }}%
                <sub class="slGrey">{{ number_format($allAvgs["techs"][$char][$fld]) }}</sub>
                @else <span class="slGrey">0%</span> @endif </td>
        @endif @endforeach
    </tr>
    @endif
@empty
@endforelse
</table>

</div>
