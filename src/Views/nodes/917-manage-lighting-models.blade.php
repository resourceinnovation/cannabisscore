<!-- resources/views/vendor/cannabisscore/nodes/917-manage-lighting-models.blade.php -->
<div class="slCard nodeWrap">
    <h2>All Lighting Models</h2>
        @forelse ($models as $i => $model)
            <div class="mT5 pL15 pR15 pT5 pB5 @if ($i%2 == 0) row2 @endif ">
                <div class="row">
                    <div class="col-sm-4">
                        @if (isset($manufacts[$model->lgt_mod_manu_id])) 
                            {{ $manufacts[$model->lgt_mod_manu_id] }} 
                        @endif
                    </div><div class="col-sm-4">
                        {{ $model->lgt_mod_name }}
                    </div><div class="col-sm-2">
                        {{ $model->lgt_mod_tech }}
                    </div><div class="col-sm-2">
                    @if (isset($model->lgt_mod_wattage) 
                        && intVal($model->lgt_mod_wattage) > 0)
                        {{ $model->lgt_mod_wattage }}W
                    @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="pT20 slGrey">No lighting models found</div>
        @endforelse
</div>

<div class="slCard nodeWrap">
    <h4>Add/Update Lighting Models</h4>
    <p>One per line, tab-separated, with: Manufacturer Model Type Watts</p>
    <textarea name="addModels" 
        class="form-control w100 mB20"></textarea>
    <input type="submit" value="Add All" 
        class="nFormNext btn btn-primary btn-lg">
</div>
