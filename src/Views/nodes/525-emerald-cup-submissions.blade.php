<!-- generated from resources/views/vendor/cannabisscore/nodes/525-emerald-cup-submissions.blade.php -->

<a class="btn btn-secondary float-right" href="?excel=1"><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
    Export to Excel</a>
<h1 class="slBlueDark">{{ $filtTitle }} PowerScores</h1>
<a id="tblScrlBtn" class="btn btn-secondary btn-sm float-right" href="javascript:;"
    >Show All <i class="fa fa-expand mL5"></i></a>
<a class="btn btn-secondary btn-sm float-right mR10" href="?getRandom=1"
    >Pick Random <i class="fa fa-search mL5"></i></a>
<p class="slGrey">
Click a PowerScore's row or ID# to load the full report lower down on this page. 
Click the <i class="fa fa-external-link" aria-hidden="true"></i> icon 
to load the full report in a new window/tab (also the best link to share).
For now, please use Ctrl+F to search this page for farm names, emails, etc. &lt;3
</p>
</div>
<div id="tblScrl">
<table border=0 class="table table-striped w100">
<tr>
<th> </th>
<th>Score ID#</th>
<th>Farm Name, County, Email</th>
<th>Submitted</th>
<th>
    <b>Overall % Rank</b>, Sub-Ranks: 
    <div class="fPerc80"><b>F</b>acility, <b>P</b>roduction, <b>L</b>ighting, <b>H</b>VAC</div>
