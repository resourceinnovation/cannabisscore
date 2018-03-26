<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs.blade.php -->
<a class="pull-right btn btn-default mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
<h1 class="mT0 slBlueDark">Completed PowerScore Averages</h1>
<div class="slGrey"><i>Categories with at least five scores.</i></div>
<table border=0 class="table table-striped w100">
{!! view('vendor.rii.nodes.170-avg-powerscores-innertable', [ "allAvgs" => $allAvgs ])->render() !!}
</table>