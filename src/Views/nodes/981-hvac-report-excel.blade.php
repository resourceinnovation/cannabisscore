<!-- generated from resources/views/vendor/cannabisscore/nodes/981-hvac-report-excel.blade.php -->

<tr>
<th colspan=10 align="left" >
    HVAC Report 
    @if (trim($fltStateClim) != '')
        - {!! $GLOBALS["SL"]->states->getZoneOrState($fltStateClim) !!}
    @endif
</th>
</tr>
<tr><td>
    Under each Lighting Sub-Score (weighted by SqFt) 
    is its breakdown by growth stage.
</td></tr>

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
    @if ($typeID == 144) 1a. 
    @elseif ($typeID == 145) 1b. 
    @else 1c. 
    @endif 
    {{ $typeName }} Scores by Type of Flowering HVAC
    </td></tr>
    {!! $scoreSets["statScorHvcF" . $typeID]->printScoreAvgsExcel('hvac') !!}
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
    @if ($typeID == 144) 2a. 
    @elseif ($typeID == 145) 2b. 
    @else 2c. 
    @endif 
    {{ $typeName }} Scores by Type of Vegetative HVAC
    {!! $scoreSets["statScorHvcV" . $typeID]->printScoreAvgsExcel('hvac') !!}
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >
        @if ($typeID == 144) 3a. 
        @elseif ($typeID == 145) 3b. 
        @else 3c. 
        @endif 
        {{ $typeName }} Scores by Type of Cloning/Mother HVAC
    {!! $scoreSets["statScorHvcC" . $typeID]->printScoreAvgsExcel('hvac') !!}
@endforeach

@foreach ($sfFarms[0] as $i => $farmDef)
    @if ($sfFarms[1][$i] != 'Outdoor')
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><th colspan=10 align="left" >
            @if ($sfFarms[0][$i] == 144) 4a. 
            @elseif ($sfFarms[0][$i] == 145) 4b. 
            @else 4c. 
            @endif 
            {{ $sfFarms[1][$i] }} Square Footage
        </th></tr>

        <tr>
        <th></th>
        <th class="brdRgt">Averages</th><th>(count)</th>
        <th>System A</th><th>(count)</th>
        <th>System B</th><th>(count)</th>
        <th>System C</th><th>(count)</th>
        <th>System D</th><th>(count)</th>
        <th>System E</th><th>(count)</th>
        <th>System F</th><th>(count)</th>
        <th>Other System</th><th>(count)</th>
        <th>None</th><th>(count)</th>
        </tr>

        @foreach ($sfAreasGrow[0] as $i => $areaDef)
            <tr>
                <th align="left" ><nobr>{{ $sfAreasGrow[1][$i] }}</nobr></th>
                <td style="border-right: 1px #777 solid;">{{ number_format($hvacSqft[$farmDef][$areaDef][0]) }}</td>
                <td style="color: #777;">{{ $hvacSqft[$farmDef][$areaDef][1] }}</td>
            @foreach ($sfHvac[0] as $i => $hvacDef)
                <td>{{ number_format($hvacSqft[$farmDef][$areaDef][2][$hvacDef][0]) }}</td>
                <td style="color: #777;">{{ count($hvacSqft[$farmDef][$areaDef][2][$hvacDef][1]) }}</td>
            @endforeach
            </tr>
        @endforeach

    @endif
@endforeach
