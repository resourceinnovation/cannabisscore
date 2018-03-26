<!-- generated from resources/views/vendor/cannabisscore/nodes/490-report-calculations.blade.php -->
<div class="p10 m5"></div>
<h1>PowerScore Report #{{ $sessData["PowerScore"][0]->getKey() }}</h1>
<div class="row mT20 mB10">
    <div class="col-md-4"><h2 class="m0 scoreBig">Facility Efficiency:</h2></div>
    <div class="col-md-4"><h2 class="m0 scoreBig">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility)) 
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficFacility, 3) }}
        @else 0 @endif &nbsp;&nbsp;kWh / Square foot
    </h2></div>
    <div class="col-md-4">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility))
            <div class="row">
                <div class="col-md-4">
                    <img class="mTn10" border=0 style="height: 55px;"
                        @if (($efficPercs["Facility"]["ash"][0]+$efficPercs["Facility"]["ash"][1]) > 5)
                            src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Facility"]["ash"][3]/20)) }}.gif"
                        @elseif (($efficPercs["Facility"]["all"][0]+$efficPercs["Facility"]["all"][1]) > 5)
                            src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Facility"]["all"][3]/20)) }}.gif"
                        @endif >
<?php /*
                    <div id="meter1Base" class="meterAnim mTn10" style="height: 55px;"
                        data-meterid="1" data-speed="12"
                        @if (($efficPercs["Facility"]["ash"][0]+$efficPercs["Facility"]["ash"][1]) > 5)
                            data-destination="{{ $efficPercs["Facility"]["ash"][3] }}"
                        @elseif (($efficPercs["Facility"]["all"][0]+$efficPercs["Facility"]["all"][1]) > 5)
                            data-destination="{{ $efficPercs["Facility"]["all"][3] }}"
                        @endif >
                        <div id="meter1Needle" class="meterNeedle">
                            <img src="/cannabisscore/uploads/greenometer-only_needle-shade.png" border=0 >
                        </div>
                        <img id="meter1Img" src="/cannabisscore/uploads/greenometer-only_meter-shade.png" border=0 height=100% >
                    </div>
                    <script type="text/javascript">
                        setTimeout('document.getElementById("meter1Base").click()', 50);
                    </script>
*/ ?>
                </div>
                <div class="col-md-8 pT10">
                    @if (($efficPercs["Facility"]["ash"][0]+$efficPercs["Facility"]["ash"][1]) > 5)
                        Better than {!! $efficPercs["Facility"]["ash"][3] !!}% 
                        in climate zone {{ $sessData["PowerScore"][0]->PsAshrae }}
                    @elseif (($efficPercs["Facility"]["all"][0]+$efficPercs["Facility"]["all"][1]) > 5)
                        Better than {!! $efficPercs["Facility"]["all"][3] !!}% in the U.S.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<hr>
<div class="row mT20 mB10">
    <div class="col-md-4"><h2 class="m0 scoreBig">Production Efficiency:</h2></div>
    <div class="col-md-4"><h2 class="m0 scoreBig">
        @if (isset($sessData["PowerScore"][0]->PsEfficProduction)) 
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficProduction, 3) }}
        @else 0 @endif &nbsp;&nbsp;Grams / kWh
    </h2></div>
    <div class="col-md-4">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility))
            <div class="row">
                <div class="col-md-4">
                    <img class="mTn10" border=0 style="height: 55px;"
                    @if (($efficPercs["Production"]["ash"][0]+$efficPercs["Production"]["ash"][1]) > 5)
                        src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Production"]["ash"][3]/20)) }}.gif"
                    @elseif (($efficPercs["Production"]["all"][0]+$efficPercs["Production"]["all"][1]) > 5)
                        src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Production"]["all"][3]/20)) }}.gif"
                    @endif >
<?php /*
                    <div id="meter2Base" class="meterAnim mTn10" style="height: 55px;"
                        data-meterid="2" data-speed="12"
                        @if (($efficPercs["Production"]["ash"][0]+$efficPercs["Production"]["ash"][1]) > 5)
                            data-destination="{{ $efficPercs["Production"]["ash"][3] }}"
                        @elseif (($efficPercs["Production"]["all"][0]+$efficPercs["Production"]["all"][1]) > 5)
                            data-destination="{{ $efficPercs["Production"]["all"][3] }}"
                        @endif >
                        <div id="meter2Needle" class="meterNeedle">
                            <img src="/cannabisscore/uploads/greenometer-only_needle-shade.png" border=0 >
                        </div>
                        <img id="meter2Img" src="/cannabisscore/uploads/greenometer-only_meter-shade.png" border=0 height=100% >
                    </div>
                    <script type="text/javascript">
                        setTimeout('document.getElementById("meter2Base").click()', 50);
                    </script>
*/ ?>
                </div>
                <div class="col-md-8 pT10">
                    @if (($efficPercs["Production"]["ash"][0]+$efficPercs["Production"]["ash"][1]) > 5)
                        Better than {!! $efficPercs["Production"]["ash"][3] !!}% 
                        in climate zone {{ $sessData["PowerScore"][0]->PsAshrae }}
                    @elseif (($efficPercs["Production"]["all"][0]+$efficPercs["Production"]["all"][1]) > 5)
                        Better than {!! $efficPercs["Production"]["all"][3] !!}% in the U.S.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<hr>
