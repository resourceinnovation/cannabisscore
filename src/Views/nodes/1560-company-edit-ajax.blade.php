/* resources/views/vendor/cannabisscore/nodes/1560-company-edit-ajax.blade.php */

var slugSpinner = '<div><i class="fa-li fa fa-spinner fa-spin"></i></div>';
function slugCompName() {
    var newVal = document.getElementById('partnerCompanyNameID').value;
    document.getElementById('partnerCompanySlugID').value=slugify(newVal);
    chkSlug();
}
$(document).on("blur", "#partnerCompanyNameID", function(e) { slugCompName(); });
setTimeout(function() { slugCompName(); }, 10);

function chkSlug() {
    if (document.getElementById("slugSearchResult")) {
        document.getElementById("slugSearchResult").innerHTML=slugSpinner;
        var slug = document.getElementById("partnerCompanySlugID").value;
        var comID = document.getElementById("companyID").value;
        var url = "/ajax/check-slug?slug="+slug+"&comID="+comID+"";
console.log(url);
        $("#slugSearchResult").load(url);
    }
}
$(document).on("keyup", "#partnerCompanySlugID", function(e) { chkSlug(); });
