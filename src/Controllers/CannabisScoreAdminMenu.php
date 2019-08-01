<?php
/**
  * CannabisScoreAdminMenu is responsible for building the menu inside the dashboard area for all user types.
  *
  * Open Police Complaints
  * @package  flexyourrights/openpolice
  * @author  Morgan Lesko <wikiworldorder@protonmail.com>
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

            $treeMenu[] = $this->admMenuLnk('/dashboard', 'Dashboard', '<i class="fa fa-home"></i>');
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-tachometer" aria-hidden="true"></i>', 1, [
                $this->admMenuLnk('/dash/compare-powerscores',  'Raw Score Data', '', 1, [
                    $this->admMenuLnk('/dash/compare-powerscores',              'Compare All Scores'),
                    $this->admMenuLnk('/dash/compare-powerscores?lighting=1',   'Raw Lighting Data'),
                    $this->admMenuLnk('/dash/cultivation-classic-final-report', 'Cultivation Classic'),
                    $this->admMenuLnk('/dash/powerscore-outliers',              'Outliers')
                    ]),
                $this->admMenuLnk('/dash/founders-circle-report', 'Score Analysis', '', 1, [
                    $this->admMenuLnk('/dash/founders-circle-report',       'Founders Circle'),
                    $this->admMenuLnk('/dash/compare-powerscore-averages',  'Score Averages'),
                    $this->admMenuLnk('/dash/average-powerscores-lighting', 'Lighting'),
                    $this->admMenuLnk('/dash/average-powerscores-hvac',     'HVAC'),
                    $this->admMenuLnk('/dash/more-power-statistics',        'More Statistics'),
                    $this->admMenuLnk('/dash/basic-power-stats',            'Basic Stats'),
                    $this->admMenuLnk('/dash/powerscore-final-report',      'Written Report')
                    ]),
                $this->admMenuLnk('/dash/process-uploads', 'Process Uploads', '', 1, []),
                $this->admMenuLnk('/dash/manage-manufacturers', 'Manufacturers', '', 1, [
                    $this->admMenuLnk('/dash/competitive-performance', 'Competitive Performance'),
                    $this->admMenuLnk('/dash/manage-manufacturers',    'Manufacturers'),
                    $this->admMenuLnk('/dash/manage-lighting-models',  'Lighting Models')
                    ]),
                $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'More Tools', '', 1, [
                    $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'Troubleshooting'),
                    $this->admMenuLnk('/dash/in-survey-feedback',                  'In-Survey Feedback'),
                    $this->admMenuLnk('/dash/powerscore-beta-feedback-surveys',    'Followup Survey'),
                    $this->admMenuLnk('/dash/export-emails',                       'Emails Export'),
                    $this->admMenuLnk('/dash/nwpcc-import',                        'NWPCC Import')
                    ])
                ]);
            return $this->addAdmMenuBasics($treeMenu);

        } elseif ($this->currUser->hasRole('staff')) {

            $treeMenu[] = $this->addAdmMenuCollapse();
            $treeMenu[] = $this->admMenuLnk('/dashboard', 'Dashboard', '<i class="fa fa-home"></i>');
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'Raw Score Data', 
                '<i class="fa fa-tachometer" aria-hidden="true"></i>', 1, [
                $this->admMenuLnk('/dash/compare-powerscores',              'Compare All Scores'),
                $this->admMenuLnk('/dash/compare-powerscores?lighting=1',   'Raw Lighting Data'),
                $this->admMenuLnk('/dash/cultivation-classic-final-report', 'Cultivation Classic'),
                $this->admMenuLnk('/dash/powerscore-outliers',              'Outliers')
            ]);
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'Score Analysis', 
                '<i class="fa fa-table" aria-hidden="true"></i>', 1, [
                $this->admMenuLnk('/dash/founders-circle-report',       'Founders Circle'),
                $this->admMenuLnk('/dash/compare-powerscore-averages',  'Score Averages'),
                $this->admMenuLnk('/dash/average-powerscores-lighting', 'Lighting'),
                $this->admMenuLnk('/dash/average-powerscores-hvac',     'HVAC'),
                $this->admMenuLnk('/dash/more-power-statistics',        'More Statistics'),
                $this->admMenuLnk('/dash/basic-power-stats',            'Basic Stats'),
                $this->admMenuLnk('/dash/powerscore-final-report',      'Written Report')
            ]);
            $treeMenu[] = $this->admMenuLnk('/dash/process-uploads', 'Process Uploads', 
                '<i class="fa fa-upload" aria-hidden="true"></i>', 1, []);
            $treeMenu[] = $this->admMenuLnk('/dash/manage-manufacturers', 'Manufacturers', 
                '<i class="fa fa-lightbulb-o mL5" aria-hidden="true"></i>', 1, [
                $this->admMenuLnk('/dash/competitive-performance', 'Competition Report'),
                $this->admMenuLnk('/dash/manage-manufacturers',    'Manufacturers'),
                $this->admMenuLnk('/dash/manage-lighting-models',  'Lighting Models')
            ]);
            $treeMenu[] = $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'More Tools', 
                '<i class="fa fa-life-ring" aria-hidden="true"></i>', 1, [
                $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'Troubleshooting'),
                $this->admMenuLnk('/dash/in-survey-feedback',                  'In-Survey Feedback'),
                $this->admMenuLnk('/dash/powerscore-beta-feedback-surveys',    'Followup Survey'),
                $this->admMenuLnk('/dash/export-emails',                       'Emails Export'),
                $this->admMenuLnk('/dash/nwpcc-import',                        'NWPCC Import')
            ]);
            return $treeMenu;

        } elseif ($this->currUser->hasRole('partner')) {

            $treeMenu[] = $this->admMenuLnk('/dash/partner-members-dashboard', 'Dashboard', '<i class="fa fa-home"></i>');
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'My Scores', '<i class="fa fa-star"></i>', 1, [
                    $this->admMenuLnk('/dash/competitive-performance',             'Competition'),
                    $this->admMenuLnk('/dash/partner-compare-powerscores',         'Individual Scores'),
                    $this->admMenuLnk('/dash/partner-compare-powerscore-averages', 'My Averages'),
                    $this->admMenuLnk('/dash/founders-circle-report',              'My Analysis')
                ]);
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'All PowerScores', '<i class="fa fa-tachometer" aria-hidden="true"></i>', 1, [
                    $this->admMenuLnk('/dash/partner-compare-powerscores?all=1',         'Compare All Scores'),
                    $this->admMenuLnk('/dash/partner-compare-powerscore-averages?all=1', 'Score Averages'),
                    $this->admMenuLnk('/dash/founders-circle-report?all=1',              'Founders Circle'),
                    $this->admMenuLnk('/dash/powerscore-final-report',                   'Written Report')
                ]);

        }
        return $treeMenu;
    }
    
}