@if (!isset($sessData["PowerScore"][0]->PsEfficLighting) || $sessData["PowerScore"][0]->PsEfficLighting == 0)
    <div id="hidivBtnWrp" class="disBlo mT20 mB10">
        <a href="javascript:;" id="hidivBtnLgtInfo" class="hidivBtn slGrey">
        <i>Not enough data provided for breakdown of lighting efficiency.</i></a>
    </div>
    <div id="hidivLgtInfo" class="disNon">
@endif
<div class="row mT20 mB10">
    <div class="col-md-4"><h2 class="m0 scoreBig">Lighting Efficiency:</h2></div>
    <div class="col-md-4"><h2 class="m0 scoreBig">
        @if (isset($sessData["PowerScore"][0]->PsEfficLighting))
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLighting, 3) }} 
        @else 0 @endif &nbsp;&nbsp;kWh / Square foot
    </h2></div>
    <div class="col-md-4">
        @if (isset($sessData["PowerScore"][0]->PsEfficFacility))
            <div class="row">
                <div class="col-md-4">
                    <img class="mTn10" border=0 style="height: 55px;"
                        @if (($efficPercs["Lighting"]["ash"][0]+$efficPercs["Lighting"]["ash"][1]) > 5)
                            src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Lighting"]["ash"][3]/20)) }}.gif"
                        @elseif (($efficPercs["Lighting"]["all"][0]+$efficPercs["Lighting"]["all"][1]) > 5)
                            src="/cannabisscore/uploads/greenometer-anim-{{ (5*ceil($efficPercs["Lighting"]["all"][3]/20)) }}.gif"
                        @endif >
<?php /*
                    <div id="meter3Base" class="meterAnim mTn10" style="height: 55px;"
                        data-meterid="3" data-speed="12"
                        @if (($efficPercs["Lighting"]["ash"][0]+$efficPercs["Lighting"]["ash"][1]) > 5)
                            data-destination="{{ $efficPercs["Lighting"]["ash"][3] }}"
                        @elseif (($efficPercs["Lighting"]["all"][0]+$efficPercs["Lighting"]["all"][1]) > 5)
                            data-destination="{{ $efficPercs["Lighting"]["all"][3] }}"
                        @endif >
                        <div id="meter3Needle" class="meterNeedle">
                            <img src="/cannabisscore/uploads/greenometer-only_needle-shade.png" border=0 >
                        </div>
                        <img id="meter3Img" src="/cannabisscore/uploads/greenometer-only_meter-shade.png" border=0 height=100% >
                    </div>
                    <script type="text/javascript">
                        setTimeout('document.getElementById("meter3Base").click()', 50);
                    </script>
*/ ?>
                </div>
                <div class="col-md-8 pT10">
                    @if (($efficPercs["Lighting"]["ash"][0]+$efficPercs["Lighting"]["ash"][1]) > 5)
                        Better than {!! $efficPercs["Lighting"]["ash"][3] !!}% 
                        in climate zone {{ $sessData["PowerScore"][0]->PsAshrae }}
                    @elseif (($efficPercs["Lighting"]["all"][0]+$efficPercs["Lighting"]["all"][1]) > 5)
                        Better than {!! $efficPercs["Lighting"]["all"][3] !!}% in the U.S.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<div class="row mT10 mB10 slGrey">
    <div class="col-md-4"><div class="pL10">
    = 
    @if (isset($sessData["PowerScore"][0]->PsEfficLightingMother) 
        && $sessData["PowerScore"][0]->PsEfficLightingMother > 0)
        ( (Mother Watts x 24) / Mother Square foot )
        <div class="pL10"> + 
    @endif
    ( (Clone Watts x 24) / Clone Square foot )
    @if (!isset($sessData["PowerScore"][0]->PsEfficLightingMother) 
        || $sessData["PowerScore"][0]->PsEfficLightingMother == 0)
        <div class="pL10">
    @endif
    + 
    ( (Veg Watts x 18) / Veg Square foot )<br /> + 
    ( (Flower Watts x 12) / Flower Square foot )
    </div></div></div>
    <div class="col-md-4"><div class="pL10">
        = 
        @if (isset($sessData["PowerScore"][0]->PsEfficLightingMother) 
            && $sessData["PowerScore"][0]->PsEfficLightingMother > 0)
            @if (isset($sessData["PowerScore"][0]->PsEfficLightingMother)) 
                {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLightingMother, 3) }} 
            @else 0 @endif Mother
            <div class="pL10"> +
        @endif
        @if (isset($sessData["PowerScore"][0]->PsEfficLightingClone)) 
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLightingClone, 3) }} 
        @else 0 @endif Clone<br /> 
        @if (!isset($sessData["PowerScore"][0]->PsEfficLightingMother) 
            || $sessData["PowerScore"][0]->PsEfficLightingMother == 0)
            <div class="pL10">
        @endif
        +
        @if (isset($sessData["PowerScore"][0]->PsEfficLightingVeg)) 
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLightingVeg, 3) }} 
        @else 0 @endif Veg<br /> + 
        @if (isset($sessData["PowerScore"][0]->PsEfficLightingFlower)) 
            {{ $GLOBALS["SL"]->sigFigs($sessData["PowerScore"][0]->PsEfficLightingFlower, 3) }} 
        @else 0 @endif Flower
        </div>
    </div></div>
