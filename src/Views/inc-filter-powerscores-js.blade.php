/* generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-js.blade.php */

function gatherFilts() {
    var baseUrl = "";
    @if ($GLOBALS["SL"]->REQ->has('lighting')) baseUrl += "&lighting=1"; @endif
    if (document.getElementById("dataSetID")) {
        baseUrl += "&dataSet="+document.getElementById("dataSetID").value.trim();
    }
    if (document.getElementById("filtFarmID")) {
        baseUrl += "&fltFarm="+document.getElementById("filtFarmID").value.trim();
    }
    if (document.getElementById("fltStateClimID") && document.getElementById("fltStateClimID").value.trim() != '') {
        baseUrl += "&fltStateClim="+document.getElementById("fltStateClimID").value.trim();
    }
    if (document.getElementById("filtClimateID") && document.getElementById("filtClimateID").value.trim() != '') {
        baseUrl += "&fltClimate="+document.getElementById("filtClimateID").value.trim();
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
    if (document.getElementById("fltRenewID") && parseInt(document.getElementById("fltRenewID").value) > 0) {
        baseUrl += "&fltRenew="+parseInt(document.getElementById("fltRenewID").value);
    }
    if (document.getElementById("fltWaterSourceID") && parseInt(document.getElementById("fltWaterSourceID").value) > 0) {
        baseUrl += "&fltWaterSource="+parseInt(document.getElementById("fltWaterSourceID").value);
    }
    if (document.getElementById("fltWaterStoreID") && parseInt(document.getElementById("fltWaterStoreID").value) > 0) {
        baseUrl += "&fltWaterStore="+parseInt(document.getElementById("fltWaterStoreID").value);
    }
    if (document.getElementById("fltWaterStoreSysID") && parseInt(document.getElementById("fltWaterStoreSysID").value) > 0) {
        baseUrl += "&fltWaterStoreSys="+parseInt(document.getElementById("fltWaterStoreSysID").value);
    }
    if (document.getElementById("fltWaterStoreMethID") && parseInt(document.getElementById("fltWaterStoreMethID").value) > 0) {
        baseUrl += "&fltWaterStoreMeth="+parseInt(document.getElementById("fltWaterStoreMethID").value);
    }
    if (document.getElementById("fltGrowMediaID") && parseInt(document.getElementById("fltGrowMediaID").value) > 0) {
        baseUrl += "&fltGrowMedia="+parseInt(document.getElementById("fltGrowMediaID").value);
    }
    if (document.getElementById("fltTechniquesID") && parseInt(document.getElementById("fltTechniquesID").value) > 0) {
        baseUrl += "&fltTechniques="+parseInt(document.getElementById("fltTechniquesID").value);
    }
    if (document.getElementById("fltCmplID") && document.getElementById("fltCmplID").value >= 0) {
        baseUrl += "&fltCmpl="+document.getElementById("fltCmplID").value;
    }
    if (document.getElementById("fltManuLgtID") && document.getElementById("fltManuLgtID").value.trim() != "" && document.getElementById("fltManuLgtID").value.trim() != "0") {
        baseUrl += "&fltManuLgt="+document.getElementById("fltManuLgtID").value;
    }
    if (document.getElementById("fltCupID") && document.getElementById("fltCupID").value > 0) {
        baseUrl += "&fltCup="+document.getElementById("fltCupID").value;
    }
    if (document.getElementById("fltPartnerID") && document.getElementById("fltPartnerID").value > 0) {
        baseUrl += "&fltPartner="+document.getElementById("fltPartnerID").value;
    }
    if (document.getElementById("fltFacilityID") && document.getElementById("fltFacilityID").value > 0) {
        baseUrl += "&fltFacility="+document.getElementById("fltFacilityID").value;
    }
    return baseUrl;
}
