<!-- generated from resources/views/vendor/cannabisscore/nodes/70-total-kwh.blade.php -->
<div class="nodeAnchor"><a name="n{{ $nID }}"></a></div>
<div id="node{{ $nID }}" class="nodeWrap">
<div class="nodeHalfGap"></div>
<div id="nLabel{{ $nID }}" class="nPrompt">
    <label for="n{{ $nID }}FldID" class="w100">
        Over the <b>same 12-month period</b>, how many total annual kilowatt hours (kWh) did you use?
    </label>
    <div id="nLabel{{ $nID }}notes" class="subNote">
        <p>
        Please report the most recent 12 months of energy consumption, or the previous calendar year's energy 
        consumption. Then click the button below to "Add Up All kWh & Apply Total".
        </p><p>
        Please select the month featuring the most days in the billing cycle. 
        For example, billing cycle Dec. 18-Jan. 17 has more days in January, so January should be selected.
        </p>
        
        
    @if ($GLOBALS["SL"]->REQ->has('test'))
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped mT10 monthers" >
                <tr>
                    <td><i>Month</i></td>
                    <td>&nbsp;</td>
                    <td><i>kWh</i></td>
                </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped monthers">
                @for ($mon = 1; $mon <= 12; $mon++)
                    @if ($mon == 7)
                        </table></div><div class="col-md-6"><table class="table table-striped monthers">
                    @endif
                    <tr>
                    <td id="elec{{ $mon }}th" @if ($mon != 1) class="slBlueDark pL15 pT15" @endif >
                        @if ($mon == 1)
                            <select name="elecMonth" id="elecMonthID" class="form-control ntrStp slTab slBlueDark" 
                                style="width: 60px;" autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!}>
                            @for ($j = 1; $j <= 12; $j++)
                                <option value="{{ $j }}" @if ($j == $powerMonths[0]->PsMonthMonth) SELECTED @endif >
                                    {{ $GLOBALS["SL"]->num2Month3($j) }}</option>
                            @endfor
                            </select>
                        @else {{ $GLOBALS["SL"]->num2Month3($powerMonths[$mon-1]->PsMonthMonth) }} @endif
                    </td>
                    <td> @if ($mon > 1) + @endif </td>
                    <td><nobr>
                        <input type="number" name="elec{{ $mon }}a" id="elec{{ $mon }}aID" 
                            class="form-control disIn ntrStp slTab"
                            @if (isset($powerMonths[$mon-1]->PsMonthKWH1)) value="{{ $powerMonths[$mon-1]->PsMonthKWH1 }}" 
                            @endif autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!}> kWh
                    </nobr></td>
                    </tr>
                @endfor
                </table>
                <a id="monthlyCalcTot" href="javascript:;" class="btn btn-secondary w100 mT20"
                    >Add Up All kWh & Apply Total &darr;</a>
            </div>
        </div>
    @else
        <table class="table table-striped mT10 monthers" >
        <tr>
            <td><i>Month</i></td>
            <td>&nbsp;</td>
            <td><i>kWh</i></td>
            <td><i>Explain any uniquenesses we should know about.</i></td>
        </tr>
        @for ($mon = 1; $mon <= 12; $mon++)
            <tr>
            <td id="elec{{ $mon }}th" @if ($mon != 1) class="slBlueDark pL15 pT15" @endif >
                @if ($mon == 1)
                    <select name="elecMonth" id="elecMonthID" class="form-control ntrStp slTab slBlueDark" 
                        style="width: 60px;" autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!}>
                    @for ($j = 1; $j <= 12; $j++)
                        <option value="{{ $j }}" @if ($j == $powerMonths[0]->PsMonthMonth) SELECTED @endif >
                            {{ $GLOBALS["SL"]->num2Month3($j) }}</option>
                    @endfor
                    </select>
                @else {{ $GLOBALS["SL"]->num2Month3($powerMonths[$mon-1]->PsMonthMonth) }} @endif
            </td>
            <td> @if ($mon > 1) + @endif </td>
            <td><nobr>
                <input type="number" name="elec{{ $mon }}a" id="elec{{ $mon }}aID" 
                    class="form-control disIn ntrStp slTab"
                    @if (isset($powerMonths[$mon-1]->PsMonthKWH1)) value="{{ $powerMonths[$mon-1]->PsMonthKWH1 }}" 
                    @endif autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!}> kWh
            </nobr></td>
            <td class="w100">
                <input type="text" name="elec{{ $mon }}d" id="elec{{ $mon }}dID" class="form-control ntrStp slTab"
                    @if (isset($powerMonths[$mon-1]->PsMonthNotes)) value="{{ $powerMonths[$mon-1]->PsMonthNotes }}" 
                    @endif autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!}>
            </td>
            </tr>
        @endfor
        <tr><td colspan="4" >
            <a id="monthlyCalcTot" href="javascript:;" class="btn btn-secondary w100"
                >Add Up All kWh & Apply Total &darr;</a>
        </td></tr>
        </table>
    @endif

    </div>
</div>
<div class="nFld mT0 slBlueDark fPerc133">
    <h3 class="disIn mR20">Total Annual Kilowatt Hours:</h3>
    <input type="number" name="n{{ $nID }}fld" id="n{{ $nID }}FldID" data-nid="{{ $nID }}" min="0" autocomplete="off"
        class="form-control form-control-lg ntrStp slTab slNodeChange disIn mL20" style="width: 130px;" 
         {!! $GLOBALS["SL"]->tabInd() !!}
        @if (isset($powerScore) && isset($powerScore->PsKWH)) value="{{ $powerScore->PsKWH }}" @endif >
    <span class="mL5">kWh</span> <span class="red mL10">*required</span>
</div>
<div class="nodeHalfGap"></div>
</div> <!-- end #node{{ $nID }} -->


<script type="text/javascript">
var months = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
$(document).ready(function(){

    $(document).on("change", "#elecMonthID", function() {
        var curr = $(this).val()-1;
        for (var i = 2; i <= 12; i++) {
            if (document.getElementById("elec"+i+"th")) {
                curr++;
                if (curr == 12) curr = 0;
                document.getElementById("elec"+i+"th").innerHTML = months[curr];
            }
        }
    });
    
    function updateMonthlyEnergy() {
        if (!document.getElementById("n{{ $nID }}FldID")) return false;
        var newTot = 0;
        for (var i=1; i<=12; i++) {
            if (document.getElementById("elec"+i+"aID") && document.getElementById("elec"+i+"aID").value > 0) {
                newTot += (1*document.getElementById("elec"+i+"aID").value);
            }
        }
        document.getElementById("n{{ $nID }}FldID").value = newTot;
        return true;
    }
    $(document).on("click", "#monthlyCalcTot", function() { updateMonthlyEnergy(); });

});
</script>
<style>
.monthers tr td, .monthers tr th { border: 0px none; color: #8C8676; }
.monthers tr td.slBlueDark { color: #8EAD67; }
.monthers tr th select.form-control { width: 60px; }
.monthers tr td .form-control.disIn, .monthers tr th .form-control.disIn { width: 90px; }

#arears tr td div { padding-top: 7px; }
#arears tr td.taR { font-size: 16px; }
@media screen and (max-width: 480px) {
    #arears tr td div { padding-top: 0px; }
    #arears tr td.taR { font-size: 13px; }
}
</style>