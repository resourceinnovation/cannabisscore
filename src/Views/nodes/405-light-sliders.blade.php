<!-- generated from resources/views/vendor/cannabisscore/nodes/405-light-sliders.blade.php -->
<div id="node405" class="nodeWrap">
<div class="nodeHalfGap"></div>
<div id="nLabel405" class="nPrompt"><label for="n405FldID" class="w100">
    During each phase, what <span class="txtInfo">percentage of light used is artificial?</span>
    <div id="nLabel405notes" class="subNote"><p>
        Enter the percentage number, or drag the sliders. 
        If grown completely from seed, check boxes for no mothers and no clones.
    </p></div>
</label></div>
<div class="nFld" style="font-size: 20px;">
	<table class="table w100 areaTbl">
	
		<tr>
		<th class="areaTh"><nobr>Mother Plants</nobr></th>
		<td colspan=2 id="n405r1Td" class="w100 opac100">
		    <div class="row m0 p0">
		        <div class="col-5">
                    <nobr><input type="number" name="n405r1fld" id="n405r1FldID" data-nid="405" 
                        @if (isset($psAreas["Mother"]->PsAreaLgtArtifPerc)) 
                            value="{{ $psAreas['Mother']->PsAreaLgtArtifPerc }}" 
                        @endif class="form-control ntrStp slTab disIn taR slidePercFld"
                        {!! $GLOBALS["SL"]->tabInd() !!}> % Artificial&nbsp;&nbsp;</nobr>
                </div>
		        <div class="col-7 slideCol">
                    <div id="n405r1slider" class="ui-slider ui-slider-horizontal slSlider"></div>
                </div>
            </div>
        </td>
		<td class="areaNa"><label class="w100 fPerc66 opac50" id="n405r1naLbl">
			<nobr><input type="checkbox" name="n405r1na" id="n405r1naID" class="mR5" value="N" 
			    @if (isset($powerScore->PsMotherSeed) && intVal($powerScore->PsMotherSeed) == 1) CHECKED @endif
			    > No</nobr> Mothers
		</label></td>
		</tr>
	
		<tr>
		<th class="areaTh"><nobr>Clone Plants</nobr></th>
		<td colspan=2 id="n405r2Td" class="w100 opac100">
		    <div class="row m0 p0">
		        <div class="col-5">
                    <nobr><input type="number" name="n405r2fld" id="n405r2FldID" data-nid="405" 
                        @if (isset($psAreas["Clone"]->PsAreaLgtArtifPerc)) 
                            value="{{ $psAreas['Clone']->PsAreaLgtArtifPerc }}" 
                        @endif class="form-control ntrStp slTab disIn taR slidePercFld"
                        {!! $GLOBALS["SL"]->tabInd() !!}> % Artificial&nbsp;&nbsp;</nobr>
                </div>
		        <div class="col-7 slideCol">
                    <div id="n405r2slider" class="ui-slider ui-slider-horizontal slSlider"></div>
                </div>
            </div>
        </td>
		<td class="areaNa"><label class="w100 fPerc66 opac50" id="n405r2naLbl">
			<nobr><input type="checkbox" name="n405r2na" id="n405r2naID" class="mR5" value="N" 
			    @if (isset($powerScore->PsCloneSeed) && intVal($powerScore->PsCloneSeed) == 1) CHECKED @endif
			    > No</nobr> Clones
		</label></td>
		</tr>
		
		<tr>
		<th class="areaTh"><nobr>Vegetating &nbsp;</nobr></th>
		<td colspan=2 class="w100">
		    <div class="row m0 p0">
		        <div class="col-5">
                    <nobr><input type="number" name="n405r3fld" id="n405r3FldID" data-nid="405" 
                        @if (isset($psAreas["Veg"]->PsAreaLgtArtifPerc)) 
                            value="{{ $psAreas['Veg']->PsAreaLgtArtifPerc }}" 
                        @endif class="form-control ntrStp slTab disIn taR slidePercFld"
                        {!! $GLOBALS["SL"]->tabInd() !!}> % Artificial&nbsp;&nbsp;</nobr>
                </div>
		        <div class="col-7 slideCol">
                    <div id="n405r3slider" class="ui-slider ui-slider-horizontal slSlider"></div>
                </div>
            </div>
        </td>
		<td></td>
		</tr>
		
		<tr>
		<th class="areaTh"><nobr>Flowering &nbsp;</nobr></th>
		<td colspan=2 class="w100">
		    <div class="row m0 p0">
		        <div class="col-5">
                    <nobr><input type="number" name="n405r4fld" id="n405r4FldID" data-nid="405" 
                        @if (isset($psAreas["Flower"]->PsAreaLgtArtifPerc)) 
                            value="{{ $psAreas['Flower']->PsAreaLgtArtifPerc }}" 
                        @endif class="form-control ntrStp slTab disIn taR slidePercFld"
                        {!! $GLOBALS["SL"]->tabInd() !!}> % Artificial&nbsp;&nbsp;</nobr>
                </div>
		        <div class="col-7 slideCol">
                    <div id="n405r4slider" class="ui-slider ui-slider-horizontal slSlider"></div>
                </div>
            </div>
        </td>
		<td></td>
		</tr>
		
	</table>
