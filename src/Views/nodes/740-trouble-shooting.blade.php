<!-- generated from resources/views/vendor/cannabisscore/nodes/740-trouble-shooting.blade.php -->
<div class="slCard nodeWrap">
    <h2>Troubleshooting</h2>
    @if (isset($importResult)) {!! $importResult !!} @endif

    <p>&nbsp;</p>
    <h4>Recalculate All KPIs</h4>
    <p>
        This process wipes all PowerScore calculations clean, and re-performs them fresh.
        This is useful for anything cases of any changes to our scoring logic, big or small.
        It is also a fine method to ensure that [manual] record changes to one or more 
        PowerScores are reflected in the calculations.
        Occasionally good for mysterious problems.
    </p>
    <p>
        <a href="?recalc=1" class="btn btn-primary btn-lg">Recalculate All KPIs</a>
    </p>

    <p>&nbsp;</p>
    <h4>Recalculate All PowerScore Rankings</h4>
    <p>
        This process wipes all PowerScore rankings clean, and re-performs them fresh.
        This happens every time a PowerScore is completed, and should be run after
        any manual record changes, or score logic changes, etc. 
        Occasionally good for mysterious problems.
    </p>
    <p>
        <a href="?refresh=1" class="btn btn-primary btn-lg">Recalculate All Rankings</a>
    </p>
</div>

<div class="slCard nodeWrap">
    <h4>Temporary Trouble</h4>
    <p>
        <a href="?artifChk=1" class="btn btn-primary btn-lg"
            >Check Bad Artificial Lighting Data</a>
    </p>
    @if ($GLOBALS["SL"]->REQ->has('artifChk'))
        @if (isset($artifErrs) && trim($artifErrs) != '')
            {!! $artifErrs !!}
        @else
            <i>No artificial lighting data errors detected.</i>
        @endif
    @endif
    </div>
</div>



@if (isset($logOne) && trim($logOne) != '')
    <div class="jumbotron">{!! $logOne !!}</div>
@endif

@if (isset($chk1) && $chk1->isNotEmpty())
    <h3 class="slBlueDark fC">Found {{ sizeof($chk1) }} Wiped Records!</h3>
    <table class="table table-striped">
    @foreach ($chk1 as $i => $chk)
        <tr>
        <td><pre style="width: 300px;">{!! print_r($chk) !!}</pre></td>
        <td> @if (isset($chks2[$i])) <pre style="width: 500px;">{!! print_r($chks2[$i]) !!}</pre> @endif </td>
        </tr>
    @endforeach
    </table>
@endif

<?php /*
<iframe id="calcRefresh" src="" class="w100 h50"></iframe>
<script type="text/javascript">
@forelse ($allScoreIDs as $i => $id)
    setTimeout("document.getElementById('calcRefresh').src='/calculated/u-{{ $id->ps_id }}?refresh=1'", {{ (1+($i*7000)) }});
@empty
@endforelse
</script>
*/ ?>