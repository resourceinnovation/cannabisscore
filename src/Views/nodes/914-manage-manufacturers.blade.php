<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers.blade.php -->

<div class="row">
    <div class="col-md-8">
        <div class="slCard greenline nodeWrap">
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
                @if ($manu->ManuCntFlower > 0
                    || $manu->ManuCntVeg > 0
                    || $manu->ManuCntClone > 0
                    || $manu->ManuCntMother > 0)
                    <?php $cnt++; ?>
                    <div class="w100 pT10 pT10 @if ($cnt%2 > 0) row2 @endif ">
                        <div class="row">
                            <div class="col-4">
                                <a href="/dash/competitive-performance?manu={{
                                    urlencode($manu->ManuName)
                                    }}">{{ $manu->ManuName }}</a>
                            </div>
                            @foreach (['Flower', 'Veg', 'Clone', 'Mother'] as $nick)
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
        </div>
        <div class="slCard greenline nodeWrap">
            <h2>All Manufacturers</h2>
            <p>
                Of lighting and/or HVAC 
                equipment used by growers.
            </p>
            <div class="row">
                <div class="col-sm-6">
                    @forelse ($manus as $i => $manu)
                        {{ $manu->ManuName }}<br />
                        @if ($i == ceil(sizeof($manus)/2)) 
                            </div><div class="col-sm-6"> 
                        @endif
                    @empty 
                        <div class="pT20 slGrey">
                            No manufacturers found
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="slCard greenline nodeWrap">
            <h4>Add Manufacturers</h4>
            <p>One per line.</p>
            <textarea class="form-control w100 mB20"
                name="addManu" ></textarea>
            <input type="submit" value="Add All" 
                class="nFormNext btn btn-primary btn-lg">
        </div>
    </div>
</div>
