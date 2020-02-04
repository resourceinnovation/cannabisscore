<!-- Stored in resources/views/cannabisscore/powerscore-report-preview.blade.php -->
<a href="/calculated/read-{{ $sessData['powerscore'][0]->getKey() }}"
    ><h3 class="mT0">Overall PowerScore: {!! 
        round($sessData["powerscore"][0]->ps_effic_overall) !!}%</h3></a>
<div class="row slReportPreview">
    <div class="col-6 pB10">
        <div class="pL20">
        @if (isset($sessData["powerscore"][0]->ps_effic_facility) 
            && $sessData["powerscore"][0]->ps_effic_facility > 0)
            <div class="row"><div class="col-4">Facility:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_facility, 3) !!}
            </div><div class="col-5"><nobr>kBtu / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_production) 
            && $sessData["powerscore"][0]->ps_effic_production > 0)
            <div class="row"><div class="col-4">Production:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_production, 3) !!}
            </div><div class="col-5"><nobr>grams / kBtu</nobr></div></div>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_hvac) 
            && $sessData["powerscore"][0]->ps_effic_hvac > 0)
            <div class="row"><div class="col-4">HVAC:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_hvac, 3) !!}
            </div><div class="col-5"><nobr>kWh / sq ft</nobr></div></div>
        @endif
        @if (isset($sessData["powerscore"][0]->ps_effic_lighting) 
            && $sessData["powerscore"][0]->ps_effic_lighting > 0)
            <div class="row"><div class="col-4">Lighting:</div><div class="col-3 taR">
            {!! $GLOBALS["SL"]->sigFigs($sessData["powerscore"][0]->ps_effic_lighting, 3) !!}
            </div><div class="col-5"><nobr>W / sq ft</nobr></div></div>
        @endif
        </div>
    </div>
    <div class="col-1"></div>
    <div class="col-5 pB10 slGrey">
        <a href="/calculated/read-{{ $sessData['powerscore'][0]->getKey() }}"
            ><i class="slGrey">PowerScore ID# {{ $sessData["powerscore"][0]->getKey() }}</i></a><br />
        <i>Submitted: {{ date('n/j/y', strtotime($sessData["powerscore"][0]->updated_at)) }}</i>
        @if ($uID == $sessData["powerscore"][0]->ps_user_id)
            <div class="mT5"><a href="/cpySess/1/{{ $sessData['powerscore'][0]->getKey() 
                }}?redir={{ urlencode('/u/calculator/your-farm') }}" class="btn btn-sm btn-secondary"
                ><i class="fa fa-files-o" aria-hidden="true"></i> Create A Copy</a></div>
        @endif
    </div>
</div>