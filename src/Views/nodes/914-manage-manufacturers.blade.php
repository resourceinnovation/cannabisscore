<!-- resources/views/vendor/cannabisscore/nodes/914-manage-manufacturers.blade.php -->

<div class="row">
    <div class="col-md-8">

        <div class="slCard greenline nodeWrap">
            {!! view(
                'vendor.cannabisscore.nodes.914-manage-manufacturers-adoption', 
                [ 'manus' => $manus ]
            )->render() !!}
        </div>

        <div class="slCard greenline nodeWrap">
            <h2>All Manufacturers</h2>
            <p>
                Of lighting and/or HVAC 
                equipment used by growers.
            </p>
            <div class="row">
                <div class="col-sm-6">
                    @forelse ($manus as $i => $manu)
                        {{ $manu->manu_name }}<br />
                        @if ($i == ceil(sizeof($manus)/2)) 
                            </div><div class="col-sm-6"> 
                        @endif
                    @empty 
                        <div class="pT20 slGrey">
                            No manufacturers found
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-4">

        <div class="slCard greenline nodeWrap">
            <h4>Lighting Partners</h4>
            @forelse ($partners as $partner)
                @if (isset($partner->manufacturers)
                    && sizeof($partner->manufacturers) > 0)
                    <p><hr></p>
                    @if (isset($partner->company) 
                        && trim($partner->company) != '')
                        <h5 class="mB0">{{ $partner->company }}</h5>
                    @endif
                    @if (sizeof($partner->manufacturers) == 1
                        && isset($partner->manufacturers[0]->manu_name)
                        && strtolower($partner->manufacturers[0]->manu_name) 
                            != strtolower($partner->company))
                        <a href="/dash/competitive-performance?manu={{
                            urlencode($partner->manufacturers[0]->manu_name) 
                            }}">{{ $partner->manufacturers[0]->manu_name 
                            }}</a><br />
                    @else
                        @foreach ($partner->manufacturers as $manu)
                            <a href="/dash/competitive-performance?manu={{
                                urlencode($manu->manu_name) }}">{{ 
                                $manu->manu_name }}</a><br />
                        @endforeach
                    @endif
                    @if (isset($partner->name) 
                        && trim($partner->name) != '')
                        User: <a href="/profile/{{ urlencode($partner->name) }}"
                            >{{ $partner->name }}</a><br />
                    @endif
                    @if (isset($partner->email) 
                        && trim($partner->email) != '')
                        <div class="mTn5">
                            <a href="mailto:{{ $partner->email }}"
                                class="fPerc80">{{ str_replace('@', ' @', 
                                $partner->email) }}</a>
                        </div>
                    @endif
                @endif
            @empty
            @endforelse

            <p><hr></p>
            <p><b>Add New Partner:</b></p>
            <form name="addPartnerManu" method="post" 
                action="?addPartnerManu=1">
            <input type="hidden" id="csrfTok" 
                name="_token" value="{{ csrf_token() }}">
            <select name="partnerUser" autocomplete="off" 
                class="form-control w100 mB10" 
                onChange="return getPrtnCompany(this.value)">
                <option value="" SELECTED >Select partner user account</option>
            @forelse ($partners as $partner)
                @if (isset($partner->name))
                    <option value="{{ $partner->id }}"
                        >{{ $partner->name }} - {{ $partner->email }}</option>
                @endif
            @empty
            @endforelse
            </select>
            <div>Company Name:</div>
            <input name="partnerCompanyName" id="partnerCompanyNameID" 
                autocomplete="off" value="" 
                class="form-control w100 mB10">
            <div>Grant Data Access:</div>
            <select name="partnerManu" autocomplete="off" 
                class="form-control w100 mB10">
                <option value="" SELECTED >Select lighting manufacturer</option>
            @forelse ($manufacts as $mID => $manu)
                <option value="{{ $mID }}">{{ $manu }}</option>
            @empty
            @endforelse
            </select>
            <input type="submit" value="Add Permissions"
                class="btn btn-primary">
            </form>
            <?php /*
            <pre>{!! print_r($partners) !!}</pre>
            */ ?>
        </div>
        <div class="slCard greenline nodeWrap">
            <h4>Add Manufacturers</h4>
            <p>One per line.</p>
            <textarea class="form-control w100 mB20"
                name="addManu" ></textarea>
            <input type="submit" value="Add All" 
                class="nFormNext btn btn-primary btn-lg">
        </div>

    </div>
</div>

<script type="text/javasript">
var partners = new Array();
@foreach ($partners as $p => $partner)
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