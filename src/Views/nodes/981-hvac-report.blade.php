<!-- generated from resources/views/vendor/cannabisscore/nodes/981-hvac-report.blade.php -->

<div class="slCard nodeWrap">
    <a class="float-right btn btn-secondary btn-sm mT5 mB15" 
        @if (trim($fltStateClim) != '') 
            href="?excel=1&fltStateClim={{ $fltStateClim }}"
        @else 
            href="?excel=1"
        @endif
        ><i class="fa fa-file-excel-o mR5" aria-hidden="true"></i> Excel</a>
    <h1 class="slBlueDark">HVAC Report</h1>
    <div class="row">
        <div class="col-8">
            <p>
            Many columns are clickable to load the report 
            listing all individual reports matching the filter 
            (when possible). Small subscript counts are the 
            number of growing areas (reported in powerscores) 
            upon which each calculated average is based.<br />
            <b>Found {{ number_format($totCnt) }} PowerScores</b>
            </p>
        </div>
        <div class="col-4">
            <select name="fltStateClim" id="fltStateClimID" 
                class="form-control form-control-lg" autocomplete="off"
                onChange="window.location='?fltStateClim='+this.value;">
                <option value="" 
                    @if (trim($fltStateClim) == '') SELECTED @endif
                    >All Climates and States</option>
                <option disabled ></option>
                {!! $GLOBALS["SL"]->states->stateClimateDrop($fltStateClim) !!}
            </select>
        </div>
    </div>
</div>

<div class="nodeAnchor"><a name="hvac"></a></div>
@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h2 class="slBlueDark">
        @if ($typeID == 144) 1a. 
        @elseif ($typeID == 145) 1b. 
        @else 1c. 
        @endif 
        {{ $typeName }} Scores by Type of Flowering HVAC
    </h2>
    {!! $scoreSets["statScorHvcF" . $typeID]->printScoreAvgsTbl(
        'hvac', 
        '/dash/compare-powerscores?fltFarm=' . $typeID . '&fltHvac=162-[[val]]'
    ) !!}
    </div>
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h2 class="slBlueDark">
        @if ($typeID == 144) 2a. 
        @elseif ($typeID == 145) 2b. 
        @else 2c. 
        @endif 
        {{ $typeName }} Scores by Type of Vegetative HVAC
    </h2>
    {!! $scoreSets["statScorHvcV" . $typeID]->printScoreAvgsTbl(
        'hvac', 
        '/dash/compare-powerscores?fltFarm=' . $typeID . '&fltHvac=161-[[val]]'
    ) !!}
    </div>
@endforeach

@foreach ([ 144 => 'Indoor', 145 => 'Greenhouse/Mixed', 143 => 'Outdoor' ] 
    as $typeID => $typeName)
    <div class="slCard nodeWrap">
    <h2 class="slBlueDark">
        @if ($typeID == 144) 3a. 
        @elseif ($typeID == 145) 3b. 
        @else 3c. 
        @endif 
        {{ $typeName }} Scores by Type of Cloning/Mother HVAC
    </h2>
    {!! $scoreSets["statScorHvcC" . $typeID]->printScoreAvgsTbl(
        'hvac', 
        '/dash/compare-powerscores?fltFarm=' . $typeID . '&fltHvac=160-[[val]]'
    ) !!}
    </div>
@endforeach


@foreach ($sfFarms[0] as $i => $farmDef)
    @if ($sfFarms[1][$i] != 'Outdoor')
        <div class="slCard nodeWrap">
            <h2 class="slBlueDark">
                @if ($sfFarms[0][$i] == 144) 4a. 
                @elseif ($sfFarms[0][$i] == 145) 4b. 
                @else 4c. 
                @endif 
                {{ $sfFarms[1][$i] }} Square Footage
            </h2>

        <table class="table table-striped w100" border="0">
            <tbody><tr class="brdBot">
             <th>
         </th>              <th class="brdRgt">
        Averages<!--- <sub class="slGrey">13</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-247" target="_blank">System A</a>
        <!--- <sub class="slGrey">5</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-248" target="_blank">System B</a>
        <!--- <sub class="slGrey">1</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-249" target="_blank">System C</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-250" target="_blank">System D</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-356" target="_blank">System E</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-357" target="_blank">System F</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-251" target="_blank">Other System</a>
        <!--- <sub class="slGrey">0</sub> --->
         </th>              <th>
        <a href="/dash/compare-powerscores?fltFarm={{ $farmDef 
            }}&amp;fltHvac=160-360" target="_blank">None</a>
        <!--- <sub class="slGrey">2</sub> --->
         </th>         </tr>

        @foreach ($sfAreasGrow[0] as $i => $areaDef)
            <tr><th><nobr>{{ $sfAreasGrow[1][$i] }}</nobr></th>
                <td class="brdRgt">
                    {{ number_format($hvacSqft[$farmDef][$areaDef][0]) }}
                    <sub class="slGrey">{{ $hvacSqft[$farmDef][$areaDef][1] }}</sub>
                </td>
            @foreach ($sfHvac[0] as $i => $hvacDef)
                <td>
                    {{ number_format($hvacSqft[$farmDef][$areaDef][2][$hvacDef][0]) }}
                    <sub class="slGrey">{{ count($hvacSqft[$farmDef][$areaDef][2][$hvacDef][1]) }}</sub>
                </td>
            @endforeach
            </tr>
        @endforeach

        </tbody></table>
        </div>

    @endif
@endforeach