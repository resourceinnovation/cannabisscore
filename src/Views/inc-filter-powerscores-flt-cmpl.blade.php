<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-cmpl.blade.php -->

<div class="col-md-4 pB10"> 
    <select name="fltCmpl" id="fltCmplID" autocomplete="off" 
        class="form-control psChageFilter ntrStp slTab"
        {!! $GLOBALS["SL"]->tabInd() !!} >
        <option value="242" 
            @if (isset($fltCmpl) && $fltCmpl == 242) SELECTED @endif 
            >Incomplete Scores</option>
        <option value="243" 
            @if (!isset($fltCmpl) || $fltCmpl == 243) SELECTED @endif 
            >Completed Scores</option>
        <option value="364" 
            @if (isset($fltCmpl) && $fltCmpl == 364) SELECTED @endif 
            >Archived Scores</option>
        <option value="0" 
            @if (isset($fltCmpl) && intVal($fltCmpl) == 0) SELECTED @endif 
            >All</option>
    </select>
</div>

@if (Auth::user() && Auth::user()->hasRole('administrator|staff'))
    <div class="col-md-4 pB10"> 
        <select name="fltFut" id="fltFutID" autocomplete="off" 
            class="form-control psChageFilter ntrStp slTab"
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="232" 
                @if (!isset($fltFut) || $fltFut == 232) SELECTED @endif
                >Past-Looking Scores</option>
            <option value="233" 
                @if (isset($fltFut) && $fltFut == 233) SELECTED @endif
                >Future-Looking Scores</option>
        </select>
    </div>

    <div class="col-md-4 pB10"> 
        <select name="fltCup" id="fltCupID" autocomplete="off" 
            class="form-control psChageFilter ntrStp slTab"
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
@endif