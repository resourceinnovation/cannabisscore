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
                    'Raw Score Data',
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
                            '/dash/manage-manufacturers', 
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
            'Raw Score Data', 
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
            '/dash/manage-manufacturers', 
            'Manufacturers', 
            '<i class="fa fa-lightbulb-o mL5" aria-hidden="true"></i>', 
            1, 
            [
                $this->admMenuLnk(
                    '/dash/competitive-performance',
                    'Competition Report'
                ),
                $this->admMenuLnk(
                    '/dash/manage-manufacturers',
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
        /*
        $treeMenu[] = $this->admMenuLnk(
            '/dash/competitive-performance', 
            'Competition', 
            '<i class="fa fa-bar-chart" aria-hidden="true"></i>'
        );
        */
        $treeMenu[] = $this->admMenuLnk(
            '/dash/partner-compare-official-powerscores', 
            'Official Data', 
            '<i class="fa fa-list" aria-hidden="true"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/average-powerscores-lighting', 
            'Lighting Types', 
            '<i class="fa fa-bar-chart" aria-hidden="true"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/manufacturer-adoption', 
            'Manufacturer Use', 
            '<i class="fa fa-lightbulb-o mL5" aria-hidden="true"></i>'
        );
        $treeMenu[] = $this->admMenuLnk(
            '/dash/lighting-manufacturer-report', 
            'Manufacturer Stats', 
            '<i class="fa fa-lightbulb-o mL5" aria-hidden="true"></i>'
        );
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
    
}