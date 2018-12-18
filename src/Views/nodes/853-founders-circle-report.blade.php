<!-- generated from resources/views/vendor/cannabisscore/nodes/853-founders-circle-report.blade.php -->

<!--- <a class="float-right btn btn-secondary mT5" href="/dash/compare-powerscore-averages?excel=1"
    ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a> --->
<h1 class="slBlueDark">Founders Circle Report</h1>
<p>
Many columns are clickable to load the report listing all individual reports matching the filter (when possible).
Small subscript counts are the number of powerscores upon which each calculated average is based.
<br />&darr; 
<a href="#flowerarea" class="mL5 mR5">By Area of Flowering Canopy</a> - 
<a href="#auto" class="mL5 mR5">Automation</a> - 
<a href="#vertical" class="mL5 mR5">Vertical Stacking</a>
<a href="#enviornments" class="mL5 mR5">Environments</a> - 
<a href="#hvac" class="mL5 mR5">HVAC</a> - 
<a href="#leads" class="mL5 mR5">Leads</a>
<p>
<p><br /></p>

<div class="nodeAnchor"><a name="flowerarea"></a></div>
<h2 class="slBlueDark">1. Indoor Scores by Area of Flowering Canopy</h2>
<p>Indoor cultivation Electricity Intensity (kWh/sf of flowering canopy) by area of flowering canopy</p>
<?php $statScorSize->addCurrFilt('farm', 144); ?>
{!! $statScorSize->printScoreAvgsTbl('size') !!}
<?php $statScorSize->resetRecFilt(); ?>

<div class="nodeAnchor"><a name="auto"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">2. Indoor Scores by Use of Automation</h2>
<p>These farms responded to using manual and/or automated environmental controls.</p>
<?php $statScorAuto->addCurrFilt('farm', 144); ?>
{!! $statScorAuto->printScoreAvgsTbl('auto') !!}
<?php $statScorAuto->resetRecFilt(); ?>

<div class="nodeAnchor"><a name="vertical"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">3. Effect of Indoor Vertical Stacking</h2>
<?php $statScorVert->addCurrFilt('farm', 144); ?>
{!! $statScorVert->printScoreAvgsTbl('vert') !!}
<?php $statScorVert->resetRecFilt(); ?>

<div class="nodeAnchor"><a name="environments"></a></div>
@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] as $typeID => $typeName)
    <div class="p20"></div>
    <h2 class="slBlueDark">
        @if ($typeID == 144) 4. @elseif ($typeID == 145) 5. @else 6. @endif {{ $typeName }} Growing Environments
    </h2>
    <p><span class="slGrey">Commercial/Warehouse, House/Garage, Barn, Greenhouse, Outdoor, Other</span></p>
    <div class="row mB10">
        <div class="col-6">
            Mother/Cloning ({{ $statEnv->cmpl[$typeID][160][0] }}) {!! $statEnv->envPies[$typeID][160] !!}
        </div>
        <div class="col-6">
            Vegetating ({{ $statEnv->cmpl[$typeID][161][0] }})  {!! $statEnv->envPies[$typeID][161] !!}
        </div>
    </div>
    <div class="row mB10">
        <div class="col-6">
            Flowering ({{ $statEnv->cmpl[$typeID][162][0] }}) {!! $statEnv->envPies[$typeID][162] !!}
        </div>
        <div class="col-6">
            Drying ({{ $statEnv->cmpl[$typeID][163][0] }}) {!! $statEnv->envPies[$typeID][163] !!}
        </div>
    </div>
    <?php $statEnv->addCurrFilt('farm', $typeID); ?>
    <table border=0 class="table table-striped w100">
    {!! $statEnv->tblHeaderRow('area') !!}
    {!! $statEnv->tblPercHasDat('area', $bldDats) !!}
    </table>
    <?php $statEnv->resetRecFilt(); ?>
@endforeach

<div class="nodeAnchor"><a name="hvac"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">7. Indoor Scores by Type of Flowering HVAC</h2>
{!! $statScorHvcF->printScoreAvgsTbl('hvac', '/dash/compare-powerscores?fltFarm=144&fltHvac=162-[[val]]') !!}

<div class="p20"></div>
<h2 class="slBlueDark">8. Indoor Scores by Type of Vegetative HVAC</h2>
{!! $statScorHvcV->printScoreAvgsTbl('hvac', '/dash/compare-powerscores?fltFarm=144&fltHvac=161-[[val]]') !!}

<div class="p20"></div>
<h2 class="slBlueDark">9. Indoor Scores by Type of Cloning/Mother HVAC</h2>
{!! $statScorHvcC->printScoreAvgsTbl('hvac', '/dash/compare-powerscores?fltFarm=144&fltHvac=160-[[val]]') !!}

<div class="nodeAnchor"><a name="leads"></a></div>
<div class="p20"></div>
<h2 class="slBlueDark">10. Leads</h2>
<table border=0 class="table table-striped w100">
{!! $statLeads->tblHeaderRow('farm', '/dash/compare-powerscores?fltFarm=[[val]]') !!}
{!! $statLeads->tblPercHasDat('farm', ['nonfarm', 'upgrade', 'incent', 'contact']) !!}
</table>


