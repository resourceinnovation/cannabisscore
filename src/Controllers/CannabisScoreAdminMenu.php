<?php
/**
  * CannabisScoreAdminMenu is responsible for building the menu inside the dashboard area for all user types.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabis
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use RockHopSoft\Survloop\Controllers\Admin\AdminMenu;

class CannabisScoreAdminMenu extends AdminMenu
{
    public function loadAdmMenu($currUser = null, $currPage = '')
    {
        $this->currUser = $currUser;
        $this->currPage = $currPage;
        return $this->addAdmMenuBasics($this->loadAdmMenuAdmin());
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
                            '/dash/more-power-statistics',
                            'More Statistics'
                        ),
                        $this->admMenuLnk(
                            '/dash/basic-power-stats',
                            'Basic Stats'
                        )
                    ]
                )
            ]
        );
        return $treeMenu;
    }
    
}