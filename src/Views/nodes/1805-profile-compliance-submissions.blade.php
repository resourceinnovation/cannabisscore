<!-- generated from resources/views/vendor/cannabisscore/nodes/1805-profile-compliance-submissions.blade.php -->
@if (($complies && $complies->isNotEmpty())
    || ($incompletes && $incompletes->isNotEmpty()))
    <div class="slCard nodeWrap">
        <h2 class="slBlueDark">MA Compliance</h2>
    @if ($complies && $complies->isNotEmpty())
        <p>
            Click the “Copy Into Fresh PowerScore” button for a deeper 
            understanding of how your facility’s use of energy and water 
            compares to other facilities throughout North America.
            This provides a head start in a fresh PowerScore.
        </p>
        @foreach ($complies as $comply)
            <?php if (!isset($comply->com_ma_grams_dry) && isset($comply->com_ma_grams)) {
                $comply->com_ma_grams_dry = $comply->com_ma_grams_dry;
            } ?>
            <div class="row mT15 mB30">
                <div class="col-lg-8">
                    <h5 class="mT0">
                        Electric Production Efficiency:
                    </h5>
                    @if (isset($comply->com_ma_grams_dry) 
                        && $comply->com_ma_grams_dry > 0
                        && isset($comply->com_ma_tot_kwh) 
                        && $comply->com_ma_tot_kwh > 0)
                        <h1 class="slBlueDark mT0">
                        {{ $GLOBALS["SL"]->sigFigs(
                            ($comply->com_ma_grams_dry/$comply->com_ma_tot_kwh), 
                            3
                        ) }}
                        </h1>
                        <h5 class="slBlueDark mT0"><nobr>grams / kWh</nobr></h5>
                    @else
                        <h5 class="slBlueDark mT0"><nobr>? grams / kWh</nobr></h5>
                    @endif
                </div>
                <div class="col-lg-4 pT5">
                <a href="/ma-report/read-{{ $comply->com_ma_id }}/full"
                    >MA Compliance #{{ $comply->com_ma_id }}</a><br />
                Started: {{ date('n/j/y', strtotime($comply->created_at)) }}<br />
                <a href="/start/calculator?new=1&go=pro&time=232&cpyMa={{ 
                    $comply->com_ma_id }}-{{ $comply->com_ma_unique_str }}" 
                    class="btn btn-sm btn-secondary mT10 mB30"
                    ><i class="fa fa-files-o" aria-hidden="true"></i>
                    Copy Into Fresh PowerScore</a>
                </div>
            </div>
        @endforeach
    @else
        <p>No completed compliance submissions found.</p>
    @endif

    @if ($incompletes && $incompletes->isNotEmpty())
            <div class="p15"></div>
            <h4>Incomplete Submissions</h4>
        @foreach ($incompletes as $comply)
            <?php if (!isset($comply->com_ma_grams_dry) && isset($comply->com_ma_grams)) {
                $comply->com_ma_grams_dry = $comply->com_ma_grams_dry;
            } ?>
            <div class="row mT15 mB30">
                <div class="col-lg-8">
                    <h5 class="m0">
                        Electric Production Efficiency:
                    </h5>
                    @if (isset($comply->com_ma_grams_dry) 
                        && intVal($comply->com_ma_grams_dry) > 0
                        && isset($comply->com_ma_tot_kwh) 
                        && intVal($comply->com_ma_tot_kwh) > 0)
                        <h1 class="slBlueDark mT0">
                        {{ $GLOBALS["SL"]->sigFigs(
                            ($comply->com_ma_grams_dry/$comply->com_ma_tot_kwh), 
                            3
                        ) }}
                        </h1>
                        <h5 class="slBlueDark mT0"><nobr>grams / kWh</nobr></h5>
                    @else
                        <h5 class="slBlueDark mT0"><nobr>? grams / kWh</nobr></h5>
                    @endif
                </div>
                <div class="col-lg-4 pT5">
                    <a href="/ma-report/read-{{ $comply->com_ma_id }}/full"
                        >MA Compliance #{{ $comply->com_ma_id }}</a><br />
                    Started: {{ date('n/j/y', strtotime($comply->created_at)) }}
                    <div class="mBn5">{!! view(
                        'vendor.survloop.forms.unfinished-record-row', 
                        [
                            "tree"    => 71,
                            "cID"     => $comply->com_ma_id,
                            "title"   => '', 
                            "desc"    => '<div class="mTn15"></div>',
                            "warning" => $warning
                        ]
                    )->render() !!}</div>
                    <a href="/start/calculator?new=1&go=pro&time=232&cpyMa={{ 
                        $comply->com_ma_id }}-{{ $comply->com_ma_unique_str }}" 
                        class="btn btn-sm btn-secondary mT10 mB30"
                        ><i class="fa fa-files-o" aria-hidden="true"></i>
                        Copy Into Fresh PowerScore</a>
                </div>
            </div>
        @endforeach
    @endif
    </div>
@endif
