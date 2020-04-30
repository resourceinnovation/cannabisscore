<!-- generated from resources/views/vendor/cannabisscore/nodes/744-cult-classic-report-innertable.blade.php -->

<tr><b>
<th>Farm Name</th>
<th>Points</th>
<th>Complete?</th>
<th>Score ID#</th>
<th class="brdLftGrey">Overall</th>
<th>Facility Rank</th>
<th>Production Rank</th>
<th>HVAC Rank</th>
<th>Lighting Rank</th>
<th class="brdLftGrey">Facility Score <div class="fPerc66 slGrey">kBtu / sq ft</div></th>
<th>Production Score <div class="fPerc66 slGrey">g / kBtu</div></th>
<th>HVAC Score <div class="fPerc66 slGrey">kBtu / sq ft</div></th>
<th>Lighting Score <div class="fPerc66 slGrey">kWh / day</div></th>
<th>Lighting Power Density <div class="fPerc66 slGrey">W / sq ft</div></th>
<th class="brdLftGrey">Grams</th>
<th>kWh</th>
<th>Sq Ft</th>
<th class="brdLftGrey">County</th>
<th></th>
<th>Email</th>
<th>Submitted</th>
<th>URL</th>
</b></tr>

@forelse ($farms as $i => $f)
    <tr>
    <td class=" 
        @if ($f['ps'] 
            && isset($f['ps']->ps_status) 
            && in_array($f['ps']->ps_status, [ 
                $GLOBALS['SL']->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS['SL']->def->getID('PowerScore Status', 'Archived') 
            ])) slGreenDark
        @else txtDanger @endif " >
        @if (isset($f["name"]) && trim($f["name"]) != '') {{ $f["name"] }} @endif
    </td>
    @if (!isset($f["ps"]) || !isset($f["ps"]->ps_id))
        <td class="txtDanger"><b>0</b></td>
        <td colspan="2" >No 
            @if ($GLOBALS["SL"]->REQ->has("search") && sizeof($f["srch"]) > 0)
                <span class="slGrey fPerc80">
                @foreach ($f["srch"] as $psID => $psName)
                    , <a href="/calculated/u-{{ $psID }}" target="_blank">{{ $psName }}</a>
                @endforeach </span>
            @endif
        </td>
        <td colspan="16" class="brdLftGrey">&nbsp;</td>
    @else
        <td @if (in_array($f["ps"]->ps_status, [
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived') 
                ])) class="slGreenDark"
            @else class="txtDanger" 
            @endif ><b>
            @if ($f["ps"]->ps_status 
                == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete')) 0
            @elseif ($f["ps"]->ps_effic_over_similar > 66) 2
            @elseif ($f["ps"]->ps_effic_over_similar > 33) 1.5
            @else 1
            @endif
        </b></td>
        <td>
            @if (in_array($f["ps"]->ps_status, [ 
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived')
                ])) Yes 
            @else No 
            @endif
        </td>
        @if ($GLOBALS["SL"]->REQ->has('excel'))
            <td>#{{ $f["ps"]->ps_id }}</td>
        @else
            <td>
                <a href="/calculated/u-{{ $f['ps']->ps_id }}" target="_blank"
                @if ($f["ps"]->ps_status 
                    == $GLOBALS["SL"]->def->getID('PowerScore Status', 'Incomplete'))
                    class="txtDanger"
                @endif >#{{ $f["ps"]->ps_id }}</a>
            </td>
        @endif
        @if (in_array($f["ps"]->ps_status, [
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Complete'),
                $GLOBALS["SL"]->def->getID('PowerScore Status', 'Archived') ]))
            @if (!isset($f["ranks"]) || !$f["ranks"])
                <td colspan=5 class="brdLftGrey"><i>ranking not found</i></td>
            @else
                <td class="brdLftGrey">{{ round($f["ps"]->ps_effic_over_similar) }}%</td>
                <td>{{ round($f["ranks"]->ps_rnk_facility) }}%</td>
                <td>{{ round($f["ranks"]->ps_rnk_production) }}%</td>
                <td>{{ round($f["ranks"]->ps_rnk_hvac) }}%</td>
                <td>{{ round($f["ranks"]->ps_rnk_lighting) }}%</td>
            @endif
            <td class="brdLftGrey">
                {{ $GLOBALS["SL"]->sigFigs($f["ps"]->ps_effic_facility, 3) }}
            </td>
            <td>{{ $GLOBALS["SL"]->sigFigs($f["ps"]->ps_effic_production, 3) }}</td>
            <td>
                @if ($f["ps"]->ps_effic_hvac > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($f["ps"]->ps_effic_hvac, 3) }}
                @else 0 
                @endif
            </td>
            <td>
                @if ($f["ps"]->ps_effic_lighting > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($f["ps"]->ps_effic_lighting, 3) }}
                @else 0 
                @endif
            </td>
            <td>
                @if ($f["ps"]->ps_lighting_power_density > 0.00001)
                    {{ $GLOBALS["SL"]->sigFigs($f["ps"]
                        ->ps_lighting_power_density, 3) }}
                @else 0 
                @endif
            </td>
            <td class="brdLftGrey">{{ number_format($f["ps"]->ps_grams) }}</td>
            <td>{{ number_format($f["ps"]->ps_kwh) }}</td>
            <td>{{ number_format($f["ps"]->ps_flower_canopy_size) }}</td>
        @else
            <td colspan=13 class="brdLftGrey txtDanger" >
                <i>Page: {!! 
                    $GLOBALS["SL"]->getNodePageName($f["ps"]->ps_submission_progress) 
                !!}</i>
            </td>
        @endif
        <td class="brdLftGrey">
            {{ $f["ps"]->ps_county }} {{ $f["ps"]->ps_state }}
        </td>
        <td>
            @if (isset($f["ps"]->ps_email) && trim($f["ps"]->ps_email) != '')
                {{ $f["ps"]->ps_email }}
            @endif
        </td>
        <td>{{ date("n/j/Y", strtotime($f["ps"]->created_at)) }}</td>
        <td>
            @if ($GLOBALS["SL"]->REQ->has('excel')) 
                http://cannabispowerscore.org/calculated/u-{{ $f["ps"]->ps_id }}
            @else
                <a href="/calculated/u-{{ $f['ps']->ps_id }}" target="_blank"
                    >http://cannabispowerscore.org/calculated/u-{{ $f["ps"]->ps_id }}</a>
            @endif
        </td>
    @endif
    </tr>
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif