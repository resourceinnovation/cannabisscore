<!-- generated from resources/views/vendor/cannabisscore/nodes/853-founders-circle-report.blade.php -->

<!--- <a class="float-right btn btn-secondary mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
<div class="slCard nodeWrap">
<h1 class="slBlueDark">Founders Circle Report</h1>
<p>
Many columns are clickable to load the report listing all 
individual reports matching the filter (when possible).
Small subscript counts are the number of powerscores 
upon which each calculated average is based.
<br />&darr; 
<a href="#flowerarea" class="mL5 mR5">By Area of Flowering Canopy</a> - 
<a href="#auto" class="mL5 mR5">Automation</a> - 
<a href="#vertical" class="mL5 mR5">Vertical Stacking</a>
<a href="#enviornments" class="mL5 mR5">Environments</a> - 
<a href="#leads" class="mL5 mR5">Leads</a>
</p>
</div>

<div class="nodeAnchor"><a name="flowerarea"></a></div>
@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h2 class="slBlueDark">
    @if ($typeID == 144) 1a. 
    @elseif ($typeID == 145) 1b. 
    @else 1c. 
    @endif 
    {{ $typeName }} Scores by Area of Flowering Canopy</h2>
    <p>{{ $typeName }} cultivation Electricity Intensity 
    (kBtu / sq ft of flowering canopy) by area of flowering canopy</p>
    <?php $scoreSets["statScorSize" . $typeID]->addCurrFilt('farm', $typeID); ?>
    {!! $scoreSets["statScorSize" . $typeID]->printScoreAvgsTbl('size') !!}
    <?php $scoreSets["statScorSize" . $typeID]->resetRecFilt(); ?>
    </div>
@endforeach

<div class="nodeAnchor"><a name="auto"></a></div>
<div class="slCard nodeWrap">
<h2 class="slBlueDark">2. Indoor Scores by Use of Automation</h2>
<p>These farms responded to using manual and/or automated environmental controls.</p>
<?php $scoreSets["statScorAuto"]->addCurrFilt('farm', 144); ?>
{!! $scoreSets["statScorAuto"]->printScoreAvgsTbl('auto') !!}
<?php $scoreSets["statScorAuto"]->resetRecFilt(); ?>
</div>

<div class="nodeAnchor"><a name="vertical"></a></div>
<div class="slCard nodeWrap">
<h2 class="slBlueDark">3. Effect of Indoor Vertical Stacking</h2>
<?php $scoreSets["statScorVert"]->addCurrFilt('farm', 144); ?>
{!! $scoreSets["statScorVert"]->printScoreAvgsTbl('vert') !!}

<p>The experimental 'Production Density' calculation 
divides the Production Sub-Score by the average total 
square feet of flowering canopy (for each farm).</p>
<table border=0 class="table table-striped w100">
    <tr>
        <td></td>
        <th>Average</th>
        <th>Without Vertical Stacking</th>
        <th>With Vertical Stacking</th>
    </tr>
    <tr>
        <th>Production Density (g/kWh/SqFt)</th>
        <td>{{ $GLOBALS["SL"]->sigFigs($vertDense[2], 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($vertDense[0][0], 3) }}</td>
        <td>{{ $GLOBALS["SL"]->sigFigs($vertDense[1][0], 3) }}</td>
    </tr>
</table>
<?php /*
<table border=0 class="table table-striped w100">
    {!! $scoreSets["statScorVert"]->tblFltRowsCalc('vert', 'farm', 'prodens', 'avg') !!}
</table>
*/ ?>
<?php $scoreSets["statScorVert"]->resetRecFilt(); ?>
</div>

<div class="nodeAnchor"><a name="environments"></a></div>
@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h2 class="slBlueDark">
        @if ($typeID == 144) 4a. 
        @elseif ($typeID == 145) 4b. 
        @else 4c. 
        @endif 
        {{ $typeName }} Growing Environments
    </h2>
    <p><span class="slGrey">
        Commercial/Warehouse, House/Garage, Barn, Greenhouse, Outdoor, Other
    </span></p>
    <div class="row mB10">
        <div class="col-6">
            Mother/Cloning ({{ $statEnv->cmpl[$typeID][160][0] }}) 
            {!! $statEnv->envPies[$typeID][160] !!}
        </div>
        <div class="col-6">
            Vegetating ({{ $statEnv->cmpl[$typeID][161][0] }})  
            {!! $statEnv->envPies[$typeID][161] !!}
        </div>
    </div>
    <div class="row mB10">
        <div class="col-6">
            Flowering ({{ $statEnv->cmpl[$typeID][162][0] }}) 
            {!! $statEnv->envPies[$typeID][162] !!}
        </div>
        <div class="col-6">
            Drying ({{ $statEnv->cmpl[$typeID][163][0] }}) 
            {!! $statEnv->envPies[$typeID][163] !!}
        </div>
    </div>
    <?php $statEnv->addCurrFilt('farm', $typeID); ?>
    <table border=0 class="table table-striped w100">
    {!! $statEnv->tblHeaderRow('area') !!}
    {!! $statEnv->tblPercHasDat('area', $bldDats) !!}
    </table>
    <?php $statEnv->resetRecFilt(); ?>
    </div>
@endforeach

<div class="nodeAnchor"><a name="leads"></a></div>
<div class="slCard nodeWrap">
<h2 class="slBlueDark">5. Leads</h2>
<table border=0 class="table table-striped w100">
{!! $statLeads->tblHeaderRow('farm', '/dash/compare-powerscores?fltFarm=[[val]]') !!}
{!! $statLeads->tblPercHasDat('farm', ['nonfarm', 'upgrade', 'incent', 'contact']) !!}
</table>
</div>