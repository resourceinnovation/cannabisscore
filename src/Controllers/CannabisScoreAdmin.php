<?php
namespace ResourceInnovation\CannabisScore\Controllers;

use Auth;
use Illuminate\Http\Request;
use ResourceInnovation\CannabisScore\Controllers\CannabisScore;
use RockHopSoft\Survloop\Controllers\Admin\AdminController;

class CannabisScoreAdmin extends AdminController
{
    protected function initExtra(Request $request)
    {
        $this->custReport = new CannabisScore($request);
        
        if (!isset($this->v["currPage"])) {
            $this->v["currPage"] = ['/dashboard', ''];
        }
        if (trim($this->v["currPage"][0]) == '') {
            $this->v["currPage"][0] = '/dashboard';
        }
        $this->v["allowEdits"] = ($this->v["user"]->hasRole('administrator|staff'));
        
        $this->loadSysSettings();
        
        $this->v["management"] = ($this->v["user"]->hasRole('administrator|staff'));
        $this->v["volunOpts"] = 1;
        if ($request->session()->has('volunOpts')) {
            $this->v["volunOpts"] = $request->session()->get('volunOpts');
        }
        return true;
    }
    
    protected function loadSearchSuggestions()
    {    
        $this->v["searchSuggest"] = [];
        return true;
    }
    
}
