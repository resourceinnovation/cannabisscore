<!-- generated from resources/views/vendor/cannabisscore/nodes/362-utilities-by-state.blade.php -->
@forelse ($statePowerUtils as $state => $utils)
    <h4>{{ $state }}</h4>
    <ul>
    @forelse ($utils as $i => $u)
        @if (isset($powerUtilsInd[$u]))
        <?php $uAbbr = 'Ut' . $powerUtils[$powerUtilsInd[$u]]["id"] 
            . $GLOBALS["SL"]->states->getStateAbrr($state); ?>
            <li><a href="javascript:;" class="hidivBtn" id="hidivBtn{{ $uAbbr }}"
                >{{ $powerUtils[$powerUtilsInd[$u]]["name"] }}</a>
            <div id="hidiv{{ $uAbbr }}" class="disNon slGrey fPerc80">
                {!! implode(', ', $powerUtils[$powerUtilsInd[$u]]["zips"]) !!}
            </div>
            </li>
        @endif
    @empty
    @endforelse
    </ul>
@empty
    <p><i>Nothing found in database.</i></p>
@endforelse
<p><i>{{ number_format(sizeof($powerUtils)) }} Utility Companies Listed.</i></p>