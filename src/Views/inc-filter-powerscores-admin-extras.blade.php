<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-admin-extras.blade.php -->

@if (Auth::user() && Auth::user()->hasRole('administrator|staff'))
    <div id="fltFutWrap" class="col-md-4 pB10"
        @if (!isset($fltFut) 
            || intVal($fltFut) <= 0
            || (isset($fltFut) && intVal($fltFut) == 232)) style="display: none;" @endif >
        <div class="w100 round5" style="background: #fff;">
            <select name="fltFut" id="fltFutID" autocomplete="off" 
                class="form-control psChangeFilter ntrStp slTab"
                {!! $GLOBALS["SL"]->tabInd() !!} >
                <option value="232" 
                    @if (!isset($fltFut) || $fltFut == 232) SELECTED @endif
                    >Past-Looking Scores</option>
                <option value="233" 
                    @if (isset($fltFut) && $fltFut == 233) SELECTED @endif
                    >Future-Looking Scores</option>
            </select>
        </div>
    </div>

    <div id="fltCupWrap" class="col-md-4 pB10"
        @if (!isset($fltCup) || intVal($fltCup) <= 0) style="display: none;" @endif >
        <div class="w100 round5" style="background: #fff;">
            <select name="fltCup" id="fltCupID" autocomplete="off" 
                class="form-control psChangeFilter ntrStp slTab"
                {!! $GLOBALS["SL"]->tabInd() !!} >
                <option value="0" 
                    @if (!isset($fltCup) || intVal($fltCup) == 0) SELECTED @endif 
                    >All Referral Data Sets</option>
                <option value="230" 
                    @if (isset($fltCup) && $fltCup == 230) SELECTED @endif 
                    >Cultivation Classic</option>
                <option value="231" 
                    @if (isset($fltCup) && $fltCup == 231) SELECTED @endif 
                    >Emerald Cup Regenerative Award</option>
                <option value="369" 
                    @if (isset($fltCup) && $fltCup == 369) SELECTED @endif 
                    >NWPCC Import</option>
            </select>
        </div>
    </div>
@endif