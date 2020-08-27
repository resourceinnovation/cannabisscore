<!-- generated from resources/views/vendor/cannabisscore/inc-partner-ref-disclaim.blade.php -->

@if ($GLOBALS["SL"]->x["usrInfo"]
    && sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
    && isset($GLOBALS['SL']->x['usrInfo']->companies[0]->slug)
    && trim($GLOBALS['SL']->x['usrInfo']->companies[0]->slug) != ''
    && (!isset($GLOBALS['SL']->x['usrInfo']->companies[0]->manus)
        || sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->manus) == 0))
    <p>
        This report includes PowerScores completed 
        through your 
        @if (sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0
            && sizeof($GLOBALS['SL']->x['usrInfo']->companies[0]->facs) > 0)
            facility-specific links and your general
        @endif
        <nobr>company referral link:</nobr><br />
    @if (sizeof($GLOBALS['SL']->x['usrInfo']->companies) > 0)
        <a href="https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->companies[0]->slug
            }}">https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->companies[0]->slug }}</a>
    @elseif (isset($GLOBALS['SL']->x['usrInfo']->slug)
        && trim($GLOBALS['SL']->x['usrInfo']->slug) != '')
        <a href="https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug 
            }}">https://powerscore.resourceinnovation.org/start-for-{{ 
            $GLOBALS['SL']->x['usrInfo']->slug }}</a>
    @endif
    </p>
@endif
