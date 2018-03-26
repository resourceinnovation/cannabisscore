<!-- generated from resources/views/vendor/cannabisscore/nodes/398-total-sqft.blade.php -->
<div class="nodeAnchor"><a name="n398"></a></div>
<div id="node398" class="nodeWrap">
<div class="nodeHalfGap"></div>
<div id="nLabel398" class="nPrompt">
    <label for="n398FldID" class="w100">
        Average <span class="slBlueDark">square footage of flowering canopy</span> under production throughout the 
        12-month reporting period aligned with your utility bill submissions:
        <span class="red">*required</span>
    </label>
    <div id="nLabel398notes" class="subNote">
        <p>Please include a reasonable estimate of average canopy under production, not total area available for 
        planting. Exclude all mother/clone, vegetative, drying/curing, office, storage and other areas.</p>
        
        <div id="areaCalc">
            <table class="table table-striped" id="arears" >
            <tr id="calcIn1row">
            <td class="taR"><div>Mother area, if relevant and distinct from other areas:</div></td>
            <td><input type="number" name="calcIn1" id="calcI1" class="form-control ntrStp slTab" 
                {!! $GLOBALS["SL"]->tabInd() !!}></td>
            <td><div>ft<sup>2</sup></div></td>
            </tr>
            <tr id="calcIn2row">
                <td class="taR"><div><b>+</b> 
                    Clone area, if relevant and distinct from other areas:</div></td>
                <td><input type="number" name="calcIn2" id="calcI2" class="form-control ntrStp slTab"
                    {!! $GLOBALS["SL"]->tabInd() !!}></td>
                <td><div>ft<sup>2</sup></div></td>
            </tr>
            <tr id="calcIn3row">
                <td class="taR"><div><b>+</b> Vegetative canopy:</div></td>
                <td><input type="number" name="calcIn3" id="calcI3" class="form-control ntrStp slTab"
                    {!! $GLOBALS["SL"]->tabInd() !!}></td>
                <td><div>ft<sup>2</sup></div></td>
            </tr>
            <tr id="calcIn4row">
                <td class="taR"><div><b>+</b> Flowering canopy:</div></td>
                <td><input type="number" name="calcIn4" id="calcI4" class="form-control ntrStp slTab"
                    {!! $GLOBALS["SL"]->tabInd() !!}></td>
                <td><div>ft<sup>2</sup></div></td>
            </tr>
            <tr>
                <td colspan=2 class="taR"><a href="javascript:;" class="pull-right btn btn-default areaCalcTot"
                    >Add Up Square Feet & Apply Total &darr;</a></td>
                <td></td>
            </tr>
            </table>
        </div>
    </div>
</div>
<div class="nFld" style="font-size: 20px;">
    <input class="form-control input-lg ntrStp slTab" type="number" name="n398fld" id="n398FldID" 
        onKeyUp=" checkNodeUp('398', -1, 0);  checkMin('398', 0); "  data-nid="398" min="0" 
        @if (isset($powerScore) && isset($powerScore->PsTotalSize)) value="{{ $powerScore->PsTotalSize }}" @endif 
        {!! $GLOBALS["SL"]->tabInd() !!}>
</div>
<div class="nodeHalfGap"></div>
</div> <!-- end #node398 -->


<script type="text/javascript">
$(document).ready(function(){
    function updateAreaTot() {
        if (!document.getElementById("n398FldID")) return false;
        var newTot = 0;
        for (var j=1; j<5; j++) {
            if (document.getElementById("calcI"+j+"") && document.getElementById("calcI"+j+"").value > 0) {
                newTot += (1*document.getElementById("calcI"+j+"").value);
            }
        }
        if (newTot > 0) document.getElementById("n398FldID").value = newTot;
        return true;
    }
    $(document).on("click", ".areaCalcTot", function() { updateAreaTot(); });
    $(document).on("click", "#areaCalcBtn", function() { $("#areaCalc").slideDown('fast'); });
    
    $(document).on("keyup", "#n483FldID", function() {
        document.getElementById("calcI1").value=document.getElementById("n483FldID").value;
    });
    $(document).on("keyup", "#n342FldID", function() {
        document.getElementById("calcI2").value=document.getElementById("n342FldID").value;
    });
    $(document).on("keyup", "#n324FldID", function() {
        document.getElementById("calcI3").value=document.getElementById("n324FldID").value;
    });
    $(document).on("keyup", "#n343FldID", function() {
        document.getElementById("calcI4").value=document.getElementById("n343FldID").value;
    });
    
    var grabSquareFeets = function () {
        if (document.getElementById("n483FldID") && document.getElementById("calcI1")) {
            document.getElementById("calcI1").value=document.getElementById("n483FldID").value;
        }
        if (document.getElementById("n342FldID") && document.getElementById("calcI2")) {
            document.getElementById("calcI2").value=document.getElementById("n342FldID").value;
        }
        if (document.getElementById("n324FldID") && document.getElementById("calcI3")) {
            document.getElementById("calcI3").value=document.getElementById("n324FldID").value;
        }
        if (document.getElementById("n343FldID") && document.getElementById("calcI4")) {
            document.getElementById("calcI4").value=document.getElementById("n343FldID").value;
        }
        if (document.getElementById("calcIn1row") && !document.getElementById("n483FldID")) {
            document.getElementById("calcIn1row").style.display='none';
        }
        if (document.getElementById("calcIn2row") && !document.getElementById("n342FldID")) {
            document.getElementById("calcIn2row").style.display='none';
        }
        if (document.getElementById("n398FldID") && (document.getElementById("n398FldID").value.trim() == '' || document.getElementById("n398FldID").value == 0)) {
            updateAreaTot();
        }
        return true;
    }
    setTimeout(grabSquareFeets, 100);

});
</script>
<style>
#arears tr td, #arears tr th { border: 0px none; color: #8C8676; }
#areaCalc { display: block; margin: 20px 0px -20px 0px; }
#arears tr td div { padding-top: 7px; }
#arears tr td.taR { font-size: 16px; }
@media screen and (max-width: 480px) {
    #arears tr td div { padding-top: 0px; }
    #arears tr td.taR { font-size: 13px; }
}
</style>