<!-- generated from resources/views/vendor/cannabisscore/inc-tbl-powerscore-submissions.blade.php -->
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
<th>KPIs</th>
<th>Grams, kWh, Sq Ft</th>
</tr>
@forelse ($scores as $i => $s)
    <tr id="psRowA{{ $s->ps_id }}" class="psRowA1" >
    <td>
        <a href="/calculated/u-{{ $s->ps_id }}" target="_blank" class="mR10 mT10"
            ><i class="fa fa-external-link" aria-hidden="true"></i></a></nobr>
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        <nobr><a href="javascript:;" class="btn btn-primary" 
            >#{{ $s->ps_id }}</a>
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        <div class="fPerc125 bld mTn5"> @if (isset($s->ps_name) && trim($s->ps_name) != '') {{ $s->ps_name }} @endif </div>
        {{ $s->ps_county }} {{ $s->ps_state }}
        @if (isset($s->ps_email) && trim($s->ps_email) != '') 
            <br /><a href="mailto:{{ $s->ps_email }}">{{ $s->ps_email }}</a>
        @endif
    </td><td class="psOpen" data-psid="{{ $s->ps_id }}">
        @if (isset($s->ps_status))
            <div class=" @if ($s->ps_status 
                == $GLOBALS['SL']->def->getID('PowerScore Status', 'Ranked Data Set')) 
            slGreenDark @else txtDanger @endif ">
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $s->ps_status) }}</div>
        @endif
        {{ date("n/j, g:ia", strtotime($s->created_at)) }}
    </td>
    @if ($s->ps_status == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Ranked Data Set'))
        <td class="psOpen" data-psid="{{ $s->ps_id }}">
            <b class="fPerc133">{{ round($s->ps_effic_overall) }}%</b> <div class="fPerc80">
            @if (isset($s->ps_rnk_facility) && $s->ps_rnk_facility > 0) 
                <span class="slGrey">, <nobr>F:</span>{{ round($s->ps_rnk_facility) }}%</nobr> @endif
            @if (isset($s->ps_rnk_production) && $s->ps_rnk_production > 0) 
                <span class="slGrey">, <nobr>P:</span>{{ round($s->ps_rnk_production) }}%</nobr> @endif
            @if (isset($s->ps_rnk_lighting) && $s->ps_rnk_lighting > 0) 
                <span class="slGrey">, <nobr>L:</span>{{ round($s->ps_rnk_lighting) }}%</nobr> @endif
            @if (isset($s->ps_rnk_hvac) && $s->ps_rnk_hvac > 0) 
                <span class="slGrey">, <nobr>H:</span>{{ round($s->ps_rnk_hvac) }}%</nobr> @endif
            </div>
        </td>
        <td class="psOpen fPerc80" data-psid="{{ $s->ps_id }}">
            @if (isset($s->ps_effic_facility) && $s->ps_effic_facility > 0)
                <span class="slGrey">F:</span> {{ $GLOBALS["SL"]->sigFigs($s->ps_effic_facility, 3) 
                    }} kBtu / sq ft<br /> @endif
            @if (isset($s->ps_effic_production) && $s->ps_effic_production > 0) 
                <span class="slGrey">P:</span> {{ $GLOBALS["SL"]->sigFigs($s->ps_effic_production, 3) 
                    }} grams / kBtu<br /> @endif
            @if (isset($s->ps_effic_lighting) && $s->ps_effic_lighting > 0)
                <span class="slGrey">L:</span> {{ $GLOBALS["SL"]->sigFigs($s->ps_effic_lighting, 3) 
                    }} W / sq ft<br /> @endif
            @if (isset($s->ps_effic_hvac) && $s->ps_effic_hvac > 0) 
                <span class="slGrey">H:</span> {{ $GLOBALS["SL"]->sigFigs($s->ps_effic_hvac, 3) 
                    }} kWh / sq ft<br /> @endif
        </td>
        <td class="psOpen" data-psid="{{ $s->ps_id }}">
            {{ number_format($s->ps_grams) }} g<br />
            {{ number_format($s->ps_kwh_tot_calc) }} kWh<br />
            {{ number_format($s->ps_flower_canopy_size) }} sq ft
        </td>
    @else 
        <td colspan=4 class="psOpen" data-psid="{{ $s->ps_id }}" ><i class="txtDanger">
            {!! $GLOBALS["SL"]->getNodePageName($s->ps_submission_progress) !!}
        </i></td>
    @endif
    </td>
    </tr>
@empty
    <tr><td colspan=8 class="slGrey"><i>No records found.</i></td></tr>
@endif
</table>