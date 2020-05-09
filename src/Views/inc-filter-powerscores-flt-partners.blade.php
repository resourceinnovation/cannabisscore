<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-partners.blade.php -->

@if (!isset($GLOBALS["SL"]->x["officialSet"])
    || !$GLOBALS["SL"]->x["officialSet"])
    @if (Auth::user() && Auth::user()->hasRole('administrator|staff'))
        <div class="col-md-4 pB10"> 
            <select name="fltPartner" id="fltPartnerID" 
                class="form-control psChageFilter ntrStp slTab"
                autocomplete="off" {!! $GLOBALS["SL"]->tabInd() !!} >
                <option value="0"
                    @if (!isset($fltPartner) 
                        || in_array(trim($fltPartner), ["", "0"])) 
                        SELECTED
                    @endif >All Partners</option>
            @forelse ($usrCompanies as $c => $company)
                <option value="{{ $company->usr_user_id }}" 
                    @if (isset($fltPartner) 
                        && trim($fltPartner) == trim($company->usr_user_id))
                        SELECTED
                    @endif >{{ $company->usr_company_name }}</option>
            @empty
            @endforelse
            </select>
        </div>
    @elseif (Auth::user() && Auth::user()->hasRole('partner'))
        <input name="fltPartner" id="fltPartnerID" DISABLED 
            type="hidden" value="{{ $GLOBALS['SL']->x['partnerID'] }}">
    @endif
@endif