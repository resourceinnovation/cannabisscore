<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSMonthly extends Model
{
    protected $table      = 'RII_PSMonthly';
    protected $primaryKey = 'PsMonthID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsMonthPSID', 
		'PsMonthMonth', 
		'PsMonthKWH1', 
		'PsMonthKWH2', 
		'PsMonthNotes', 
		'PsMonthOrder', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
