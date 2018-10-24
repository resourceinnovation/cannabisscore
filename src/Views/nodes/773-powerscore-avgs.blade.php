<!-- generated from resources/views/vendor/cannabisscore/nodes/773-powerscore-avgs.blade.php -->
&darr; <a href="#sqft" class="mL5 mR5">Sqaure Footage</a> - 
<a href="#prod" class="mL5 mR5">Production Types</a> - 
<a href="#techniques" class="mL5 mR5">Technique Adoption</a>
<a href="#lighting" class="mL5 mR5">Lighting kWh</a> - 
<a href="#lightAdopt" class="mL5 mR5">Lighting Adoption</a> - 
<a href="#sqftFixture" class="mL5 mR5">Sqft/Fixture</a> - 
<a href="#hvac" class="mL5 mR5">HVAC Adoption</a>

<!--- <a class="float-right btn btn-secondary mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
<h1 class="slBlueDark">Completed PowerScore Averages</h1>

<table border=0 class="table table-striped w100">
{!! $statScor->tblTagHeaderRow('over') !!}
{!! str_replace('brdTop', 'brdTop fPerc125', $statScor->tblTagRows('over', '1')) !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagRows('over', 'farm') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagRows('over', 'cups') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'flw-lgty') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'veg-lgty') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'cln-lgty') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'flw-hvac') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'veg-hvac') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'cln-hvac') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'tech') !!}
{!! $statScor->tblTagSpacerRow('over') !!}
{!! $statScor->tblTagHeaderRow('over') !!}
{!! $statScor->tblTagRows('over', 'powr') !!}
</table>

<div class="nodeAnchor"><a name="sqft"></a></div>
<h2 class="slBlueDark">Square Footage by Growth Stage</h2>
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

<div class="nodeAnchor"><a name="prod"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">Stats & Techniques by Production Types</h2>
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

<div class="nodeAnchor"><a name="lighting"></a></div>
<div class="p20"></div>
<h1 class="slBlueDark">Lighting Techniques By Growth Stage</h1>
<table border=0 class="table table-striped w100">
{!! $statLgts->tblHeaderRow('area') !!}
{!! $statLgts->tblFltBlksPercHasDat('area', 'farm', [ 'arf', 'dep', 'sun' ]) !!}
</table>

<div class="p20"></div>
<h1 class="slBlueDark">Lighting Kilowatt Hours (kWh)</h1>
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
{!! $statLgts->tblSpacerRow('area') !!}
{!! $statLarf->tblFltRowsCalcDiv('area', 'farm', 'W', 'sqft') !!}
</table>

<div class="nodeAnchor"><a name="lightAdopt"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">Lighting Adoption: All Farms</h2>
{!! $statLgts->pieTblPercHas('area', 'lgty') !!}

{!! $statLgts->pieTblBlksPercHas('area', 'lgty', 'farm', 'Lighting Adoption: ') !!}

<div class="nodeAnchor"><a name="sqftFixture"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">Square Feet per Lighting Fixture</h2>
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

<div class="nodeAnchor"><a name="hvac"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">HVAC Adoption: All Farms</h2>
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
{!! $statHvac->pieTblPercHas('area', 'hvac') !!}

{!! $statHvac->pieTblBlksPercHas('area', 'hvac', 'farm', 'Lighting Adoption: ') !!}



<div class="nodeAnchor"><a name="sources"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">On-Site Energy Sources</h2>
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
