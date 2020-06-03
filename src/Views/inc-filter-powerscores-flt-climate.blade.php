<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-climate.blade.php -->

<div class="col-md-4 pB10">
    <select name="fltStateClim" id="fltStateClimID" 
        class="form-control psChageFilter ntrStp slTab"
        autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
        <option value="" @if (isset($fltStateClim) && trim($fltStateClim) == '') 
                SELECTED
            @endif >All Climates and States</option>
        <option disabled ></option>
        {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
    </select>
    @if (isset($fltStateClim)) <!-- fltStateClim: {{ $fltStateClim }} --> @endif
</div>
