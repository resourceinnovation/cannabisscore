<!-- generated from resources/views/vendor/cannabisscore/nodes/525-emerald-cup-submissions.blade.php -->

<a class="btn btn-secondary float-right" href="?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
    Export to Excel</a>
<h1 class="slBlueDark">{{ $filtTitle }} PowerScores</h1>
<a id="tblScrlBtn" class="btn btn-secondary btn-sm float-right" 
    href="javascript:;">Show All <i class="fa fa-expand mL5"></i></a>
<a class="btn btn-secondary btn-sm float-right mR10" 
    href="?getRandom=1">Pick Random <i class="fa fa-search mL5"></i></a>
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
    <div class="fPerc80">
        <b>F</b>acility, <b>P</b>roduction, <b>L</b>ighting, <b>H</b>VAC
    </div>
</th>
<th>Sub-Scores</th>
<th>Grams, kWh, Sq Ft</th>
</tr>
@forelse ($cupScores as $i => $s)
    <tr id="psRowA{{ $s->ps_id }}" class="psRowA1" >
    <td>
        <a href="/calculated/u-{{ $s->ps_id }}" target="_blank" class="mR10 mT10"
            ><i class="fa fa-external-link" aria-hidden="true"></i></a></nobr>
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        <nobr><a href="javascript:;" class="btn btn-primary" 
            >#{{ $s->ps_id }}</a>
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        <div class="fPerc125 bld mTn5">
            @if (isset($s->ps_name) && trim($s->ps_name) != '') {{ $s->ps_name }} @endif
        </div>
        {{ $s->ps_county }} {{ $s->ps_state }}
        @if (isset($s->ps_email) && trim($s->ps_email) != '') 
            <br /><a href="mailto:{{ $s->ps_email }}">{{ $s->ps_email }}</a>
        @endif
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        {{ date("n/j, g:ia", strtotime($s->created_at)) }}
        @if (in_array($s->ps_id, $emeraldIds)) <br /><span class="slBlueDark"
            ><i class="fa fa-certificate" aria-hidden="true"></i> Emerald Cup</span> @endif
        @if (in_array($s->ps_id, $cultClassicIds)) <br /><span class="slBlueDark"
            ><i class="fa fa-certificate" aria-hidden="true"></i> Cultivation Classic</span> @endif
    </td>
    @if ($s->ps_status == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'))
        <td class="psOpen" data-psid="{{ $s->ps_id }}">
            <b class="fPerc133">{{ round($s->ps_effic_overall) }}%</b> 
            <div class="fPerc80">
            @if (isset($s->ps_rnk_facility) && $s->ps_rnk_facility > 0) 
                <span class="slGrey">, <nobr>F:</span>{{ round($s->ps_rnk_facility) }}%</nobr> 
            @endif
            @if (isset($s->ps_rnk_production) && $s->ps_rnk_production > 0) 
                <span class="slGrey">, <nobr>P:</span>{{ round($s->ps_rnk_production) }}%</nobr>
            @endif
            @if (isset($s->ps_rnk_lighting) && $s->ps_rnk_lighting > 0) 
                <span class="slGrey">, <nobr>L:</span>{{ round($s->ps_rnk_lighting) }}%</nobr> 
            @endif
            @if (isset($s->ps_rnk_hvac) && $s->ps_rnk_hvac > 0) 
                <span class="slGrey">, <nobr>H:</span>{{ round($s->ps_rnk_hvac) }}%</nobr>
            @endif
            </div>
        </td>
        <td class="psOpen fPerc80" data-psid="{{ $s->ps_id }}">
            @if (isset($s->ps_effic_facility) && $s->ps_effic_facility > 0)
                <span class="slGrey">F:</span> {{ 
                $GLOBALS["SL"]->sigFigs($s->ps_effic_facility, 3) 
                }} kBtu / sq ft<br />
            @endif
            @if (isset($s->ps_effic_production) && $s->ps_effic_production > 0) 
                <span class="slGrey">P:</span> {{ $GLOBALS["SL"]->sigFigs($s->ps_effic_production, 3) 
                }} grams / kBtu<br />
            @endif
            @if (isset($s->ps_effic_lighting) && $s->ps_effic_lighting > 0)
                <span class="slGrey">L:</span> {{ 
                $GLOBALS["SL"]->sigFigs($s->ps_effic_lighting, 3) 
                }} kWh / sq ft<br /> 
            @endif
            @if (isset($s->ps_effic_hvac) && $s->ps_effic_hvac > 0) 
                <span class="slGrey">H:</span> {{ 
                $GLOBALS["SL"]->sigFigs($s->ps_effic_hvac, 3) 
                }} kWh / sq ft<br /> 
            @endif
        </td>
        <td class="psOpen" data-psid="{{ $s->ps_id }}">
            {{ number_format($s->ps_grams) }} g<br />
            {{ number_format($s->ps_kwh) }} kWh<br />
            {{ number_format($s->ps_total_size) }} sq ft
        </td>
    @else 
        <td colspan=4 class="psOpen" data-psid="{{ $s->ps_id }}" ><i class="txtDanger">
            {!! $GLOBALS["SL"]->getNodePageName($s->ps_submission_progress) !!}
        </i></td>
    @endif
    </td>
    </tr>
    <tr id="psRowB{{ $s->ps_id }}" class="disNon"></tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif
</table>
</div>

<style>
.psOpen { 
    cursor: pointer; 
}
tr.psRowA1 { 
    border: inherit; 
}
tr.psRowA2 {
    border-top: 1px #8EAD67 solid; 
    border-bottom: 1px #8EAD67 solid; 
    box-shadow: 0px 0px 10px #8EAD67; 
}
.psFrame { 
    height: 700px; 
    width: 100%; 
    box-shadow: 0px 0px 10px #8EAD67; 
    border: 0px none; 
    border-collapse: collapse; 
}
td.psRowTd, table tr td.psRowTd {
    padding: 0px;
}
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
    
    @if ($GLOBALS["SL"]->REQ->has('refresh') 
        && intVal($GLOBALS["SL"]->REQ->get('refresh')) == 1)
        @forelse ($cupScores as $i => $s)
            setTimeout(function() {
                loadPsReport({{ $s->ps_id }}); 
            }, {{ $i*10000 }});
        @empty
        @endforelse
    @endif
    
});
</script>
