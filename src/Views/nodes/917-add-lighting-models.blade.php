<!-- resources/views/vendor/cannabisscore/nodes/917-add-lighting-models.blade.php -->
</form>

@if (trim($importResults) != '')
    <div class="slCard">
        <h2 class="slBlueDark">
            <i class="fa fa-upload" aria-hidden="true"></i> 
            Lighting Speadsheet Uploaded
        </h2>
        {!! $importResults !!}
    </div>
    <div class="p30"></div>
@endif

<div class="slCard nodeWrap">
    <a class="pull-right btn btn-secondary" href="?" 
        ><i class="fa fa-angle-left mR3" aria-hidden="true"></i> 
        Model List</a>
    <a href="?add=1"><h2>Add/Update Lighting Models</h2></a>

    <h4>Import From Excel</h4>

    <form method="post" name="modelImportExcel" 
        action="/dash/manage-lighting-models?add=1&import=excel" 
        enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="add" value="1">
    <input type="hidden" name="import" value="excel">

    <div class="row">
        <div class="col-5 pB30">
            <label id="importTypeNonelab" class="fingerAct">
                <div class="disIn mR5">
                    <input type="radio" value="None" CHECKED
                        id="importTypeNone" name="importType" 
                        class="updateImportType slTab ntrStp" 
                        autocomplete="off">
                </div>
                General list of lighting models
            </label>
            <label id="importTypeDLClab" class="finger">
                <div class="disIn mR5">
                    <input type="radio" value="DLC" 
                        id="importTypeDLC" name="importType" 
                        class="updateImportType slTab ntrStp" 
                        autocomplete="off">
                </div>
                DLC QPL
            </label>
        </div>
        <div class="col-1 pB30"></div>
        <div class="col-6 pB30">
            <div id="importDescNone" class="disBlo">
                <p>
                    First row should contain column headers.
                    Columns should be in this order:
                </p>
                <ul>
                    <li>Make/Brand</li>
                    <li>Model</li>
                    <li>Type (e.g. LED)</li>
                    <li>Watts</li>
                </ul>
            </div>
            <div id="importDescDLC" class="disNon">
                <ol>
                    <li><a href="https://www.designlights.org/lighting-controls/download-the-qpl/"
                        target="_blank">Download the DLC QPL</a>.</li>
                    <li>Upload that file below:</li>
                </ol>
            </div>
        </div>
    </div>

    <input type="file" name="importExcel" id="importExcelID"
        class="form-control form-control-lg" autocomplete="off"
        {!! $GLOBALS["SL"]->tabInd() !!}>
    <input type="submit" value="Upload Excel" autocomplete="off" 
        class="btn btn-primary btn-lg mT15">
    </form>

    <div class="p30"></div>
    <hr>
    <hr>
    <div class="p30"></div>

    <h4>Tab-Seaparated Text Import</h4>
    <p>One per line, tab-separated, with: Manufacturer/Make/Brand, Model, Type, Watts</p>

    <form name="companyName" action="?add=1" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <textarea name="addModels" class="form-control" 
        style="height: 400px;"></textarea>
    <input type="submit" value="Add All" autocomplete="off" 
        class="btn btn-primary btn-lg mT15">
    </form>

</div>

<form>

<script type="text/javascript"> $(document).ready(function(){

function updateImportType() {
    var typeImport = "";
    if (document.getElementById("importTypeNone") && document.getElementById("importTypeNone").checked) {
        typeImport = "None";
    } else if (document.getElementById("importTypeDLC") && document.getElementById("importTypeDLC").checked) {
        typeImport = "DLC";
    }
    if (typeImport == "DLC") {
        document.getElementById("importTypeNonelab").className="finger";
        document.getElementById("importTypeDLClab").className="fingerAct";
        $("#importDescNone").slideUp(150);
        $("#importDescDLC").slideDown(150);
    } else {
        document.getElementById("importTypeNonelab").className="fingerAct";
        document.getElementById("importTypeDLClab").className="finger";
        $("#importDescNone").slideDown(150);
        $("#importDescDLC").slideUp(150);
    }
}
$(document).on("click", ".updateImportType", function() { updateImportType(); });


}); </script>