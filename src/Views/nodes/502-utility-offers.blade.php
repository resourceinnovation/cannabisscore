<!-- generated from resources/views/vendor/cannabisscore/nodes/502-utility-offers.blade.php -->
@if (isset($utilOffer) && isset($utilOffer[0]) && trim($utilOffer[0]) != '' 
    && (!$GLOBALS["SL"]->REQ->has("hidePromos") || intVal($GLOBALS["SL"]->REQ->get("hidePromos")) != 1))
    <div class="row">
        <div class="col-md-8 pR20 pT20">
            <h3><i class="fa fa-star" aria-hidden="true"></i> <span style="color: #000;">{{ $utilOffer[0] }}</span> 
            may be able to offer help and incentives to make your grow more efficient, lowering your bills.</h3>
        </div><div class="col-md-4">
            <div class="pT10 pB10">
                <a href="{{ $utilOffer[1] }}" class="btn btn-primary btn-xl" 
                    style="background: #6E9045; box-shadow: 0px 1px 10px #FFF; color: #FFF;"
                    >Send your report to <i>{{ $utilOffer[0] }}</i></a>
            </div>
        </div>
    </div>
    <style> #node501 { margin: 40px 0px; } </style>
@else
    <h2>
    <i class="fa fa-star" aria-hidden="true"></i> 
    Your farm may be eligible for upgrade incentives from your utility.
    </h2>
    <h3>
    We encourage you to contact your electricity provider to ask about incentives on efficient lighting and HVAC.
    </h3>
    <style> #node501 { margin: 40px 0px; } </style>
@endif