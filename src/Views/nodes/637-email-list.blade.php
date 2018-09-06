<!-- generated from resources/views/vendor/cannabisscore/nodes/637-email-list.blade.php -->
<div><br /></div>
<a id="hidivBtnAllEma" class="hidivBtn pull-right" href="javascript:;">All Email Addresses</a>
<h1 class="slBlueDark disIn">Send Bulk Email</h1>
<a href="/dashboard/emails" class="mL10">Manage Email Templates</a>

{!! $sendResults !!}

<form method="post" name="emailBlast" action="?wchLst={{ $wchLst }}&wchEma={{ $wchEma }}">
<input type="hidden" id="csrfTok" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="sub" value="1">
<input type="hidden" id="yesSendID" name="yesSend" value="0">

<table border=0 class="w100 h100"><tr><td class="w100 vaT">
    
    <div class="p10"></div>
    
    <select name="list" id="listID" class="form-control input-lg w100">
        <option value="all" @if (!isset($wchLst) || trim($wchLst) == '' || trim($wchLst) == 'all') SELECTED @endif 
            >Send to all email addresses of completed PowerScores</option>
        <option value="abv" @if (isset($wchLst) && trim($wchLst) == 'abv') SELECTED @endif 
            >Send to email addresses of Above Average completed PowerScores</option>
        <option value="avg" @if (isset($wchLst) && trim($wchLst) == 'avg') SELECTED @endif 
            >Send to email addresses of Average completed PowerScores</option>
        <option value="blw" @if (isset($wchLst) && trim($wchLst) == 'blw') SELECTED @endif 
            >Send to email addresses of Below Average completed PowerScores</option>
        <option value="inc" @if (isset($wchLst) && trim($wchLst) == 'inc') SELECTED @endif 
            >Send to email addresses who haven't fully completed their PowerScores</option>
    </select>
    
    <div class="p15"></div>
    
    <div class="row">
        <div class="col-md-8">
            <select name="ema" id="emaID" class="form-control input-lg w100">
                <option value="0" @if (!isset($wchEma) || intVal($wchEma) == 0) SELECTED @endif 
                    >Select an email template to send</option>
                {!! $GLOBALS["SL"]->loadEmailDropOpts($wchEma, 1) !!}
            </select>
            <div class="p10"></div>
            <div class="row">
                <div class="col-md-2 pT5">
                    Email From:
                </div>
                <div class="col-md-5">
                    <input type="text" name="replyTo" id="replyToID" class="form-control w100"
                        value="{!! 'info@' . $GLOBALS['SL']->getParentDomain() !!}" >
                </div>
                <div class="col-md-5">
                    <input type="text" name="replyName" id="replyNameID" class="form-control w100"
                        value="{!! $GLOBALS['SL']->sysOpts['site-name'] !!}" >
                </div>
            </div>
            
            <div id="testSendLoading" class="w100 disNon taC">{!! $GLOBALS["SL"]->sysOpts["spinner-code"] !!}</div>
            <div id="testSendResults" class="w100"></div>
        </div>
        <div class="col-md-4">
            <div class="row2 p20 mL20 mR20 mTn10">
                First, Send A Test Email To: <input type="text" name="testEmail" id="testEmailID" class="form-control" 
                    @if ($user && isset($user->email)) value="{{ $user->email }}" @endif >
                <a id="sendTestBtn" class="btn btn-info w100 mT10" href="javascript:;">Send Test Email</a>
            </div>
        </div>
    </div>
    
    <div class="p10"></div>
    
    <div class="row">
        <div class="col-md-8">
        @if (isset($scoreLists[$wchLst]) && sizeof($scoreLists[$wchLst]) > 0)
            <input type="checkbox" name="scoreAll" value="{{ sizeof($scoreLists[$wchLst]) }}" class="mR10" 
                onClick="checkBoxAll('score', this.value, this.checked);" autocomplete="off"
                CHECKED > Found {{ sizeof($scoreLists[$wchLst]) }} records:
            <table class="table table-striped">
            @foreach ($scoreLists[$wchLst] as $i => $scr)
                <tr>
                <td><input type="checkbox" id="score{{ $i }}" name="scores[]" value="{{ $scr['id'] }}" CHECKED ></td>
                <td><a href="/calculated/u-{{ $scr['id'] }}">#{{ $scr["id"] }}</a></td>
                <td>{{ round($scr["score"]) }}%</td>
                <td>{{ $scr["email"] }}</td>
                <td>{{ $scr["farm"] }}</td>
                </tr>
            @endforeach
            </table>
        @endif
        </div>
        <div class="col-md-4 pT20">
            <a id="hidivBtnSendChk" class="btn btn-primary btn-xl w100 hidivBtn" href="javascript:;">Send Bulk Email</a>
            <div id="hidivSendChk" class="disNon pT20 red">
                <i class="red fPerc133">Please confirm you want to bulk send...</i><br />
                <a id="sendCnfmBtn" class="btn btn-primary btn-xl w100" href="javascript:;">Yes, SEND IT</a>
            </div>
        </div>
    </div>
    
</td><td class="vaT">
    <div id="hidivAllEma" class="disNon pL20" style="min-width: 400px;">
    @if (isset($emailList) && sizeof($emailList) > 0)
        <table class="table table-striped">
        @foreach ($emailList as $state => $stateList)
            <tr><td><h2> @if (trim($state) != '') {{ $state }} @else ? @endif </h2></td><td>
                <textarea class="w100 flexarea">{!! implode(', ', $stateList) !!}</textarea>
            </td></tr>
        @endforeach
        </table>
    @else <i>No email addresses found.</i>
    @endif
    </div>
</td></tr></table>

</form>