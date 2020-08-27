<!-- generated from resources/views/vendor/cannabisscore/nodes/502-utility-offers.blade.php -->
@if (isset($utilOffer) 
    && isset($utilOffer[0]) 
    && trim($utilOffer[0]) != '' 
    && (!$GLOBALS["SL"]->REQ->has("hidePromos") 
        || intVal($GLOBALS["SL"]->REQ->get("hidePromos")) != 1))
    <div class="row">
        <div class="col-8 pR20 pT20">
            <h5><i class="fa fa-star" aria-hidden="true"></i> {{ $utilOffer[0] }}
            may be able to offer help and incentives to make your grow more efficient, lowering your bills.</h4>
        </div><div class="col-4">
            <div class="pT10 pB10">
                <a href="{{ $utilOffer[1] }}" class="btn btn-primary btn-lg btn-xl" 
                    style="background: #6E9045; box-shadow: 0px 1px 10px #FFF; color: #FFF;"
                    >Send your report to <i>{{ $utilOffer[0] }}</i></a>
            </div>
        </div>
    </div>
@else
    <div class="fR w95">
        <h5 class="mTn5">
           Your facility may be eligible for upgrade incentives 
           from your regional utilities <nobr>and efficiency programs.</nobr>
        </h5>
        <p>
            We encourage you to contact your energy providers and local 
            efficiency programs to ask about incentives on efficient lighting, 
            HVAC and dehumidification, and controls technologies.
        </p>
    </div>
    <h5 class="mT0"><i class="fa fa-star" aria-hidden="true"></i></h5>
    <div class="fC"></div>
@endif
<style> #node501 { margin-top: 40px; } </style>
