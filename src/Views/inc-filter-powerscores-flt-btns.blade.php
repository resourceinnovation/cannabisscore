<!-- generated from resources/views/vendor/cannabisscore/inc-filter-powerscores-flt-btns.blade.php -->

<a id="updateScoreFiltsBtn2" href="javascript:;"
    class="btn btn-primary btn-block updateScoreFilts"
    >Apply Filters</a>

@if (!isset($GLOBALS["SL"]->x["partnerVersion"])
    || !$GLOBALS["SL"]->x["partnerVersion"]
    || $GLOBALS["SL"]->x["partnerLevel"] >= 4)
    <div class="mT10">
        <a id="hidivBtnFiltsAdv" class="hidivBtn" href="javascript:;"
            ><i class="fa fa-cogs"></i></a>
    </div>
@endif
