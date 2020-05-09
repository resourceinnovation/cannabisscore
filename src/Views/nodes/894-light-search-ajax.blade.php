<!-- resources/views/vendor/cannabisscore/nodes/894-light-search-ajax.blade.php -->
@if (sizeof($results["set"]) > 0)
    <div class="pB10">Click a search result to select the light:</div>
    @foreach ($results["set"] as $i => $model)
        <a href="javascript:;" onClick="return loadModel({{ 
                json_encode($manufacts[$model->lgt_mod_manu_id]) 
            }}, {{ 
                json_encode($model->lgt_mod_name) 
            }}, {{ 
                $lightImportTypeConvert[$model->lgt_mod_tech] 
            }}, {{ 
                ((isset($model->lgt_mod_wattage)) ? intVal($model->lgt_mod_wattage) : 0)
            }});" class="btn btn-secondary btn-sm w100 taL mB5" >
            @if (isset($manufacts[$model->lgt_mod_manu_id])) {{ $manufacts[$model->lgt_mod_manu_id] }}: @endif
            {{ $model->lgt_mod_name }}
            <?php /* <span class="slGrey">{{ $model->lgt_mod_tech }}</span> */ ?>
            </a>
    @endforeach
@else
    <div class="slGrey">
        @if ($lgtSrch["make"] == '' 
            && $lgtSrch["make"] == '' 
            && $lgtSrch["make"] == '')
            Select a type of lighting fixture, or start 
            typing the make or model of your lights.
        @else
            No results matched your search.
        @endif
        <br /><br />
        <a class="listAllModels" href="javascript:;"
            >List all lighting models</a>
    </div>
@endif