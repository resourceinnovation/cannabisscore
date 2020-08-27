<!-- Stored in resources/views/cannabisscore/powerscore-report-preview.blade.php -->
<a href="/calculated/read-{{ $sessData['powerscore'][0]->getKey() }}"
    ><h4 class="mT0">Overall PowerScore: 
    @if ((!isset($sessData["powerscore"][0]->ps_kwh)
            || intVal($sessData["powerscore"][0]->ps_kwh) <= 0)
        && (isset($sessData["powerscore"][0]->ps_upload_energy_bills)
        && intVal($sessData["powerscore"][0]->ps_upload_energy_bills) == 1))
        Pending<sup>*</sup>
    @else
        {!! round($sessData["powerscore"][0]->ps_effic_over_similar) !!}%
    @endif
    </h4></a>
@if ((!isset($sessData["powerscore"][0]->ps_kwh)
        || intVal($sessData["powerscore"][0]->ps_kwh) <= 0)
    && (isset($sessData["powerscore"][0]->ps_upload_energy_bills)
    && intVal($sessData["powerscore"][0]->ps_upload_energy_bills) == 1))
    <div class="pB10">
        <sup>*</sup> The RII Team is reviewing your uploaded energy bills.
    </div>
@endif
<div class="row slReportPreview">
    <div class="col-8 pB10">
        <table class="table table-striped">
        @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
            && $sessData["powerscore"][0]->ps_effic_facility > 0)
            <tr>
                <td><nobr>Electric Facility</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_facility, 
                    3
                ) !!}</td>
                <td><nobr>kBtu / sq ft</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_non_electric) 
            && $sessData["powerscore"][0]->ps_effic_non_electric > 0)
            <tr>
                <td><nobr>Non-Electric Facility</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_non_electric, 
                    3
                ) !!}</td>
                <td><nobr>kBtu / sq ft</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_production) 
            && $sessData["powerscore"][0]->ps_effic_production > 0)
            <tr>
                <td><nobr>Electric Production</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_production, 
                    3
                ) !!}</td>
                <td><nobr>grams / kBtu</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
            && $sessData["powerscore"][0]->ps_effic_lighting > 0)
            <tr>
                <td><nobr>Lighting</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_lighting, 
                    3
                ) !!}</td>
                <td><nobr>W / sq ft</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_hvac) 
            && $sessData["powerscore"][0]->ps_effic_hvac > 0)
            <tr>
                <td><nobr>HVAC</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_hvac, 
                    3
                ) !!}</td>
                <td><nobr>kWh / sq ft</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_water) 
            && $sessData["powerscore"][0]->ps_effic_water > 0)
            <tr>
                <td><nobr>Water</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_water, 
                    3
                ) !!}</td>
                <td><nobr>gallons / sq ft</nobr></td>
            </tr>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_waste) 
            && $sessData["powerscore"][0]->ps_effic_waste > 0)
            <tr>
                <td><nobr>Waste</nobr></td>
                <td>{!! $GLOBALS["SL"]->sigFigs(
                    $sessData["powerscore"][0]->ps_effic_waste, 
                    3
                ) !!}</td>
                <td><nobr>lbs / sq ft</nobr></td>
            </tr>
        @endif
        </table>
    </div>
    <div class="col-4 pB10 slGrey">
        <div>
            <a href="/calculated/read-{{ $sessData['powerscore'][0]->getKey() }}"
                ><i class="slGrey">PowerScore ID<br />
                    #{{ $sessData["powerscore"][0]->getKey() }}</i></a><br />
            <i>Submitted: 
            {{ date('n/j/y', strtotime($sessData["powerscore"][0]->created_at)) }}</i>
        </div>
        @if (isset($GLOBALS["SL"]->x["psCompany"][$sessData["powerscore"][0]->ps_id])
            && trim($GLOBALS["SL"]->x["psCompany"][$sessData["powerscore"][0]->ps_id]) != '')
            {!! $GLOBALS["SL"]->x["psCompany"][$sessData["powerscore"][0]->ps_id] !!}
        @endif
        @if ($uID == $sessData["powerscore"][0]->ps_user_id)
            <div class="mT5"><a href="/cpySess/1/{{ $sessData['powerscore'][0]->getKey() 
                }}?redir={{ urlencode('/u/calculator/your-farm') }}" class="btn btn-sm btn-secondary"
                ><i class="fa fa-files-o" aria-hidden="true"></i> Create A Copy</a></div>
        @endif
    </div>
</div>