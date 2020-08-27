/* resources/views/vendor/cannabisscore/nodes/1560-facilities-edit-ajax.blade.php */

var slugSpinner = '<div><i class="fa-li fa fa-spinner fa-spin"></i></div>';

function slugFacName(i) {
    if (document.getElementById("facName"+i+"ID") && document.getElementById("facSlug"+i+"ID")) {
        var newVal = document.getElementById("facName"+i+"ID").value;
        document.getElementById("facSlug"+i+"ID").value=slugify(newVal);
        chkFacSlug(i);
    }
}
function chkFacSlug(i) {
    if (document.getElementById("facSlugSearch"+i+"")) {
        document.getElementById("facSlugSearch"+i+"").innerHTML=slugSpinner;
        var slug = document.getElementById("facSlug"+i+"ID").value;
        var facID = document.getElementById("facilityID"+i+"").value;
        var url = "/ajax/check-slug?slug="+slug+"&facID="+facID+"&div=facSlugSearch"+i+"";
console.log(url);
        $("#facSlugSearch"+i+"").load(url);
    }
}
@for ($i = 0; $i < $facLimit; $i++) 
    $(document).on("blur", "#facName{{ $i }}ID", function(e) { slugFacName({{ $i }}); });
    $(document).on("keyup", "#facSlug{{ $i }}ID", function(e) { chkFacSlug({{ $i }}); });
@endfor