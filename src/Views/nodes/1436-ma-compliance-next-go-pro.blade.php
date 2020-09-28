<!-- resources/views/vendor/cannabisscore/nodes/1436-ma-compliance-next-go-pro.blade.php -->

<h3 class="slBlueDark">Next, Calculate Your PowerScore™</h3>
<div class="row">
    <div class="col-lg-8">
        <h4>Want to learn more about your facility’s efficiency performance?</h4>
        <p>
            by 
            <a href="https://resourceinnovation.org/" target="_blank"
                >Resource Innovation Institute</a> — 
            <a href="https://cannabispowerscore.org/" target="_blank"
                >cannabispowerscore.org</a> 
        </p>
    </div>
    <div class="col-lg-4">
        <center>
        <img src="/cannabisscore/uploads/CannabisPowerscore-logo-dial.png" 
            class="mT30 mB30" border="0"
            style="width: 90%; max-width: 200px;" >
        </center>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <p>
            Click the “Benchmark My Performance” button for a deeper 
            understanding of how your facility’s use of energy and water 
            compares to other facilities throughout North America.
        </p>
        <a href="/start/calculator?new=1&go=pro&time=232&cpyMa={{ 
            $rec->com_ma_id }}-{{ $rec->com_ma_unique_str }}" 
            class="btn btn-primary btn-xl btn-block mT20 mB30"
            >BENCHMARK MY PERFORMANCE</a>
    </div>
    <div class="col-lg-4 taC">
    @if (isset($rec->com_ma_grams) 
        && $rec->com_ma_grams > 0
        && isset($rec->com_ma_tot_kwh) 
        && $rec->com_ma_tot_kwh > 0)
        <h5 class="mT0">
            Electric Production Efficiency:
        </h5>
        <h1 class="slBlueDark mT0">
            {{ $GLOBALS["SL"]->sigFigs(
                ($rec->com_ma_grams/$rec->com_ma_tot_kwh), 
                3
            ) }}
        </h1>
        <h5 class="slBlueDark mTn20">
            <nobr>grams / kWh</nobr>
        </h5>
    @endif
    </div>
</div>
