<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSMonthly extends Model
{
    protected $table      = 'rii_ps_monthly';
    protected $primaryKey = 'ps_month_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_month_psid', 
		'ps_month_month', 
		'ps_month_kwh1', 
		'ps_month_grams', 
		'ps_month_waste_lbs', 
		'ps_month_order', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
