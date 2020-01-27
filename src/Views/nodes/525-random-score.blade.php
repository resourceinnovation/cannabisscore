<!-- generated from resources/views/vendor/cannabisscore/nodes/525-random-score.blade.php -->

<h1 class="slBlueDark">Random Completed PowerScore</h1>
<p class="slGrey">
This little tool selects a random PowerScore which have completed 
the survey, and have data for their total utility bill
(either entered or uploaded).
</p>

@if ($GLOBALS["SL"]->REQ->has('getRandom') 
    && intVal($GLOBALS["SL"]->REQ->get('getRandom')) == 2)
    <div id="randScore" class="w100"></div>
    <script type="text/javascript"> $(document).ready(function(){
        setTimeout(function() {
            $("#randScore").load("/calculated/u-{{ $randScore->ps_id }}?ajax=1");
        }, 10);
    }); </script>
@else
    <center><div class="p20 m20"></div>
    <a class="btn btn-lg btn-xl btn-primary" 
        href="?getRandom=2">Pick A Random Score</a>
    <div class="p20 m20"></div></center>
@endif
