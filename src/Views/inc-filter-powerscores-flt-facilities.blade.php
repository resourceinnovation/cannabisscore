<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-facilities.blade.php -->

@if ((!isset($GLOBALS["SL"]->x["officialSet"])
        || !$GLOBALS["SL"]->x["officialSet"])
    && Auth::user() 
    && Auth::user()->hasRole('partner')
    && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
    
    <div class="col-md-4 pB10"> 
        <select name="fltFacility" id="fltFacilityID" 
            class="form-control psChageFilter ntrStp slTab"
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0"
                @if (!isset($fltFacility) 
                    || in_array(trim($fltFacility), ["", "0"])) 
                    SELECTED
                @endif >All Facilities</option>
        @foreach ($GLOBALS['SL']->x['usrInfo']->companies[0]->facs as $i => $fac)
            <option value="{{ (1+$i) }}" 
                @if (isset($fltFacility)
                    && intVal($fltFacility) == (1+$i))
                    SELECTED
                @endif >Facility: {{ $fac->name }}</option>
        @endforeach
        </select>
    </div>

@endif
