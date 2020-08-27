<!-- resources/views/vendor/cannabisscore/nodes/1560-company-edit.blade.php -->


<div class="slCard nodeWrap">

    <div class="float-right">
        <a @if ($isEditing) href="?add=1" class="btn btn-secondary"
            @else href="javascript:;" id="hidivBtnAddCom" 
                class="btn btn-secondary hidivBtn"
            @endif >Create New Company</a>
    <?php /* <a href="?refresh=1" class="btn btn-secondary btn-sm"
            ><i class="fa fa-refresh" aria-hidden="true"></i></a> */ ?>
    </div>
    <h2 class="mT0 slBlueDark">Manage Partner Companies</h2>
    
    <div id="hidivAddCom" class=" 
        @if ($isEditing || $GLOBALS['SL']->REQ->has('add')) disBlo 
        @else disNon 
        @endif ">

    @if ($isEditing)
        <hr>
        <h3 class="mB0">Editing Company: {{ $editPartner->name }}</h3>
    @else
        <hr>
        <h3>Create New Company</h3>
    @endif
        <p>
            These company records track collections 
            of data accessible by multiple users.
            Companies linked to lighting <b>manufacturers</b> track
            sub-sets of the ranked data set using their product.
            A Company can track sub-sets of its overall data collection,
            grouping them by <b>facility</b>.
        </p>
        <div class="pT15"></div>

        <form name="addPartnerCom" method="post" action="?save=1">
        <input type="hidden" name="save" value="1">
    @if ($isEditing)
        <input type="hidden" name="edit" id="companyID" value="{{ $editPartner->id }}">
    @else
        <input type="hidden" name="addPartnerCom" value="1">
    @endif
        <input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="col-lg-5">

                <p><b class="slBlueDark">New Company Name</b></p>
                <input name="partnerCompanyName" id="partnerCompanyNameID" 
                    autocomplete="off" class="form-control mB30" 
                    value="{{ $editPartner->name }}" >

                <p>
                    <b class="slBlueDark">With Referral Link ID</b>
                    <i class="mL10">/start-for-{link}</i>
                </p>
                <input name="partnerCompanySlug" id="partnerCompanySlugID" 
                    autocomplete="off" class="form-control" 
                    value="{{ $editPartner->slug }}" >
                <div id="slugSearchResult" class="w100 mB30"></div>

                <p><b class="slBlueDark">Manufacturers</b></p>
                <select name="partnerManu" autocomplete="off" class="form-control">
                    <option value="" 
                    @if (!$isEditing || sizeof($editPartner->manus) == 0)
                        SELECTED
                    @endif >Select Manufacturer (Optional)</option>
                    <option value="" DISABLED >--- Lighting Manufacturers ---</option>
                @forelse ($manufacts as $mID => $manu)
                    <option value="{{ $mID }}"
                    @if ($isEditing && $editPartner->hasManuID($mID))
                        SELECTED
                    @endif >{{ $manu }}</option>
                @empty
                @endforelse
                </select>

                <input type="submit" id="addPartnerBtn"
                    class="btn btn-primary btn-lg btn-block mT30"
                    @if ($GLOBALS['SL']->REQ->has('add')) value="Add Company" 
                    @else value="Save Changes"
                    @endif >

            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-6">
                {!! view(
                    'vendor.cannabisscore.nodes.1560-edit-facility-list',
                    [
                        "editPartner" => $editPartner,
                        "facLimit"    => $facLimit
                    ]
                )->render() !!}
            </div>
        </div>

        </form>

    </div>

</div>

