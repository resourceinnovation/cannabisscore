<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-top-refresh.blade.php -->
<div id="ajax490refresh"></div> <!-- end ajax490refresh -->
<style>
    #ajax490refresh { height: 50%; min-height: 400px; width: 100%; }
</style>
<script type="text/javascript"> $(document).ready(function() { 
    function nextRecalc() {
        if (document.getElementById("ajax490refresh")) {
            $("#ajax490refresh").load("/ajax/report-ajax/?psid={{ $psid }}&recalc=1");
        }
        return true;
    }
    setTimeout(function() { nextRecalc(); }, 1000);
}); </script>