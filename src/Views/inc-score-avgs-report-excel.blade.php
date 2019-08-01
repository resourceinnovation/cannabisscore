<!-- generated from resources/views/vendor/cannabisscore/inc-score-avgs-report-excel.blade.php -->
@forelse ($tbl->rows as $i => $row)
    <tr @if (in_array($i, $tbl->lineRows)) class="brdBot" @endif >
    @forelse ($row as $j => $cell) {!! $cell->toExcel($j, in_array($j, $tbl->lineCols)) !!} @empty @endforelse
    </tr>
@empty
@endforelse
