<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-cmpl.blade.php -->

<div class="col-md-4 pB10"> 
    <div class="w100 round5" style="background: #fff;">
        <select name="fltCmpl" id="fltCmplID" autocomplete="off" 
            class="form-control psChangeFilter ntrStp slTab"
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="243" 
                @if (!isset($fltCmpl) || $fltCmpl == 243) SELECTED @endif 
                >Cannabis Ranked Data Set</option>
            <option value="667" 
                @if (isset($fltCmpl) && $fltCmpl == 667) SELECTED @endif 
                >CEA Ranked Data Set</option>
            <option value="597" 
                @if (isset($fltCmpl) && $fltCmpl == 597) SELECTED @endif 
                >Home Grow</option>
            <option value="364" 
                @if (isset($fltCmpl) && $fltCmpl == 364) SELECTED @endif 
                >Archived Scores</option>
            <option value="556" 
                @if (isset($fltCmpl) && $fltCmpl == 556) SELECTED @endif 
                >New / Unreviewed Scores</option>
            <option value="242" 
                @if (isset($fltCmpl) && $fltCmpl == 242) SELECTED @endif 
                >Incomplete Scores</option>
            <option value="0" 
                @if (isset($fltCmpl) && intVal($fltCmpl) == 0) SELECTED @endif 
                >( All )</option>
        </select>
    </div>
</div>
