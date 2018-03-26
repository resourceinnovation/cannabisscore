<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSGreenhouses extends Model
{
    protected $table      = 'RII_PSGreenhouses';
    protected $primaryKey = 'PsGrnID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsGrnPSID', 
		'PsGrnSize', 
		'PsGrnLightDep', 
		'PsGrnAreaType', 
		'PsGrnAreaTypeOther', 
		'PsGrnDaysCycle', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
