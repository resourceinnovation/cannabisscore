<!-- generated from resources/views/vendor/cannabisscore/nodes/744-cult-classic-report.blade.php -->

<div class="slCard nodeWrap">
    <h1 class="mT0 slBlueDark">Cultivation Classic Final Report {{ $year }}</h1>
    <h4>
        Out of <span class="slBlueDark">{{ sizeof($namesChecked) }}</span> farms, 
        <span class="slBlueDark">{{ $farmTots[1] }}</span> successfully completed, and 
        <span class="slBlueDark">{{ $farmTots[0] }}</span> more attempted.
    </h4>
    <p>The {{ $year }} competition reports facilities' performance from {{ ($year-1) }}.</p>
    <div class="pB30">
        <select class="form-control pull-left mR15" style="width: 80px;"
            onChange="window.location='?year='+this.value;">
        @for ($yr = 2018; $yr <= intVal(date("Y")); $yr++)
            <option value="{{ $yr }}" @if ($yr == $year) SELECTED @endif
                >{{ $yr }}</option>
        @endfor
        </select>
        <a class="btn btn-secondary pull-left mR15" href="?excel=1"
            ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
            Export to Excel
        </a>
        <a class="btn btn-secondary pull-left mR15" 
            href="/dash/cultivation-classic-multi-year-report"
            ><i class="fa fa-bar-chart mR5" aria-hidden="true"></i>
            Multi-Year Report
        </a>
    </div>
</div>

<div class="slCard nodeWrap">
<table border=0 class="table table-striped w100">
{!! view(
    'vendor.cannabisscore.nodes.744-cult-classic-report-innertable', 
    [ "farms" => $farms ]
)->render() !!}
</table>
<div class="p20"></div>
@if (isset($entryFarmNames) && trim($entryFarmNames) != '')
    <i class="fPerc133">All submission attempts with farm names:</i><br />
    {!! $entryFarmNames !!}
@endif

@if (isset($reportExtras)) {!! $reportExtras !!} @endif
</div>