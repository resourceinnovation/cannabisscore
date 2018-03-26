<!-- generated from resources/views/vendor/cannabisscore/nodes/740-trouble-shooting.blade.php -->

<h2>Troubleshooting</h2>

@if (isset($logOne) && trim($logOne) != '')
    <div class="jumbotron">{!! $logOne !!}</div>
@endif

@if (isset($chk1) && $chk1 && sizeof($chk1) > 0)
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

