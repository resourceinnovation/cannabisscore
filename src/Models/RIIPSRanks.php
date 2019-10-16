<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSRanks extends Model
{
    protected $table      = 'RII_PSRanks';
    protected $primaryKey = 'PsRnkID';
    public $timestamps    = true;
    protected $fillable   = 
    [
        'PsRnkManuID',
		'PsRnkFilters', 
		'PsRnkTotCnt', 
		'PsRnkOverallAvg', 
		'PsRnkFacility', 
		'PsRnkProduction', 
		'PsRnkHVAC', 
		'PsRnkLighting', 
        'PsRnkWater',
        'PsRnkWaste',
		'PsRnkAvgSqftKwh',
		'PsRnkAvgSqftGrm', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
