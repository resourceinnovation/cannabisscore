<!-- resources/views/vendor/cannabisscore/nodes/917-manage-lighting-models.blade.php -->

<div class="slCard nodeWrap">
    <a class="pull-right btn btn-secondary" href="?add=1" 
        ><i class="fa fa-plus mR3" aria-hidden="true"></i>
        Add Models</a>
    <a href="?modelSrch="><h2>All Lighting Models</h2></a>

    <div class="row mT30 mB30">
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

<?php $prev = $curr = ''; $cnt = 0; ?>
@forelse ($models as $i => $model)
    <?php $curr = ((isset($manufacts[$model->lgt_mod_manu_id]))
        ? trim($manufacts[$model->lgt_mod_manu_id]) : ''); ?>
    @if ($curr != $prev)
        <div class="mT15 pL15 pR15 pT5 pB5">
            <h5 class="m0">{{ $curr }}</h5>
        </div>
        <?php $prev = $curr; $cnt = 0; ?>
    @endif
    <?php $cnt++; ?>
    <div class="mT5 pL15 pR15 pT5 pB5 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-sm-4 pT5 pB5">
                {{ $model->lgt_mod_name }}
            </div>
            <div class="col-sm-2 pT5 pB5">
            @if (isset($model->lgt_mod_wattage) 
                && intVal($model->lgt_mod_wattage) > 0)
                {{ $model->lgt_mod_wattage }}W
            @endif
            </div>
            <div class="col-sm-2">
                <select name="lgtType{{ $model->lgt_mod_id }}"
                    id="lgtType{{ $model->lgt_mod_id }}ID"
                    data-lgt-id="{{ $model->lgt_mod_id }}"
                    class="form-control form-control-sm changeLgtType">
                    <option value="0"
                    @if (!isset($model->lgt_mod_type) 
                        || intVal($model->lgt_mod_type) == 0) SELECTED
                    @endif >no type linked</option>
                {!! $GLOBALS["SL"]->def->getSetDrop(
                    'PowerScore Light Types', 
                    $model->lgt_mod_type
                ) !!}
                </select>
            </div>
            <div class="col-sm-2 pT5 pB5">
                {{ $model->lgt_mod_tech }}
            </div>
            <div class="col-sm-2 pT5 pB5">
                <div id="lgtSave{{ $model->lgt_mod_id }}" class="pull-right"></div>
                <label>
                    <input type="checkbox" name="lgtDlc{{ $model->lgt_mod_id }}"
                        id="lgtDlc{{ $model->lgt_mod_id }}ID" value="dlc"
                        data-lgt-id="{{ $model->lgt_mod_id }}" class="clickLgtType"
                        @if (isset($model->lgt_mod_is_dlc)
                            && intVal($model->lgt_mod_is_dlc) == 1) CHECKED
                        @endif > DLC
                </label>
            </div>
        </div>
    </div>
@empty
    <div class="pT20 slGrey">No lighting models found</div>
@endforelse

</div>


<script type="text/javascript"> $(document).ready(function(){

function updateLgtType(lgtID) {
    var url = "/ajadm/adm-lgt-edit?lgt="+lgtID+"&type=";
    if (document.getElementById("lgtType"+lgtID+"ID")) {
        url += document.getElementById("lgtType"+lgtID+"ID").value;
    }
    if (document.getElementById("lgtDlc"+lgtID+"ID") && document.getElementById("lgtDlc"+lgtID+"ID").checked) {
        url += "&dlc=1";
    }
    console.log(url);
    $("#lgtSave"+lgtID+"").load(url);
}
$(document).on("change", ".changeLgtType", function() {
    var lgtID = $(this).attr("data-lgt-id");
    setTimeout(function() { updateLgtType(lgtID); }, 10);
});
$(document).on("click", ".clickLgtType", function() {
    var lgtID = $(this).attr("data-lgt-id");
    setTimeout(function() { updateLgtType(lgtID); }, 10);
});

}); </script>
