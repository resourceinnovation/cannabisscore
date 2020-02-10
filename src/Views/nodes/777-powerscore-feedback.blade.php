<!-- generated from resources/views/vendor/cannabisscore/nodes/777-powerscore-feedback.blade.php -->
<div class="slCard nodeWrap">
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
<td>
    @if (isset($row->psf_feedback1))
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback1) !!}
    @endif
</td>
<td>
    @if (isset($row->psf_feedback2)) 
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback2) !!} 
    @endif 
</td>
<td> 
    @if (isset($row->psf_feedback3)) 
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback3) !!} 
    @endif 
</td>
<td> 
    @if (isset($row->psf_feedback4)) 
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback4) !!} 
    @endif 
</td>
<td> 
    @if (isset($row->psf_feedback5)) 
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback5) !!} 
    @endif 
</td>
<td> 
    @if (isset($row->psf_feedback6)) 
        {!! $GLOBALS["SL"]->plainLineBreaks($row->psf_feedback6) !!} 
    @endif 
</td>
<td>
    @if (isset($row->ps_effic_overall)) {{ round($row->ps_effic_overall) }}% @endif
</td>
<td>
    <a href="/calculated/u-{{ $row->psf_psid }}" target="_blank">#{{ $row->psf_psid }} 
        @if (isset($row->ps_name)) <br />{{ $row->ps_name }} @endif </a>
</td>
<td>
    @if (isset($row->created_at))
        {{ date("n/j/y g:ia", strtotime($row->created_at)) }}
    @endif
</td>
</tr>
@empty

@endforelse
</table>
</div>