<!-- resources/views/vendor/cannabisscore/nodes/915-manage-partners.blade.php -->

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
            <div class="col-1">
            @if (isset($partner->expiration) && intVal($partner->expiration) > 0)
                {{ $partner->expiration }} days
            @else
                <i class="slGrey">never</i>
            @endif
            </div>
            <div class="col-1">
                <a href="?edit={{ $partner->usrInfoID }}" class="btn btn-secondary btn-sm"
                    ><i class="fa fa-pencil" aria-hidden="true"></i></a>
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
            <div class="col-1">
            @if (isset($partner->usr_membership_expiration) 
                && intVal($partner->usr_membership_expiration) > 0)
                {{ $partner->usr_membership_expiration }} days
            @else
                <i class="slGrey">never</i>
            @endif
            </div>
            <div class="col-1">
                <a href="?edit={{ $partner->usrInfoID }}" class="btn btn-secondary btn-sm"
                    ><i class="fa fa-pencil" aria-hidden="true"></i></a>
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
