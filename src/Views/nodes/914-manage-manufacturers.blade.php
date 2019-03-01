<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers.blade.php -->
<div class="row">
    <div class="col-md-8">
        <div class="slCard nodeWrap">
            <h2>All Manufacturers</h2>
            <p>Of lighting and/or HVAC equipment used by growers.</p>
            <div class="row"><div class="col-sm-6">
                @forelse ($manus as $i => $manu)
                    {{ $manu->ManuName }}<br />
                    @if ($i == ceil(sizeof($manus)/2)) </div><div class="col-sm-6"> @endif
                @empty <div class="pT20 slGrey">No manufacturers found</div>
                @endforelse
            </div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="slCard nodeWrap">
            <h4>Add Manufacturers</h4>
            <p>One per line.</p>
            <textarea name="addManu" class="form-control w100 mB20"></textarea>
            <input type="submit" value="Add All" class="nFormNext btn btn-primary btn-lg">
        </div>
    </div>
</div>
