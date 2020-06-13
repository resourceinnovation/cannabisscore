<!-- generated from resources/views/vendor/cannabisscore/inc-partner-ref-disclaim.blade.php -->
<?php /* <pre>{!! print_r($GLOBALS['SL']->x['usrInfo']) !!}</pre> */ ?>
@if ($GLOBALS["SL"]->x["usrInfo"]
    && (isset($GLOBALS['SL']->x['usrInfo']->slug)
        && trim($GLOBALS['SL']->x['usrInfo']->slug) != '')
    && (!isset($GLOBALS['SL']->x['usrInfo']->manufacturers)
        || sizeof($GLOBALS['SL']->x['usrInfo']->manufacturers) == 0))
    <p>
        This report includes PowerScores completed 
        through your custom referral link:<br />
        <a href="https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug 
            }}">https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug }}</a>
    </p>
@endif
