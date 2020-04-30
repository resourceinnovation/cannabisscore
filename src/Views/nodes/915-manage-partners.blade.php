<!-- resources/views/vendor/cannabisscore/nodes/915-manage-partners.blade.php -->

<div class="slCard nodeWrap">

    <div class="float-right">
        <a href="?refresh=1" class="btn btn-secondary btn-sm"
            ><i class="fa fa-refresh" aria-hidden="true"></i></a>
    </div>
    <h2 class="mT0 slBlueDark">Invite & Manage Partners</h2>
    <p>
        If you want to invite a new partner, just put their 
        email into this form and click the button to Add them. 
        Then let them know to sign in (draft sample email below).
        For happy software, each record here should be linked to 
        <b>an Invite Email Address or a User Account</b>.
        Expiration counts the days after the invitee's first login.
    </p>
    <div class="pT15"></div>

    <form name="addPartnerManu" method="post" action="?addPartnerManu=1">
    <input type="hidden" name="addPartnerManu" value="1">
    <input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
        <div class="row mB30">
            <div class="col-4">
                <p><b class="slBlueDark">Company Name</b></p>
                <input name="partnerCompanyName" id="partnerCompanyNameID" 
                    autocomplete="off" value="" class="form-control w100">
            </div>
            <div class="col-4">
                <p><b class="slBlueDark">Invite Email Address</b></p>
                <input name="partnerInviteEmail" value="" 
                    autocomplete="off" class="form-control w100">
            </div>
            <div class="col-4">
                <p><b class="slBlueDark">User Account</b></p>
                <select name="partnerUser" 
                    onChange="return getPrtnCompany(this.value)"
                    class="form-control w100" autocomplete="off">
                    <option value="" SELECTED >Select partner user account</option>
                @forelse ($GLOBALS["SL"]->x["partners"] as $partner)
                    @if (isset($partner->name))
                        <option value="{{ $partner->id }}"
                            >{{ $partner->name }} - {{ $partner->email }}</option>
                    @endif
                @empty
                @endforelse
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <p><b class="slBlueDark">Manufacturers</b></p>
                <select name="partnerManu" autocomplete="off" 
                    class="form-control w100 mB10">
                    <option value="" SELECTED >Select lighting manufacturer</option>
                @forelse ($manufacts as $mID => $manu)
                    <option value="{{ $mID }}">{{ $manu }}</option>
                @empty
                @endforelse
                </select>
            </div>
            <div class="col-4">
                <p><b class="slBlueDark">Trial Level</b></p>
                <select name="partnerLevel" 
                    class="form-control w100" autocomplete="off">
                @forelse ($GLOBALS["SL"]->def->getSet('Partner Levels') as $i => $lev)
                    <option value="{{ $lev->def_id }}" @if ($i == 2) SELECTED @endif
                        >{{ $lev->def_value }}</option>
                @empty
                @endforelse
                </select>
            </div>
            <div class="col-2">
                <p><b class="slBlueDark">Expires In</b></p>
                <select name="partnerExpire" 
                    class="form-control w100" autocomplete="off">
                    <option value="0">Never</option>
                @for ($i = 1; $i < 366; $i++)
                    <option value="{{ $i }}" @if ($i == 30) SELECTED @endif
                        >{{ $i }} @if ($i > 1) days @else day @endif </option>
                @endfor
                </select>
            </div>
            <div class="col-2 pT30 taC">
                <input type="submit" value="Add Partner" 
                    class="btn btn-primary btn-lg">
            </div>
        </div>
    </form>

</div>
<div class="slCard nodeWrap">

    <h3 class="mT0">Invited Partner Users</h3>
    <div class="row brdBot">
        <div class="col-2"><p class="slBlueDark">Company Name</p></div>
        <div class="col-2"><p class="slBlueDark">Manufacturers</p></div>
        <div class="col-2"><p class="slBlueDark">User Account</p></div>
        <div class="col-2"><p class="slBlueDark">Invite Email Address</p></div>
        <div class="col-2"><p class="slBlueDark">Trial Level</p></div>
        <div class="col-2"><p class="slBlueDark">Expires In</p></div>
    </div>

