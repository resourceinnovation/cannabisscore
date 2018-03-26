function chkArtifLight() {
    for (var i = 0; i < {{ sizeof($areas) }}; i++) {
        if (document.getElementById("n617tbl"+i+"fld0") && document.getElementById("n617tbl"+i+"fld0").checked) {
            if (document.getElementById("node589cyc"+i+"")) $("#node589cyc"+i+"").slideDown("fast");
        } else {
            if (document.getElementById("node589cyc"+i+"")) $("#node589cyc"+i+"").slideUp("fast");
        }
    }
    setTimeout(function() { chkArtifLight(); }, 500);
}
setTimeout(function() { chkArtifLight(); }, 10);