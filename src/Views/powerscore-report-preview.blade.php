<!-- Stored in resources/views/cannabisscore/powerscore-report-preview.blade.php -->
<a href="/calculated/read-{{ $sessData['PowerScore'][0]->getKey() }}"
    ><h3 class="mT0">Overall PowerScore: {!! round($sessData["PowerScore"][0]->PsEfficOverall) !!}%</h3></a>
<div class="row slReportPreview">
    <div class="col-6 pB10">
        <div class="pL20">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
            <div class="row"><div class="col-4">Facility:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) !!}
            </div><div class="col-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
            <div class="row"><div class="col-4">Production:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) !!}
            </div><div class="col-5"><nobr>grams / kWh</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
            <div class="row"><div class="col-4">HVAC:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) !!}
            </div><div class="col-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
            <div class="row"><div class="col-4">Lighting:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) !!}
            </div><div class="col-5"><nobr>W / sq ft</nobr></div></div>
        @endif
        </div>
    </div>
    <div class="col-1"></div>
    <div class="col-5 pB10 slGrey">
        <a href="/calculated/read-{{ $sessData['PowerScore'][0]->getKey() }}"
            ><i class="slGrey">PowerScore ID# {{ $sessData["PowerScore"][0]->getKey() }}</i></a><br />
        <i>Submitted: {{ date('n/j/y', strtotime($sessData["PowerScore"][0]->updated_at)) }}</i>
        @if ($uID == $sessData["PowerScore"][0]->PsUserID)
            <div class="mT5"><a href="/cpySess/1/{{ $sessData['PowerScore'][0]->getKey() 
                }}?redir={{ urlencode('/u/calculator/your-farm') }}" class="btn btn-sm btn-secondary"
                ><i class="fa fa-files-o" aria-hidden="true"></i> Create A Copy</a></div>
        @endif
    </div>
</div>