@forelse ($GLOBALS["SL"]->x["partners"] as $cnt => $partner)
    <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-2">
            @if (isset($partner->company) && trim($partner->company) != '')
                <b>{{ $partner->company }}</b>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->manufacturers)
                && sizeof($partner->manufacturers) > 0)
                @if (sizeof($partner->manufacturers) == 1
                    && isset($partner->manufacturers[0]->manu_name)
                    && strtolower($partner->manufacturers[0]->manu_name) 
                        != strtolower($partner->company))
                    <a href="/dash/competitive-performance?manu={{
                        urlencode($partner->manufacturers[0]->manu_name) 
                        }}">{{ $partner->manufacturers[0]->manu_name 
                        }}</a>
                @else
                    @foreach ($partner->manufacturers as $m => $manu)
                        @if ($m > 0) , @endif
                        <a href="/dash/competitive-performance?manu={{
                            urlencode($manu->manu_name) }}">{{ 
                            $manu->manu_name }}</a>
                    @endforeach
                @endif
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->name) && trim($partner->name) != '')
                <a href="/profile/{{ urlencode($partner->name) }}"
                    >{{ $partner->name }}</a>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->email) && trim($partner->email) != '')
                <a href="mailto:{{ $partner->email }}">{{ 
                    $GLOBALS["SL"]->charLimitDotDotDot($partner->email, 18) 
                    }}</a>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->levelDef) && intVal($partner->levelDef) > 0)
                {{ $GLOBALS["SL"]->def->getVal('Partner Levels', $partner->levelDef) }}
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->expiration) && intVal($partner->expiration) > 0)
                {{ $partner->expiration }} days
            @else
                <i class="slGrey">never</i>
            @endif
            </div>
        </div>
    </div>
@empty
    <div class="w100 pT30 pB10">
        <p><i>At the moment, no partner invitations have 
        converted into full user accounts.</i></p>
    </div>
@endforelse

</div>
<div class="slCard nodeWrap">

    <h3 class="mT0">Pending Partner Invitations</h3>
    <div class="row brdBot">
        <div class="col-3"><p class="slBlueDark">Company Name</p></div>
        <div class="col-2"><p class="slBlueDark">Manufacturers</p></div>
        <div class="col-3"><p class="slBlueDark">Invite Email Address</p></div>
        <div class="col-2"><p class="slBlueDark">Trial Level</p></div>
        <div class="col-2"><p class="slBlueDark">Expires In</p></div>
    </div>

@forelse ($GLOBALS["SL"]->x["partnerInvites"] as $cnt => $partner)
    <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-3">
            @if (isset($partner->usr_company_name) 
                && trim($partner->usr_company_name) != '')
                <b>{{ $partner->usr_company_name }}</b>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2 slGrey">
            @if (isset($partner->usr_manu_ids) 
                && trim($partner->usr_manu_ids) != '')
                {{ $partner->usr_manu_ids }}
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-3">
            @if (isset($partner->usr_invite_email) 
                && trim($partner->usr_invite_email) != '')
                <a href="mailto:{{ $partner->usr_invite_email }}">{{ 
                    $GLOBALS["SL"]->charLimitDotDotDot($partner->usr_invite_email, 32)
                    }}</a>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->usr_level) && intVal($partner->usr_level) > 0)
                {{ str_replace(': Basic Tracking', '', 
                    str_replace(': Full Data Access', '', 
                    $GLOBALS["SL"]->def->getVal('Partner Levels', $partner->usr_level)
                )) }}
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->usr_membership_expiration) 
                && intVal($partner->usr_membership_expiration) > 0)
                {{ $partner->usr_membership_expiration }} days
            @else
                <i class="slGrey">never</i>
            @endif
            </div>
        </div>
    </div>
@empty
    <div class="w100 pT30 pB10">
        <p><i>At the moment, all partner invitations have converted
        into full user accounts listed above.</i></p>
    </div>
@endforelse

</div>
<div class="slCard nodeWrap">

    <h3 class="mT0">Sample Invitation Email</h3>
    <p>Hi Jordan,</p>
    <p>
        We have just updated our system giving you access to a free trial of the
        Cannabis PowerScore's new partner tools! All you need to do is <b>create a 
        user account linked with this email address</b> I have for you:
    </p>
    <p>
        <a href="https://powerscore.resourceinnovation.org/register" target="_blank"
            >https://powerscore.resourceinnovation.org/register</a>
    </p>
    <p>
        From there, you can finish setting up your partner account. 
        Please let us know if you have any questions. Thanks so much!
    </p>
    <p>Sincerely,</p>
    <p>This One Shouldn't Be Auto-Emailed</p>

    <?php /*
    <pre>{!! print_r($GLOBALS["SL"]->x["partners"]) !!}</pre>
    */ ?>

</div>

<script type="text/javasript">
var partners = new Array();
@foreach ($GLOBALS["SL"]->x["partners"] as $p => $partner)
    partners[{{ $partner->id }}] = "{{ $partner->company }}";
@endforeach
function getPrtnCompany(partnerID) {
    var company = "";
    if (partners[partnerID]) company = partners[partnerID];
    if (document.getElementById("partnerCompanyNameID")) {
        document.getElementById("partnerCompanyNameID").value=company;
    }
    return true;
}
</script>