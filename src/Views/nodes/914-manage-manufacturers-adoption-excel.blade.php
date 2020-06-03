<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers-adoption-excel.blade.php -->

<tr>
    <th>Manufacturer</th>
    <th>Total Installs</th>
    <th>Flower</th>
    <th>Veg</th>
    <th>Clone</th>
    <th>Mother</th>
</tr>

<?php $cnt = 0; ?>
@forelse ($manus as $m => $manu)
    @if ($manu->manu_cnt_flower > 0
        || $manu->manu_cnt_veg > 0
        || $manu->manu_cnt_clone > 0
        || $manu->manu_cnt_mother > 0)
        <?php $cnt++; ?>
        <tr>
        <td>{{ $manu->manu_name }}</td>
        <td>{{ number_format(sizeof($manusTots[$m])) }}</td>
        @foreach (['flower', 'veg', 'clone', 'mother'] as $nick)
	        <td>
	        @if ($manu->{ 'manu_cnt_' . $nick } > 0)
			    {{ number_format($manu->{ 'manu_cnt_' . $nick }) }}
			@endif
			</td>
        @endforeach
        </tr>
    @endif
@empty
    <tr><td>No matches found.</td></tr>
@endforelse

<tr>
    <td>Sums</td>
    <td>{{ number_format($manusTotSum) }}</td>
    <td>{{ number_format($stageTots["flower"]) }}</td>
    <td>{{ number_format($stageTots["veg"]) }}</td>
    <td>{{ number_format($stageTots["clone"]) }}</td>
    <td>{{ number_format($stageTots["mother"]) }}</td>
</tr>

