<?php
namespace CannabisScore\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\SLDefinitions;

use CannabisScore\Controllers\CannabisScoreReport;
use SurvLoop\Controllers\AdminSubsController;

class CannabisScoreAdmin extends AdminSubsController
{
    public $classExtension = 'CannabisScoreAdmin';
    public $treeID         = 1;
    
    public function initPowerUser($uID = -3)
    {
        if ($this->v["uID"] <= 0 
            || !$this->v["user"]->hasRole('administrator|staff|databaser|brancher|partner')) {
            return redirect('/');
        }
        return [];
    }
    
    public function loadAdmMenu()
    {
        $treeMenu = [];
        if ($this->v["user"]->hasRole('administrator|staff|databaser|brancher')) {
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-star"></i>', 1, [
                $this->admMenuLnk('javascript:;',  'Raw Score Data', '', 1, [
                    $this->admMenuLnk('/dash/compare-powerscores',              'Compare All Scores'),
                    $this->admMenuLnk('/dash/compare-powerscores?review=1',     'Scores Under Review'),
                    $this->admMenuLnk('/dash/compare-powerscores?lighting=1',   'Raw Lighting Data'),
                    $this->admMenuLnk('/dash/cultivation-classic-final-report', 'Cultivation Classic'),
                    $this->admMenuLnk('/dash/nwpcc-import',                     'NWPCC Import')
                    ]),
                $this->admMenuLnk('javascript:;', 'Score Analysis', '', 1, [
                    $this->admMenuLnk('/dash/compare-powerscore-averages', 'Score Averages'),
                    $this->admMenuLnk('/dash/powerscore-final-report', 'Written Report')
                    ]),
                $this->admMenuLnk('/dash/process-uploads', 'Process Uploads', '', 1, []),
                $this->admMenuLnk('javascript:;', 'More Tools', '', 1, [
                    $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'Troubleshooting'),
                    $this->admMenuLnk('/dash/in-survey-feedback',                  'In-Survey Feedback'),
                    $this->admMenuLnk('/dash/powerscore-beta-feedback-surveys',    'Followup Survey'),
                    $this->admMenuLnk('/dash/export-emails', 'Emails Export')
                    ])
                ]);
            return $this->addAdmMenuBasics($treeMenu);
        } elseif ($this->v["user"]->hasRole('partner')) {
            $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-star"></i>', 1, [
                $this->admMenuLnk('/dash/partner-compare-powerscores',         'Compare All Scores'),
                $this->admMenuLnk('/dash/partner-compare-powerscore-averages', 'Score Averages'),
                $this->admMenuLnk('/dash/powerscore-final-report',             'Written Report')
                ]);
        }
        return $treeMenu;
    }
    
    protected function tweakAdmMenu($currPage = '')
    {
        if ($this->v["user"]->hasRole('partner')) {
            
        }
        return true; 
    }
    
    protected function initExtra(Request $request)
    {
        $this->CustReport = new CannabisScoreReport($request);
        
        if (!isset($this->v["currPage"])) $this->v["currPage"] = ['/dashboard', ''];
        if (trim($this->v["currPage"][0]) == '') $this->v["currPage"][0] = '/dashboard';
        $this->v["allowEdits"] = ($this->v["user"]->hasRole('administrator|staff'));
        
        $this->loadSysSettings();
        
        $this->v["management"] = ($this->v["user"]->hasRole('administrator|staff'));
        $this->v["volunOpts"] = 1;
        if ($this->REQ->session()->has('volunOpts')) {
            $this->v["volunOpts"] = $this->REQ->session()->get('volunOpts');
        }
        return true;
    }
    
    public function dashboardDefault(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('administrator|staff|databaser|brancher')) {
            if ($user->hasRole('volunteer')) {
                return redirect('/volunteer');
            }
            return redirect('/');
        }
        return redirect( '/dash/all-powerscores' );
    }
    
    protected function loadSearchSuggestions()
    {    
        $this->v["searchSuggest"] = [];
        return true;
    }
    
}
