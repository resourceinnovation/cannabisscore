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
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficFacility, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficProduction, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficLighting, 3) }}</b></th>
    <th><b>{{ $GLOBALS["SL"]->sigFigs($psAvg->PsEfficHvac, 3) }}</b></th>
    <th><b>{{ number_format($psAvg->PsGrams) }}</b></th>
    <th><b>{{ number_format($psAvg->PsKWH) }}</b></th>
    <th><b>{{ number_format($psAvg->PsTotalSize) }}</b></th>
    <th colspan=2 >&nbsp;</th>
</tr>

<tr>
    <th colspan=2 ><b>{{ number_format($allscores->count()) }} Found</b></th>
    <td class="slGrey">{{ number_format($psCnt->PsEfficFacility) }}</td>
    <td class="slGrey">{{ number_format($psCnt->PsEfficProduction) }}</td>
    <td class="slGrey">{{ number_format($psCnt->PsEfficLighting) }}</td>
    <td class="slGrey">{{ number_format($psCnt->PsEfficHvac) }}</td>
    <th colspan=5 >
</tr>

@if ($allscores && $allscores->isNotEmpty())
    @foreach ($allscores as $i => $ps)

        <tr @if ($i%2 == 0) class="row2" @endif >

        <td>
            <a href="/calculated/u-{{ $ps->PsID }}" target="_blank">
            @if ($nID == 808) {{ $ps->PsName }} @else #{{ $ps->PsID }} @endif </a>
        </td>
        <td>
            {{ round($ps->PsEfficOverall) }}%
            @if (!$isExcel && isset($allranks) 
                && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkOverallAvg))
                <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkOverallAvg) }}%
                </div>
            @endif
        </td>
        <td>
        @if (isset($ps->PsEfficFacilityStatus) 
            && intVal($ps->PsEfficFacilityStatus) == $defCmplt)
            @if ($ps->PsEfficFacility < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficFacility, 3) }}
            @endif
            @if (!$isExcel && isset($allranks) 
                && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkFacility))
                <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkFacility) }}%
                </div>
            @endif
        @endif
        </td>
        <td>
        @if (isset($ps->PsEfficProductionStatus) 
            && intVal($ps->PsEfficProductionStatus) == $defCmplt)
            @if ($ps->PsEfficProduction < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficProduction, 3) }}
            @endif
            @if (!$isExcel && isset($allranks) 
                && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkProduction)) 
                <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkProduction) }}%
                </div>
            @endif
        @endif
        </td>
        <td>
        @if (isset($ps->PsEfficLightingStatus) 
            && intVal($ps->PsEfficLightingStatus) == $defCmplt)
            @if ($ps->PsEfficLighting < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficLighting, 3) }}
            @endif
            @if (!$isExcel && isset($allranks) 
                && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkLighting))
                <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkLighting) }}%
                </div>
            @endif
        @endif
        </td>
        <td>
        @if (isset($ps->PsEfficHvacStatus) 
            && intVal($ps->PsEfficHvacStatus) == $defCmplt)
            @if ($ps->PsEfficHvac < 0.000001) 0
            @else {{ $GLOBALS["SL"]->sigFigs($ps->PsEfficHvac, 3) }}
            @endif
            @if (!$isExcel && isset($allranks) 
                && isset($allranks[$ps->PsID]) 
                && isset($allranks[$ps->PsID]->PsRnkHVAC)) 
                <div class="slGrey fPerc66">
                    {{ $GLOBALS["SL"]->sigFigs($allranks[$ps->PsID]->PsRnkHVAC) }}%
                </div>
            @endif
        @endif 
        </td>
        @if (!$isExcel)
            <td> @if ($ps->PsGrams > 0) {{ number_format($ps->PsGrams) }} @endif </td>
            <td> @if ($ps->PsKWH > 0) {{ number_format($ps->PsKWH) }} @endif </td>
            <td> @if ($ps->PsTotalSize > 0) {{ number_format($ps->PsTotalSize) }} @endif </td>
        @else
            <td>{{ number_format($ps->PsGrams) }}</td>
            <td>{{ number_format($ps->PsKWH) }}</td>
            <td>{{ number_format($ps->PsTotalSize) }}</td>
        @endif 
        <td>
            {{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', 
                $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $ps->PsCharacterize)) }}
            @if (isset($fltCmpl) && $fltCmpl == 0 
                && Auth::user()->hasRole('administrator|staff'))
                <br />
                @if ($ps->PsStatus == 243) 
                    <span class="slBlueDark">Complete</span>
                @elseif ($ps->PsStatus == 364) 
                    <span class="txtDanger">Archived</span>
                @else {{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $ps->PsStatus) }}
                @endif
            @endif
        </td>
        <td>
            {{ $GLOBALS["SL"]->allCapsToUp1stChars($ps->PsCounty) }}
            {{ $ps->PsState }}
        @if (Auth::user()->hasRole('administrator|staff'))
            {{ $ps->PsZipCode }}
        @endif
        </td>
        <?php /* 
        <td class="fPerc80">'{{ substr($ps->PsYear, 2) }}
            @if (!$isExcel && in_array($ps->PsID, $cultClassicIds))
                <i class="fa fa-certificate mL3" aria-hidden="true"></i> CC
            @endif
            @if (!$isExcel && in_array($ps->PsID, $emeraldIds))
                @if (!in_array($ps->PsID, $cultClassicIds))
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
                <td class="taR"><div class="mTn10 slGrey fPerc66">Review Notes:</div></td>
                <td colspan=9 ><div class="mTn15"><i>{!! $ps->PsNotes !!}</i></div></td>
            </tr>
        @endif

    @endforeach
@else
    <tr><td colspan=11 class="slGrey">
        <i>No PowerScores found.</i>
    </td></tr>
@endif
</table>