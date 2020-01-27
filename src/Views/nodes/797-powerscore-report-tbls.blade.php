<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-report-tbls.blade.php -->
<div class="slWebReport">

<div class="alert alert-danger fade in alert-dismissible show"
    style="padding: 10px 15px;">
    This report is an old work in progress, and contains errors.
    But we welcome <b>your</b> feedback on how to make these reports most useful.
</div>

<div class="slCard nodeWrap">
    <h2 class="slBlueDark">Written Report</h2>
    <h4>
    As of {{ date("F j, Y") }}, Resource Innovation Institute's 
    <b class="slBlueDark">Cannabis PowerScore</b> 
    has collected complete data for 
    <b class="slBlueDark">{{ number_format($allscores->count()) }} observations</b> 
    of growers' annual production.
    </h4>
    <p>
    Thus far,
    {{ round(100*$statMisc->getDatTot('ps_incentive_wants')/$allscores->count()) }}%
    of the survey respondents would like to work with their 
    utilities to increase the energy efficiency of their operations,
    and {{ round(100*$statMisc->getDatTot('ps_incentive_used')/$allscores->count()) 
    }}% have done so in the past.
    And a total of {{ 
    round(100*$statMisc->getDatTot('ps_consider_upgrade')/$allscores->count()) }}% 
    are considering upgrades in the next 12 months.
    </p>
</div>

<div class="slCard nodeWrap">
    <h3>Table 1: Lighting Power Density by Grow Type</h3>
    <table border=0 class="table w100">
    <tr>
        <th>&nbsp;</th>
        <th>kWh Lighting</th>
        <th>SQF in Lighting analysis</th>
        <th>Lighting kWh/SF of Canopy</th>
        <th># of Observations</th>
    </tr>
    @foreach ($farmTypes as $name => $defID)
        <tr>
            <th>{{ $name }}</th>
            <td>{{ number_format($statLgts->getDatTot('kWh', 'a' . $defID)) }}</td>
            <td>{{ number_format($statLgts->getDatTot('sqft', 'a' . $defID)) }}</td>
            <td>
                @if ($statLgts->getDatTot('sqft', 'a' . $defID) > 0) 
                    {{ $GLOBALS["SL"]->sigFigs(
                        $statLgts->getDatTot('kWh', 'a' . $defID)
                            /$statLgts->getDatTot('sqft', 'a' . $defID)
                    , 3) }}
                @else 0 
                @endif 
            </td>
            <td>{{ number_format($statMisc->getDatCnt('a' . $defID)) }}</td>
        </tr>
    @endforeach
    <tr class="brdTop">
        <th>Aggregated</th>
        <td>{{ number_format($statLgts->getDatTot('kWh')) }}</td>
        <td>{{ number_format($statLgts->getDatTot('sqft')) }}</td>
        <td>
            @if ($statLgts->getDatTot('sqft') > 0) 
                {{ $GLOBALS["SL"]->sigFigs(
                    $statLgts->getDatTot('kWh')/$statLgts->getDatTot('sqft')
                , 3) }} 
            @else 0 
            @endif 
        </td>
        <td>{{ number_format($statMisc->getDatCnt()) }}</td>
    </tr>
    </table>
</div>

<div class="slCard nodeWrap">
    <h3>Table 2: HVAC Power Density by Grow Type</h3>
    <table border=0 class="table w100">
    <tr>
        <th>&nbsp;</th>
        <th>HVAC, Pumping, etc.</th>
        <th>SQF in HVAC analysis</th>
        <th>HVAC, etc. kWh/SF of Canopy</th>
        <th># of Observations</th>
    </tr>
    @foreach ($farmTypes as $name => $defID)
        <tr>
            <th>{{ $name }}</th>
            <td><?php /* {{ number_format($statLgts->getDatTot('sqft', 'a' . $defID)
                *$statScor->tagTot["a"]["a" . $defID]["avg"]["row"][3]) }} */ ?></td>
            <td>{{ number_format($statLgts->getDatTot('sqft', 'a' . $defID)) }}</td>
            <td><?php /* {{ number_format($statScor->tagTot["a"]["a" . $defID]["avg"]["row"][3]) }} */ ?></td>
            <td>{{ number_format($statMisc->getDatCnt('a' . $defID)) }}</td>
        </tr>
    @endforeach
    <tr class="brdTop">
        <th>Aggregated</th>
        <td><?php /* {{ number_format($statLgts->getDatTot('sqft')*$statScor->tagTot["a"]["1"]["avg"]["row"][3]) }} */ ?></td>
        <td>{{ number_format($statLgts->getDatTot('sqft')) }}</td>
        <td><?php /* {{ number_format($statScor->tagTot["a"]["1"]["avg"]["row"][3]) }} */ ?></td>
        <td>{{ number_format($statMisc->getDatCnt()) }}</td>
    </tr>
    </table>
</div>

