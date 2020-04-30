<!-- generated from resources/views/vendor/cannabisscore/nodes/1381-cult-classic-multi-year.blade.php -->

<div class="slCard nodeWrap">
    <h1 class="slBlueDark">Cultivation Classic Multi-Year Report</h1>
</div>

<div class="slCard nodeWrap">
    <table border=0 class="table table-striped w100">

<?php $cnt = 0; ?>
@forelse ($farms as $name => $farm)
    @if (sizeof($farm) > 1)
        @if ($cnt%5 == 0)
            @if ($cnt > 0) <tr><td colspan=17 >&nbsp;</td></tr> @endif
            {!! view(
                'vendor.cannabisscore.nodes.1381-cult-classic-multi-year-headers'
            )->render() !!}
        @endif
        <?php $cnt++; ?>
        <tr><td colspan=17 ><h4>{{ $name }}</h4></td></tr>
        @foreach ($farm as $year => $f)
            <tr>
            <td>{{ $year }}</td>
            <td><a href="/calculated/u-{{ $f['ps']->ps_id }}" 
                target="_blank">#{{ $f["ps"]->ps_id }}</a></td>
            <td>{{ $GLOBALS["SL"]->def->getVal(
                'PowerScore Status', 
                $f["ps"]->ps_status
            ) }}</td>
            <td>{{ str_replace('Greenhouse/Hybrid/Mixed Light', 'Hybrid', $GLOBALS["SL"]->def->getVal(
                'PowerScore Farm Types', 
                $f["ps"]->ps_characterize
            )) }}</td>
            @if (!isset($f["rnk"]) || !$f["rnk"])
                <td colspan=5 class="brdLftGrey"><i>ranking not found</i></td>
            @else
                <td class="brdLftGrey">{{ round($f["ps"]->ps_effic_over_similar) }}%</td>
                <td>{{ round($f["rnk"]->ps_rnk_facility) }}%</td>
                <td>{{ round($f["rnk"]->ps_rnk_production) }}%</td>
                <td>{{ round($f["rnk"]->ps_rnk_hvac) }}%</td>
                <td>{{ round($f["rnk"]->ps_rnk_lighting) }}%</td>
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

            <td class="brdLftGrey">{{ date("n/j/Y", strtotime($f["ps"]->created_at)) }}</td>

            </tr>
        @endforeach
    @endif
@empty
    <tr><td colspan=8 ><i>No records found.</i></td></tr>
@endif

    </table>

</div>