</th>
<th>Sub-Scores</th>
<th>Grams, kWh, Sq Ft</th>
</tr>
@forelse ($cupScores as $i => $s)
    <tr id="psRowA{{ $s->PsID }}" class="psRowA1" >
    <td>
        <a href="/calculated/u-{{ $s->PsID }}" target="_blank" class="mR10 mT10"
            ><i class="fa fa-external-link" aria-hidden="true"></i></a></nobr>
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        <nobr><a href="javascript:;" class="btn btn-primary" 
            >#{{ $s->PsID }}</a>
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        <div class="fPerc125 bld mTn5">
            @if (isset($s->PsName) && trim($s->PsName) != '') {{ $s->PsName }} @endif
        </div>
        {{ $s->PsCounty }} {{ $s->PsState }}
        @if (isset($s->PsEmail) && trim($s->PsEmail) != '') 
            <br /><a href="mailto:{{ $s->PsEmail }}">{{ $s->PsEmail }}</a>
        @endif
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        {{ date("n/j, g:ia", strtotime($s->created_at)) }}
        @if (in_array($s->PsID, $emeraldIds)) <br /><span class="slBlueDark"
            ><i class="fa fa-certificate" aria-hidden="true"></i> Emerald Cup</span> @endif
        @if (in_array($s->PsID, $cultClassicIds)) <br /><span class="slBlueDark"
            ><i class="fa fa-certificate" aria-hidden="true"></i> Cultivation Classic</span> @endif
    </td>
    @if ($s->PsStatus == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'))
        <td class="psOpen" data-psid="{{ $s->PsID }}">
            <b class="fPerc133">{{ round($s->PsEfficOverall) }}%</b> <div class="fPerc80">
            @if (isset($s->PsRnkFacility) && $s->PsRnkFacility > 0) 
                <span class="slGrey">, <nobr>F:</span>{{ round($s->PsRnkFacility) }}%</nobr> @endif
            @if (isset($s->PsRnkProduction) && $s->PsRnkProduction > 0) 
                <span class="slGrey">, <nobr>P:</span>{{ round($s->PsRnkProduction) }}%</nobr> @endif
            @if (isset($s->PsRnkLighting) && $s->PsRnkLighting > 0) 
                <span class="slGrey">, <nobr>L:</span>{{ round($s->PsRnkLighting) }}%</nobr> @endif
            @if (isset($s->PsRnkHVAC) && $s->PsRnkHVAC > 0) 
                <span class="slGrey">, <nobr>H:</span>{{ round($s->PsRnkHVAC) }}%</nobr> @endif
            </div>
        </td>
        <td class="psOpen fPerc80" data-psid="{{ $s->PsID }}">
            @if (isset($s->PsEfficFacility) && $s->PsEfficFacility > 0)
                <span class="slGrey">F:</span> {{ $GLOBALS["SL"]->sigFigs($s->PsEfficFacility, 3) 
                    }} kWh / sq ft<br /> @endif
            @if (isset($s->PsEfficProduction) && $s->PsEfficProduction > 0) 
                <span class="slGrey">P:</span> {{ $GLOBALS["SL"]->sigFigs($s->PsEfficProduction, 3) 
                    }} grams / kWh<br /> @endif
            @if (isset($s->PsEfficLighting) && $s->PsEfficLighting > 0)
                <span class="slGrey">L:</span> {{ $GLOBALS["SL"]->sigFigs($s->PsEfficLighting, 3) 
                    }} kWh / sq ft<br /> @endif
            @if (isset($s->PsEfficHvac) && $s->PsEfficHvac > 0) 
                <span class="slGrey">H:</span> {{ $GLOBALS["SL"]->sigFigs($s->PsEfficHvac, 3) 
                    }} kWh / sq ft<br /> @endif
        </td>
        <td class="psOpen" data-psid="{{ $s->PsID }}">
            {{ number_format($s->PsGrams) }} g<br />
            {{ number_format($s->PsKWH) }} kWh<br />
            {{ number_format($s->PsTotalSize) }} sq ft
        </td>
    @else 
        <td colspan=4 class="psOpen" data-psid="{{ $s->PsID }}" ><i class="txtDanger">
            {!! $GLOBALS["SL"]->getNodePageName($s->PsSubmissionProgress) !!}
        </i></td>
    @endif
    </td>
    </tr>
    <tr id="psRowB{{ $s->PsID }}" class="disNon"></tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif
</table>
</div>

<style>
.psOpen { cursor: pointer; }
tr.psRowA1 { border: inherit; }
tr.psRowA2 { border-top: 1px #8EAD67 solid; border-bottom: 1px #8EAD67 solid; box-shadow: 0px 0px 10px #8EAD67; }
.psFrame { height: 700px; width: 100%; box-shadow: 0px 0px 10px #8EAD67; 
    border: 0px none; border-collapse: collapse; }
td.psRowTd, table tr td.psRowTd { padding: 0px; }
</style>
<script type="text/javascript">
$(document).ready(function(){
    
    function checkScrollHgt(psid) {
        /*
        if (document.getElementById("psFrame"+psid+"")) {
alert(document.getElementById("psFrame"+psid+"").scrollHeight);
            if (document.getElementById("psFrame"+psid+"").scrollHeight > 0) {
                var newH = parseInt(document.getElementById("psFrame"+psid+"").style.height.replace("px", ""))+100;
                document.getElementById("psFrame"+psid+"").style.height = (newH)+"px";
            }
            setTimeout(function() { checkScrollHgt(psid) }, 2000);
        }
        */
        return true;
    }
    function loadPsReport(psid) {
        if (document.getElementById("psRowB"+psid+"")) {
            document.getElementById("psRowB"+psid+"").style.display='table-row';
            document.getElementById("psRowB"+psid+"").innerHTML='<td colspan=8 class="psRowTd"><iframe <?php
                ?>id="psFrame'+psid+'" class="psFrame" src="/calculated/u-'+psid+'?refresh=1&frame=1<?php
                ?>&hidePromos=1"></iframe></td>';
            setTimeout(function() { checkScrollHgt(psid) }, 500);
        }
        return true;
    }
    $(document).on("click", ".psOpen", function() {
        var psid = $(this).attr("data-psid");
        loadPsReport(psid);
    });
    $(document).on("mouseenter", ".psOpen", function() {
        var psid = $(this).attr("data-psid");
        if (document.getElementById("psRowA"+psid+"")) {
            document.getElementById("psRowA"+psid+"").className="psRowA2";
        }
    });
    $(document).on("mouseleave", ".psOpen", function() {
        var psid = $(this).attr("data-psid");
        if (document.getElementById("psRowA"+psid+"")) {
            document.getElementById("psRowA"+psid+"").className="psRowA1";
        }
    });
    
    @if ($GLOBALS["SL"]->REQ->has('refresh') && intVal($GLOBALS["SL"]->REQ->get('refresh')) == 1)
        @forelse ($cupScores as $i => $s)
            setTimeout(function() { loadPsReport({{ $s->PsID }}); }, {{ $i*10000 }});
        @empty
        @endforelse
    @endif
    
});
</script>
