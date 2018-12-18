<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs.blade.php -->

<!--- <a class="float-right btn btn-secondary mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
<h1 class="slBlueDark">PowerScore Category Averages</h1>
<p>
&darr; <a href="#farm" class="mL5 mR5">Farm Types</a> - 
<a href="#cups" class="mL5 mR5">Competitions</a> - 
<a href="#flw-lgty" class="mL5 mR5">Lighting by Growth Stage</a>
<a href="#tech" class="mL5 mR5">Techniques</a> - 
<a href="#powr" class="mL5 mR5">Power Sources</a>
</p>

@foreach ($scoreSets as $i => $set)
    <a name="{{ $set[0] }}"></a><div class="p20"></div>
    <h3 id="tblTitle{{ $i }}">{{ (1+$i) }}. {{ $set[1] }}</h3>
    {!! $set[2] !!}
@endforeach

<style> #tblTitle7 { display: none; } </style>