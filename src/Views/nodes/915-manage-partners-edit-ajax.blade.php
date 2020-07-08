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
