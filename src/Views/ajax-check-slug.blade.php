<!-- generated from resources/views/vendor/cannabisscore/ajax-check-slug.blade.php -->
@if ($slugFound)

    <div class="pT5">
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-exclamation-triangle mR5" aria-hidden="true"></i>
            <b>This URL has already been taken. It must be unique.</b>
        </div>
    </div>
    <script type="text/javascript">
    function slugNotOk() {
        document.getElementById("addPartnerBtn").disabled=true;
    }
    setTimeout("slugNotOk()", 10);
    </script>

@else

    <script type="text/javascript">
    function slugOk() {
        document.getElementById("addPartnerBtn").disabled=false;
    }
    setTimeout("slugOk()", 10);
    </script>

@endif