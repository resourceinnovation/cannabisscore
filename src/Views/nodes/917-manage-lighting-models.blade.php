<!-- resources/views/vendor/cannabisscore/nodes/917-manage-lighting-models.blade.php -->
<div class="slCard nodeWrap">
    <a href="?modelSrch="><h2>All Lighting Models</h2></a>

    <div class="row">
        <div class="col-8">
            <input type="text" class="form-control" 
                name="modelSrch" id="modelSrchID"
                @if ($GLOBALS['SL']->REQ->has('modelSrch'))
                    value="{{ trim($GLOBALS['SL']->REQ->modelSrch) }}"
                @endif >
        </div>
        <div class="col-4">
            <a id="modelSrchBtn" href="javascript:;"
                class="btn btn-secondary btn-block"
                onClick="window.location='?modelSrch='+document.getElementById('modelSrchID').value; return false;">
                <i class="fa fa-search mR3" aria-hidden="true"></i> Search</a>
        </div>
    </div>

    <div class="pT15 pB30">
    {!! $GLOBALS["SL"]->printAccordian(
        'Add/Update Lighting Models',
        '<form name="companyName" action="?add=1" method="post">
        <input type="hidden" name="_token" value="' . csrf_token() . '">
        <p>One per line, tab-separated, with: Manufacturer Model Type Watts</p>
        <div class="row"><div class="col-8">
        <textarea name="addModels" class="form-control w100 mB20"></textarea>
        </div><div class="col-4">
        <input type="submit" value="Add All" autocomplete="off" 
            class="btn btn-primary btn-block">
        </div></div></form>',
        false,
        false,
        'text'
    ) !!}
    </div>

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
