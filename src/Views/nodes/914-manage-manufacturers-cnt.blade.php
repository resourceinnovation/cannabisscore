<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers-cnt.blade.php -->
@if ($manu->{ 'ManuCnt' . $nick } == 0)
    <div class="col-2 taC slGrey">-</div>
@else
    <div class="col-2 taC">{{ 
        number_format($manu->{ 'ManuCnt' . $nick }) 
    }}</div>
@endif