<!-- generated from resources/views/vendor/cannabisscore/nodes/859-report-more-stats.blade.php -->

<div class="slCard greenline nodeWrap">
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
            <select name="fltStateClim" id="fltStateClimID" class="form-control form-control-lg"
                onChange="window.location='?fltStateClim='+this.value;" autocomplete="off">
                <option value="" @if (trim($fltStateClim) == '') SELECTED @endif
                    >All Climates and States</option>
                <option disabled ></option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
            </select>
        </div>
    </div>
</div>

<div class="nodeAnchor"><a name="sqft"></a></div>
<div class="slCard greenline nodeWrap">
<h3 class="slBlueDark">1. Square Footage by Growth Stage</h3>
<table border=0 class="table table-striped w100">
{!! $statSqft->tblHeaderRow('area') !!}
{!! $statSqft->tblFltRowsCalc('area', 'farm', 'sqft', 'avg') !!}
{!! $statSqft->tblSpacerRow('area') !!}
{!! $statSqft->tblFltRowsCalc('area', 'farm', 'sqft') !!}
{!! $statSqft->tblSpacerRow('area') !!}
{!! $statSqft->tblHeaderRow('area') !!}
{!! $statSqft->tblFltDatColPerc('area', 'farm', 'sqft', 'Percent Canopy Square Feet') !!}
{!! $statSqft->tblSpacerRow('area') !!}
{!! $statSqft->tblFltDatRatio2Col('area', 'farm', 'sqft', 162, 'Average Ratios to Flowering') !!}
</table>
</div>

<div class="nodeAnchor"><a name="prod"></a></div>
<div class="slCard greenline nodeWrap">
<h3 class="slBlueDark">2. Stats & Techniques by Production Types</h3>
<table border=0 class="table table-striped w100">
{!! $statMisc->tblHeaderRow('farm') !!}
{!! $statMisc->tblAvgTotScale('farm', 'g', 0.002204623, 'Pounds') !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblAvgTot('farm', 'g') !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblAvgTot('farm', 'kWh') !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblHeaderRow('farm') !!}
{!! $statSqft->tblPercHas('farm', 'area') !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblPercHasDat('farm', [ 'PsHarvestBatch', 'PsHasWaterPump', 'PsHeatWater', 
    'PsControls', 'PsControlsAuto', 'PsVerticalStack' ]) !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblHeaderRow('farm') !!}
{!! $statMisc->tblPercHasDat('farm', [ 'rnw149', 'rnw159', 'rnw151', 'rnw150', 'rnw158', 'rnw153', 'rnw154', 'rnw155', 
    'rnw156', 'rnw157', 'rnw241' ]) !!}
{!! $statMisc->tblSpacerRow('farm') !!}
{!! $statMisc->tblHeaderRow('farm') !!}
{!! $statMisc->tblPercHasDat('farm', [ 'PsConsiderUpgrade', 'PsIncentiveWants', 'PsIncentiveUsed', 'PsNewsletter' ]) !!}
</table>
</div>

<div class="nodeAnchor"><a name="lighting"></a></div>
<div class="slCard greenline nodeWrap">
<h1 class="slBlueDark">3. Lighting Techniques By Growth Stage</h1>
<table border=0 class="table table-striped w100">
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltBlksPercHasDat('area', 'farm', [ 'arf', 'dep', 'sun' ]) !!}
</table>
</div>

<div class="slCard greenline nodeWrap">
<h1 class="slBlueDark">4. Lighting Kilowatt Hours (kWh)</h1>
<table border=0 class="table table-striped w100">
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltRowsCalc('area', 'farm', 'kWh', 'avg') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->tblFltRowsCalc('area', 'farm', 'kWh') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->tblFltRowsCalcDiv('area', 'farm', 'kWh', 'sqft') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltDatRowPerc('area', 'farm', 'kWh') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->tblFltDatColPerc('area', 'farm', 'kWh') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltRowsCalcDiv('area', 'farm', 'W', 'sqft') !!}
</table>
</div>

