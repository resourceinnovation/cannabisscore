<!-- generated from resources/views/vendor/cannabisscore/nodes/490-future-yields.blade.php -->
<div class="row">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-4"></div>
            <div class="col-4">Light Types</div>
            <div class="col-4">HVAC Systems</div>
        </div>
        
        
        <div class="row">
            <div class="col-4"></div>
            <div class="col-4">Light Types</div>
            <div class="col-4">HVAC Systems</div>
        </div>
        
    </div>
    <div class="col-lg-4">
    
    </div>
</div>

<div class="p10"></div>

<style>
@media screen and (max-width: 1200px) {
}
@media screen and (max-width: 992px) {
}
@media screen and (max-width: 768px) {
}
@media screen and (max-width: 600px) {
}                                              
@media screen and (max-width: 480px) {
}
</style>
<script type="text/javascript"> $(document).ready(function() {
	
    {!! view('vendor.cannabisscore.inc-filter-powerscores-js', [ "psid" => $psid ])->render() !!}
	function reloadGuages() {
	    var spn = '<i class="fa-li fa fa-spinner fa-spin"></i>';
	    /*
	    if (document.getElementById('psScoreOverall')) document.getElementById('psScoreOverall').innerHTML = spn;
	    var baseUrl = "/ajax/powerscore-rank?ps={{ $psid }}{!! $hasRefresh !!}"+gatherFilts();
        setTimeout(function() { $("#psScoreOverall").load(   ""+baseUrl+"&eff=Overall"); },    2400);
        */
        return true;
    }
    setTimeout(function() { reloadGuages(); }, 300);
    
    $(document).on("click", ".updateScoreFilts", function() { reloadGuages(); });
    
});

</script>