<?php
namespace CannabisScore\Controllers;

use CannabisScore\Controllers\CannabisScore;

class CannabisScoreReport extends CannabisScore
{
    
    public $classExtension  = 'CannabisScoreReport';
    public $treeID          = 1;
    protected $isReport     = true;
    public $hideDisclaim    = false;
    
    
    public function prepReport()
    {
        return true;
    }
    
    public function printPreviewReport($isAdmin = false)
    {
        $this->prepReport();
        return view('vendor.cannabisscore.powerscore-report-preview', [
            "uID"      => $this->v["uID"],
            "sessData" => $this->sessData->dataSets 
        ]);
    }
    
}
