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
    public $classExtension     = 'CannabisScoreAdmin';
    public $treeID             = 1;
    
    public function initPowerUser($uID = -3)
    {
        if ($this->v["uID"] <= 0 || !$this->v["user"]->hasRole('administrator|staff|databaser|brancher|volunteer')) {
            return redirect('/');
        }
        return [];
    }
    
    public function loadAdmMenu()
    {
        $treeMenu = [];
        $treeMenu[] = $this->admMenuLnk('javascript:;', 'PowerScores', '<i class="fa fa-star"></i>', 1, [
            $this->admMenuLnk('/dash/compare-powerscores',  'Compare All Scores', '', 1, [
                $this->admMenuLnk('/dash/compare-powerscore-averages', 'Score Averages')
                ]),
            $this->admMenuLnk('/dash/all-powerscore-submissions', 'All Submissions', '', 1, [
                $this->admMenuLnk('/dash/all-powerscores', 'Complete'),
                $this->admMenuLnk('/dash/incomplete-powerscores',  'Incomplete')
                ]),
            $this->admMenuLnk('/dash/all-cultivation-classic-report', 'All Cultivation Classic', '', 1, [
                $this->admMenuLnk('/dash/cultivation-classic-powerscores',  'Complete'),
                $this->admMenuLnk('/dash/incomplete-cultivation-classic',   'Incomplete'),
                $this->admMenuLnk('/dash/cultivation-classic-final-report', 'Final Report'),
                ]),
            $this->admMenuLnk('/dash/all-emerald-cup-report', 'All Emerald Cup', '', 1, [
                $this->admMenuLnk('/dash/emerald-cup-powerscores', 'Complete'),
                $this->admMenuLnk('/dash/incomplete-emerald-cup',  'Incomplete')
                ]),
            //$this->admMenuLnk('/dash/manage-published-scores', 'Un-Publish Scores', '', 1, []),
            $this->admMenuLnk('/dash/process-uploads', 'Process Uploads', '', 1, []),
            $this->admMenuLnk('/dash/powerscore-software-troubleshooting', 'Troubleshooting', '', 1, []),
            $this->admMenuLnk('/dash/powerscore-beta-feedback-surveys', 'Feedback Survey', '', 1, [])
            ]);
        return $this->addAdmMenuBasics($treeMenu);
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
