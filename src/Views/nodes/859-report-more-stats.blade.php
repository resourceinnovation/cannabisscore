<!-- generated from resources/views/vendor/cannabisscore/nodes/859-report-more-stats.blade.php -->

<div class="slCard nodeWrap">
    <?php /*
    <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        @if (trim($fltStateClim) != '') href="?excel=1&fltStateClim={{ $fltStateClim }}"
        @else href="?excel=1"
        @endif
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
    */ ?>
    <h1 class="slBlueDark">More Live Statistics</h1>
    <div class="row">
        <div class="col-8">
            <p>
            &darr; <a href="#sqft" class="mL5 mR5">Sqaure Footage</a> - 
            <a href="#prod" class="mL5 mR5">Production Types</a> - 
            <a href="#techniques" class="mL5 mR5">Technique Adoption</a>
            <a href="#lighting" class="mL5 mR5">Lighting kWh</a> - 
            <a href="#lightAdopt" class="mL5 mR5">Lighting Adoption</a> - 
            <a href="#sqftFixture" class="mL5 mR5">Sqft/Fixture</a> - 
            <a href="#hvac" class="mL5 mR5">HVAC Adoption</a>
            </p>
        </div>
        <div class="col-4">
            {!! $GLOBALS["SL"]->states->stateClimateTagsSelect($fltStateClimTag, 859, 'psChangeFilterDelay') !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsList($fltStateClimTag, 859) !!}
            {!! $GLOBALS["SL"]->states->stateClimateTagsJS($fltStateClimTag, 859, 'psClickFilterDelay') !!}
        </div>
    </div>
</div>


<div class="nodeAnchor"><a name="sqft"></a></div>
<div class="slCard nodeWrap">
    <h3 class="slBlueDark">1. Square Footage by Growth Stage</h3>

    <table class="table table-striped w100">
    {!! $statSqft->printInnerTblFltRowsCalc('area', 'farm', 'sqft', 'avg', '', $colLnk) !!}
    {!! $statSqft->tblPercSpacerRowHtml('area') !!}

    {!! $statSqft->printInnerTblFltRowsCalc('area', 'farm', 'sqft', 'sum', '', $colLnk) !!}
    {!! $statSqft->tblPercSpacerRowHtml('area') !!}
    </table>

    <table class="table table-striped w100">
    {!! $statSqft->printInnerTblFltDatColPerc('area', 'farm', 'sqft', 'Percent Canopy Square Feet', $colLnk) !!}
    {!! $statSqft->tblPercSpacerRowHtml('area') !!}

    {!! $statSqft->printInnerTblFltDatRatio2Col('area', 'farm', 'sqft', 162, 'Average Ratios to Flowering', $colLnk) !!}
    </table>
</div>

<div class="nodeAnchor"><a name="prod"></a></div>
<div class="slCard nodeWrap">
    <h3 class="slBlueDark">2. Stats & Techniques by Production Types</h3>
    <table class="table table-striped w100">
    {!! $statMisc->printInnerTblFltHeaderRow('sum', 'farm', 'brdBotGrey', $colLnk, 2) !!}
    {!! $statMisc->printInnerTblCalcRecCntRow('farm', 'sum', '1', 'brdBotBlue2') !!}
    {!! $statMisc->printInnerTblAvgTotScale('farm', 'g', 0.002204623, ' Pounds') !!}
    {!! $statMisc->tblPercSpacerRowHtml('farm') !!}

    {!! $statMisc->printInnerTblAvgTot('farm', 'g') !!}
    {!! $statMisc->tblPercSpacerRowHtml('farm') !!}

    {!! $statMisc->printInnerTblAvgTot('farm', 'kWh') !!}
    </table>

    <p><br /></p>

    <table class="table table-striped w100">
    {!! $statSqft->printInnerTblMultiPercHas('area', 'farm', $colLnk) !!}
    {!! $statMisc->tblSpacerRowHtml('farm') !!}

    {!! $statMisc->printInnerTblPercHasDat(
        'farm', 
        [
            'ps_has_water_pump', 'ps_heat_water', // 'ps_harvest_batch'
            'ps_controls', 'ps_controls_auto', 'ps_vertical_stack' 
        ], 
        [],
        $colLnk
    ) !!}
    {!! $statMisc->tblPercSpacerRowHtml('farm') !!}
    {!! $statMisc->printInnerTblPercHasDat(
        'farm', 
        [
            'rnw149', 'rnw159', 'rnw151', 'rnw150', 'rnw158', 'rnw153', 
            'rnw154', 'rnw155', 'rnw156', 'rnw157', 'rnw241'
        ], 
        [],
        $colLnk
    ) !!}
    {!! $statMisc->tblPercSpacerRowHtml('farm') !!}
    {!! $statMisc->printInnerTblPercHasDat(
        'farm', 
        [ 
            'ps_consider_upgrade', 'ps_incentive_wants', 
            'ps_incentive_used', 'ps_newsletter' 
        ], 
        [],
        $colLnk
    ) !!}
    </table>
