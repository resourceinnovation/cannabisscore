<!-- resources/views/vendor/cannabisscore/nodes/894-light-search-ajax.blade.php -->
@if (sizeof($results["set"]) > 0)
    <div class="pB10">Click a search result to select the light:</div>
    @foreach ($results["set"] as $i => $model)
        <a onClick="return loadModel({{ json_encode($manufacts[$model->LgtModManuID]) }}, {{ 
            json_encode($model->LgtModName) }}, {{ $lightImportTypeConvert[$model->LgtModTech] 
            }});" class="btn btn-secondary btn-sm w100 taL mB5" href="javascript:;">
            @if (isset($manufacts[$model->LgtModManuID])) {{ $manufacts[$model->LgtModManuID] }}: @endif
            {{ $model->LgtModName }}
            <?php /* <span class="slGrey">{{ $model->LgtModTech }}</span> */ ?>
            </a>
    @endforeach
@else
    <div class="slGrey">
        @if ($lgtSrch["make"] == '' && $lgtSrch["make"] == '' && $lgtSrch["make"] == '')
            Select a type of lighting fixture, or start typing the make or model of your lights.
        @else No results matched your search.
        @endif
        <br /><br /><a class="listAllModels" href="javascript:;">List all lighting models</a>
    </div>
@endif