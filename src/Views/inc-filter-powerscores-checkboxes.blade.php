<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-checkboxes.blade.php -->
    <label><input type="checkbox" name="fltPerp" id="fltPerpID" value="1" autocomplete="off"
        @if (isset($fltPerp) && intVal($fltPerp) == 1) CHECKED @endif 
        > <span class="mL5">Perpetual Harvesting</span></label><br />
    <label><input type="checkbox" name="fltPump" id="fltPumpID" value="1" autocomplete="off"
        @if (isset($fltPump) && intVal($fltPump) == 1) CHECKED @endif 
        > <span class="mL5">Water Pumps</span></label><br />
    <label><input type="checkbox" name="fltWtrh" id="fltWtrhID" value="1" autocomplete="off"
        @if (isset($fltWtrh) && intVal($fltWtrh) == 1) CHECKED @endif 
        > <span class="mL5">Mechanical Water Heating</span></label><br />
    <label><input type="checkbox" name="fltManu" id="fltManuID" value="1" autocomplete="off"
        @if (isset($fltManu) && intVal($fltManu) == 1) CHECKED @endif 
        > <span class="mL5">Manual Environmental Controls</span></label><br />
    <label><input type="checkbox" name="fltAuto" id="fltAutoID" value="1" autocomplete="off"
        @if (isset($fltAuto) && intVal($fltAuto) == 1) CHECKED @endif 
        > <span class="mL5">Automatic Environmental Controls</span></label><br />
    <label><input type="checkbox" name="fltVert" id="fltVertID" value="1" autocomplete="off"
        @if (isset($fltVert) && intVal($fltVert) == 1) CHECKED @endif 
        > <span class="mL5">Vertical Stacking</span></label><br />
@if ($nID != 490) </div><div class="col-2 pT10"> 
@else <br /><u class="slGrey fPerc133">Energy Sources</u><br /> @endif
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew1" value="149" autocomplete="off"
        @if (isset($fltRenew) && in_array(149, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Solar PV</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew2" value="159" autocomplete="off"
        @if (isset($fltRenew) && in_array(159, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Wind</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew3" value="151" autocomplete="off"
        @if (isset($fltRenew) && in_array(151, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Biomass</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew4" value="150" autocomplete="off"
        @if (isset($fltRenew) && in_array(150, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Geothermal</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew5" value="158" autocomplete="off"
        @if (isset($fltRenew) && in_array(158, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Pelton Wheel</span></label><br />
@if ($nID != 490) </div><div class="col-2 pT10"> @endif
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew6" value="153" autocomplete="off"
        @if (isset($fltRenew) && in_array(153, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Generator</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew7" value="154" autocomplete="off"
        @if (isset($fltRenew) && in_array(154, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Propane</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew8" value="155" autocomplete="off"
        @if (isset($fltRenew) && in_array(155, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Natural Gas</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew9" value="156" autocomplete="off"
        @if (isset($fltRenew) && in_array(156, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Woodstove</span></label><br />
    <label><input type="checkbox" name="fltRenew[]" id="fltRenew10" value="157" autocomplete="off"
        @if (isset($fltRenew) && in_array(157, $fltRenew)) CHECKED @endif 
        > <span class="mL5">Other Energy Source</span></label><br />