</div>


<div class="nodeAnchor"><a name="lighting"></a></div>
<div class="slCard nodeWrap">
<h1 class="slBlueDark">3. Lighting Techniques By Growth Stage</h1>
{!! $statLgts->printTblFltBlksPercHasDat(
    'area', 
    'farm', 
    [ 'arf', 'dep', 'sun' ], 
    $colLnk
) !!}
</div>

<div class="slCard nodeWrap">
<h1 class="slBlueDark">4. Lighting Kilowatt Hours (kWh)</h1>
<table border=0 class="table table-striped w100">
{!! $statLgts->printInnerTblFltRowsCalc('area', 'farm', 'kWh', 'avg', '', $colLnk) !!}
{!! $statLgts->tblPercSpacerRowHtml('area') !!}

{!! $statLgts->printInnerTblFltRowsCalc('area', 'farm', 'kWh', 'sum', '', $colLnk) !!}
{!! $statLgts->tblPercSpacerRowHtml('area') !!}

{!! $statLgts->printInnerTblFltRowsCalcDiv('area', 'farm', 'kWh', 'sqft', '', $colLnk) !!}
{!! $statLgts->tblPercSpacerRowHtml('area') !!}

{!! $statLgts->printInnerTblFltRowsCalcDiv('area', 'farm', 'W', 'sqft', '', $colLnk) !!}
</table>

<p><br /></p>

<table border=0 class="table table-striped w100">
{!! $statLgts->printInnerTblFltDatRowPerc('area', 'farm', 'kWh', '', $colLnk) !!}
{!! $statLgts->tblSpacerRowHtml('area') !!}

{!! $statLgts->printInnerTblFltDatColPerc('area', 'farm', 'kWh', '', $colLnk) !!}
</table>
</div>


<div class="nodeAnchor"><a name="lightAdopt"></a></div>
<div class="slCard nodeWrap">
<h3 class="slBlueDark">5. Lighting Adoption: All Farms</h3>
{!! $statLgts->pieTblMutliPercHas('lgty', 'area') !!}
{!! $statLgts->pieTblBlksMultiPercHas('area', 'lgty', 'farm', '5. Lighting Adoption: ') !!}
</div>

<div class="nodeAnchor"><a name="sqftFixture"></a></div>
<div class="slCard nodeWrap">
<h3 class="slBlueDark">6. Square Feet per Lighting Fixture</h3>
<table border=0 class="table table-striped w100">

{!! $statLgts->printInnerTblFltRowsCalcDiv('area', 'farm', 'sqft', 'lgtfx', 'Square Feet per Fixture') !!}
{!! $statLgts->tblSpacerRowHtml('area') !!}

{!! $statLgts->addCurrHide('lgty', 2) !!}
{!! $statLgts->printInnerTblFltRowsCalc('area', 'lgty', 'sqft/lgtfx', 'sum', 'Square Feet per Fixture', '', false) !!}
{!! $statLgts->tblSpacerRowHtml('area') !!}

