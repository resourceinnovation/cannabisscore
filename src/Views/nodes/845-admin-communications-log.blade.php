<!-- generated from resources/views/vendor/cannabisscore/nodes/845-admin-communications-log.blade.php -->
<div style="padding: 0px 40px 0px 40px;">
    <div style="margin-top: 27px;"><h4>Communications Log</h4></div>
    <p class="fPerc80"><span class="slGrey">
    Admins can log each significant communication with PowerScore users to track any important updates.
    </span></p>
    <div class="mTn10">
    @forelse ($comms as $i => $com)
        <div class="brdBot pT10 pB10">
            {!! str_replace("\n", "<br />", $com->PsComDescription) !!}<br />
            - {!! str_replace('a href=', 'a style="color: #000;" href=', $adms[$com->PsComUser]) !!},
            {{ date("g:ia n/j/y", strtotime($com->created_at)) }}
        </div>
    @empty
        <i class="slGrey fPerc80">Nothing logged yet.</i>
    @endif
    </div>
    <a id="hidivBtnLogComm" class="hidivBtnSelf btn btn-secondary mT10" href="javascript:;"
        ><i class="fa fa-plus mR5" aria-hidden="true"></i> Log New Communication</a>
    <div id="hidivLogComm" class="disNon p10"></div>
    <br /><br />
</div>
<script type="text/javascript">
function loadCommFrame() {
    if (document.getElementById('hidivLogComm')) {
        document.getElementById('hidivLogComm').innerHTML = '<iframe src="<?= $GLOBALS['SL']->sysOpts['app-url'] 
            ?>/ajadm/adm-comms?ps={{ $ps }}&print=1" frameborder=0 class="w100" ></iframe>';
    }
}
setTimeout("loadCommFrame()", 1000);
</script>