</div>
@if (!isset($sessData["PowerScore"][0]->PsEfficLighting) || $sessData["PowerScore"][0]->PsEfficLighting == 0)
    </div>
@endif

<div class="row mT20">
    <div class="col-md-8">
        <p><a href="/public-comments-stakeholder-feedback"><i>
        Please explore Public Commenting to share more insight and stakeholder feedback.</i></a></p>
    </div>
    <div class="col-md-4">
        <div class="slGrey">
        <p><i>Ranking (Above Average, Average, Below Average) within 
        <span class="scoreBig"><a href="https://www.ashrae.org/" target="_blank">ASHRAE</a> 
        Climate 
        @if (isset($sessData["PowerScore"][0]->PsAshrae) && trim($sessData["PowerScore"][0]->PsAshrae) != '')
            Zone {{ $sessData["PowerScore"][0]->PsAshrae }} 
        @else Zones @endif </span> is coming soon...</i></p>
        </div>
    </div>
</div>

<div class="p20 m10"></div>

<style>
#blockWrap492 { margin-bottom: 40px; }
.scoreBig { color: #FFF; text-shadow: -1px 1px 2px #000; }
</style>
<script type="text/javascript"> $(document).ready(function(){
        
	$(document).on("click", "#hidivBtnLgtInfo", function() {
        if (document.getElementById("hidivBtnWrp")) { $("#hidivBtnWrp").slideUp("fast"); }
	});
	
<?php /*
	var meters = new Array();
    function spinMeter(id) {
        if (meters[id] && meters[id][0] <= meters[id][1]) {
            var degree = ((180*meters[id][0])/100)-90;
            var needMargTop = 2;
            var needMargLeft = parseInt((meters[id][5]/2)-(meters[id][6]/2)); // parseInt(meters[id][5]*0.05);
            var trig = Math.cos((degree*3.14)/180)*meters[id][4];
            if (degree <= 0) needMargLeft = parseInt((meters[id][5]/2)-(meters[id][6]/2)-trig);
if (id == 1) { document.getElementById("tmpLog").innerHTML+='id: '+id+', '+parseInt(meters[id][0])+' = '+parseInt(degree)+' , trig: '+parseInt(trig)+' marginL: '+needMargLeft+'<br />'; }
            $("#meter"+id+"Needle").css({
                '-webkit-transform' : 'rotate('+degree+'deg)',
                '-moz-transform'    : 'rotate('+degree+'deg)',
                '-ms-transform'     : 'rotate('+degree+'deg)',
                'transform'         : 'rotate('+degree+'deg)',
                'margin-left'       : ''+needMargLeft+'px', 
                'margin-top'        : ''+needMargTop+'px'
            });
            meters[id][0]+=meters[id][2];
            setTimeout(function() { spinMeter(id); }, meters[id][3]);
        }
        return true;
    }
    function loadMeter(id, dest, inc, speed) {
        $("#meter"+id+"Base").css({
            'width' : $("#meter"+id+"Img").width() + 'px'
        });
        var imgHeight = parseInt($("#meter"+id+"Img").height()*0.9);
        $("#meter"+id+"Needle").css({
            'height'  : imgHeight + 'px',
            'display' : 'block'
        });
        $("#meter"+id+"Needle img").css({
            'height'  : imgHeight + 'px'
        });
	    meters[id] = [0, dest, inc, speed, imgHeight, $("#meter"+id+"Img").width(), $("#meter"+id+"Needle").width()];
        spinMeter(id);
        return true;
    }
	$(document).on("click", ".meterAnim", function() {
        var id    = parseInt($(this).attr("data-meterid"));
        var dest  = parseInt($(this).attr("data-destination"));
        var speed = parseInt($(this).attr("data-speed"));
        var inc   = (dest/30); // parseInt($(this).attr("data-increment"));
        //alert("umm? "+id+" "+dest+" "+inc+" "+speed);
        setTimeout(function() { loadMeter(id, dest, inc, speed); }, speed);
    });
*/ ?>

}); </script>

<?php /*
<style>
.meterAnim {
    position: relative;
    width: 100%;
}
.meterNeedle {
    position: absolute;
    -webkit-transform-origin: center bottom;
    -moz-transform-origin: center bottom;
    -ms-transform-origin: center bottom;
    transform-origin: center bottom;
    margin-left: 50%;
    margin-top: 0%;
    display: none;
    border: 1px #00ff00 solid;
}
.meterNeedle img {
    height: 100%;
    border: 1px #ff0000 solid;
}
</style>
*/ ?>
