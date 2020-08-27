<!-- resources/views/vendor/cannabisscore/nodes/915-manage-partners.blade.php -->

<div class="slCard nodeWrap">

    <h3 class="mT0">Partner Users</h3>
    <p>
        Companies are collections of data submitted using one or more 
        custom referral links, and accessible to multiple users.
        Lighting manufacturer partners access reports of the 
        ranked data set, filtered for records using their product.
    </p>
    <div class="row brdBot">
        <div class="col-4"><p class="slBlueDark">User Account,<br />Email Address</p></div>
        <div class="col-3"><p class="slBlueDark">Company</p></div>
        <div class="col-2"><p class="slBlueDark">Trial Level</p></div>
        <div class="col-3"><p class="slBlueDark">Expiration Date,<br />First Login Date</p></div>
    </div>

@forelse ($GLOBALS["SL"]->x["partners"] as $cnt => $partner)
    <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-4">
            @if (isset($partner->name) && trim($partner->name) != '')
                <a href="/profile/{{ urlencode($partner->name) }}"
                    ><b>{{ $partner->name }}</b></a>
            @else
                <span class="slGrey">-</span>
            @endif
            @if (isset($partner->email) && trim($partner->email) != '')
                <br /><a href="mailto:{{ $partner->email }}" class="slGrey">{{ 
                    $GLOBALS["SL"]->charLimitDotDotDot($partner->email, 40) 
                    }}</a>
            @endif
            </div>
            <div class="col-3">
            @if (sizeof($partner->companies) > 0)
                {{ $partner->listCompanyNames() }}
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
            @if (!isset($partner->expiration) || intVal($partner->expiration) == 0)
                Never
            @else
                {{ date("n/j/y", $partner->expireTime) }}
                @if ($partner->isExpired)
                    <i class="slRedDark mL5">Expired</i>
                @endif
            @endif
            @if (isset($partner->trialStart) && trim($partner->trialStart) != '')
                <br /><span class="slGrey">{{ date("n/j/y", strtotime($partner->trialStart)) }}</span>
                @if (isset($partner->expiration) && intVal($partner->expiration) > 0)
                    <nobr>+ {{ $partner->expiration }} days</nobr>
                @endif
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
        <div class="col-5"><p class="slBlueDark">Invite Email Address</p></div>
        <div class="col-3"><p class="slBlueDark">Company</p></div>
        <div class="col-2"><p class="slBlueDark">Trial Level</p></div>
        <div class="col-2"><p class="slBlueDark">Expires In</p></div>
    </div>

@forelse ($GLOBALS["SL"]->x["partnerInvites"] as $cnt => $partner)
    <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-5">
            @if (isset($partner->email) && trim($partner->email) != '')
                <a href="mailto:{{ $partner->email }}">{{ 
                    $GLOBALS["SL"]->charLimitDotDotDot($partner->email, 40)
                }}</a>
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-3">
            @if (sizeof($partner->companies) > 0)
                {{ $partner->listCompanyNames() }}
            @else
                <span class="slGrey">-</span>
            @endif
            </div>
            <div class="col-2">
            @if (isset($partner->levelDef) && intVal($partner->levelDef) > 0)
                {{ str_replace(': Basic Tracking', '', 
                    str_replace(': Full Data Access', '', 
                    $GLOBALS["SL"]->def->getVal('Partner Levels', $partner->levelDef)
                )) }}
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
                <a href="?edit={{ $partner->usrInfoID }}" 
                    class="btn btn-secondary btn-sm"
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
        <a href="https://powerscore.resourceinnovation.org/register-pro" target="_blank"
            >https://powerscore.resourceinnovation.org/register-pro</a>
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
