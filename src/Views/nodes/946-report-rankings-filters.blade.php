<!-- generated from resources/views/vendor/cannabisscore/nodes/946-report-rankings-filters.blade.php -->
@if (!$GLOBALS["SL"]->REQ->has('isPreview'))

<div id="scoreRankFiltWrap">{!! $psFilters !!}</div>

<style>
#scoreRankFiltWrap { background: #8dc63f; }
#scoreBottomBuffer { padding-top: 100px; }
#blockWrap151 { margin-top: 40px; }
#reportTitleWrap { margin: 20px 0px 15px 0px; }
.efficGuageWrap img, .efficGuageWrap .mpt { height: 45px; margin: -10px 0px -1px 32px; opacity:0.90; filter:alpha(opacity=90); }
.efficGuageWrapBig img { height: 100px; margin: 2px 0px -13px 80px; }
.efficGuageTxt, .efficGuageTxtOver { text-align: left; width: 250px; }
.efficGuageTxt { margin: -5px 0px 0px -9px; }
.efficGuageTxt .slGrey, .efficGuageTxtOver .slGrey { font-size: 12px; line-height: 16px; }
#efficBlockOver { background: #8dc63f; color: #FFF; min-height: 100px; }
#efficBlockOverTitle { margin-top: 20px; }
#efficBlockOverGuageTitle { margin-top: 20px; }
.efficBlock { width: 100%; min-height: 54px; padding: 15px 0px 5px 0px; border-top: 1px #DDD solid; }
.efficHeads { padding: 15px; }

@media screen and (max-width: 1200px) {
    .efficBlock { min-height: 55px; }
    #efficBlockOver { min-height: 100px; }
    .efficGuageWrapBig img { margin: -8px 0px -13px 22px; }
    .efficGuageTxtOver { margin: -8px 0px 0px -30px; }
}
@media screen and (max-width: 992px) {
    .efficBlock { min-height: 96px; }
    #efficBlockOver { min-height: 170px; }
    .efficHeads { padding-left: 20px; }
    .efficHeadScore { padding-left: 30px; }
    .efficGuageTxtOver { width: 365px; margin: 24px 0px 35px -347px; }
    .efficGuageWrapBig { padding-top: 20px; }
    .efficGuageWrapBig img { height: 120px; margin: -14px 0px 10px 472px; padding-top: 20px; }
    .efficGuageWrap img { height: 70px; margin: -54px 0px 26px 496px; }
    .efficGuageTxt { margin: -58px 0px 21px 36px; }
    #farmFilts { width: 90%; margin: -10px 15px 20px 15px; }
    #cmtLnkSpacer { margin-top: -10px; }
}
@media screen and (max-width: 768px) {
    .efficBlock { min-height: 84px; }
    #efficBlockOver { min-height: 195px; }
    .efficGuageTxtOver { width: 265px; margin: -115px 0px 35px 15px; }
    .efficGuageWrapBig img { margin: -5px 0px 10px 325px; }
    .efficGuageWrap img { margin: -60px 0px 19px 377px; }
    .efficGuageTxt { margin: -84px 0px 21px 265px; }
    .efficGuageTxt h4, #blockWrap492 .efficGuageTxt h4 { font-size: 1.2rem; color: #B5CE96; }
}
@media screen and (max-width: 600px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: -165px 0px 0px 15px; }
    .efficGuageWrapBig img { height: 130px; margin: -15px 0px 50px 305px; }
    .efficGuageWrap img { height: 85px; margin: -44px 0px 0px 281px; }
    .efficGuageTxt { margin: -35px 0px 15px 15px; }
}                                              
@media screen and (max-width: 480px) {
    .efficBlock { min-height: 148px; }
    #efficBlockOver { min-height: 215px; }
    .efficGuageTxtOver { width: 255px; margin: 0px 0px 0px 15px; }
    .efficGuageWrapBig { text-align: center; }
    .efficGuageWrapBig img { height: 160px; margin: -30px 0px 0px 0px; }
    .efficGuageWrap img { height: 70px; margin: -50px 0px 0px 272px; }
    .efficGuageTxt { margin: -15px 0px 40px 15px; }
    #efficBlockOver .efficHeads h2 { font-size: 28px; }
}
</style>
<script type="text/javascript"> $(document).ready(function() {
    
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
    var spn = '<i class="fa-li fa fa-spinner fa-spin mL20 mT10"></i>';
    function reloadGuages() {
        if (document.getElementById('psScoreOverall')) document.getElementById('psScoreOverall').innerHTML = spn;
        if (document.getElementById('psScoreFacility')) document.getElementById('psScoreFacility').innerHTML = spn;
        if (document.getElementById('psScoreProduction')) document.getElementById('psScoreProduction').innerHTML = spn;
        if (document.getElementById('psScoreHvac')) document.getElementById('psScoreHvac').innerHTML = spn;
        if (document.getElementById('psScoreLighting')) document.getElementById('psScoreLighting').innerHTML = spn;
        var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}"+gatherFilts();
        setTimeout(function() { $("#psScoreOverall").load(   ""+baseUrl+"&eff=Overall"); },    2400);
        setTimeout(function() { $("#psScoreFacility").load(  ""+baseUrl+"&eff=Facility"); },   2000);
        setTimeout(function() { $("#psScoreProduction").load(""+baseUrl+"&eff=Production"); }, 1600);
        setTimeout(function() { $("#psScoreHvac").load(      ""+baseUrl+"&eff=HVAC"); },       1200);
        setTimeout(function() { $("#psScoreLighting").load(  ""+baseUrl+"&eff=Lighting"); },      1);
        console.log(baseUrl+"&eff=Overall");
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
@if (!$isPast)
    setTimeout(function() { $("#futureForm").load("/ajax/future-look?ps={{ $psid }}"); }, 3000);
@endif
    
});

@if ($GLOBALS["SL"]->REQ->has('print')) setTimeout("window.print()", 3000); @endif

</script>

@endif