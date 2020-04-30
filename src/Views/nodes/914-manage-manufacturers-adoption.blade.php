<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers-adoption.blade.php -->

<h2>Manufacturer Adoption</h2>
<p>
    Out of the Lighting Sub-Scores in the
    PowerScore's official data set.
</p>
<div class="row">
    <div class="col-4 slBlueDark">Manufacturer</div>
    <div class="col-2 slBlueDark taC">Flower</div>
    <div class="col-2 slBlueDark taC">Veg</div>
    <div class="col-2 slBlueDark taC">Clone</div>
    <div class="col-2 slBlueDark taC">Mother</div>
</div>
<?php $cnt = 0; ?>
@forelse ($manus as $i => $manu)
    @if ($manu->manu_cnt_flower > 0
        || $manu->manu_cnt_veg > 0
        || $manu->manu_cnt_clone > 0
        || $manu->manu_cnt_mother > 0)
        <?php $cnt++; ?>
        <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
            <div class="row">
                <div class="col-4">
                    <?php /*
                    <a href="/dash/competitive-performance?manu={{
                        urlencode($manu->manu_name) }}"
                    */ ?>
                    <a href="/dash/compare-powerscores?fltManuLgt={{
                        urlencode($manu->manu_id) }}#n867"
                        >{{ $manu->manu_name }}</a>
                </div>
                @foreach (['flower', 'veg', 'clone', 'mother'] as $nick)
                {!! view(
                    'vendor.cannabisscore.nodes.914-manage-manufacturers-cnt', 
                    [
                        'manu' => $manu,
                        'nick' => $nick
                    ]
                )->render() !!}
                @endforeach
            </div>
        </div>
    @endif
@empty
@endforelse