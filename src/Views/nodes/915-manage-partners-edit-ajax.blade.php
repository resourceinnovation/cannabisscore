/* resources/views/vendor/cannabisscore/nodes/915-manage-partners-edit-ajax.blade.php */

var partners = new Array();
@foreach ($GLOBALS["SL"]->x["partners"] as $p => $partner)
    partners[{{ $partner->id }}] = "{{ addslashes($partner->company) }}";
@endforeach

function getPrtnCompany(partnerID) {
    var company = "";
    if (partners[partnerID]) company = partners[partnerID];
    if (document.getElementById("partnerCompanyNameID") && document.getElementById("partnerCompanyNameID").value.trim() == "") {
        document.getElementById("partnerCompanyNameID").value=company;
    }
    return true;
}

function changePartnerUser() {
    var userID = 0;
    if (document.getElementById("partnerUserID")) {
        userID = parseInt(document.getElementById("partnerUserID").value);
    }
    if (userID > 0) {
        $("#inviteEmailAddy").slideUp(50);
        getPrtnCompany(userID);
    } else {
        $("#inviteEmailAddy").slideDown(50);
    }
    return true;
}
$(document).on("change", "#partnerUserID", function() { return changePartnerUser(); });
setTimeout(function() { changePartnerUser(); }, 1);



function changePartnerCompany() {
    var comID = 0;
    if (document.getElementById("partnerCompanyID")) {
        comID = parseInt(document.getElementById("partnerCompanyID").value);
        /*
        if (document.getElementById("partnerCompanyID").value.trim() != "") {
            $("#partnerManuWrap").slideUp(50);
        } else {
            $("#partnerManuWrap").slideDown(50);
        }
        */
    }
    if (comID == 0) {
        $("#partnerCompanyWrap").slideDown(50);
    } else {
        $("#partnerCompanyWrap").slideUp(50);
    }
    return true;
}
$(document).on("change", "#partnerCompanyID", function() { return changePartnerCompany(); });
setTimeout(function() { changePartnerCompany(); }, 1);


function slugCompName() {
    var newVal = document.getElementById('partnerCompanyNameID').value;
    document.getElementById('partnerCompanySlugID').value=slugify(newVal);
    chkSlug();
}
$(document).on("blur", "#partnerCompanyNameID", function(e) { slugCompName(); });
setTimeout(function() { slugCompName(); }, 10);

function chkSlug() {
    if (document.getElementById('slugSearchResult')) {
        var url = "/ajax/check-slug?slug="+document.getElementById('partnerCompanySlugID').value;
console.log(url);
        $("#slugSearchResult").load(url);
    }
}
$(document).on("keyup", "#partnerCompanySlugID", function(e) { chkSlug(); });



