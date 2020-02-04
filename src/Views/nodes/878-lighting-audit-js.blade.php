/* generated from resources/views/vendor/cannabisscore/nodes/878-lighting-audit-js.blade.php */
function lightAuditError() {
    if (document.getElementById("pageBtns") && document.getElementById("formErrorMsg")) {
        document.getElementById("pageBtns").innerHTML={!! json_encode($auditTbl) !!}+document.getElementById("pageBtns").innerHTML;
@if (sizeof($auditAreas) > 0)
        document.getElementById("formErrorMsg").innerHTML={!! 
            json_encode(
                '<h4 class="red">Please complete lighting information...'
                    . '</h4><ul><li class="red">'
                    . implode('</li><li class="red">', $auditAreas) 
                    . '</li></ul><p>'
                    . 'If you are not using artificial light in these rooms, '
                    . 'please go back to correct this on the previous page.</p>'
            )
        !!};
        document.getElementById("formErrorMsg").style.display="block";
@endif
    }
    return true;
}
setTimeout("lightAuditError()", 10);