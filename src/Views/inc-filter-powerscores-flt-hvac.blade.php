<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-hvac.blade.php -->

<div class="col-md-4 pB10">
    <select name="fltHvac" id="fltHvacID" 
        class="form-control psChageFilter ntrStp slTab" 
        autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
        <option value="" @if (!$GLOBALS["SL"]->REQ->has('fltHvac') 
            || trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '') SELECTED @endif 
            >All HVAC Systems</option>
        <option value="247" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '247') SELECTED @endif 
            >System A - Conventional Air Conditioning with 
            Supplemental Portable Dehumidification Units</option>
        <option value="248" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '248') SELECTED @endif 
            >System B - Conventional Air Conditioning 
            with Enhanced Dehumidification</option>
        <option value="249" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '249') SELECTED @endif 
            >System C - Conventional Air Conditioning 
            with Split Dehumidification Systems</option>
        <option value="250" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '250') SELECTED @endif 
            >System D - Fully Integrated Cooling 
            and Dehumidification System</option>
        <option value="356" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '356') SELECTED @endif 
            >System E - Chilled Water Dehumidification System</option>
        <option value="357" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '357') SELECTED @endif 
            >System F - Greenhouse HVAC Systems</option>
        <option value="251" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '251') SELECTED @endif 
            >Other System</option>
        <option value="360" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == '360') SELECTED @endif 
            >None</option>
    @foreach ($areaTypesFilt as $type => $defID)
        <option value="" DISABLED > </option>
        <option value="{{ $defID }}-247" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-247') SELECTED @endif 
            >{{ $type }} - System A - Conventional Air Conditioning 
            with Supplemental Portable Dehumidification Units</option>
        <option value="{{ $defID }}-248" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-248') SELECTED @endif 
            >{{ $type }} - System B - Conventional Air Conditioning 
            with Enhanced Dehumidification</option>
        <option value="{{ $defID }}-249" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-249') SELECTED @endif 
            >{{ $type }} - System C - Conventional Air Conditioning 
            with Split Dehumidification Systems</option>
        <option value="{{ $defID }}-250" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-250') SELECTED @endif 
            >{{ $type }} - System D - Fully Integrated Cooling 
            and Dehumidification System</option>
        <option value="{{ $defID }}-356" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-356') SELECTED @endif 
            >{{ $type }} - System E - Chilled Water 
            Dehumidification System</option>
        <option value="{{ $defID }}-357" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-357') SELECTED @endif 
            >{{ $type }} - System F - Greenhouse HVAC Systems</option>
        <option value="{{ $defID }}-251" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-251') SELECTED @endif 
            >{{ $type }} - Other System</option>
        <option value="{{ $defID }}-360" @if ($GLOBALS["SL"]->REQ->has('fltHvac') 
            && trim($GLOBALS["SL"]->REQ->get('fltHvac')) == $defID . '-360') SELECTED @endif 
            >{{ $type }} - None</option>
    @endforeach
    </select>
</div>