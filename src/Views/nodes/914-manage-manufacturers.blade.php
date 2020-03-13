<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers.blade.php -->

<div class="slCard nodeWrap">
    {!! view(
        'vendor.cannabisscore.nodes.914-manage-manufacturers-adoption', 
        [ 'manus' => $manus ]
    )->render() !!}
</div>

<div class="slCard nodeWrap">
    <h3>All Manufacturers</h3>
    <p>
        Of lighting and/or HVAC 
        equipment used by growers.
    </p>
    <div class="row">
        <div class="col-sm-6">
            @forelse ($manus as $i => $manu)
                {{ $manu->manu_name }}<br />
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