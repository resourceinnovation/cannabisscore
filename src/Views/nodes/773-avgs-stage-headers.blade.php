<!-- generated from resources/views/vendor/cannabisscore/nodes/773-avgs-stage-headers.blade.php -->
<tr>
    <th>&nbsp;</th>
    @if (!isset($noTotal) || !$noTotal) <th class="brdRgt">Total</th> @endif
    @if (isset($areaTypesFilt))
        @foreach ($areaTypesFilt as $nick => $area) <th>{{ $nick }}</th> @endforeach
    @elseif (isset($areaTypes))
        @foreach ($areaTypes as $nick => $area) <th>{{ $nick }}</th> @endforeach
    @endif
    @if (isset($xtraCol)) {!! $xtraCol !!} @endif
</tr>