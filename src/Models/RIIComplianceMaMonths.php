<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIComplianceMaMonths extends Model
{
    protected $table      = 'rii_compliance_ma_months';
    protected $primaryKey = 'com_ma_month_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'com_ma_month_com_ma_id', 
		'com_ma_month_month', 
		'com_ma_month_kwh', 
		'com_ma_month_kw', 
		'com_ma_month_renew_kwh', 
		'com_ma_month_natural_gas_therms', 
		'com_ma_month_diesel_gallons', 
		'com_ma_month_biofuel_wood_tons', 
		'com_ma_month_propane', 
		'com_ma_month_fuel_oil', 
		'com_ma_month_water', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
