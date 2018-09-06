<!-- generated from resources/views/vendor/cannabisscore/nodes/777-powerscore-feedback.blade.php -->

<h1 class="slBlueDark">PowerScore Feedback Survey</h1>
<a href="/start/powerscore-feedback" target="_blank">/start/powerscore-feedback</a>
<table border=0 class="table table-striped w100">
<tr>
<th>Time</th>
<th>Value</th>
<th>Changes</th>
<th>Edu Campaign</th>
<th>Anything Else</th>
<th>Overall Score</th>
<th>Which</th>
<th>Date</th>
</tr>
@forelse ($feedback as $i => $row)
<tr>
<td> @if (isset($row->PsfFeedback1)) {!! $GLOBALS["SL"]->plainLineBreaks($row->PsfFeedback1) !!} @endif </td>
<td> @if (isset($row->PsfFeedback2)) {!! $GLOBALS["SL"]->plainLineBreaks($row->PsfFeedback2) !!} @endif </td>
<td> @if (isset($row->PsfFeedback3)) {!! $GLOBALS["SL"]->plainLineBreaks($row->PsfFeedback3) !!} @endif </td>
<td> @if (isset($row->PsfFeedback4)) {!! $GLOBALS["SL"]->plainLineBreaks($row->PsfFeedback4) !!} @endif </td>
<td> @if (isset($row->PsfFeedback5)) {!! $GLOBALS["SL"]->plainLineBreaks($row->PsfFeedback5) !!} @endif </td>
<td> @if (isset($row->PsEfficOverall)) {{ round($row->PsEfficOverall) }}% @endif </td>
<td><a href="/calculated/u-{{ $row->PsfPsID }}" target="_blank">#{{ $row->PsfPsID }} 
    @if (isset($row->PsName)) <br />{{ $row->PsName }} @endif </a></td>
<td> @if (isset($row->created_at)) {{ date("n/j g:ia", strtotime($row->created_at)) }} @endif </td>
</tr>
@empty

@endforelse

</table>