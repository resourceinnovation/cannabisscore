<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSLighting extends Model
{
    protected $table      = 'RII_PSLighting';
    protected $primaryKey = 'PsLgtID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsLgtPSID', 
		'PsLgtType', 
		'PsLgtArea', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
