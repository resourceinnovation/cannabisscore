<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
<div class="row">
@if (!isset($eff) || trim($eff) == '')
@elseif (trim($eff) == 'Overall')
    <div class="col-md-6 efficGuageWrapBig disNon" id="guageOverall"></div>
    <div class="col-md-6 taR disNon" id="guageOverallTxt"><div class="efficGuageTxtOver">
        <div class="m0 scoreBig fPerc133">
            Overall: 
            @if ($currGuage > 66) Leader @elseif ($currGuage > 33) Middle-of-the-Pack @else Upgrade Candidate @endif
        </div>
        <div class="slGrey">Your farm is performing overall<sup>*</sup> in the</div>
        <h2 class="m0 scoreBig">{{ $currGuage }}<sup>th</sup> percentile</h2>
        <div class="slGrey mB10">
            within the overall data set of {{ number_format($ranksCache->PsRnkTotCnt) }} past growing
            @if ($ranksCache->PsRnkTotCnt > 1) years @else year @endif
            @if ($filtFarm == 0) of <span class="wht">all farm types</span>,
            @else of {{ strtolower($GLOBALS["SL"]->getDefValue('PowerScore Farm Types', $filtFarm)) }} farms</span>,
            @endif
            @if ($filtClimate == 0) in the <span class="wht">U.S.</span>.
            @else in <span class="wht">ASHRAE Climate Zone {{ $powerscore->PsAshrae }}</span>.
            @endif
        </div>
    </div></div>
@else
    <div class="col-md-5 efficGuageWrap disNon" id="guage{{ $eff }}"></div>
    <div class="col-md-7 efficGuageWrapTxt disNon" id="guage{{ $eff }}Txt"><div class="efficGuageTxt">
        <h4 class="disIn">{{ $currGuage }}<sup>th</sup> percentile</h4>
        @if (isset($currGuageTot) && intVal($currGuageTot) > 0)
            <div class="slGrey">out of {{ $currGuageTot }}</div>
        @endif
    </div></div>
@endif
</div>

<script type="text/javascript"> $(document).ready(function() {
$("#guage{{ $eff }}").append('<img src="/cannabisscore/uploads/greenometer-anim-{{ $currGuage }}.gif?'+Math.random()+'" />');
setTimeout(function() { $("#guage{{ $eff }}").fadeIn(800); }, 1);
setTimeout(function() { $("#guage{{ $eff }}Txt").fadeIn(3000); }, 400);
}); </script>