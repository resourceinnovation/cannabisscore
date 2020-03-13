<!-- resources/views/vendor/cannabisscore/nodes/1039-partner-my-profile.blade.php -->

@if (!isset($company) 
    || $company == '' 
    || !isset($usrInfo->slug)
    || trim($usrInfo->slug) == '')
    <form name="companyName" action="?companyName=1" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="companyName" value="1">
    <div class="slCard nodeWrap">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mTn10">What is the name of your company?</h4>
                <input name="myProfileCompanyName" id="myProfileCompanyNameID" 
                    class="form-control form-control-lg"
                    type="text" onKeyUp=""
                    @if (isset($company)) value="{{ $company }}" @endif >
            </div>
        </div>
        <div class="row mT20">
            <div class="col-md-8">
                How should it appear in URLs?
                <input name="myProfileCompanySlug" id="myProfileCompanySlugID"
                    type="text" class="form-control form-control-lg mT10"
                    @if (isset($usrInfo->usr_referral_slug)) 
                        value="{{ $usrInfo->usr_referral_slug }}"
                    @endif >
            </div>
            <div class="col-md-4 taC pT30">
                <input type="submit" value="Save" class="btn btn-primary btn-lg">
            </div>
        </div>
    </div>
    </form>

    <script type="text/javascript"> $(document).ready(function() {
        function slugCompName() {
            var newVal = document.getElementById('myProfileCompanyNameID').value;
            document.getElementById('myProfileCompanySlugID').value=slugify(newVal);
        }
        $(document).on("keyup", "#myProfileCompanyNameID", function(e) { slugCompName(); });
        setTimeout(function() { slugCompName(); }, 10);
    }); </script>
@endif

<div class="slCard nodeWrap">
    <center><a href="/dashboard" class="btn btn-xl btn-primary">
        @if ($company && $company != '') {{ $company }} @else Partner @endif
        Dashboard</a>
    </center>
</div>
