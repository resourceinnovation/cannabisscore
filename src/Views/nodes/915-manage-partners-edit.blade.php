<!-- resources/views/vendor/cannabisscore/nodes/915-manage-partners-edit.blade.php -->


<div class="slCard nodeWrap">

    <div class="float-right">
        <a @if ($isEditing) href="?add=1" class="btn btn-secondary"
            @else href="javascript:;" id="hidivBtnAddPartner" 
                class="btn btn-secondary hidivBtn"
            @endif >Create New Partner</a>
    <?php /* <a href="?refresh=1" class="btn btn-secondary btn-sm"
            ><i class="fa fa-refresh" aria-hidden="true"></i></a> */ ?>
    </div>
    <h2 class="mT0 slBlueDark">Invite & Manage Partners</h2>
    
    <div id="hidivAddPartner" class=" 
        @if ($isEditing || $GLOBALS['SL']->REQ->has('add')) disBlo 
        @else disNon 
        @endif ">

    @if ($isEditing)
        <hr>
        <h3 class="mB0">Editing Partner: {{ $editPartner->name }}</h3>
        <h5 class="mT0">{{ $editPartner->email }}</h5>
        <p>
            All partner users should be associated with a Company record.
            Companies are collections of data accessible by multiple users.
            Expiration will count the days after the invitee's 
            first login (to finally be enforced soon).
        </p>
    @else
        <hr>
        <h3>Create New Partner</h3>
        <p>
            All partner users should be associated with a Company record.
            Companies are collections of data accessible by multiple users.
            If you want to invite a new partner, just put their 
            email into this form and click the button to Add them. 
            Then let them know to sign in (draft sample email below).
            Expiration will count the days after the invitee's 
            first login (to finally be enforced soon).
        </p>
    @endif
        <div class="pT15"></div>

        <form name="addPartnerManu" method="post" action="?addPartnerManu=1">
    @if ($isEditing)
        <input type="hidden" name="edit" value="{{ $editPartner->usrInfoID }}">
        <input type="hidden" name="save" value="1">
    @else
        <input type="hidden" name="addPartnerManu" value="1">
    @endif
        <input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="col-lg-6">

        @if (!$isEditing)
                <p><b class="slBlueDark">User Account</b></p>
                <select name="partnerUser" id="partnerUserID"
                    class="form-control mB30" autocomplete="off">
                    <option value="0" @if (!$isEditing) SELECTED @endif
                        >New User Invitation (Enter Email To Invite)</option>
                    <option value="" DISABLED ></option>
                    <option value="" DISABLED 
                        >----- OR Select An Existing User Account...-----</option>
            @forelse ($GLOBALS["SL"]->x["partners"] as $partner)
                @if (isset($partner->name))
                    <option value="{{ $partner->usrInfoID }}"
                        @if ($isEditing 
                            && $editPartner->usrInfoID == $partner->usrInfoID)
                            SELECTED
                        @endif
                        >{{ $partner->name }} - {{ $partner->email }}</option>
                @endif
            @empty
            @endforelse
            <?php /*
                    <option value="" DISABLED ></option>
                    <option value="" DISABLED 
                        >----- OR Select An Existing Invitation...-----</option>
            @forelse ($GLOBALS["SL"]->x["partnerInvites"] as $cnt => $partner)
                @if (isset($partner->usrInfoID)
                    && isset($partner->email))
                    <option value="{{ $partner->usrInfoID }}"
                        @if ($isEditing 
                            && $editPartner->usrInfoID == $partner->usrInfoID)
                            SELECTED
                        @endif
                        >
                        {{ $partner->email }}
                    </option>
                @endif
            @empty
            @endforelse
            */ ?>
                </select>
        @endif
        @if (isset($editPartner->id) && $editPartner->id > 0)
            </div>
            <div class="col-lg-6">
        @endif
        @if (!$isEditing || $editPartner->id <= 0)
                <div id="inviteEmailAddy" class="disBlo pB30">
                    <p><b class="slBlueDark">Invite Email Address</b></p>
                    <input name="partnerInviteEmail" 
                        autocomplete="off" class="form-control"
                    @if ($isEditing && isset($editPartner->email))
                        value="{{ $editPartner->email }}"
                    @else
                        value="" 
                    @endif >
                </div>
        @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <p><b class="slBlueDark">Company / Organization</b></p>
                <select name="partnerCompany" id="partnerCompanyID"
                    class="form-control" autocomplete="off">
                    <option value="" @if (!$isEditing) SELECTED @endif
                        >Select Company (Optional)</option>
                @forelse ($GLOBALS["SL"]->x["allCompanies"] as $com)
                    <option value="{{ $com->usr_com_id }}"
                        @if (in_array($com->usr_com_id, $editPartner->companyIDs)) SELECTED @endif
                        >{{ $com->usr_com_name }}</option>
                @empty
                @endforelse
                    <option value="" DISABLED ></option>
                    <option value="0" @if (!$isEditing) SELECTED @endif
                        >New Company / Organization</option>
                </select>
            </div>
            <div class="col-lg-6">
                <div id="partnerCompanyWrap" class="disNon">
                    <p><b class="slBlueDark">New Company Name</b></p>
                    <input name="partnerCompanyName" id="partnerCompanyNameID" 
                        autocomplete="off" class="form-control mB15" value="" >
                    <p>
                        <b class="slBlueDark">With Referral Link ID</b>
                        <i class="mL10">/start-for-{link}</i>
                    </p>
                    <input name="partnerCompanySlug" id="partnerCompanySlugID" 
                        autocomplete="off" class="form-control" value="" >
                    <div id="slugSearchResult" class="w100"></div>
                </div>
            </div>
        </div>

        <div class="row mT30">
            <div class="col-lg-6">
                <p><b class="slBlueDark">Trial Level</b></p>
                <select name="partnerLevel" class="form-control" autocomplete="off">
                @forelse ($GLOBALS["SL"]->def->getSet('Partner Levels') as $i => $lev)
                    <option value="{{ $lev->def_id }}" 
                    @if ($isEditing)
                        @if ($editPartner->levelDef == $lev->def_id) SELECTED @endif
                    @else
                        @if ($i == 2) SELECTED @endif
                    @endif >{{ $lev->def_value }}</option>
                @empty
                @endforelse
                </select>
            </div>
            <div class="col-lg-6">
                <p><b class="slBlueDark">Expires In</b></p>
                <select name="partnerExpire" class="form-control" autocomplete="off">
                    <option value="0">Never</option>
                @for ($i = 1; $i < 366; $i++)
                    <option value="{{ $i }}" 
                    @if ((!$isEditing && $i == 30)
                        || ($isEditing && $i == $editPartner->expiration))
                        SELECTED
                    @endif >{{ $i }} @if ($i > 1) days @else day @endif </option>
                @endfor
                </select>
            </div>
        </div>
                
        <div class="row mT30">
            <div class="col-lg-6">
                <input type="submit" id="addPartnerBtn"
                    class="btn btn-primary btn-lg btn-block"
                    @if ($GLOBALS['SL']->REQ->has('add')) value="Add Partner" 
                    @else value="Save Changes"
                    @endif >
            </div>
        </div>
        </form>

    </div>

</div>

