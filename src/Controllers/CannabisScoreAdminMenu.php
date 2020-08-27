<?php
/**
  * CannabisScoreAdminMenu is responsible for building the menu inside the dashboard area for all user types.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use Auth;
use App\Models\SLDefinitions;
use App\Models\RIIUserInfo;
use SurvLoop\Controllers\Admin\AdminMenu;

class CannabisScoreAdminMenu extends AdminMenu
{
    public function loadAdmMenu($currUser = null, $currPage = '')
    {
        $this->currUser = $currUser;
        $this->currPage = $currPage;
        $treeMenu = [];
        if ($this->currUser->hasRole('administrator|databaser|brancher')) {

            return $this->addAdmMenuBasics($this->loadAdmMenuAdmin());

        } elseif ($this->currUser->hasRole('staff')) {

            return $this->loadAdmMenuStaff();

        } elseif ($this->currUser->hasRole('partner')) {

            return $this->loadAdmMenuPartner();

        }
        return $treeMenu;
    }

    protected function loadAdmMenuAdmin()
    {
        $treeMenu = [];
        $treeMenu[] = $this->addAdmMenuHome();
        $treeMenu[] = $this->admMenuLnk(
            'javascript:;', 
            'PowerScores', 
            '<i class="fa fa-tachometer" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/compare-powerscores',
                    'Ranked Data Set',
                    '',
                    1,
                    [
                        $this->admMenuLnk(
                            '/dash/compare-powerscores',
                            'Ranked Data Set'
                        ),
                        $this->admMenuLnk(
                            '/dash/powerscore-outliers',
                            'Process Outliers', 
                        ),
                        $this->admMenuLnk(
                            '/dash/process-uploads', 
                            'Process Uploads'
                        ),
                        $this->admMenuLnk(
                            '/dash/raw-lighting-calculations',
                            'Raw Lighting Data'
                        ),
                        $this->admMenuLnk(
                            '/dash/cultivation-classic-final-report',
                            'Cultivation Classic'
                        )
                    ]
                ),
                $this->admMenuLnk(
                    '/dash/resource-benchmarking-report',
                    'Score Analysis', 
                    '', 
                    1, 
                    [
                        $this->admMenuLnk(
                            '/dash/resource-benchmarking-report',
                            'Resource Benchmarking'
                        ),
                        $this->admMenuLnk(
                            '/dash/compare-powerscore-averages',
                            'Score Averages'
                        ),
                        $this->admMenuLnk(
                            '/dash/average-powerscores-lighting',
                            'Lighting'
                        ),
                        $this->admMenuLnk(
                            '/dash/average-powerscores-hvac',
                            'HVAC'
                        ),
                        $this->admMenuLnk(
                            '/dash/more-power-statistics',
                            'More Statistics'
                        ),
                        $this->admMenuLnk(
                            '/dash/basic-power-stats',
                            'Basic Stats'
                        ),
                        $this->admMenuLnk(
                            '/dash/powerscore-final-report',
                            'Written Report'
                        )
                    ]
                ),
                $this->admMenuLnk(
                    '/dash/manage-partners',
                    'Manage Partners',
                    '', 
                    1, 
                    [
                        $this->admMenuLnk(
                            '/dash/manage-partners',
                            'Manage Partners',
                        ),
                        $this->admMenuLnk(
                            '/dash/manage-company-facilities', 
                            'Companies', 
                        ),
                        $this->admMenuLnk(
                            '/dash/manufacturer-adoption', 
                            'Manufacturers', 
                        ),
                        $this->admMenuLnk(
                            '/dash/manage-lighting-models',
                            'Lighting Models'
                        ),
                        $this->admMenuLnk(
                            '/dash/competitive-performance', 
                            'Competitive Performance'
                        )
                    ]
                ),
                $this->admMenuLnk(
                    '/dash/ma-comply-submissions',
                    'Compliance'
                ),
                $this->admMenuLnk(
                    '/dash/handbook', 
                    'More Tools', 
                    '', 
                    1, 
                    [
                        $this->admMenuLnk(
                            '/dash/handbook',
                            'Handbook'
                        ),
                        $this->admMenuLnk(
                            '/dash/powerscore-software-troubleshooting', 
                            'Troubleshooting'
                        ),
                        $this->admMenuLnk(
                            '/dash/in-survey-feedback',
                            'In-Survey Feedback'
                        ),
                        $this->admMenuLnk(
                            '/dash/powerscore-beta-feedback-surveys',
                            'Followup Survey'
                        ),
                        $this->admMenuLnk(
                            '/dash/export-emails',
                            'Emails Export'
                        )
                    ]
                )
            ]
        );
        return $treeMenu;
    }

    protected function loadAdmMenuStaff()
    {
        $treeMenu = [];
        $treeMenu[] = $this->addAdmMenuCollapse();
        $treeMenu[] = $this->addAdmMenuHome();
        $treeMenu[] = $this->admMenuLnk(
            'javascript:;', 
            'Ranked Data Set', 
            '<i class="fa fa-tachometer" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/compare-powerscores',
                    'Compare All Scores'
                ),
                $this->admMenuLnk(
                    '/dash/cultivation-classic-final-report',
                    'Cultivation Classic'
                ),
                $this->admMenuLnk(
                    '/dash/raw-lighting-calculations',
                    'Raw Lighting Data'
                ),
                $this->admMenuLnk(
                    '/dash/powerscore-outliers',
                    'Outliers'
                )
            ]
        );
        $treeMenu[] = $this->admMenuLnk(
            'javascript:;', 
            'Score Analysis', 
            '<i class="fa fa-table" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/resource-benchmarking-report',
                    'Resource Benchmarking'
                ),
                $this->admMenuLnk(
                    '/dash/compare-powerscore-averages',
                    'Score Averages'
                ),
                $this->admMenuLnk(
                    '/dash/average-powerscores-lighting',
                    'Lighting'
                ),
                $this->admMenuLnk(
                    '/dash/average-powerscores-hvac',
                    'HVAC'
                ),
                $this->admMenuLnk(
                    '/dash/more-power-statistics',
                    'More Statistics'
                ),
                $this->admMenuLnk(
                    '/dash/basic-power-stats',
                    'Basic Stats'
                ),
                $this->admMenuLnk(
                    '/dash/powerscore-final-report',
                    'Written Report'
                )
            ]
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/process-uploads',
            'Process Uploads', 
            '<i class="fa fa-upload" aria-hidden="true"></i>', 
            1, 
            []
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/manufacturer-adoption', 
            'Manufacturers', 
            '<i class="fa fa-lightbulb-o mL5" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/competitive-performance',
                    'Competition Report'
                ),
                $this->admMenuLnk(
                    '/dash/manufacturer-adoption',
                    'Manufacturers'
                ),
                $this->admMenuLnk(
                    '/dash/manage-lighting-models',
                    'Lighting Models'

                )
            ]
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/handbook', 
            'More Tools', 
            '<i class="fa fa-life-ring" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/handbook',
                    'Handbook'
                ),
                $this->admMenuLnk(
                    '/dash/powerscore-software-troubleshooting',
                    'Troubleshooting'
                ),
                $this->admMenuLnk(
                    '/dash/in-survey-feedback',
                    'In-Survey Feedback'
                ),
                $this->admMenuLnk(
                    '/dash/powerscore-beta-feedback-surveys',
                    'Followup Survey'
                ),
                $this->admMenuLnk(
                    '/dash/export-emails',
                    'Emails Export'
                ),
                $this->admMenuLnk(
                    '/dash/nwpcc-import',
                    'NWPCC Import'
                )
            ]
        );
        return $treeMenu;
    }

    protected function loadAdmMenuPartner()
    {
        $treeMenu = [];
        $treeMenu[] = $this->addAdmMenuCollapse();
        $treeMenu[] = $this->addAdmMenuHome();
        $treeMenu[] = $this->admMenuLnk(
            '/dash/partner-compare-powerscores', 
            'Individual Scores', 
            '<i class="fa fa-star"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/partner-compare-powerscore-averages', 
            'Score Averages', 
            '<i class="fa fa-area-chart" aria-hidden="true"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/competitive-performance', 
            'Competition', 
            '<i class="fa fa-bar-chart" aria-hidden="true"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/partner-compare-ranked-powerscores', 
            'Ranked Data', 
            '<i class="fa fa-list" aria-hidden="true"></i>'
        );
        if ($this->isPartnerSustainOrCorner()) {
            $treeMenu[] = $this->admMenuLnk(
                '/dash/average-powerscores-lighting', 
                'Lighting Types', 
                '<i class="fa fa-lightbulb-o mL3 mR3" aria-hidden="true"></i>'
            );
            if ($this->isPartnerSustaining()) {
                $treeMenu[] = $this->admMenuLnk(
                    '/dash/manufacturer-adoption', 
                    'Manufacturer Use', 
                    '<nobr><i class="fa fa-lightbulb-o mR3" aria-hidden="true"></i>'
                        . '<i class="fa fa-ellipsis-v mT3" aria-hidden="true"></i></nobr>'
                );
                $treeMenu[] = $this->admMenuLnk(
                    '/dash/lighting-manufacturer-report', 
                    'Manufacturer Stats', 
                    '<nobr><i class="fa fa-lightbulb-o mLn3 mR0" aria-hidden="true"></i>'
                        . '<i class="fa fa-line-chart fPerc66" aria-hidden="true"></i></nobr>'
                );
            }
        }
        /*
        $treeMenu[] = $this->admMenuLnk(
            'javascript:;', 
            'All PowerScores', 
            '<i class="fa fa-tachometer" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/compare-powerscores',
                    'Compare All Scores'
                ),
                $this->admMenuLnk(
                    '/dash/compare-powerscore-averages', 
                    'Score Averages'
                ),
                $this->admMenuLnk(
                    '/dash/resource-benchmarking-report',
                    'Resource Benchmarking'
                ),
                $this->admMenuLnk(
                    '/dash/powerscore-final-report',
                    'Written Report'
                )
            ]
        );
        */
        return $treeMenu;
    }

    private function isPartnerSustaining()
    {
        $def = SLDefinitions::where('def_set', 'Value Ranges')
            ->where('def_subset', 'Partner Levels')
            ->where('def_value', 'Sustaining Partner')
            ->first();
        $chk = RIIUserInfo::where('usr_user_id', Auth::user()->id)
            ->first();
        return ($chk 
            && $def
            && isset($chk->usr_level) 
            && isset($def->def_id)
            && $chk->usr_level == $def->def_id);
    }

    private function isPartnerCornerstone()
    {
        $def = SLDefinitions::where('def_set', 'Value Ranges')
            ->where('def_subset', 'Partner Levels')
            ->where('def_value', 'Cornerstone Partner')
            ->first();
        $chk = RIIUserInfo::where('usr_user_id', Auth::user()->id)
            ->first();
        return ($chk 
            && $def
            && isset($chk->usr_level) 
            && isset($def->def_id)
            && $chk->usr_level == $def->def_id);
    }

    private function isPartnerSustainOrCorner()
    {
        return ($this->isPartnerSustaining() && $this->isPartnerCornerstone());
    }
    
}