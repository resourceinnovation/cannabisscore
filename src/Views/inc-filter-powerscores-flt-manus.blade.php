<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-manus.blade.php -->

@if (Auth::user())
    @if (Auth::user()->hasRole('administrator|staff')
        || (Auth::user()->hasRole('partner') 
            && is_array($GLOBALS["SL"]->x["partnerManuIDs"])
            && sizeof($GLOBALS["SL"]->x["partnerManuIDs"]) > 0
            && sizeof($manuList) > 0))
        <div class="col-md-4 pB10">
            <select name="fltManuLgt" id="fltManuLgtID" 
                class="form-control psChageFilter ntrStp slTab"
                autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                <option value="0" 
                    @if (!isset($fltManuLgt) 
                        || in_array(trim($fltManuLgt), ["", "0"])) 
                        SELECTED
                    @endif >All Light Manufacturers</option>
        @forelse ($manuList as $m => $manu)
            @if (Auth::user()->hasRole('administrator|staff')
                || (Auth::user()->hasRole('partner')
                    && isset($GLOBALS["SL"]->x["partnerManuIDs"])
                    && in_array($manu->manu_id, 
                        $GLOBALS["SL"]->x["partnerManuIDs"])))
                <option value="{{ $manu->manu_id }}" 
                    @if (isset($fltManuLgt) 
                        && intVal($fltManuLgt) == $manu->manu_id) 
                        SELECTED 
                    @endif >{{ $manu->manu_name }}</option>
            @endif
        @empty
        @endforelse
            </select>
        </div>
    @endif
@endif