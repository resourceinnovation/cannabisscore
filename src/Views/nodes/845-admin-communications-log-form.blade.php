@extends('vendor.survloop.master')
@section('content')
<!-- generated from resources/views/vendor/cannabisscore/nodes/845-admin-communications-log-form.blade.php -->
<form name="logCommForm" method="post" 
    action="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/ajadm/adm-comms?print=1">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="ps" value="{{ $ps }}">
<textarea class="form-control form-control-lg" style="height: 60px;" 
    name="logCommFld" id="logCommFldID"></textarea>
<input type="submit" class="btn btn-xs btn-secondary mT5" 
    value="Add New Communication">
</form>
@endsection