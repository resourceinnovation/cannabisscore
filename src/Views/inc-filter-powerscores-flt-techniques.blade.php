<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-techniques.blade.php -->

<div id="fltTechniquesWrap" class="col-md-4 pB10"
    @if (!isset($fltTechniques) || intVal($fltTechniques) <= 0) style="display: none;" @endif >
    <div class="w100 round5" style="background: #fff;">
        <select name="fltTechniques" id="fltTechniquesID" autocomplete="off" 
            class="form-control psChangeFilter ntrStp slTab"
            {!! $GLOBALS["SL"]->tabInd() !!} >
            <option value="0" 
                @if (!isset($fltTechniques) || intVal($fltTechniques) == 0) SELECTED @endif 
                >All Techniques</option>
            <option value="1" 
                @if (isset($fltTechniques) && intVal($fltTechniques) == 1) SELECTED @endif 
                >Perpetual Harvesting</option> <!-- fltPerp -->
            <option value="2" 
                @if (isset($fltTechniques) && intVal($fltTechniques) == 2) SELECTED @endif 
                >Water Pumps</option> <!-- fltPump -->
            <option value="3" 
                @if (isset($fltTechniques) && intVal($fltTechniques) == 3) SELECTED @endif 
                >Manual Environmental Controls</option> <!-- fltManu -->
            <option value="4" 
                @if (isset($fltTechniques) && intVal($fltTechniques) == 4) SELECTED @endif 
                >Automatic Environmental Controls</option> <!-- fltAuto -->
            <option value="5" 
                @if (isset($fltTechniques) && intVal($fltTechniques) == 5) SELECTED @endif 
                >Vertical Stacking</option> <!-- fltVert -->
        </select>
    </div>
</div>
