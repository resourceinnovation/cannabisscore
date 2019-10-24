<!-- generated from resources/views/vendor/cannabisscore/nodes/170-all-powerscores.blade.php -->
<div class="slCard nodeWrap">
<div class="row">
    <div class="col-8">
        <a href="?refresh=1"><h2 class="slBlueDark">
    @if (isset($GLOBALS["SL"]->x["partnerVersion"])
        && $GLOBALS["SL"]->x["partnerVersion"])
        @if (isset($usrInfo) && isset($usrInfo->company))
            {{ $usrInfo->company }}
        @else Largely Lumens, Inc. PowerScores
        @endif
    @else   
        @if ($nID == 808) NWPCC Data Import 
        @else Compare All PowerScores 
        @endif
    @endif
        </h2></a>
    <?php /* <pre>{!! print_r($usrInfo) !!}</pre> */ ?>
    </div><div class="col-4 taR"><div class="mTn10 pB10">
    @if (Auth::user()->hasRole('administrator|staff'))
        @if (!$GLOBALS["SL"]->REQ->has('review'))
            <a class="btn btn-secondary mT20 mR5" 
                href="/dash/compare-powerscores?review=1"
                >Under Review
            </a>
        @else 
            <a class="btn btn-secondary mT20 mR5" 
                href="/dash/compare-powerscores"
                >All Complete
            </a>
        @endif
        <a class="btn btn-secondary mT20 mR5" target="_blank"
            href="/dash/compare-powerscores?random=1" 
            >Get Random
        </a>
        <a class="btn btn-secondary mT20" 
            href="/dash/compare-powerscores?srt={{ $sort[0] 
            }}&srta={{ $sort[1] }}{{ $urlFlts }}&excel=1"
            ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> 
            Excel
        </a>
    @endif
    </div></div>
</div>
@if (isset($psFilters))
    @if (!$GLOBALS["SL"]->REQ->has('review')) 
        <div class="round20 row2 mB20 p15">
            {!! $psFilters !!}
        </div>
    @else <div></div>
    @endif
@elseif (isset($psFilter))
    <div class="mB5">
        <b class="mR20">{{ $allscores->count() }} Found</b>
        {!! $psFilter !!}
    </div>
@endif

{!! $allListings !!}

</div>

<style>
@if ($nID == 170) 
     #updateScoreFiltsBtn2, #updateScoreFiltsBtn3 { display: none; } 
@endif
</style>

@if (isset($reportExtras))
    <div class="slCard nodeWrap">{!! $reportExtras !!}</div>
@endif