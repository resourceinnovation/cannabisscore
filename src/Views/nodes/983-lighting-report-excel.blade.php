<!-- generated from resources/views/vendor/cannabisscore/nodes/983-lighting-report-excel.blade.php -->

<tr>
<th colspan=10 align="left" >Lighting Report 
    @if (trim($fltStateClim) != '') 
        - {!! $GLOBALS["SL"]->states->getZoneOrState($fltStateClim) !!} 
    @endif
    @if ($GLOBALS["SL"]->REQ->has('fltNoNWPCC'))
        - No NWPCC
    @endif
    @if ($GLOBALS["SL"]->REQ->has('fltNoLgtError'))
        - No Obvious Lighting Errors
    @endif
</th>
</tr>
<tr><td>
    Many columns are clickable to load the report listing all 
    individual reports matching the filter (when possible).
    Small subscript counts are the number of growing areas 
    (reported in powerscores) upon which each calculated 
    average is based. This report only shows PowerScores 
    with Lighting KPIs greater than zero (AKA use 
    any artifical lighting), and have not been archived.
</td></tr>
<tr><td>
    <b>Found {{ number_format($totCnt) }} PowerScores</b>
</td></tr>

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
        @if ($typeID == 144) 1a. 
        @else 1b. 
        @endif {{ $typeName }} 
        Scores by Type of Flowering Lighting
    </td></tr>
    {!! $scoreSets["statScorLgtF" . $typeID]->printScoreAvgsExcel2() !!}
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
        @if ($typeID == 144) 2a. 
        @elseif ($typeID == 145) 2b. 
        @else 2c. 
        @endif {{ $typeName }}
        Scores by Type of Vegetative Lighting
    </td></tr>
    {!! $scoreSets["statScorLgtV" . $typeID]->printScoreAvgsExcel2() !!}
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
        @if ($typeID == 144) 3a. 
        @elseif ($typeID == 145) 3b. 
        @else 3c. 
        @endif {{ $typeName }}
        Scores by Type of Clone Lighting
    </td></tr>
    {!! $scoreSets["statScorLgtC" . $typeID]->printScoreAvgsExcel2() !!}
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
        @if ($typeID == 144) 4a. 
        @elseif ($typeID == 145) 4b. 
        @else 4c. 
        @endif {{ $typeName }}
        Scores by Type of Mother Lighting
    </td></tr>
    {!! $scoreSets["statScorLgtM" . $typeID]->printScoreAvgsExcel2() !!}
@endforeach
