<!-- generated from resources/views/vendor/cannabisscore/inc-partner-ref-disclaim.blade.php -->

@if ($GLOBALS["SL"]->x["usrInfo"]
    && isset($GLOBALS['SL']->x['usrInfo']->slug)
    && trim($GLOBALS['SL']->x['usrInfo']->slug) != '')
    <p>
        This report includes PowerScores completed 
        through your custom referral link:<br />
        <a href="https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug 
            }}">https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug }}</a>
    </p>
@endif
