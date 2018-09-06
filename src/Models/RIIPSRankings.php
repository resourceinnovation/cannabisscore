<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSRankings extends Model
{
    protected $table      = 'RII_PSRankings';
    protected $primaryKey = 'PsRnkID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsRnkPSID', 
		'PsRnkFilters', 
		'PsRnkOverall', 
		'PsRnkOverallAvg', 
		'PsRnkFacility', 
		'PsRnkProduction', 
		'PsRnkHVAC', 
		'PsRnkLighting', 
		'PsRnkTotCnt', 
		'PsRnkFacilityCnt', 
		'PsRnkProductionCnt', 
		'PsRnkHVACCnt', 
		'PsRnkLightingCnt', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
