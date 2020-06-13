<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs.blade.php -->

<div class="slCard nodeWrap">
    <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        @if (trim($fltStateClim) != '') 
            href="?excel=1&fltStateClim={{ $fltStateClim }}"
        @else 
            href="?excel=1"
        @endif
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
        Excel</a>
    <h1 class="slBlueDark">
        @if ($nID == 801
            && isset($GLOBALS["SL"]->x["partnerCompany"])
            && trim($GLOBALS["SL"]->x["partnerCompany"]) != '')
            {{ $GLOBALS["SL"]->x["partnerCompany"] }}
        @else 
            Ranked Data Set
        @endif
        Averages <nobr>by Category</nobr>
    </h1>

    {!! view('vendor.cannabisscore.inc-partner-ref-disclaim')->render() !!}

    <div class="row">
        <div class="col-8">
            <p>
            &darr; <a href="#farm" class="mL5 mR5">Farm Types</a> - 
            <!-- <a href="#cups" class="mL5 mR5">Competitions</a> - -->
            <a href="#flw-lgty" class="mL5 mR5">Lighting by Growth Stage</a> - 
            <a href="#tech" class="mL5 mR5">Techniques</a> - 
            <a href="#powr" class="mL5 mR5">Power Sources</a>
            </p>
        </div>
        <div class="col-4">
            <select name="fltStateClim" id="fltStateClimID" 
                class="form-control form-control-lg" autocomplete="off"
                onChange="window.location='?fltStateClim='+this.value;">
                <option value="" @if (trim($fltStateClim) == '') SELECTED @endif
                    >All Climates and States</option>
                <option disabled ></option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
            </select>
        </div>
    </div>
</div>

@foreach ($scoreSets as $i => $set)
    <a name="{{ $set[0] }}"></a>
    <div id="tblCard{{ $i }}" class="slCard nodeWrap">
    <h3 id="tblTitle{{ $i }}">{{ (1+$i) }}. {{ $set[1] }}</h3>
    @if (isset($set[2])) {!! $set[2] !!} 
    @else <i>No completed records found.</i>
    @endif
    </div>
@endforeach

<style>
body { overflow-x: visible; }
#tblTitle7 { display: none; }
#tblCard7 { border-top: 0px none; }
</style>
