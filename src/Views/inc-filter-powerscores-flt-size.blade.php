<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-size.blade.php -->

<div class="col-md-4 pB10">
    <select name="fltSize" id="fltSizeID" autocomplete="off" 
        class="form-control psChageFilter ntrStp slTab"
        {!! $GLOBALS["SL"]->tabInd() !!} >
        <option value="0" 
            @if (!isset($fltSize) || intVal($fltSize) == 0) SELECTED @endif 
            >All Farm Sizes (Square feet of flower canopy, annual average)
            </option>
        <option value="375" 
            @if (isset($fltSize) && $fltSize == 375) SELECTED @endif 
            >&lt; 5,000 square feet</option>
        <option value="376" 
            @if (isset($fltSize) && $fltSize == 376) SELECTED @endif 
            >5,000-10,000 sf</option>
        <option value="431" 
            @if (isset($fltSize) && $fltSize == 431) SELECTED @endif 
            >10,000-30,000 sf</option>
        <option value="377" 
            @if (isset($fltSize) && $fltSize == 377) SELECTED @endif 
            >30,000-50,000 sf</option>
        <option value="378" 
            @if (isset($fltSize) && $fltSize == 378) SELECTED @endif 
            >50,000+ sf</option>
    </select>
</div>
