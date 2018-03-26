<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsReferral extends Model
{
    protected $table      = 'RII_PsReferral';
    protected $primaryKey = 'PsRefID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsRefVersionAB', 
		'PsRefSubmissionProgress', 
		'PsRefIPaddy', 
		'PsRefTreeVersion', 
		'PsRefUniqueStr', 
		'PsRefUserID', 
		'PsRefIsMobile', 
		'PsRefPowerScore', 
		'PsRefUtility', 
		'PsRefAddress', 
		'PsRefEmail', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
