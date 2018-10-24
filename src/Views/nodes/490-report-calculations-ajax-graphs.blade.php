<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
<div class="row">
@if (!isset($eff) || trim($eff) == '')
@elseif (trim($eff) == 'Overall')
    @if (!$GLOBALS["SL"]->REQ->has('print'))
        @if ($hasOverall)
            <div class="col-6 efficGuageWrapBig disNon" id="guageOverall"></div>
        @else
            <div class="col-6"><div class="disNon"><div class="disNon" id="guageOverall"></div></div></div>
        @endif
        <div class="col-6 taR disNon" id="guageOverallTxt">
    @else
        <div class="col-12 taR" id="guageOverallTxt">
    @endif
    <div class="efficGuageTxtOver">
        @if ($hasOverall)
            <div class="m0 scoreBig fPerc133">
                Overall: 
                @if ($currGuage > 66) Leader @elseif ($currGuage > 33) Middle-of-the-Pack @else Upgrade Candidate @endif
            </div>
            <div class="slGrey">Your farm is performing overall<sup>*</sup> in the</div>
            <h2 class="m0 scoreBig">{!! $currGuage . $GLOBALS["SL"]->numSupscript($currGuage) !!} percentile</h2>
            <div class="slGrey mB10">
        @else
            <div class="slGrey mB10">
            We did not have enough information to calculate this farm's Overall PowerScore<sup>*</sup>
        @endif
            within the overall data set of <?php /* {{ number_format($ranksCache->PsRnkTotCnt) }} past growing
            @if ($ranksCache->PsRnkTotCnt > 1) years @else year @endif of */ ?>
            <span class="wht">
            @if ($fltFarm == 0) all farm types
            @else {{ str_replace('/', '/ ', strtolower($GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $fltFarm)))
                }} farms
            @endif
            </span>
            {!! $xtraFltsDesc !!}
            @if ($fltState == '' && $fltClimate == '') in the U.S. and Canada.
            @elseif ($fltState != '')
                @if ($fltState == 'US') in the United States.
                @elseif ($fltState == 'Canada') in Canada.
                @else in <span class="wht">{{ $GLOBALS["SL"]->getState($fltState) }}</span>.
                @endif
            @else
                @if ($fltClimate == 'US') in the United States.
                @elseif ($fltClimate == 'Canada') in Canada.
                @else in <span class="wht">ASHRAE Climate Zone {{ $fltClimate }}</span>.
                @endif
            @endif
        </div>
    </div></div>
@else
    @if (!$GLOBALS["SL"]->REQ->has('print'))
        <div class="col-5 efficGuageWrap disNon" id="guage{{ $eff }}"></div>
        <div class="col-7 efficGuageWrapTxt disNon" id="guage{{ $eff }}Txt">
    @else
        <div class="col-12 taR efficGuageWrapTxt" id="guage{{ $eff }}Txt">
    @endif
    <div class="efficGuageTxt">
        <h4 class="disIn">{!! $currGuage . $GLOBALS["SL"]->numSupscript($currGuage) !!} percentile</h4>
    </div></div>
@endif
</div>

<script type="text/javascript"> $(document).ready(function() {
$("#guage{{ $eff }}").append('<img src="/cannabisscore/uploads/greenometer-anim-{{ $currGuage 
    }}.gif?'+Math.random()+'" />');
setTimeout(function() { $("#guage{{ $eff }}").fadeIn(800); }, 1);
setTimeout(function() { $("#guage{{ $eff }}Txt").fadeIn(3000); }, 400);
}); </script>