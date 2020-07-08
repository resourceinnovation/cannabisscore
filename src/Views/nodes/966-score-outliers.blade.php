<!-- generated from resources/views/vendor/cannabisscore/nodes/966-score-outliers.blade.php -->
<div class="slCard nodeWrap">
    <div class="row">
        <div class="col-8">
            <a href="/dash/powerscore-outliers"><h2 class="slBlueDark">
                PowerScore Outliers
            </h2></a>
            <p>
                Below each sub-score (Facility, Production, Lighting, and HVAC) 
                are that sub-score's Standard Deviations (SD) from the group's 
                average, then Interquartile Range (IQR) from the median.
                These are flagged as <span class="slRedDark">red</span> when at 
                least 2 SDs from mean, lower than (Quartile 1 ​− 1.5*IQR), 
                or higher than (Quartile 3 + 1.5*IQR).
            </p><p>
                In addition to the efficiency sub-scores, the final columns show the
                reported flowering canopy square feet per lighting fixture and
                vegitative canopy square feet per lighting fixture.
                These are currently set to flag <span class="slRedDark">red</span> 
                when each light covers less than 2 sq ft, or greater than 64 sq ft.
            </p>
        </div><div class="col-4 taR"><div class="mT10 pB10">
            <select name="status" id="statusID" 
                class="form-control w100 filterOutliers">
                <option value="all" 
                    @if (!$GLOBALS["SL"]->REQ->has('status') 
                        || trim($GLOBALS["SL"]->REQ->get('status')) == 'all') 
                        SELECTED 
                    @endif
                    >All Complete & Archived</option>
                <option value="complete" 
                    @if ($GLOBALS["SL"]->REQ->has('status') 
                        && trim($GLOBALS["SL"]->REQ->get('status')) == 'complete') 
                        SELECTED 
                    @endif
                    >Ranked Dataset Only</option>
            </select>
            <select name="sizes" id="sizesID" 
                class="form-control w100 filterOutliers">
                <option value="yes" 
                    @if (!$GLOBALS["SL"]->REQ->has('sizes') 
                        || trim($GLOBALS["SL"]->REQ->get('sizes')) == 'yes') 
                        SELECTED
                    @endif
                    >Break Down By Flowing Sizes</option>
                <option value="no" 
                @if ($GLOBALS["SL"]->REQ->has('sizes') 
                    && trim($GLOBALS["SL"]->REQ->get('sizes')) == 'no') 
                    SELECTED 
                @endif
                >Skip Size Break Down</option>
            </select>
        </div></div>
    </div>
</div>

<form action="?saveArchives=1&refresh=1{{ 
    (($GLOBALS['SL']->REQ->has('status')) ? '&status=' . $GLOBALS['SL']->REQ->get('status') : '')
    . (($GLOBALS['SL']->REQ->has('sizes')) ? '&sizes=' . $GLOBALS['SL']->REQ->get('sizes') : '')
    }}" method="post">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">

@foreach ($farmTypesOrd as $type) <?php /* Indoor, Hybrid, Outdoor */ ?>
    <hr><hr><hr>
    @foreach ($sizes as $size) 
        {!! $GLOBALS["SL"]->printAccard(
            str_replace('Greenhouse/Hybrid/Mixed Light', 'Greenhouse', 
                    $GLOBALS["SL"]->def->getVal('PowerScore Farm Types', $type)) 
                . ' ' . $GLOBALS["SL"]->def->getVal('Indoor Size Groups', $size) 
                . '<span class="slBlueDark mL30">'
                . number_format($stats[$type][$size]["Facility"]["cnt"]) . '</span>'
                . ((isset($catAlerts[$type]) 
                    && isset($catAlerts[$type][$size]) 
                    && $catAlerts[$type][$size] > 0)
                    ? '<span class="slRedDark">'
                        . '<i class="fa fa-star mL30 mR5" aria-hidden="true"></i> ' 
                        . $catAlerts[$type][$size] . ' New</span>' 
                    : ''),
            view(
                'vendor.cannabisscore.nodes.966-score-outliers-inner', 
                [
                    "type"             => $type,
                    "size"             => $size,
                    "stats"            => $stats,
                    "outlierCols"      => $outlierCols,
                    "scores"           => $scores,
                    "defCmplt"         => $defCmplt,
                    "showStats"        => $showStats,
                    "scoresVegSqFtFix" => $scoresVegSqFtFix
                ]
            )->render(),
            (isset($catAlerts[$type]) 
                && isset($catAlerts[$type][$size]) 
                && $catAlerts[$type][$size] > 0)
        ) !!}
    @endforeach
@endforeach
<center><input type="submit" value="Save All Archive Changes" 
    class="btn btn-xl btn-primary mT30"></center>
</form>


<script type="text/javascript"> $(document).ready(function(){
    function filterOutliers() {
        var url = '';
        if (document.getElementById('statusID')) {
            url += '&status='+document.getElementById('statusID').value;
        }
        if (document.getElementById('sizesID')) {
            url += '&sizes='+document.getElementById('sizesID').value;
        }
        window.location='?'+url.substring(1);
        return true;
    }
    $(document).on("change", "select.filterOutliers", function() {
        filterOutliers();
    });
}); </script>
