<?php
/**
  * ScorePartners is a mid-level extension of the SurvLoop class, TreeSurvForm.
  * This class contains the majority of processes which generate things
  * specific to Partner users.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace CannabisScore\Controllers;

use CannabisScore\Controllers\ScoreAdminMisc;

class ScorePartners extends ScoreAdminMisc
{
    
    public function printPartnerProfileDashBtn($nID)
    {
        $usrInfo = null;
        if (isset($this->v["usrInfo"])) {
            $usrInfo = $this->v["usrInfo"];
        }
        return view(
            'vendor.cannabisscore.nodes.1039-partner-my-profile', 
            [
                'company' => $this->getPartnerCompany(),
                'usrInfo' => $usrInfo
            ]
        )->render();
    }
    
    public function printPartnerProfileDashHead($nID)
    {
        $title = 'Partner Members Dashboard';
        $company = $this->getPartnerCompany();
        if (trim($company) != '') {
            $title = $company . ' PowerScore Dashboard';
        }
        $usrInfo = null;
        if (isset($this->v["usrInfo"])) {
            $usrInfo = $this->v["usrInfo"];
        }
        return view(
            'vendor.cannabisscore.nodes.1040-partner-dashboard', 
            [
                'usrInfo' => $usrInfo,
                'company' => $company,
                'title'   => $title
            ]
        )->render();
    }

}
