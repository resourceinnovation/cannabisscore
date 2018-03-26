<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSHvac extends Model
{
    protected $table      = 'RII_PSHvac';
    protected $primaryKey = 'PsHvcID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsHvcPSID', 
		'PsHvcUnitType', 
		'PsHvcCount', 
		'PsHvcSize', 
		'PsHvcEfficiency', 
		'PsHvcMake', 
		'PsHvcModel', 
		'PsHvcHours', 
		'PsHvcMonths', 
		'PsHvcUnitTypeOther', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