</div>
<style> 
table.areaTbl tr th, table.areaTbl tr td {
    font-size: 16px;
}
@media screen and (max-width: 480px) {
    table.areaTbl {
        margin: 0px -10px;
    }
}
th.areaTh, table tr th.areaTh, table.areaTbl tr th.areaTh {
    padding-top: 14px;
}
td.areaNa, table tr td.areaNa, table.areaTbl tr td.areaNa {
    padding-top: 6px;
}
.slideCol {
    padding: 9px 0px 0px 5px;
}
.slidePercFld {
    width: 55px; 
    padding: 6px 6px;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
    
	$("#n405r1slider").slider({ 
	    step: 5,
	    change: function( event, ui ) {
            document.getElementById("n405r1FldID").value=$("#n405r1slider").slider("value");
        } 
	});
	$("#n405r2slider").slider({ 
	    step: 5,
	    change: function( event, ui ) {
            document.getElementById("n405r2FldID").value=$("#n405r2slider").slider("value");
        } 
	});
	$("#n405r3slider").slider({ 
	    step: 5,
	    change: function( event, ui ) {
            document.getElementById("n405r3FldID").value=$("#n405r3slider").slider("value");
        } 
	});
	$("#n405r4slider").slider({ 
	    step: 5,
	    change: function( event, ui ) {
            document.getElementById("n405r4FldID").value=$("#n405r4slider").slider("value");
        } 
	});
	
    $(document).on("keyup", "#n405r1FldID", function() {
        $("#n405r1slider").slider('value', document.getElementById("n405r1FldID").value);
	});
    $(document).on("keyup", "#n405r2FldID", function() {
        $("#n405r2slider").slider('value', document.getElementById("n405r2FldID").value);
	});
    $(document).on("keyup", "#n405r3FldID", function() {
        $("#n405r3slider").slider('value', document.getElementById("n405r3FldID").value);
	});
    $(document).on("keyup", "#n405r4FldID", function() {
        $("#n405r4slider").slider('value', document.getElementById("n405r4FldID").value);
	});
	
	setTimeout(function() {
        $("#n405r1slider").slider('value', document.getElementById("n405r1FldID").value);
        $("#n405r2slider").slider('value', document.getElementById("n405r2FldID").value);
        $("#n405r3slider").slider('value', document.getElementById("n405r3FldID").value);
        $("#n405r4slider").slider('value', document.getElementById("n405r4FldID").value);
	}, 5);
	
    $(document).on("click", "#n405r1naID", function() { checkPump(); });
    $(document).on("click", "#n405r2naID", function() { checkPump(); });
	
    function checkPump() {
        if (document.getElementById("node305")) {
            var sunVeg = (document.getElementById("n405r3FldID") && document.getElementById("n405r3FldID").value < 100);
            var sunFlo = (document.getElementById("n405r4FldID") && document.getElementById("n405r4FldID").value < 100);
            if (sunVeg || sunFlo) {
                if (document.getElementById("node305").style.display == "none") { $("#node305").slideDown("fast"); }
            } else {
                if (document.getElementById("node305").style.display != "none") { $("#node305").slideUp("fast"); }
            }
        }
	    if (document.getElementById("n405r1Td") && document.getElementById("n405r1naLbl")) {
            if (document.getElementById("n405r1naID") && document.getElementById("n405r1naID").checked) {
                document.getElementById("n405r1Td").className="w100 opac20";
                document.getElementById("n405r1naLbl").className="w100 fPerc66 opac100";
            } else {
                document.getElementById("n405r1Td").className="w100 opac100";
                document.getElementById("n405r1naLbl").className="w100 fPerc66 opac50";
            }
        }
	    if (document.getElementById("n405r2Td") && document.getElementById("n405r2naLbl")) {
            if (document.getElementById("n405r2naID") && document.getElementById("n405r2naID").checked) {
                document.getElementById("n405r2Td").className="w100 opac20";
                document.getElementById("n405r2naLbl").className="w100 fPerc66 opac100";
            } else {
                document.getElementById("n405r2Td").className="w100 opac100";
                document.getElementById("n405r2naLbl").className="w100 fPerc66 opac50";
            }
        }
        setTimeout(function() { checkPump(); }, 500);
        return true;
    }
    setTimeout(function() { checkPump(); }, 1);
    
});
</script>
<div class="nodeHalfGap"></div>
</div>