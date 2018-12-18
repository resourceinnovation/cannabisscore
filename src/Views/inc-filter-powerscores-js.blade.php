<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-js.blade.php -->

function gatherFilts() {
    var baseUrl = @if ($GLOBALS["SL"]->REQ->has('lighting')) "&lighting=1"; @else ""; @endif
    if (document.getElementById("fltStateID") && document.getElementById("fltStateID").value.trim() != '') {
        baseUrl += "&fltState="+document.getElementById("fltStateID").value.trim();
    }
    if (document.getElementById("filtClimateID") && document.getElementById("filtClimateID").value.trim() != '') {
        baseUrl += "&fltClimate="+document.getElementById("filtClimateID").value.trim();
    }
    if (document.getElementById("filtFarmID") && parseInt(document.getElementById("filtFarmID").value) > 0) {
        baseUrl += "&fltFarm="+document.getElementById("filtFarmID").value.trim();
    }
    if (document.getElementById("fltFutID") && parseInt(document.getElementById("fltFutID").value) > 0) {
        baseUrl += "&fltFut="+document.getElementById("fltFutID").value.trim();
    }
    if (document.getElementById("fltLghtID") && document.getElementById("fltLghtID").value.trim() != '') {
        baseUrl += "&fltLght="+document.getElementById("fltLghtID").value.trim();
    }
    if (document.getElementById("fltHvacID") && document.getElementById("fltHvacID").value.trim() != '') {
        baseUrl += "&fltHvac="+document.getElementById("fltHvacID").value.trim();
    }
    if (document.getElementById("fltSizeID") && parseInt(document.getElementById("fltSizeID").value) > 0) {
        baseUrl += "&fltSize="+parseInt(document.getElementById("fltSizeID").value);
    }
    if (document.getElementById("fltPerpID") && document.getElementById("fltPerpID").checked) {
        baseUrl += "&fltPerp=1";
    }
    if (document.getElementById("fltPumpID") && document.getElementById("fltPumpID").checked) {
        baseUrl += "&fltPump=1";
    }
    if (document.getElementById("fltWtrhID") && document.getElementById("fltWtrhID").checked) {
        baseUrl += "&fltWtrh=1";
    }
    if (document.getElementById("fltManuID") && document.getElementById("fltManuID").checked) {
        baseUrl += "&fltManu=1";
    }
    if (document.getElementById("fltAutoID") && document.getElementById("fltAutoID").checked) {
        baseUrl += "&fltAuto=1";
    }
    if (document.getElementById("fltVertID") && document.getElementById("fltVertID").checked) {
        baseUrl += "&fltVert=1";
    }
    var fltRenew = "";
    for (var i=1; i <= 10; i++) {
        if (document.getElementById("fltRenew"+i+"") && document.getElementById("fltRenew"+i+"").checked) {
            fltRenew += ","+document.getElementById("fltRenew"+i+"").value;
        }
    }
    if (fltRenew.trim() != '') {
        baseUrl += "&fltRenew="+fltRenew.substring(1);
    }
    if (document.getElementById("fltCmplID") && document.getElementById("fltCmplID").value >= 0) {
        baseUrl += "&fltCmpl="+document.getElementById("fltCmplID").value;
    }
    if (document.getElementById("fltCupID") && document.getElementById("fltCupID").value > 0) {
        baseUrl += "&fltCup="+document.getElementById("fltCupID").value;
    }
    return baseUrl;
}
