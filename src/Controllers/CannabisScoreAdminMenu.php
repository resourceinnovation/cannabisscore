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
        if ($this->currUser->hasRole('administrator|staff|databaser|brancher')) {
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-star"></i>', 1, [
                $this->admMenuLnk('/dash/compare-powerscores',  'Raw Score Data', '', 1, [
                    $this->admMenuLnk('/dash/compare-powerscores',              'Compare All Scores'),
                    $this->admMenuLnk('/dash/compare-powerscores?lighting=1',   'Raw Lighting Data'),
                    $this->admMenuLnk('/dash/cultivation-classic-final-report', 'Cultivation Classic')
                    ]),
                $this->admMenuLnk('/dash/founders-circle-report', 'Score Analysis', '', 1, [
                    $this->admMenuLnk('/dash/founders-circle-report',      'Founders Circle'),
                    $this->admMenuLnk('/dash/compare-powerscore-averages', 'Score Averages'),
                    $this->admMenuLnk('/dash/more-power-statistics',       'More Statistics'),
                    $this->admMenuLnk('/dash/powerscore-final-report',     'Written Report')
                    ]),
                $this->admMenuLnk('/dash/process-uploads', 'Process Uploads', '', 1, []),
                $this->admMenuLnk('/dash/manage-manufacturers', 'Manufacturers', '', 1, [
                    $this->admMenuLnk('/dash/manage-manufacturers', 'Manufacturers'),
                    $this->admMenuLnk('/dash/manage-lighting-models', 'Lighting Models')
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
        } elseif ($this->currUser->hasRole('partner')) {
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-star"></i>', 1, [
                $this->admMenuLnk('/dash/partner-compare-powerscores',         'Compare All Scores'),
                $this->admMenuLnk('/dash/partner-compare-powerscore-averages', 'Score Averages'),
                $this->admMenuLnk('/dash/founders-circle-report',              'Founders Circle'),
                $this->admMenuLnk('/dash/powerscore-final-report',             'Written Report')
                ]);
        }
        return $treeMenu;
    }
    
}