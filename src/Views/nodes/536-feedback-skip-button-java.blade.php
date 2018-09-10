/* generated from resources/views/vendor/cannabisscore/nodes/536-feedback-skip-button-java.blade.php */
function changeBtns() {
  document.getElementById('nFormNextBtn').value='Finish';
  @if (isset($psOwner) && intVal($psOwner) > 0)
    document.getElementById('nodeSubBtns').innerHTML='<a class="fL btn btn-secondary" href="/calculated/u-{{ $psOwner }}">No Thanks, Take Me To My PowerScore Report</a>'+document.getElementById('nodeSubBtns').innerHTML;
  @endif
}
setTimeout("changeBtns()", 100);