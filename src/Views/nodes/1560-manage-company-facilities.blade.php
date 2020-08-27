<!-- resources/views/vendor/cannabisscore/nodes/1560-manage-company-facilities.blade.php -->

<div class="slCard nodeWrap">

    <h2 class="mT0">All Companies & Facilities</h2>
    <div class="row brdBot">
        <div class="col-3"><p class="slBlueDark">
            Company Name,<br />Referral Link ID,<br />[Manufacturer]
        </p></div>
        <div class="col-3"><p class="slBlueDark">
            Users
        </p></div>
        <div class="col-2"><p class="slBlueDark">
            Facility Names
        </p></div>
        <div class="col-2"><p class="slBlueDark">
            Facility Referral Link IDs,<br />PowerScore Counts
        </p></div>
        <div class="col-2"><p class="slBlueDark">
            Company PowerScore Count
        </p></div>
    </div>

@forelse ($companies as $cnt => $com)
    <div class="w100 pT10 pB10 @if ($cnt%2 > 0) row2 @endif ">
        <div class="row">
            <div class="col-3">
                @if (isset($com->name)) <b class="slBlueDark">{{ $com->name }}</b> @endif
                @if (isset($com->slug)) 
                    <br /><a href="/start-for-{{ $com->slug }}" 
                        target="_blank">{{ $com->slug }}</a>
                @endif
                @if (sizeof($com->manus) > 0)
                    <br /><i class="fa fa-lightbulb-o mR5 slGreenDark" aria-hidden="true"></i>
                    {!! $com->listManufacturerNames() !!}
                @endif
            </div>
            <div class="col-3">
                <nobr>{!! $com->listUsers('</nobr><br /><nobr>') !!}</nobr>
            </div>
            <div class="col-2">
                @forelse ($com->facs as $i => $fac)
                    <nobr>{{ $fac->name }}</nobr><br />
                @empty
                    <span class="slGrey">-</span><br />
                @endforelse
            </div>
            <div class="col-2">
                @forelse ($com->facs as $i => $fac)
                    <nobr><a href="/start-for-{{ $fac->slug }}" 
                        target="_blank"
                        >{{ $fac->slug }}</a></nobr><br />
                @empty
                    <span class="slGrey">-</span><br />
                @endforelse
            </div>
            <div class="col-1">
                <div class="relDiv">
                    <div class="absDiv slGrey" style="left: -30px;">
                    @forelse ($com->facs as $i => $fac)
                        {{ number_format($fac->totScores) }}<br />
                    @empty
                        <span class="slGrey">-</span><br />
                    @endforelse
                    </div>
                </div>
                {!! number_format($com->totScores) !!}
            </div>
            <div class="col-1">
                <a href="?edit={{ $com->id }}" DISABLED
                    class="btn btn-secondary btn-sm"
                    ><i class="fa fa-pencil" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
@empty
    <div class="w100 pT30 pB10">
        <p><i>No companies yet.</i></p>
    </div>
@endforelse

</div>
