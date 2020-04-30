<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-lght.blade.php -->

<div class="col-md-4 pB10">
    <select name="fltLght" id="fltLghtID" 
        class="form-control psChageFilter ntrStp slTab" 
        autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
        <option value="" @if (!$GLOBALS["SL"]->REQ->has('fltLght') 
            || trim($GLOBALS["SL"]->REQ->get('fltLght')) == '') SELECTED @endif 
            >All Light Types</option>
        <option value="168" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '168') SELECTED @endif 
            >HID (double-ended HPS)</option>
        <option value="169" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '169') SELECTED @endif 
            >HID (single-ended HPS)</option>
        <option value="170" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '170') SELECTED @endif 
            >HID (double-ended MH)</option>
        <option value="171" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '171') SELECTED @endif 
            >HID (single-ended MH)</option>
        <option value="164" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '164') SELECTED @endif 
            >CMH</option>
        <option value="165" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '165') SELECTED @endif 
            >Fluorescent</option>
        <option value="203" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == '203') SELECTED @endif 
            >LED</option>
    @foreach ($areaTypesFilt as $type => $defID)
        <option value="" DISABLED > </option>
        <option value="{{ $defID }}-168" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-168') SELECTED @endif 
            >{{ $type }} - HID (double-ended HPS)</option>
        <option value="{{ $defID }}-169" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-169') SELECTED @endif 
            >{{ $type }} - HID (single-ended HPS)</option>
        <option value="{{ $defID }}-170" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-170') SELECTED @endif 
            >{{ $type }} - HID (double-ended MH)</option>
        <option value="{{ $defID }}-171" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-171') SELECTED @endif 
            >{{ $type }} - HID (single-ended MH)</option>
        <option value="{{ $defID }}-164" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-164') SELECTED @endif 
            >{{ $type }} - CMH</option>
        <option value="{{ $defID }}-165" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-165') SELECTED @endif 
            >{{ $type }} - Fluorescent</option>
        <option value="{{ $defID }}-203" @if ($GLOBALS["SL"]->REQ->has('fltLght') 
            && trim($GLOBALS["SL"]->REQ->get('fltLght')) == $defID . '-203') SELECTED @endif 
            >{{ $type }} - LED</option>
    @endforeach
    </select>
</div>