<!-- generated from resources/views/vendor/cannabisscore/nodes/742-process-uploads.blade.php -->
<p>&nbsp;</p>
<div id="animLoadingUploads" class="float-right disBlo slBlueDark">
    <i>Loading...</i> <img src="/cannabisscore/uploads/greenometer-spinner.gif" border=0 height=40 ></div>
<h2>Processing Uploads</h2>
<div class="p20 m20"></div>
</form>
<table class="table table-striped">
@forelse ($uploaders as $i => $ps)
    <form name="admPrcsUplds{{ $ps->PsID }}" id="admPrcsUplds{{ $ps->PsID }}ID" action="?sub=1&psid={{ $ps->PsID }}" 
        method="post" target="hidFrame">
    <input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="kwh{{ $ps->PsID }}a" @if (isset($ps->PsKWH)) value="{{ $ps->PsKWH }}" @endif >
    <tr id="psUpload{{ $ps->PsID }}" class="disNon">
    <td>
        <div class="nodeAnchor"><a name="ps{{ $ps->PsID }}"></a></div>
        <a href="/calculated/u-{{ $ps->PsID }}" target="_blank"
            ><h3 class="disIn mB0 mR20 slGrey">PowerScore #{{ $ps->PsID }}</h3></a>
        Status: 
        <select name="status{{ $ps->PsID }}" >
            <option value="242" @if ($ps->PsStatus == 242) SELECTED @endif >Incomplete</option>
            <option value="243" @if ($ps->PsStatus == 243) SELECTED @endif >Completed</option>
            <option value="364" @if ($ps->PsStatus == 364) SELECTED @endif >Archived</option>
        </select>
        @if (isset($ps->PsName) && trim($ps->PsName) != '') <h3 class="m0 slGrey">{{ $ps->PsName }}</h3> @endif
        <div id="upPrevs{{ $ps->PsID }}" class="w100"></div>
    </td>
    <td style="width: 100px; padding-top: 35px;">
        <div class="nFld mT0 slBlueDark">
            <div class="bld">Total Annual kWh:</div>
            <nobr><input type="number" name="kwh{{ $ps->PsID }}" id="kwh{{ $ps->PsID }}ID" min="0" autocomplete="off"
                class="form-control form-control-lg ntrStp slTab disIn" style="width: 130px;" 
                {!! $GLOBALS["SL"]->tabInd() !!} @if (isset($ps->PsKWH)) value="{{ $ps->PsKWH }}" @endif >
            <span class="mL5">kWh</span></nobr>
        </div>
        <div class="taR"><a class="btn btn-info btn-sm mT10 mB10 monthlyCalcTot" href="javascript:;" 
            data-psid="{{ $ps->PsID }}" >Add Up &larr;<br />
            <nobr>&nbsp;&nbsp;&nbsp;&uarr; & Apply Total&nbsp;&nbsp;&nbsp;</nobr></a></div>
        <a class="btn btn-primary btn-lg w100 mT20 monthlyCalcSave" href="javascript:;" data-psid="{{ $ps->PsID }}"
            ><i class="fa fa-floppy-o mR5" aria-hidden="true"></i> Save</a>
    </td>
    <td style="width: 100px; padding-top: 40px;">
        <table class="table table-striped" >
        @for ($mon = 1; $mon <= 12; $mon++)
            <tr>
            <td class="taR slBlueDark pR10 pT5">{{ $GLOBALS["SL"]->num2Month3($mon) }}</td>
            <td style="padding: 0px"><nobr>
                <input type="number" name="kwh{{ $ps->PsID }}m{{ $mon }}" id="kwh{{ $ps->PsID }}m{{ $mon }}ID"
                    class="form-control disIn ntrStp" {!! $GLOBALS["SL"]->tabInd() !!} autocomplete="off" 
                    style="width: 90px;" @if (isset($upMonths[$ps->PsID]) && isset($upMonths[$ps->PsID][$mon])
                        && isset($upMonths[$ps->PsID][$mon]->PsMonthKWH1)) 
                        value="{{ $upMonths[$ps->PsID][$mon]->PsMonthKWH1 }}" @endif > 
                    <span class="mR5 slGrey">kWh</span>
            </nobr></td>
            </tr>
            @if ($mon == 6)
                </table></td><td style="width: 100px; padding-top: 40px;"><table class="table table-striped" >
            @endif
        @endfor
        </table>
    </td>
    </tr>
    </form>
@empty
@endforelse
</table>

@if (isset($log1) && trim($log1) != '') <div class="row2 p15">{!! $log1 !!}</div> @endif

<form>

<script type="text/javascript">
$(document).ready(function(){
    
    $(document).on("click", ".monthlyCalcTot", function() {
		var psid = $(this).attr("data-psid");
        if (document.getElementById("kwh"+psid+"ID")) {
            var newTot=0;
            for (var mon = 1; mon <= 12; mon++) {
                if (document.getElementById("kwh"+psid+"m"+mon+"ID") && document.getElementById("kwh"+psid+"m"+mon+"ID").value.trim() != "") {
                    newTot+=parseInt(document.getElementById("kwh"+psid+"m"+mon+"ID").value);
                }
            }
            document.getElementById("kwh"+psid+"ID").value=newTot;
        }
    });
    $(document).on("click", ".monthlyCalcSave", function() {
		var psid = $(this).attr("data-psid");
        document.getElementById("admPrcsUplds"+psid+"ID").submit();
    });
    
@forelse ($uploaders as $i => $ps)
    setTimeout( function() {
        $("#upPrevs{{ $ps->PsID }}").load("/ajax/powerscore-uploads?p={{ $ps->PsID
            . (($i == sizeof($uploaders)-1) ? '&last=1' : '') }}");
    }, {{ (1+(300*$i)) }});
@empty @endforelse
});
</script>