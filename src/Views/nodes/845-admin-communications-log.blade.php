<!-- generated from resources/views/vendor/cannabisscore/nodes/845-admin-communications-log.blade.php -->
<div class="mTn10">
@forelse ($comms as $i => $com)
    <div class="brdBot pT10 pB10">
        {!! str_replace("\n", "<br />", $com->PsComDescription) !!}<br />
        - {!! str_replace('a href=', 'a style="color: #000;" href=', $adms[$com->PsComUser]) !!},
        {{ date("g:ia n/j/y", strtotime($com->created_at)) }}
    </div>
@empty
    <i class="slGrey">Nothing logged yet.</i>
@endif
</div>

<iframe src="<?= $GLOBALS['SL']->sysOpts['app-url'] ?>/ajadm/adm-comms?ps={{ $ps }}&print=1" 
    frameborder=0 class="w100" style="min-height: 150px;" ></iframe>
