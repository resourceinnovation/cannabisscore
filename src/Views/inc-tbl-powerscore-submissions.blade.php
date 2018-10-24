<!-- generated from resources/views/vendor/cannabisscore/inc-tbl-powerscore-submissions.blade.php -->
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
@forelse ($scores as $i => $s)
    <tr id="psRowA{{ $s->PsID }}" class="psRowA1" >
    <td>
        <a href="/calculated/u-{{ $s->PsID }}" target="_blank" class="mR10 mT10"
            ><i class="fa fa-external-link" aria-hidden="true"></i></a></nobr>
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        <nobr><a href="javascript:;" class="btn btn-primary" 
            >#{{ $s->PsID }}</a>
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        <div class="fPerc125 bld mTn5"> @if (isset($s->PsName) && trim($s->PsName) != '') {{ $s->PsName }} @endif </div>
        {{ $s->PsCounty }} {{ $s->PsState }}
        @if (isset($s->PsEmail) && trim($s->PsEmail) != '') 
            <br /><a href="mailto:{{ $s->PsEmail }}">{{ $s->PsEmail }}</a>
        @endif
    </td><td class="psOpen" data-psid="{{ $s->PsID }}">
        @if (isset($s->PsStatus))
            <div class=" @if ($s->PsStatus == $GLOBALS['SL']->def->getID('PowerScore Status', 'Complete')) 
            slGreenDark @else slRedDark @endif ">
            {{ $GLOBALS["SL"]->def->getVal('PowerScore Status', $s->PsStatus) }}</div>
        @endif
        {{ date("n/j, g:ia", strtotime($s->created_at)) }}
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
                    }} W / sq ft<br /> @endif
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
        <td colspan=4 class="psOpen" data-psid="{{ $s->PsID }}" ><i class="slRedDark">
            {!! $GLOBALS["SL"]->getNodePageName($s->PsSubmissionProgress) !!}
        </i></td>
    @endif
    </td>
    </tr>
@empty
    <tr><td colspan=8 class="slGrey"><i>No records found.</i></td></tr>
@endif
</table>