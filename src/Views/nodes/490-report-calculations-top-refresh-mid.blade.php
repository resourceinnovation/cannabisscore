<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations-top-refresh-mid.blade.php -->
<center><div class="spnWrp p20">{!! $GLOBALS["SL"]->sysOpts["spinner-code"] !!}</div>
@if (isset($msg)) {!! $msg !!} @endif
</center>
<script type="text/javascript"> $(document).ready(function() { 
    function nextRecalc() {
        if (document.getElementById("ajax490refresh")) {
            $("#ajax490refresh").load("/ajax/report-ajax/?psid={{ $psid }}&refresh={!! 
                ((isset($nextFlt)) ? (($nextFlt == 'done') ? '-1' : '2&currFlt=' . $nextFlt) : '2')
                !!}");
        }
        return true;
    }
    setTimeout(function() { nextRecalc(); }, 200);
}); </script>