<div class="nodeAnchor"><a name="lightAdopt"></a></div>
<div class="slCard greenline nodeWrap">
<?php /* <h3 class="slBlueDark">5. Lighting Adoption: All Farms</h3>
{!! $statLgts->pieTblPercHas('area', 'lgty') !!} */ ?>
{!! $statLgts->pieTblBlksPercHas('area', 'lgty', 'farm', '5. Lighting Adoption: ') !!}
</div>

<div class="nodeAnchor"><a name="sqftFixture"></a></div>
<div class="slCard greenline nodeWrap">
<h3 class="slBlueDark">6. Square Feet per Lighting Fixture</h3>
<table border=0 class="table table-striped w100">
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltRowsCalcDiv('area', 'farm', 'sqft', 'lgtfx', 'Square Feet per Fixture') !!}
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLgts->addCurrHide('lgty', 2) !!}
{!! $statLgts->tblFltRowsCalc('area', 'lgty', 'sqft/lgtfx', 'sum', 'Square Feet per Fixture', false) !!}
{!! str_replace('Total Square Feet per Fixture', 'Square Feet per Fixture', 
    $statLgts->tblFltRowsBlksCalc('area', 'lgty', 'farm', 'sqft/lgtfx', 'sum', 'Square Feet per Fixture: ')) !!}
{!! $statLgts->delCurrHide('lgty') !!}
</table>
</div>

<div class="nodeAnchor"><a name="hvac"></a></div>
<div class="slCard greenline nodeWrap">
<h3 class="slBlueDark">7. HVAC Adoption</h3>
<ul>
<li><b>System A</b> - Conventional Air Conditioning with Supplemental Portable Dehumidification Units 
    <span class="slGrey">(est. 115 kWh/SqFt)</span></li>
<li><b>System B</b> - Conventional Air Conditioning with Enhanced Dehumidification 
    <span class="slGrey">(est. 77 kWh/SqFt)</span></li>
<li><b>System C</b> - Conventional Air Conditioning with Split Dehumidification Systems 
    <span class="slGrey">(est. 104 kWh/SqFt)</span></li>
<li><b>System D</b> - Fully Integrated Cooling and Dehumidification System 
    <span class="slGrey">(est. 65 kWh/SqFt)</span></li>
<li><b>System E</b> - Chilled Water Dehumidification System</li>
<li><b>System F</b> - Greenhouse HVAC Systems</li>
</ul>
<?php /* {!! $statHvac->pieTblPercHas('area', 'hvac') !!} */ ?>
{!! $statHvac->pieTblBlksPercHas('area', 'hvac', 'farm', 'HVAC Adoption: ') !!}
</div>

<div class="nodeAnchor"><a name="sources"></a></div>
<div class="slCard greenline nodeWrap">
<h3 class="slBlueDark">8. On-Site Energy Sources</h3>
<div class="row">
    <div class="col-6">Indoor ({{ $enrgys["cmpl"][144][0] }}) {!! $enrgys["pie"][144] !!}</div>
    <div class="col-6">Greenhouse/Mixed ({{ $enrgys["cmpl"][145][0] }}) {!! $enrgys["pie"][145] !!}</div>
</div>
<div class="row">
    <div class="col-6">Outdoor ({{ $enrgys["cmpl"][143][0] }}) {!! $enrgys["pie"][143] !!}</div>
    <div class="col-6">Outdoor Adjusted ({{ sizeof($enrgys["extra"][1]) }}) {!! $enrgys["pie"][143143] !!}</div>
</div>
<table border=0 class="table table-striped w100">
    <tr>
    <th>&nbsp;</th>
    <th>Indoor</th>
    <th>Greenhouse/Mixed</th>
    <th>Outdoor</th>
    <th>Outdoor Adjusted</th>
    </tr>
@foreach ($GLOBALS["SL"]->def->getSet('PowerScore Onsite Power Sources') as $j => $renew)
    <tr>
    <th>{{ $renew->DefValue }}</th>
    @foreach ([144, 145, 143] as $type) <td>{{ $enrgys["cmpl"][$type][$renew->DefID] }}</td> @endforeach
    <td>{{ $enrgys["extra"][143][$renew->DefID] }}</td>
    </tr>
@endforeach
</table>
<div class="slGrey"><i>Outdoor Adjusted PowerScores: #{!! implode(', #', $enrgys["extra"][1]) !!}</i></div>
</div>