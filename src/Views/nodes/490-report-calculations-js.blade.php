/* generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-js.blade.php */

var spn = '<i class="fa-li fa fa-spinner fa-spin mL20 mT15"></i>';
var guageList = new Array();
guageList[guageList.length] = new Array('Overall',    2800, 0, '', '', '');
guageList[guageList.length] = new Array('Facility',   2400, 0, '', '', '');
guageList[guageList.length] = new Array('Production', 2000, 0, '', '', '');
guageList[guageList.length] = new Array('Hvac',       1600, 0, '', '', '');
guageList[guageList.length] = new Array('Lighting',   1200, 0, '', '', '');
guageList[guageList.length] = new Array('Water',      800,  0, '', '', '');
guageList[guageList.length] = new Array('Waste',      400,  0, '', '', '');
var reloadComplete = false;
var g = 0;

@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

$(document).ready(function() {
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    
    function guageLoad(g) {
        var guageUrl = "/frame/animate/meter/"+guageList[g][2]+"/"+g+"?bg="+guageList[g][5]+"";
        if (guageList[g][0] == 'Overall') {
            guageUrl = "/frame/animate/guage/"+guageList[g][2]+"?size=180";
            if (document.getElementById("efficGuageTxt"+guageList[g][0]+"2")) {
                document.getElementById("efficGuageTxt"+guageList[g][0]+"2").innerHTML=guageList[g][4];
            }
        }
        if (document.getElementById("guageFrame"+guageList[g][0]+"") && document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
            document.getElementById("guageFrame"+guageList[g][0]+"").style.display='none';
            document.getElementById("guageFrame"+guageList[g][0]+"").src=guageUrl;
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").style.display='none';
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML=guageList[g][3];
            $("#guageFrame"+guageList[g][0]+"").fadeIn(3000);
            $("#efficGuageTxt"+guageList[g][0]+"").fadeIn(3000);
        }
        return true;
    }
    
    function chkGuageReload(baseUrl) {
        if (reloadComplete) {
            for (g = 0; g < guageList.length; g++) {
                if (document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
                    document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML = spn;
                    setTimeout(guageLoad, guageList[g][1], g);
                }
            }
            return true;
        }
        setTimeout(function() { chkGuageReload(baseUrl); }, 400);
        return false;
    }
    
    function reloadGuages() {
        reloadComplete = false;
        var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}"+gatherFilts();
        $("#guageReloader").load(""+baseUrl+"&eff=Overall&loadAll=1");
        setTimeout(function() { chkGuageReload(baseUrl); }, 400);
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
@if (!$isPast) setTimeout(function() { $("#futureForm").load("/ajax/future-look?ps={{ $psid }}"); }, 3000); @endif
@if ($GLOBALS["SL"]->REQ->has('print'))
    setTimeout("window.print()", 3000);
@endif
    
});

@endif