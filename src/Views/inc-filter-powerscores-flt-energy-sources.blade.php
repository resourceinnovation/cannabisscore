<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-energy-sources.blade.php -->

<div id="fltRenewWrap" class="col-md-4 pB10"
    @if (!isset($fltRenew) || intVal($fltRenew) <= 0) style="display: none;" @endif >
    <div class="w100 round5" style="background: #fff;">
        <select name="fltRenew" id="fltRenewID" autocomplete="off" 
            class="form-control psChangeFilter ntrStp slTab"
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0" 
                @if (!isset($fltRenew) || intVal($fltRenew) == 0) SELECTED @endif 
                >All Onsite Energy Sources
                </option>
        @foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $def)
            <option value="{{ $def->def_id }}" 
                @if (isset($fltRenew) && intVal($fltRenew) == $def->def_id) SELECTED @endif 
                >{{ $def->def_value }}</option>
        @endforeach
        </select>
    </div>
</div>
