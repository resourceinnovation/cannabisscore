<!-- generated from resources/views/vendor/cannabisscore/nodes/170-powerscore-listings.blade.php -->
<table border=0 class="table w100 bgWht">
{!! view(
    'vendor.cannabisscore.nodes.170-powerscore-listings-header',
    [
        "sort"    => $sort,
        "fltCmpl" => $fltCmpl
    ]
)->render() !!}

<tr>
    <th colspan=2 ><b>Averages</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_facility, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_production, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_hvac, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_effic_lighting, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->ps_lighting_power_density, 3) }}</b></th>
    <th><b>{{ number_format($psAvg->ps_grams) }}</b></th>
    <th><b>{{ number_format($psAvg->ps_kwh) }}</b></th>
    <th><b>{{ number_format($psAvg->ps_flower_canopy_size) }}</b></th>
    <th colspan=2 >&nbsp;</th>
</tr>

@if (!isset($fltPartner) || $fltPartner <= 0)
    <tr>
        <th colspan=2 ><b>{{ number_format($allscores->count()) }} Found</b></th>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_facility) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_production) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_lighting) }}</td>
        <td class="slGrey">{{ number_format($psCnt->ps_effic_hvac) }}</td>
        <th colspan=6 >
    </tr>
@endif

@if ($allscores && $allscores->isNotEmpty())
    @foreach ($allscores as $i => $ps)

        @if ($GLOBALS["SL"]->REQ->has('test') 
            && isset($GLOBALS["SL"]->x["fakeSites"])
            && isset($GLOBALS["SL"]->x["fakeSites"][$i])
            && trim($GLOBALS["SL"]->x["fakeSites"][$i]) != '')
            <tr><td colspan=12 >
                <h4>{!! $GLOBALS["SL"]->x["fakeSites"][$i] !!}</h4>
            </td></tr>
        @endif

        @if (!$GLOBALS["SL"]->REQ->has('test') || $i < 25)

            <tr @if ($i%2 == 0) class="row2" @endif >

            <td>
            @if ($GLOBALS["SL"]->x["partnerLevel"] >= 4)
                <a href="/calculated/u-{{ $ps->ps_id }}" target="_blank">
                    @if ($nID == 808) {{ $ps->ps_name }} 
                    @else #{{ $ps->ps_id }} 
                    @endif </a>
            @else
                <b>{{ (1+$i) }})</b>
            @endif
            </td>
            <td>
                {{ round($ps->ps_effic_overall) }}%
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_overall_avg))
                    <div class="slGrey fPerc66">
                        {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->ps_id]->ps_rnk_overall_avg) }}%
                    </div>
                @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_facility_status) 
                && intVal($ps->ps_effic_facility_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_facility < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_facility, 3) }}
                @endif
                @if (!$isExcel && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_facility))
                    <div class="slGrey fPerc66">
                        {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->ps_id]->ps_rnk_facility) }}%
                    </div>
                @endif
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_production_status) 
                && intVal($ps->ps_effic_production_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_production < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_production, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_production)) 
                    <div class="slGrey fPerc66">
                        {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->ps_id]->ps_rnk_production) }}%
                    </div>
                @endif
            @endif
            </td>
            <td>
            @if ((isset($ps->ps_effic_hvac_status) 
                && intVal($ps->ps_effic_hvac_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_hvac < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_hvac, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_hvac)) 
                    <div class="slGrey fPerc66">
                        {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->ps_id]->ps_rnk_hvac) }}%
                    </div>
                @endif
            @endif 
            </td>
            <td>
            @if ((isset($ps->ps_effic_lighting_status) 
                && intVal($ps->ps_effic_lighting_status) == $defCmplt)
                || (isset($fltPartner) && $fltPartner > 0))
                @if ($ps->ps_effic_lighting < 0.000001) 0
                @else {{ $GLOBALS["SL"]->sigFigs($ps->ps_effic_lighting, 3) }}
                @endif
                @if (!$isExcel 
                    && isset($allranks) 
                    && isset($allranks[$ps->ps_id]) 
                    && isset($allranks[$ps->ps_id]->ps_rnk_lighting))
                    <div class="slGrey fPerc66">
                        {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->ps_id]->ps_rnk_lighting) }}%
                    </div>
                @endif
            @endif
            </td>
            <td>
            @if (isset($ps->ps_lighting_power_density) && $ps->ps_lighting_power_density > 0.00001)
                {{ $GLOBALS["SL"]->sigFigs($ps->ps_lighting_power_density, 3) }}
            @else 0
            @endif
            </td>
            @if (!$isExcel)
                <td> @if ($ps->ps_grams > 0) {{ number_format($ps->ps_grams) }} @endif </td>
                <td> @if ($ps->ps_kwh > 0) {{ number_format($ps->ps_kwh) }} @endif </td>
                <td> @if ($ps->ps_flower_canopy_size > 0) 
                    {{ number_format($ps->ps_flower_canopy_size) }} 
                @endif </td>
            @else
                <td>{{ number_format($ps->ps_grams) }}</td>
                <td>{{ number_format($ps->ps_kwh) }}</td>
                <td>{{ number_format($ps->ps_flower_canopy_size) }}</td>
            @endif 
            <td>
                {{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
                    $GLOBALS["SL"]->def->getVal(
                        'PowerScore Farm Types', 
                        $ps->ps_characterize
                    )) 
                }}
                @if (isset($fltCmpl) 
                    && $fltCmpl == 0 
                    && Auth::user()->hasRole('administrator|staff'))
                    <br />
                    @if ($ps->ps_status == 243) 
                        <span class="slBlueDark">Complete</span>
                    @elseif ($ps->ps_status == 364) 
                        <span class="txtDanger">Archived</span>
                    @else 
                        {{ $GLOBALS["SL"]->def->getVal(
                            'PowerScore Status', 
                            $ps->ps_status
                        ) }}
                    @endif
                @endif
            </td>
            <td>
                {{ $GLOBALS["SL"]->allCapsToUp1stChars($ps->ps_county) }}
                {{ $ps->ps_state }}
            @if (Auth::user()->hasRole('administrator|staff'))
                {{ $ps->ps_zip_code }}
            @endif
            </td>
            <?php /* 
            <td class="fPerc80">'{{ substr($ps->ps_year, 2) }}
                @if (!$isExcel && in_array($ps->ps_id, $cultClassicIds))
                    <i class="fa fa-certificate mL3" aria-hidden="true"></i> CC
                @endif
                @if (!$isExcel && in_array($ps->ps_id, $emeraldIds))
                    @if (!in_array($ps->ps_id, $cultClassicIds))
                        <i class="fa fa-certificate mL3" aria-hidden="true"></i>
                    @else ,
                    @endif EC
                @endif
            </td>
            */ ?>
            </tr>
            @if ($GLOBALS["SL"]->REQ->has('review') 
                && Auth::user()->hasRole('administrator|staff'))
                <tr class="brdTopNon @if ($i%2 == 0) row2 @endif " >
                    <td class="taR">
                        <div class="mTn10 slGrey fPerc66">Review Notes:</div>
                    </td>
                    <td colspan=9 >
                        <div class="mTn15"><i>{!! $ps->ps_notes !!}</i></div>
                    </td>
                </tr>
            @endif

        @endif

    @endforeach
@else
    <tr><td colspan=11 class="slGrey">
        <i>No PowerScores found.</i>
    </td></tr>
@endif
</table>