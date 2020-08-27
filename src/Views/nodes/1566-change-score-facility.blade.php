<!-- resources/views/vendor/cannabisscore/nodes/1566-change-score-facility.blade.php -->

<p>&nbsp;</p>
<hr>
<div>
    <i class="fa fa-users mR5" aria-hidden="true"></i> 
    Company/Facility Ownership:
</div>
<select name="changePsFacility" id="changePsFacilityID"
    class="form-control mT5 mB10" autocomplete="off">
    <option value="" @if ($psComOwner == '') SELECTED @endif
        >None</option>
@forelse ($companies as $i => $com)
    <option value="" DISABLED ></option>
    <option value="C{{ $com->id }}"
        @if ($psComOwner == ('C' . $com->id)) SELECTED @endif
        >Company: {{ $com->name }}</option>
    @forelse ($com->facs as $j => $fac)
        <option value="F{{ $fac->id }}"
            @if ($psComOwner == ('F' . $fac->id)) SELECTED @endif
            > - Facility: {{ $fac->name }}</option>
    @empty
    @endforelse
@empty
@endforelse
</select>
<div id="changePsFacilityAlert" class="pull-right slBlueDark"></div>
<a id="changePsFacilityBtn" class="btn btn-secondary"
    href="javascript:;" >Save Ownership Change</a>