<div class="slCard nodeWrap">
    <h3>Table 3: Total Power Density by Grow Type</h3>
    <table border=0 class="table w50">
    <tr>
        <th>&nbsp;</th>
        <th>Total kWh / SF of Canopy</th>
    </tr>
    @foreach ($farmTypes as $name => $defID)
        <tr>
            <th>{{ $name }}</th>
            <td><?php /* {{ number_format(
                $statScor->tagTot["a"]["a" . $defID]["avg"]["row"][0]
            ) }} */ ?></td>
        </tr>
    @endforeach
    <tr class="brdTop">
        <th>Aggregated</th>
        <td><?php /* {{ number_format(
            $statScor->tagTot["a"]["1"]["avg"]["row"][0]
        ) }} */ ?></td>
    </tr>
    </table>
</div>

<div class="slCard nodeWrap">
    <p>
    On a weighted average basis (square footage by 
    canopy environment), demand for power was about 
    {{ $GLOBALS["SL"]->sigFigs($statLgts->getDatTot('kWh/sqft'), 3) }}
    kilowatt-hours per square foot. 
    </p>
    <p>
    Overall <span class="slBlueDark">lighting represents 
    {{ number_format(round(100*$statLgts->getDatTot('kWh')/$statMisc->getDatTot('kWh'))) }}
    percent of the total power demand</span> in facility operations. 
    The highest demand of lighting is in flowering rooms at
    {{ number_format(round(100*$statLgts->getDatTotFval('kWh', 'area', 162)/$statLgts->getDatTot('kWh'))) }}
    percent. Vegetative rooms use
    {{ number_format(round(100*$statLgts->getDatTotFval('kWh', 'area', 161)/$statLgts->getDatTot('kWh'))) }}
    percent of total facility electricity use. 
    </p>
    <p>
    HVAC systems are estimated to use about {{ round(100*$statMore["hvacTotPrc"]) }} percent 
    of the total power consumption in the grow operations.
    </p>
</div>

<div class="slCard nodeWrap">
    <h3>Table 4: Total annual kWh of electricity used for grow operations</h3>
    <table border=0 class="table w50">
    <tr>
        <th>&nbsp;</th>
        <th>KWH</th>
        <th>% of total</th>
    </tr>
    <tr class="slBlueDark">
        <th>Lighting, by room:</th>
        <td><b>{{ number_format($statLgts->getDatTot('kWh')) }}</b></td>
        <td><b>{{ number_format(round(100*$statLgts->getDatTot('kWh')/$statMisc->getDatTot('kWh'))) }}%</b></td>
    </tr>
    @foreach ($areaTypesFilt as $nick => $area)
        <tr>
            <th><span class="mL20">{{ $nick }}</span></th>
            <td><span class="mL20">{{ number_format($statLgts->getDatTotFval('kWh', 'area', $area)) }}</span></td>
            <td>{{ number_format(round(100*$statLgts->getDatTotFval('kWh', 'area', $area)/$statMisc->getDatTot('kWh'))) 
                }}%</td>
        </tr>
    @endforeach
    <tr class="slBlueDark">
        <th>HVAC estimate</th>
        <td>{{ number_format(round($statMore["hvacTotKwh"])) }}</td>
        <td>{{ number_format(round(100*$statMore["hvacTotPrc"])) }}%</td>
    </tr>
    <tr class="slGrey">
        <th>Other & Unknowns</th>
        <td>{{ number_format(round($statMore["othrTotKwh"])) }}</td>
        <td>{{ number_format(round(100*$statMore["othrTotPrc"])) }}%</td>
    </tr>
    <tr class="brdTop slBlueDark">
        <th>Total</th>
        <td>{{ number_format($statMisc->getDatTot('kWh')) }}</td>
        <td>100%</td>
    </tr>
    </table>
    
    <p>
    For an indoor grow environment, the average lighting power density is 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a144"]["avg"]["row"][2], 3)) }} */ ?>
    kilowatt-hours per square foot; the lowest level is 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a144"]["min"]["row"][2], 3)) }} */ ?>
    kilowatt-hours per square foot; and highest level is about 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a144"]["max"]["row"][2], 3)) }} */ ?>
    kilowatt-hours per square foot. For greenhouses/hybrid/mixed light, the average is 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a145"]["avg"]["row"][2], 3))}} */ ?>
    kilowatt-hours per square foot; the lowest is 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a145"]["min"]["row"][2], 3)) }} */ ?>
    kilowatt-hour per square foot; and the highest is 
    __
    <?php /* {{ number_format($GLOBALS["SL"]->sigFigs($statScor->tagTot["a"]["a145"]["max"]["row"][2], 3)) }} */ ?>
    kilowatt-hours per square foot.
    </p>
</div>

