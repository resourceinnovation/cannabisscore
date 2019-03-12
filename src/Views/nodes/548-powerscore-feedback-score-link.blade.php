<!-- generated from resources/views/vendor/cannabisscore/nodes/548-powerscore-feedback-score-link.blade.php -->
<p>&nbsp;</p>
@if (isset($psOwner) && intVal($psOwner) > 0)
    <a href="/calculated/u-{{ $psOwner }}" class="btn btn-lg btn-xl btn-danger w100" style="padding: 100px 20px;"
        >Click Here<br /><nobr>to see how</nobr> <nobr>you stack up...</nobr><br /><br />
        PowerScore <nobr>Report #{{ $psOwner }}</nobr><br /></a>
@endif
<style> #node544, #debugPop, #debugPop2 { display: none; } </style>
<script type="text/javascript">
function chkPageLoadIssue() {
    if (!document.getElementById('myNavBar')) {
        window.location="/u/powerscore-feedback/thank-you";
    } else {
        document.getElementById('node544').style.display='block';
    }
    return true;
}
setTimeout("chkPageLoadIssue()", 1);
</script>