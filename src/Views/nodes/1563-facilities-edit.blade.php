<!-- resources/views/vendor/cannabisscore/nodes/1563-facilities-edit.blade.php -->

<form name="editFacilities" method="post" action="?save=1">
<input type="hidden" name="save" value="1">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">

<div class="row">
    <div class="col-lg-6">
        {!! view(
            'vendor.cannabisscore.nodes.1560-edit-facility-list',
            [
                "editPartner" => $editPartner,
                "facLimit"    => $facLimit
            ]
        )->render() !!}
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-5">
        <input type="submit" id="addPartnerBtn" value="Save Changes"
            class="btn btn-primary btn-lg btn-block mT30">
    </div>
</div>

</form>
