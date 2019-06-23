<!-- generated from resources/views/vendor/cannabisscore/nodes/{{ $nID }}-total-grams.blade.php -->
<div id="node{{ $nID }}" class="nodeWrap">
<div id="nLabel{{ $nID }}" class="nPrompt"><label for="n{{ $nID }}FldID" class="w100">
    <p>Over the <span class="txtInfo">12-month period</span>, how many total 
    <span class="txtInfo">grams of dried cannabis flower</span> were produced? 
    <span class="red">*required</span></p>
</label></div>
{!! $gramFormMonths !!}
<div class="nFld" style="font-size: 20px;">
    <nobr><input type="number" name="n{{ $nID }}fld" id="n{{ $nID }}FldID" autocomplete="off"
        onkeyup="checkMin('{{ $nID }}', 0); convertGrams();" 
        data-nid="{{ $nID }}" step="any" class="form-control form-control-lg ntrStp slTab slNodeChange disIn mR10 mB5" 
        style="width: 140px;" @if (isset($currSessData)) value="{{ $currSessData }}" @endif 
        {!! $GLOBALS["SL"]->tabInd() !!}> grams</nobr>
    <span class="mL20 mR20"></span><span class="mL20 mR20 slGrey"><i>or</i></span>
    <nobr><input type="number" name="n{{ $nID }}fldLb" id="n{{ $nID }}FldLbID" autocomplete="off"
        onkeyup="checkMin('{{ $nID }}', 0); 
        document.getElementById('n{{ $nID }}FldID').value=Math.round(this.value*453.592);" 
        value="" min="0" class="form-control form-control-lg ntrStp slTab slNodeChange disIn mR10 mB5" 
        style="width: 140px;" {!! $GLOBALS["SL"]->tabInd() !!}> pounds</nobr>
</div>
<style> #nLabel{{ $nID }} label { width: 100%; } </style>
<div class="nodeHalfGap"></div>
</div>
<script type="text/javascript">
function convertGrams() {
    document.getElementById('n{{ $nID }}FldLbID').value=document.getElementById('n{{ $nID }}FldID').value*0.002204623;
}
@if (isset($currSessData) && $currSessData > 0) setTimeout("convertGrams()", 10); @endif
</script>
