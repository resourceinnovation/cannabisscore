<!-- generated from resources/views/vendor/cannabisscore/nodes/742-process-uploads.blade.php -->
<h2>Processing Uploads</h2>
<form name="admPrcsUplds" action="?sub=1" method="post" >
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="psSavedID" name="psSaved" value="-3">
@if (isset($log1) && trim($log1) != '') <div class="jumbotron">{!! $log1 !!}</div> @endif
<table class="table table-striped">
@forelse ($uploaders as $i => $ps)
    <input type="hidden" name="kwh{{ $ps->PsID }}a" @if (isset($ps->PsKWH)) value="{{ $ps->PsKWH }}" @endif >
    <tr>
    <td>
        <div class="nodeAnchor"><a name="ps{{ $ps->PsID }}"></a></div>
        <a href="/calculated/u-{{ $ps->PsID }}" target="_blank"
            ><h3 class="disIn mR20 slGrey">PowerScore #{{ $ps->PsID }}</h3></a>
        <select name="status{{ $ps->PsID }}" >
            <option value="242" @if ($ps->PsStatus == 242) SELECTED @endif >Incomplete Status</option>
            <option value="243" @if ($ps->PsStatus == 243) SELECTED @endif >Complete Status</option>
        </select>
        <div id="upPrevs{{ $ps->PsID }}" class="w100"></div>
    </td>
    <td style="width: 100px; padding-top: 35px;">
        <div class="nFld mT0 slBlueDark">
            <div class="bld">Total Annual kWh:</div>
            <nobr><input type="number" name="kwh{{ $ps->PsID }}" id="kwh{{ $ps->PsID }}ID" min="0" autocomplete="off"
                class="form-control input-lg ntrStp slTab disIn" style="width: 130px;" 
                {!! $GLOBALS["SL"]->tabInd() !!} @if (isset($ps->PsKWH)) value="{{ $ps->PsKWH }}" @endif >
            <span class="mL5">kWh</span></nobr>
        </div>
        <div class="taR"><a class="btn btn-default btn-xs mT10 mB10 monthlyCalcTot" href="javascript:;" 
            data-psid="{{ $ps->PsID }}" >&uarr; Add Up & Apply Total &larr;</a></div>
        <a class="btn btn-primary btn-lg w100 mT20 monthlyCalcSave" href="javascript:;" data-psid="{{ $ps->PsID }}"
            ><i class="fa fa-floppy-o mR5" aria-hidden="true"></i> Save All</a>
    </td>
    <td style="width: 100px; padding-top: 40px;">
        <table class="table table-striped" >
        @for ($mon = 1; $mon <= 12; $mon++)
            <tr>
            <td class="taR slBlueDark pR10 pT5">{{ $GLOBALS["SL"]->num2Month3($mon) }}</td>
            <td style="padding: 0px"><nobr>
                <input type="number" name="kwh{{ $ps->PsID }}m{{ $mon }}" id="kwh{{ $ps->PsID }}m{{ $mon }}ID"
                    class="form-control disIn ntrStp" {!! $GLOBALS["SL"]->tabInd() !!} autocomplete="off" 
                    style="width: 90px;"> <span class="mR5">kWh</span>
            </nobr></td>
            </tr>
            @if ($mon == 6)
                </table></td><td style="width: 100px; padding-top: 40px;"><table class="table table-striped" >
            @endif
        @endfor
        </table>
    </td>
    </tr>
@empty
@endforelse
</table>
</form>

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
        document.getElementById("psSavedID").value=psid;
        document.admPrcsUplds.action+="#ps"+psid;
        document.admPrcsUplds.submit();
    });
    
@forelse ($uploaders as $i => $ps)
    setTimeout( function() {
        $("#upPrevs{{ $ps->PsID }}").load("/ajax/powerscore-uploads?p={{ $ps->PsID }}");
    }, {{ (1+(500*$i)) }});
@empty @endforelse
});
</script>