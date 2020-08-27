<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers-adoption.blade.php -->

<a class="btn btn-secondary pull-right" 
    href="?excel=1" href="javascript:;">
    <i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
    Excel</a>
<a href="?manuSrch="><h2>Manufacturer Adoption</h2></a>
<p>
    Out of the Lighting KPIs in the PowerScore's ranked data set.
</p>

<div class="row">
    <div class="col-8">
        <input type="text" class="form-control" 
            name="manuSrch" id="manuSrchID"
            @if ($GLOBALS['SL']->REQ->has('manuSrch'))
                value="{{ trim($GLOBALS['SL']->REQ->manuSrch) }}"
            @endif >
    </div>
    <div class="col-4">
        <a id="manuSrchBtn" href="javascript:;"
            class="btn btn-secondary btn-block"
            onClick="window.location='?manuSrch='+document.getElementById('manuSrchID').value; return false;">
            <i class="fa fa-search mR3" aria-hidden="true"></i> Search</a>
    </div>
</div>

{!! $addManusForm !!}

<div class="w100 pT10 pB10">
    <div class="row">
        <div class="col-3 slBlueDark">Manufacturer</div>
        <div class="col-1 slBlueDark"><nobr>Total Installs</nobr></div>
        <div class="col-2 slBlueDark taC">Flower</div>
        <div class="col-2 slBlueDark taC">Veg</div>
        <div class="col-2 slBlueDark taC">Clone</div>
        <div class="col-2 slBlueDark taC">Mother</div>
    </div>
</div>

<?php $cnt = 0; ?>
@forelse ($manus as $m => $manu)
    @if ($manu->manu_cnt_flower > 0
        || $manu->manu_cnt_veg > 0
        || $manu->manu_cnt_clone > 0
        || $manu->manu_cnt_mother > 0)
        <?php $cnt++; ?>
        <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
            <div class="row">
                <div class="col-3">
                    <?php /*
                    <a href="/dash/competitive-performance?manu={{
                        urlencode($manu->manu_name) }}"
                    */ ?>
                    <a href="/dash/compare-powerscores?fltManuLgt={{
                        urlencode($manu->manu_id) }}#n867"
                        >{{ $manu->manu_name }}</a>
                </div>
                <div class="col-1 taC">
                    {{ number_format(sizeof($manusTots[$m])) }}
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
    <p><i>No matches found.</i></p>
@endforelse

<div class="pT5 pB5"><hr></div>
<div class="row slGrey">
    <div class="col-3">Sums</div>
    <div class="col-1 taC">{{ number_format($manusTotSum) }}</div>
    <div class="col-2 taC">{{ number_format($stageTots["flower"]) }}</div>
    <div class="col-2 taC">{{ number_format($stageTots["veg"]) }}</div>
    <div class="col-2 taC">{{ number_format($stageTots["clone"]) }}</div>
    <div class="col-2 taC">{{ number_format($stageTots["mother"]) }}</div>
</div>