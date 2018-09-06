/* generated from resources/views/vendor/cannabisscore/nodes/637-email-list-ajax.blade.php */
function updateEmaDropUrl() {
    if (document.getElementById("listID") && document.getElementById("emaID")) {
        return "?wchLst="+document.getElementById("listID").value+"&wchEma="+document.getElementById("emaID").value;
    }
    return "";
}
$(document).on("change", "#listID", function() { window.location=updateEmaDropUrl(); });
$(document).on("change", "#emaID",  function() { window.location=updateEmaDropUrl(); });
$(document).on("click", "#sendCnfmBtn", function() {
    document.getElementById("yesSendID").value=1;
    document.emailBlast.action+=updateEmaDropUrl();
    document.emailBlast.submit();
});

$(document).on("click", "#sendTestBtn", function() {
    if (document.getElementById("testEmailID") && document.getElementById("emaID") && document.getElementById("replyToID")) {
        var url = "/ajadm/send-email?e="+document.getElementById("emaID").value+"&t=1&c={{ $scoreLists[$wchLst][0]["id"] 
            }}&o="+document.getElementById("testEmailID").value+"&r="+document.getElementById("replyToID").value+"&rn="+encodeURIComponent(document.getElementById("replyNameID").value)+"&l=testSendLoading";
        $("#testSendLoading").append('<a href="'+url+'" target="_blank" class="disBlo fPerc66">'+url+'</a>');
        $("#testSendLoading").fadeIn(300);
        $("#testSendResults").load(url);
    }
});
