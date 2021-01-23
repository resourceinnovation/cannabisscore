<!-- resources/views/vendor/cannabisscore/nodes/1449-ma-compliance-above-powerscore.blade.php -->

<div class="pT30">
    <h4>Massachusetts Energy and Water Report Completed</h4>
</div>
<div class="row mT30 mB30">
    <div class="col-3 taL">
        <img src="/cannabisscore/uploads/CannabisPowerscore-logo-md.png" 
            class="w100 mT15" border="0">
    </div>
    <div class="col-2 taC" style="font-size: 30px;">
        <i class="fa fa-arrow-right pT30" aria-hidden="true"></i>
    </div>
    <div class="col-2 taC slBlueDark">
        <div style="font-size: 60px;">
            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
        </div>
        <div class="mTn15 w100 taC" style="font-size: 30px;">
            <nobr>You</nobr>
        </div>
    </div>
    <div class="col-2 taC" style="font-size: 30px;">
        <i class="fa fa-arrow-right pT30" aria-hidden="true"></i>
    </div>
    <div class="col-3 taR">
    <img src="https://mass-cannabis-control.com/wp-content/uploads/2018/03/CNB_logo_rgb_web-300x139.png" 
        class="w100 mT15" border="0">
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <h5 class="slBlueDark">
            Remember, this data is not being transferred to the CCC! 
            When you're ready to submit your, simply include the PDF 
            with the rest of your license renewal materials.
        </h5>
        <p>
            <a href="/ma-report/read-{{ $id }}/full-pdf?print=1&refresh=1" 
                class="btn btn-primary btn-xl btn-block" target="_blank"
                >MAKE MY PDF</a>
        </p>

        <p><br></p>
    @if ($GLOBALS["SL"]->REQ->has('changesToComply')
        && trim($GLOBALS["SL"]->REQ->changesToComply) == 'MA')
        <div class="alert alert-danger fade in alert-dismissible show">
            <h5 class="slBlueDark">
                Any changes made in the Pro survey were just 
                copied and saved to your Comply survey.
                Please click the button above to get an updated PDF.
            </h5>
        </div>
    @else
        <p><br></p>
        <h5 class="slBlueDark">
            You may have already saved your PDF in the Comply survey, 
            but if you made changes in the expanded Pro survey you 
            can save your PDF with your updated values here.
        </h5>
        <p>
            <a class="btn btn-secondary btn-xl btn-block"
                href="?changesToComply=MA#n1448">PowerScore Changes 
                <i class="fa fa-arrow-right mL15 mR15" aria-hidden="true"></i> 
                MA Comply</a>
        </p>
    @endif
    </div>
</div>

<p><br></p>
<p><br></p>
<h4>Your PowerScore Performance Benchmark Is Below</h4>
<hr>
