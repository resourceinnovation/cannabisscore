/* generated from resources/views/vendor/cannabisscore/nodes/878-lighting-audit-js.blade.php */
function lightAuditError() {
    if (document.getElementById("pageBtns") && document.getElementById("formErrorMsg")) {
        document.getElementById("pageBtns").innerHTML={!! json_encode($auditTbl) !!}+document.getElementById("pageBtns").innerHTML;
@if (sizeof($auditAreas) > 0)
        document.getElementById("formErrorMsg").innerHTML={!! json_encode('<h4 class="red">Please enter complete lighting information for...</h4><ul><li class="red">' . implode('</li><li class="red">', $auditAreas) . '</li></ul><p>If you are not using artificial light in these rooms — or the square feet are wrong — please go back to correct this on the previous page.</p>') !!};
        document.getElementById("formErrorMsg").style.display="block";
        if (document.getElementById("nFormNextBtn")) {
            document.getElementById("nFormNextBtn").style.display="none";
        }
@endif
    }
    return true;
}
setTimeout("lightAuditError()", 10);