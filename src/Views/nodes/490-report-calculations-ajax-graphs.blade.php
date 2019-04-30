<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
<div class="row" id="guageRow{{ $eff }}">
@if (!isset($eff) || trim($eff) == '')
@elseif (trim($eff) == 'Overall')
    @if (!$GLOBALS["SL"]->REQ->has('print'))
        @if ($hasOverall)
            <div class="col-md-6 efficGuageWrapBig disNon" id="guageOverall">
                <div class="relDiv guageWrapOver">
                    <img src="/cannabisscore/uploads/greenometer2-foreground-dial.png" id="dial{{ $eff }}ID" border=0 class="guageImgDial">
                    <img src="/cannabisscore/uploads/greenometer2-background-meter.png" border=0 class="guageImgMeter">
                </div>
            </div>
        @else
            <div class="col-md-6"><div class="disNon w100"><div class="disNon w100" id="guageOverall">
            </div></div></div>
        @endif
        <div class="col-md-6 taR disNon" id="guageOverallTxt">
    @else
        <div class="col-12 taR" id="guageOverallTxt">
    @endif
    <div class="efficGuageTxtOver">
        @if ($hasOverall)
            <div id="efficBlockOverGuageTitle" class="scoreBig slBlueDark"> Overall: 
                @if ($currGuage > 66) Leader @elseif ($currGuage > 33) Middle-of-the-Pack @else Upgrade Candidate @endif
            </div>
            <div class="slGrey">
                Your farm @if ($isPast) is performing @else would perform @endif overall in the
            </div>
            <h2 class="m0 scoreBig">{!! $currGuage . $GLOBALS["SL"]->numSupscript($currGuage) !!} percentile</h2>
            <div class="slGrey mB10">
        @else
            <div class="slGrey mT20 pR15">
            We did not have enough information to calculate this farm's Overall PowerScore
        @endif
            within the overall data set of <?php /* {{ number_format($ranksCache->PsRnkTotCnt) }} past growing
            @if ($ranksCache->PsRnkTotCnt > 1) years @else year @endif of */ ?>
            <span class="slBlueDark">
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
                @else in <span class="slBlueDark">{{ $GLOBALS["SL"]->getState($fltState) }}</span>.
                @endif
            @else
                @if ($fltClimate == 'US') in the United States.
                @elseif ($fltClimate == 'Canada') in Canada.
                @else in <span class="slBlueDark">ASHRAE Climate Zone {{ $fltClimate }}</span>.
                @endif
            @endif
        </div>
    </div></div>
@else
    @if (!$GLOBALS["SL"]->REQ->has('print'))
        <div class="col-md-5 efficGuageWrap disNon" id="guage{{ $eff }}">
            <div class="relDiv guageWrap">
                <img src="/cannabisscore/uploads/greenometer2-foreground-dial.png" id="dial{{ $eff }}ID" border=0 class="guageImgDial">
                <img src="/cannabisscore/uploads/greenometer2-background-meter.png" border=0 class="guageImgMeter">
            </div>
        </div>
        <div class="col-md-7 efficGuageWrapTxt disNon" id="guage{{ $eff }}Txt">
    @else
        <div class="col-12 taR efficGuageWrapTxt" id="guage{{ $eff }}Txt">
    @endif
    <div class="efficGuageTxt">
        <h5 class="slBlueDark">{!! $currGuage . $GLOBALS["SL"]->numSupscript($currGuage) !!} percentile</h5>
    </div></div>
@endif
</div>

<script type="text/javascript"> $(document).ready(function() {
    var dial{{ $eff }} = $('#dial{{ $eff }}ID');
    function rotateDial{{ $eff }}() {
        var degrees = Math.round( ({{ $currGuage }}/100) * 180);
        dial{{ $eff }}.css('-moz-transform', 'rotate(' + degrees + 'deg)')
            .css('-webkit-transform', 'rotate(' + degrees + 'deg)')
            .css('-o-transform', 'rotate(' + degrees + 'deg)')
            .css('-ms-transform', 'rotate(' + degrees + 'deg)');
    }
    setTimeout(function() { rotateDial{{ $eff }}() }, 500);
    setTimeout(function() { $("#guage{{ $eff }}").fadeIn(800); }, 1);
    setTimeout(function() { $("#guage{{ $eff }}Txt").fadeIn(3000); }, 400);
}); </script>