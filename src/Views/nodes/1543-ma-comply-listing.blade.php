<!-- resources/views/vendor/cannabisscore/nodes/1543-ma-comply-listing.blade.php -->

<div class="slCard mT30">
    <h2 class="mT0">MA Compliance Submissions</h2>


    <div class="row">
        <div class="col-1">
            <b>MA Comply ID#</b>
        </div>
        <div class="col-1">
            <b>Survey Status</b>
        </div>
        <div class="col-1">
            <b>Started Survey</b>
        </div>
        <div class="col-1">
            <b>Renewal Date</b>
        </div>
        <div class="col-3">
            <b>MA CCC Grower ID#, <br />Site User</b>
        </div>
        <div class="col-3">
            <b>PowerScore ID#, <br />Email</b>
        </div>
        <div class="col-1">
            <b>Production Efficiency (grams/kBtu)</b>
        </div>
    </div>
    @forelse ($recs as $i => $rec)
        <div class="p10 @if ($i%2 == 0) row2 @endif " >
            <div class="row">
                <div class="col-1">
                    <a href="/ma-report/read-{{ $rec->com_ma_id }}/full"
                        >{{ $rec->com_ma_id }}</a>
                    <a href="/ma-report/read-{{ $rec->com_ma_id }}/full" target="_blank"
                        ><i class="fa fa-external-link mL5" aria-hidden="true"></i></a>
                </div>
                <div class="col-1">
                    {{ $GLOBALS["SL"]->def->getVal(
                        'Compliance Status',
                        $rec->com_ma_status
                    ) }}
                </div>
                <div class="col-1">
                    {{ date(
                        "n/j", 
                        strtotime($rec->created_at)
                    ) }}
                </div>
                <div class="col-1">
                    {{ date(
                        "n/j", 
                        strtotime($rec->com_ma_renewal_application_date)
                    ) }}
                </div>
                <div class="col-3">
                    {{ $rec->com_ma_grower_id }}
                @if (isset($rec->com_ma_user_id) 
                    && intVal($rec->com_ma_user_id) > 0
                    && isset($users[$rec->com_ma_user_id]))
                    <br />{!! $users[$rec->com_ma_user_id] !!}
                @endif
                </div>
            @if (isset($rec->com_ma_ps_id) && intVal($rec->com_ma_ps_id) > 0)
                <div class="col-3">
                    <a href="/calculated/read-{{ $rec->com_ma_ps_id }}"
                        >{{ $rec->com_ma_ps_id }}</a>
                @if (isset($rec->ps_email) && trim($rec->ps_email) != '')
                    <br /><a href="mailto:{{ $rec->ps_email }}" class="fPerc80"
                        >{{ $rec->ps_email }}</a>
                @endif
                </div>
            @else
                <div class="col-3"><span class="slGrey">-</span></div>
            @endif
                <div class="col-1">
                @if (isset($rec->com_ma_effic_production) 
                    && $rec->com_ma_effic_production > 0)
                    {{ $GLOBALS["SL"]->sigFigs($rec->com_ma_effic_production) }}
                @else
                    <span class="slGrey">-</span>
                @endif
                </div>
            </div>
        </div>
    @empty
        No records found.
    @endforelse

</div>