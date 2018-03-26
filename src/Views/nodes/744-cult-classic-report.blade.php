<!-- generated from resources/views/vendor/cannabisscore/nodes/744-cult-classic-report.blade.php -->
<a class="btn btn-default pull-right" href="?excel=1"><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
    Export to Excel</a>
<h1 class="mT0 slBlueDark">Cultivation Classic Final Report</h1>
<div class="fPerc125 mB10">
Out of <span class="slBlueDark">{{ sizeof($farms) }}</span> farms, <span class="slBlueDark">{{ $farmTots[1] }}</span> 
successfully completed, and <span class="slBlueDark">{{ $farmTots[0] }}</span> more attempted.
</div>
<table border=0 class="table table-striped w100">
{!! view('vendor.rii.nodes.744-cult-classic-report-innertable', [ "farms" => $farms ])->render() !!}
</table>
<div class="p20"></div>
@if (isset($entryFarmNames) && trim($entryFarmNames) != '')
    <i class="fPerc133">All submission attempts with farm names:</i><br />
    {!! $entryFarmNames !!}
@endif