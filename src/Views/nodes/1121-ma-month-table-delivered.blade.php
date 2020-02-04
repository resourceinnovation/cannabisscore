<!-- generated from resources/views/vendor/cannabisscore/nodes/1121-ma-month-table-delivered.blade.php -->

<div id="node{{ $nID }}" class="nodeWrap">
<div class="nodeHalfGap"></div>


<div id="nLabel{{ $nID }}" class="nPrompt">
    <h4 class="slBlueDark">Delivered fuel consumption</h4>
    <p>(if available)</p>
</div>
<div class="nFld">
    <table class="table slSpreadTbl"><tbody>

        <tr>
            <th class="sprdRowLab">Month</th>
            <th class="cl1"><b>Natural gas consumption (Therms)</b></th>
            <th class="cl1">
                <b>Other:</b>
                <input name="n{{ $nKW }}other" id="n{{ $nKW }}otherID" 
                    class="form-control form-control-lg unitFld slTab ntrStp" 
                    type="text" value="" tabindex="1">

            </th>
        </tr>

    @foreach ($GLOBALS["SL"]->monthsArray() as $m => $mon)
        <input type="hidden" name="n{{ $nID }}tbl{{ $m }}fldDef" 
            id="n{{ $nID }}tbl{{ $m }}fldDefID" value="">
        <input type="hidden" name="n{{ $nID }}tbl{{ $m }}Visible" 
            id="n{{ $nID }}tbl{{ $m }}VisibleID" value="1">
        <tr id="n{{ $nID }}tbl{{ $m }}row" class=" @if ($m%2 == 0) rw2 @endif ">

            <td id="n{{ $nID }}tbl{{ $m }}rowLab" class="sprdRowLab">
                {{ $mon }}
            </td>

            <td id="n{{ $nID }}tbl{{ $m }}row0col" class="sprdFld cl1">
                <div id="blockWrap{{ $nKWH }}tbl{{ $m }}" class="w100">
                    <div id="node{{ $nKWH }}tbl{{ $m }}" class="nodeWrap">
                        <div class=""><nobr>
                            <input type="number" data-nid="{{ $nKWH }}" 
                                class="form-control form-control-lg unitFld slTab slNodeChange ntrStp" 
                                name="n{{ $nKWH }}tbl{{ $m }}fld" 
                                id="n{{ $nKWH }}tbl{{ $m }}FldID" 
                                value="" step="any" min="0" tabindex="1"
                                onkeyup=" checkMin('{{ $nKWH }}tbl{{ $m }}', 0); " > 
                                Therms &nbsp;&nbsp;
                        </nobr></div>
                    </div> <!-- end #node{{ $nKWH }}tbl{{ $m }} -->
                </div>
            </td>

            <td id="n{{ $nID }}tbl{{ $m }}row1col" class="sprdFld cl1">
                <div id="blockWrap{{ $nKW }}tbl{{ $m }}" class="w100">
                    <div id="node{{ $nKW }}tbl{{ $m }}" class="nodeWrap">
                        <div class=""><nobr>
                            <input type="number" data-nid="{{ $nKW }}" 
                                class="form-control form-control-lg unitFld slTab slNodeChange ntrStp" 
                                name="n{{ $nKW }}tbl{{ $m }}fld" 
                                id="n{{ $nKW }}tbl{{ $m }}FldID" 
                                value="" step="any" min="0" tabindex="1"
                                onkeyup=" checkMin('{{ $nKW }}tbl{{ $m }}', 0); " > 
                                Gallons &nbsp;&nbsp;
                        </nobr></div>
                    </div> <!-- end #node{{ $nKW }}tbl{{ $m }} -->
                </div>
            </td>

        </tr>
    @endforeach


    </tbody></table>
</div>



<div class="nodeHalfGap"></div>
</div>
