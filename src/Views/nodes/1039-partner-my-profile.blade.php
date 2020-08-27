<!-- resources/views/vendor/cannabisscore/nodes/1039-partner-my-profile.blade.php -->

<div class="slCard nodeWrap">
    <div class="row">
        <div class="col-lg-6 pT15 pB15">
            <a href="/dashboard" class="btn btn-xl btn-primary btn-block">
                @if ($company && $company != '') {{ $company }} @else Partner @endif
                Dashboard
            </a>
        </div>
        <div class="col-lg-6 pT15 pB15">
            <a class="btn btn-xl btn-primary btn-block"
                @if (isset($usrInfo->companies)
                    && sizeof($usrInfo->companies) > 0
                    && isset($usrInfo->companies[0]->slug)
                    && trim($usrInfo->companies[0]->slug) != '')
                    href="/start/calculator?go=pro&partner={{ $usrInfo->companies[0]->slug }}"
                @else
                    href="/go-pro"
                @endif >Start A Fresh PowerScore</a>
        </div>
    </div>
    <div class="pT15">
        {!! view('vendor.cannabisscore.inc-register-pro-license')->render() !!}
    </div>
</div>
