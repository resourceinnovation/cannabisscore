<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-farm.blade.php -->

<div class="col-md-4 pB10">
    <div class="w100 round5" style="background: #fff;">
        <select name="fltFarm" id="filtFarmID" 
            class="form-control psChangeFilter ntrStp slTab" 
            autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0"
                @if (!isset($fltFarm) || $fltFarm == 0)  SELECTED @endif 
                >All Farm Types</option>
            <option value="143" 
                @if (isset($fltFarm) && $fltFarm == 143) SELECTED @endif 
                >Outdoor</option>
            <option value="144" 
                @if (isset($fltFarm) && $fltFarm == 144) SELECTED @endif 
                >Indoor</option>
            <option value="145" 
                @if (isset($fltFarm) && $fltFarm == 145) SELECTED @endif 
                >Greenhouse/Hybrid/Mixed Light</option>
        </select>
    </div>
</div>
