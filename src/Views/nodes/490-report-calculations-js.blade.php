/* generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-js.blade.php */

var spn = '<i class="fa-li fa fa-spinner fa-spin mL30 mT20"></i>';
var guageList = new Array();
guageList[guageList.length] = new Array('Overall',    3200, 0, '', '', '');
guageList[guageList.length] = new Array('CatEnr',     3000, 0, '', '', '');
guageList[guageList.length] = new Array('FacAll',     2800, 0, '', '', '');
guageList[guageList.length] = new Array('Facility',   2600, 0, '', '', '');
guageList[guageList.length] = new Array('FacNon',     2400, 0, '', '', '');
guageList[guageList.length] = new Array('ProdAll',    2200, 0, '', '', '');
guageList[guageList.length] = new Array('Production', 2000, 0, '', '', '');
guageList[guageList.length] = new Array('ProdNon',    1800, 0, '', '', '');
guageList[guageList.length] = new Array('Emis',       1600, 0, '', '', '');
guageList[guageList.length] = new Array('EmisProd',   1400, 0, '', '', '');
guageList[guageList.length] = new Array('Lighting',   1200, 0, '', '', '');
guageList[guageList.length] = new Array('Hvac',       1000, 0, '', '', '');
guageList[guageList.length] = new Array('CatWtr',      800, 0, '', '', '');
guageList[guageList.length] = new Array('Water',       600, 0, '', '', '');
guageList[guageList.length] = new Array('WaterProd',   400, 0, '', '', '');
guageList[guageList.length] = new Array('CatWst',      200, 0, '', '', '');
guageList[guageList.length] = new Array('Waste',       100, 0, '', '', '');
guageList[guageList.length] = new Array('WasteProd',    10, 0, '', '', '');
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
        if (document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").style.display='none';
            document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML=guageList[g][3];
            if (document.getElementById("guageFrame"+guageList[g][0]+"")) {
                document.getElementById("guageFrame"+guageList[g][0]+"").style.display='none';
                document.getElementById("guageFrame"+guageList[g][0]+"").src=guageUrl;
                $("#guageFrame"+guageList[g][0]+"").fadeIn(3000);
            }
            $("#efficGuageTxt"+guageList[g][0]+"").fadeIn(3000);
        }
        return true;
    }
    
    function chkGuageReload(baseUrl) {
        if (reloadComplete) {
            for (g = 0; g < guageList.length; g++) {
                if (document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
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
        for (g = 0; g < guageList.length; g++) {
            if (document.getElementById("efficGuageTxt"+guageList[g][0]+"")) {
                document.getElementById("efficGuageTxt"+guageList[g][0]+"").innerHTML = spn;
            }
        }
        var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}"+gatherFilts();
        var fullUrl = ""+baseUrl+"&eff=Overall&loadAll=1";
console.log("reloadGuages() "+fullUrl);
        $("#guageReloader").load(fullUrl);
        setTimeout(function() { chkGuageReload(baseUrl); }, 400);
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
@if (!$isPast) 
    setTimeout(function() {
        $("#futureForm").load("/ajax/future-look?ps={{ $psid }}");
    }, 3000);
@endif
@if ($GLOBALS["SL"]->REQ->has('print'))
    setTimeout("window.print()", 3000);
@endif
    
});

@endif