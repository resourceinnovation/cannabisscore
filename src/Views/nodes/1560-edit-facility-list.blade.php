<!-- resources/views/vendor/cannabisscore/nodes/1560-edit-facility-list.blade.php -->

<div class="row">
    <div class="col-1"></div>
    <div class="col-6">
        <p><b class="slBlueDark">Facility Name</b></p>
    </div>
    <div class="col-5">
        <p><b class="slBlueDark">Facility Referral Link ID</b></p>
    </div>
</div>
@for ($i = 0; $i < $facLimit; $i++) 
    <input name="facID{{ $i }}" id="facilityID{{ $i }}" type="hidden"
        value="{{ $editPartner->getFacID($i) }}" >
    <div class="row">
        <div class="col-1 taR pT10">{{ (1+$i) }}.</div>
        <div class="col-6">
            <input name="facName{{ $i }}" id="facName{{ $i }}ID" 
                autocomplete="off" class="form-control mB15" 
                value="{{ $editPartner->getFacName($i) }}" >
        </div>
        <div class="col-5">
            <input name="facSlug{{ $i }}" id="facSlug{{ $i }}ID" 
                autocomplete="off" class="form-control mB15" 
                value="{{ $editPartner->getFacSlug($i) }}" >
        </div>
    </div>
    <div id="facSlugSearch{{ $i }}" class="w100 mB30"></div>
@endfor