{!! str_replace(
    'Total Square Feet per Fixture', 
    'Square Feet per Fixture', 
    $statLgts->printTblFltRowsBlksCalc('area', 'lgty', 'farm', 'sqft/lgtfx', 'sum', 'Square Feet per Fixture: ',$colLnk,false)
) !!}
{!! $statLgts->delCurrHide('lgty') !!}

</table>
</div>

<div class="nodeAnchor"><a name="hvac"></a></div>
<div class="slCard nodeWrap">
<h3 class="slBlueDark">7. HVAC Adoption</h3>
<ul>
<li><b>System A</b> - 
    Conventional Air Conditioning with Supplemental Portable Dehumidification Units 
    <span class="slGrey">(est. 115 kWh/SqFt)</span></li>
<li><b>System B</b> - 
    Conventional Air Conditioning with Enhanced Dehumidification 
    <span class="slGrey">(est. 77 kWh/SqFt)</span></li>
<li><b>System C</b> - 
    Conventional Air Conditioning with Split Dehumidification Systems 
    <span class="slGrey">(est. 104 kWh/SqFt)</span></li>
<li><b>System D</b> - 
    Fully Integrated Cooling and Dehumidification System 
    <span class="slGrey">(est. 65 kWh/SqFt)</span></li>
<li><b>System E</b> - Chilled Water Dehumidification System</li>
<li><b>System F</b> - Greenhouse HVAC Systems</li>
</ul>
<?php 
// {!! $statHvac->pieTblMutliPercHas('hvac', 'area') !!}
?>
{!! $statHvac->pieTblBlksMultiPercHas('area', 'hvac', 'farm', 'HVAC Adoption: ') !!}
</div>


<div class="nodeAnchor"><a name="sources"></a></div>
<div class="slCard nodeWrap">
<h3 class="slBlueDark">8. On-Site Energy Sources</h3>
<div class="row">
    <div class="col-6">
        Indoor ({{ $enrgys["cmpl"][144][0] }}) 
        {!! $enrgys["pie"][144] !!}
    </div>
    <div class="col-6">
        Greenhouse/Mixed ({{ $enrgys["cmpl"][145][0] }}) 
        {!! $enrgys["pie"][145] !!}
    </div>
</div>
<div class="row">
    <div class="col-6">
        Outdoor ({{ $enrgys["cmpl"][143][0] }}) 
        {!! $enrgys["pie"][143] !!}
    </div>
    <div class="col-6">
        Outdoor Adjusted ({{ sizeof($enrgys["extra"][1]) }}) 
        {!! $enrgys["pie"][143143] !!}
    </div>
</div>
<table border=0 class="table table-striped w100">
    <tr>
    <th>&nbsp;</th>
    <th>Indoor</th>
    <th>Greenhouse/Mixed</th>
    <th>Outdoor</th>
    <th>Outdoor Adjusted</th>
    </tr>
@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') 
    as $j => $renew)
    <tr>
    <th>{{ $renew->def_value }}</th>
    @foreach ([144, 145, 143] as $type)
        <td>{{ $enrgys["cmpl"][$type][$renew->def_id] }}</td>
    @endforeach
    <td>{{ $enrgys["extra"][143][$renew->def_id] }}</td>
    </tr>
@endforeach
</table>
<div class="slGrey"><i>
    Outdoor Adjusted PowerScores: 
    #{!! implode(', #', $enrgys["extra"][1]) !!}
</i></div>
</div>

<script type="text/javascript"> $(document).ready(function(){

function applyFilts() {
    var baseUrl = "?filt=1";
    if (document.getElementById("n859tagIDsID") && document.getElementById("n859tagIDsID").value.trim() != '') {
        baseUrl += "&fltStateClimTag="+document.getElementById("n859tagIDsID").value.trim();
    }
    window.location = baseUrl;
    return false;
}
$(document).on("change", ".psChangeFilterDelay", function() {
    setTimeout(function() { applyFilts(); }, 200);
    return true;
});
$(document).on("click", ".psClickFilterDelay", function() {
    setTimeout(function() { applyFilts(); }, 200);
    return true;
});

}); </script>
