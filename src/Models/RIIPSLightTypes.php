<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSLightTypes extends Model
{
    protected $table      = 'RII_PSLightTypes';
    protected $primaryKey = 'PsLgTypID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsLgTypAreaID', 
		'PsLgTypLight', 
		'PsLgTypCount', 
		'PsLgTypWattage', 
		'PsLgTypHours', 
		'PsLgTypMake', 
		'PsLgTypModel', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
