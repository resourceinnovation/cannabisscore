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
        <h3>Editing Partner</h3>
        <p>
            Expiration will count the days after the invitee's 
            first login (to finally be enforced soon).
        </p>
    @else
        <hr>
        <h3>Create New Partner</h3>
        <p>
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
            <div class="row mB30">
                <div class="col-lg-6">

                    <p><b class="slBlueDark">User Account</b></p>
                    <select name="partnerUser" id="partnerUserID"
                        class="form-control" autocomplete="off">
                        <option value="0" @if (!$isEditing) SELECTED @endif
                            >New User Invitation (Enter Email To Invite)</option>
                        <option value="" DISABLED 
                            >----- OR Select An Existing User Account...-----</option>
                @forelse ($GLOBALS["SL"]->x["partners"] as $partner)
                    @if (isset($partner->name))
                        <option value="{{ $partner->id }}"
                            @if ($isEditing && $editPartner->id == $partner->id)
                                SELECTED
                            @endif
                            >{{ $partner->name }} - {{ $partner->email }}</option>
                    @endif
                @empty
                @endforelse
                    </select>

                    <div id="inviteEmailAddy" class="disBlo pT30">
                        <p><b class="slBlueDark">Invite Email Address</b></p>
                        <input name="partnerInviteEmail" 
                            autocomplete="off" class="form-control"
                        @if ($isEditing && isset($editPartner->email))
                            value="{{ $editPartner->email }}"
                        @else
                            value="" 
                        @endif >
                    </div>

                    <div class="pT30">
                        <p><b class="slBlueDark">Company Name</b></p>
                        <input name="partnerCompanyName" id="partnerCompanyNameID" 
                            autocomplete="off" class="form-control"
                        @if ($isEditing && isset($editPartner->company))
                            value="{{ $editPartner->company }}"
                        @else
                            value="" 
                        @endif >
                    </div>

                    <div class="pT30">
                        <p><b class="slBlueDark">Manufacturers</b></p>
                        <select name="partnerManu" autocomplete="off" class="form-control">
                            <option value="" 
                            @if (!$isEditing || sizeof($editPartner->manufacturers) == 0)
                                SELECTED
                            @endif >Select Manufacturer (Opitonal)</option>
                            <option value="" DISABLED >--- Lighting Manufacturers ---</option>
                        @forelse ($manufacts as $mID => $manu)
                            <option value="{{ $mID }}"
                            @if ($isEditing && sizeof($editPartner->manufacturers) > 0)
                                @foreach ($editPartner->manufacturers as $m)
                                    @if (isset($m->manu_id) && $m->manu_id == $mID)
                                        SELECTED
                                    @endif
                                @endforeach
                            @endif >{{ $manu }}</option>
                        @empty
                        @endforelse
                        </select>
                    </div>

                </div>
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
                    
                    <div class="pT30">
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
                    
                    <div class="pT30 mT30">
                        <input type="submit" class="btn btn-primary btn-lg btn-block"
                        @if ($GLOBALS['SL']->REQ->has('add')) value="Add Partner" 
                        @else value="Save Changes"
                        @endif >
                    </div>

                </div>
            </div>

        </form>

    </div>

</div>

