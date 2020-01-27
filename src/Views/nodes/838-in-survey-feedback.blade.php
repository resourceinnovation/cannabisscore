<!-- generated from resources/views/vendor/cannabisscore/nodes/838-in-survey-feedback.blade.php -->
<div id="blockWrap{{ $nID }}" class="w100" style="overflow: visible;">
<div class="container" id="treeWrap{{ $nID }}">
<div class="fC"></div>
<div class="nodeAnchor"><a id="n{{ $nID }}" name="n{{ $nID }}"></a></div>
<div class="slCard nodeWrap">
    <h1 class="mB0">In-Survey Feedback</h1>
</div>

@forelse ($feedbackPages as $p => $page)
    @if (trim($page) != '')
    <div class="slCard nodeWrap">
        <h3 class="mT0 slBlueDark">
            Feedback on Page {{ (1+$p) }}: {{ $feedbackPName[$p] }}
        </h3>
        {!! $page !!}
    </div>
    @endif
@empty @endforelse

</div>
</div>