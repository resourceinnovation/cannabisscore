<!-- Stored in resources/views/cannabisscore/powerscore-report-preview.blade.php -->
<h3 class="mT0">Overall PowerScore: {!! round($sessData["PowerScore"][0]->PsEfficOverall) !!}%</h3>
<div class="row slReportPreview">
    <div class="col-md-6 pB10">
        <div class="pL20">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility) && $sessData["PowerScore"][0]->PsEfficFacility > 0)
            <div class="row"><div class="col-md-4">Facility:</div><div class="col-md-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) !!}
            </div><div class="col-md-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficProduction) && $sessData["PowerScore"][0]->PsEfficProduction > 0)
            <div class="row"><div class="col-md-4">Production:</div><div class="col-md-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) !!}
            </div><div class="col-md-5"><nobr>grams / kWh</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficHvac) && $sessData["PowerScore"][0]->PsEfficHvac > 0)
            <div class="row"><div class="col-md-4">HVAC:</div><div class="col-md-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficHvac, 3) !!}
            </div><div class="col-md-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficLighting) && $sessData["PowerScore"][0]->PsEfficLighting > 0)
            <div class="row"><div class="col-md-4">Lighting:</div><div class="col-md-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) !!}
            </div><div class="col-md-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        </div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-5 pB10 slGrey">
        <i>PowerScore ID# {{ $sessData["PowerScore"][0]->getKey() }}</i><br />
        <i>Submitted: {{ date('n/j/y', strtotime($sessData["PowerScore"][0]->updated_at)) }}</i>
    </div>
</div>