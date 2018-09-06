<!-- generated from resources/views/vendor/cannabisscore/nodes/786-admin-search-results.blade.php -->

<h1 class="mT0">PowerScore Search Results:
@if ($GLOBALS["SL"]->REQ->has('s') && trim($GLOBALS["SL"]->REQ->get('s')) != '')
    <i class="slBlueDark mL20">{{ trim($GLOBALS["SL"]->REQ->get('s')) }}</i>
@endif </h1>

<h4>Matching farm name, email address, county, or zip code:</h4>
@if (isset($GLOBALS["SL"]->x["srchRes"]["name"]) && intVal($GLOBALS["SL"]->x["srchRes"]["name"]) > 0)
    {!! view('vendor.cannabisscore.inc-tbl-powerscore-submissions', [
        "scores" => $GLOBALS["SL"]->x["srchRes"]["name"] ])->render() !!}
@else
    <div class="p20 slGrey"><i>none</i></div>
@endif

<div class="p10"></div>
<h4>Matching any other field:</h4>
@if (isset($GLOBALS["SL"]->x["srchRes"]["dump"]) && intVal($GLOBALS["SL"]->x["srchRes"]["dump"]) > 0)
    {!! view('vendor.cannabisscore.inc-tbl-powerscore-submissions', [
        "scores" => $GLOBALS["SL"]->x["srchRes"]["dump"] ])->render() !!}
@else
    <div class="p20 slGrey"><i>none</i></div>
@endif