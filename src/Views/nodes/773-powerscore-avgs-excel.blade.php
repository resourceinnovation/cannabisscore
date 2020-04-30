<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs-excel.blade.php -->

<tr>
<th colspan=10 align="left" >
    Ranked Data Set Averages by Category
    @if (trim($fltStateClim) != '')
        - {!! $GLOBALS["SL"]->states->getZoneOrState($fltStateClim) !!}
    @endif
</th>
</tr>
<tr><td>
    Under each Lighting Sub-Score (weighted by SqFt) is its breakdown by growth stage.
</td></tr>

@foreach ($scoreSets as $i => $set)
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><th colspan=10 align="left" >{{ (1+$i) }}. {{ $set[1] }}</td></tr>
    {!! str_replace('<table border=0 class="table table-striped w100">', '', 
        str_replace('</table>', '',
        str_replace('<sub class="slGrey">', '</td><td>', 
        str_replace('</sub>', '', 
        $set[2])))) !!}
@endforeach
