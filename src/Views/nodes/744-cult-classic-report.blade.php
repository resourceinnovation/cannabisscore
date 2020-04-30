<!-- generated from resources/views/vendor/cannabisscore/nodes/744-cult-classic-report.blade.php -->

<div class="slCard nodeWrap">
    <h1 class="slBlueDark">Cultivation Classic Final Report</h1>
    <h4>
        Out of <span class="slBlueDark">{{ sizeof($namesChecked) }}</span> farms, 
        <span class="slBlueDark">{{ $farmTots[1] }}</span> successfully completed, and 
        <span class="slBlueDark">{{ $farmTots[0] }}</span> more attempted.
    </h4>
    <a class="btn btn-secondary mT10" href="?excel=1"
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
        Export to Excel
    </a>
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