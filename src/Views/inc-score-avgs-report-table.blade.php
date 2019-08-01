<!-- generated from resources/views/vendor/cannabisscore/inc-score-avgs-report-table.blade.php -->
<table border=0 class="table table-striped w100">
@forelse ($tbl->rows as $i => $row)
    <tr @if (in_array($i, $tbl->lineRows)) class="brdBot" @endif >
    @forelse ($row as $j => $cell)
        @if ($i == 0 || $j == 0) <th @else <td @endif @if (in_array($j, $tbl->lineCols)) class="brdRgt" @endif >
        {!! $cell !!}
        @if ($i == 0 || $j == 0) </th> @else </td> @endif
    @empty
    @endforelse
    </tr>
@empty
@endforelse
</table>
