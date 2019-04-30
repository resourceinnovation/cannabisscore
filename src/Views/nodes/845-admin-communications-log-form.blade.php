@extends('vendor.survloop.master')
@section('content')
<!-- generated from resources/views/vendor/cannabisscore/nodes/845-admin-communications-log-form.blade.php -->
<form name="logCommForm" action="{{ $GLOBALS['SL']->sysOpts['app-url'] }}/ajadm/adm-comms?print=1" method="post">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="ps" value="{{ $ps }}">
<textarea class="form-control form-control-lg" name="logCommFld" id="logCommFldID" style="height: 60px;"></textarea>
<input type="submit" class="btn btn-xs btn-secondary mT5" value="Add New Communication">
</form>
<style> body { background: #F9FFF0; } </style>
@endsection