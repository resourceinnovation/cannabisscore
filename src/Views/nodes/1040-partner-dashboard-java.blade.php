/* resources/views/vendor/cannabisscore/nodes/1040-partner-dashboard-java.blade.php */

function clearCopies() {
    if (document.getElementById("prtnerRefURLalert")) {
        document.getElementById("prtnerRefURLalert").innerHTML = "";
    }
@if (sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
    for (var i = 0; i < {{ sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) }}; i++) {
        if (document.getElementById("facility"+i+"URLalert")) {
            document.getElementById("facility"+i+"URLalert").innerHTML = "";
        }
    }
@endif
}
function copyRefLink() {
    clearCopies();
    copyClip('prtnerRefURL'); 
    document.getElementById("prtnerRefURLalert").innerHTML="Copied. ";
}
function copyFacLink(i) {
    clearCopies();
    copyClip("facility"+i+"URL"); 
    if (document.getElementById("facility"+i+"URLalert")) {
        document.getElementById("facility"+i+"URLalert").innerHTML="Copied. ";
    }
}
