<!-- resources/views/vendor/cannabisscore/nodes/917-manage-lighting-models.blade.php -->
<div class="row">
    <div class="col-md-8">
        <div class="slCard nodeWrap">
            <h2>All Lighting Models</h2>
                @forelse ($models as $i => $model)
                    <div class="row mT5"><div class="col-sm-4">
                        @if (isset($manufacts[$model->lgt_mod_manu_id])) 
                            {{ $manufacts[$model->lgt_mod_manu_id] }} 
                        @endif
                    </div><div class="col-sm-4">
                        {{ $model->lgt_mod_name }}
                    </div><div class="col-sm-4">
                        {{ $model->lgt_mod_tech }}
                    </div></div>
                @empty
                    <div class="pT20 slGrey">No lighting models found</div>
                @endforelse
        </div>
    </div>
    <div class="col-md-4">
        <div class="slCard nodeWrap">
            <h4>Add Lighting Model</h4>
            <p>One per line, tab-separated, with: Manufacturer Model Type</p>
            <textarea name="addModels" 
                class="form-control w100 mB20"></textarea>
            <input type="submit" value="Add All" 
                class="nFormNext btn btn-primary btn-lg">
        </div>
    </div>
</div>
