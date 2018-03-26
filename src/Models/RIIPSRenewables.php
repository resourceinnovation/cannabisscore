<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSRenewables extends Model
{
    protected $table      = 'RII_PSRenewables';
    protected $primaryKey = 'PsRnwID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsRnwPSID', 
		'PsRnwRenewable', 
		'PsRnwLoadPercent', 
		'PsRnwKWH', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
