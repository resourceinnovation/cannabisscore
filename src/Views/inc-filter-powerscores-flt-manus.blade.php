<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-manus.blade.php -->


@if (Auth::user() && sizeof($manuList) > 0)
    @if (Auth::user()->hasRole('administrator|staff')
        || (Auth::user()->hasRole('partner') 
            && isset($GLOBALS["SL"]->x["partnerVersion"])
            && $GLOBALS["SL"]->x["partnerVersion"]
            && isset($GLOBALS["SL"]->x["partnerLevel"])
            && intVal($GLOBALS["SL"]->x["partnerLevel"]) >= 4))
        <div id="fltManuLgtWrap" class="col-md-4 pB10"
            @if (!isset($fltManuLgt) || in_array(trim($fltManuLgt), ['', '0'])) style="display: none;" @endif >
            <div class="w100 round5" style="background: #fff;">
                <select name="fltManuLgt" id="fltManuLgtID" 
                    class="form-control psChangeFilter ntrStp slTab"
                    autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                    <option value="0" 
                        @if (!isset($fltManuLgt) 
                            || in_array(trim($fltManuLgt), ["", "0"])) 
                            SELECTED
                        @endif >All Light Manufacturers</option>
            @forelse ($manuList as $m => $manu)
                @if ( Auth::user()->hasRole('administrator|staff')
                    || ( Auth::user()->hasRole('partner')
                        && ( (isset($GLOBALS["SL"]->x["partnerVersion"])
                                && $GLOBALS["SL"]->x["partnerVersion"]
                                && isset($GLOBALS["SL"]->x["partnerLevel"])
                                && intVal($GLOBALS["SL"]->x["partnerLevel"]) >= 4)
                            || (isset($GLOBALS["SL"]->x["partnerManuIDs"])
                                && in_array($manu->manu_id, 
                                    $GLOBALS["SL"]->x["partnerManuIDs"])) ) ) )
                    <option value="{{ $manu->manu_id }}" 
                        @if (isset($fltManuLgt) 
                            && intVal($fltManuLgt) == $manu->manu_id) 
                            SELECTED 
                        @endif >{{ $manu->manu_name }}</option>
                @endif
            @empty
            @endforelse
                </select>
                @if (isset($fltManuLgt)) <!-- fltManuLgt: {{ $fltManuLgt }} --> @endif
            </div>
        </div>
    @endif
@endif