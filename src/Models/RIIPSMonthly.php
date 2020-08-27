<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsMonthly extends Model
{
    protected $table      = 'rii_ps_monthly';
    protected $primaryKey = 'ps_month_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_month_psid', 
		'ps_month_month', 
		'ps_month_kwh1', 
		'ps_month_kw', 
		'ps_month_kwh_renewable', 
		'ps_month_natural_gas', 
		'ps_month_generator', 
		'ps_month_biofuel_wood', 
		'ps_month_propane', 
		'ps_month_fuel_oil', 
		'ps_month_water', 
		'ps_month_waste_lbs', 
		'ps_month_grams', 
		'ps_month_order', 
		'ps_month_water_storage_source', 
		'ps_month_water_storage_recirc', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