<div class="slCard nodeWrap">
    <h3 class="mB0">Table 5: Average PowerScores for High-Efficient Lamps</h3>
    <small class="slGrey">
    High-Intensity Lamps include all HID varieties. High-Efficient Lamps include LED and Fluorescent.
    </small>
    <table border=0 class="table w100">
    <tr>
        <th>&nbsp;</th>
        <th>Overall <sub class="slGrey">%</sub></th>
        <th>Facility <sub class="slGrey">kWh/SqFt</sub></th>
        <th>Production <sub class="slGrey">g/kWh</sub></th>
        <th>Lighting <sub class="slGrey">kWh/SqFt</sub></th>
        <th>HVAC <sub class="slGrey">kWh/SqFt</sub></th>
    </tr>
    @foreach (["scrHID", "scrLED"] as $lyt)
        <tr>
            <td>
                @if ($lyt == 'scrLED') High-Efficient Lamps 
                @else High-Intensity Lamps 
                @endif 
            </td>
            <td>{{ $GLOBALS["SL"]->sigFigs($statMore[$lyt][0], 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($statMore[$lyt][1], 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($statMore[$lyt][2], 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($statMore[$lyt][3], 3) }}</td>
            <td>{{ $GLOBALS["SL"]->sigFigs($statMore[$lyt][4], 3) }}</td>
        </tr>
    @endforeach
    <tr class="slBlueDark">
        <td>Efficiency Improvement</td>
        @for ($i = 0; $i < 5; $i++)
            <td>{{ round(100*$statMore["scrLHR"][$i]) }}%</td>
        @endfor
    </tr>
    </table>
    <p>
    Compared to using HID, flower rooms with high-efficient lamps have 
    lighting sub-scores (kWh/sqft) which are {{ round(100*abs($statMore["scrLHR"][3])) }}% 
    @if ($statMore["scrLHR"][3] > 0) better, @else worse, @endif 
    facility sub-scores (kWh/sqft) which are {{ round(100*abs($statMore["scrLHR"][1])) }}% 
    @if ($statMore["scrLHR"][1] > 0) better, @else worse, @endif and
    <b class="slBlueDark">overall PowerScore rankings which are {!! round(100*abs($statMore["scrLHR"][0])) !!}% 
    @if ($statMore["scrLHR"][0] > 0) better. @else worse. @endif </b>
    </p>
</div>

<div class="slCard nodeWrap">
    <p>
    Table 6 shows additional details on the type of 
    lighting technologies in each grow environment. 
    In the flowering rooms for example, 
    {{ round(100*$statMore["flwrPercHID"]) }}
    percent of lighting was HID; 
    {{ round(100*$statLgts->getDatCnt('b162-c165')/$statLgts->getDatCnt('b162')) }}
    percent was fluorescent; and 
    {{ round(100*$statLgts->getDatCnt('b162-c203')/$statLgts->getDatCnt('b162')) }}
    percent LED.  Survey results show that on average, 1 LED lamp is used for every 
    {{ number_format($statLgts->getDatTot('sqft', 'c203', 'avg')/$statLgts->getDatTot('lgtfx', 'c203', 'avg')) }}
    square feet of grow environment. If using HID lighting, 
    {{ number_format($statMore["sqftFxtHID"]) }}
    square feet is used.
    </p>
    <h3 class="mB0">
        Table 6: Breakdown of sites and lighting 
        technologies across the survey participants
    </h3>
    <div class="pT5 pB5 slGrey">
        Totals indicate the percent of all farms 
        that use each lighting style for at least 
        one growth stage which they operate.
    </div>
    {!! $statLgts->printTblPercHas('area', 'lgty') !!}
</div>

<div class="slCard nodeWrap">
    <h3>Table 7: Business Overview: Administrative Totals</h3>
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
                <td>{{ $statMisc->getDatTot('lic141') }}</td>
                <td>{{ round(
                    100*$statMisc->getDatTot('lic141')/$allscores->count()
                ) }}%</td>
            </tr>
            <tr>
                <th>Recreational Licenses</th>
                <td>{{ $statMisc->getDatTot('lic142') }}</td>
                <td>{{ round(
                    100*$statMisc->getDatTot('lic142')/$allscores->count()
                ) }}%</td>
            </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="table w100">
            <tr>
                <th>&nbsp;</th>
                <th>Total growing square feet</th>
                <th>Percent of total</th>
            </tr>
            @foreach ($farmTypes as $name => $defID)
                <tr>
                    <th>{{ $name }}</th>
                    <td>{{ number_format(
                        $statLgts->getDatTot('sqft', 'a' . $defID)
                    ) }}</td>
                    <td>{{ number_format(
                        100*$statLgts->getDatTot('sqft', 'a' . $defID)
                            /$statLgts->getDatTot('sqft', '1')
                    ) }}%</td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
</div>

<div class="slCard nodeWrap">
    <h3>Table 8: Energy Efficiency Interest</h3>
    <table class="table w100">
    {!! $statMisc->tblHeaderRow('farm') !!}
    {!! $statMisc->tblPercHasDat(
        'farm', 
        [
            'ps_consider_upgrade', 
            'ps_incentive_wants', 
            'ps_incentive_used', 
            'ps_newsletter' 
        ]
    ) !!}
    </table>
</div>

</div>