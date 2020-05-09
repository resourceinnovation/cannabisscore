<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIComplianceMa extends Model
{
    protected $table      = 'rii_compliance_ma';
    protected $primaryKey = 'com_ma_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'com_ma_user_id', 
		'com_ma_grower_id', 
		'com_ma_name', 
		'com_ma_year', 
		'com_ma_renewable_other', 
		'com_ma_postal_code', 
		'com_ma_grams', 
		'com_ma_tot_kwh', 
		'com_ma_tot_kw', 
		'com_ma_tot_renew', 
		'com_ma_tot_natural_gas', 
		'com_ma_tot_diesel', 
		'com_ma_tot_biofuel', 
		'com_ma_tot_fuel_oil', 
		'com_ma_tot_propane', 
		'com_ma_tot_water', 
		'com_ma_version_ab', 
		'com_ma_submission_progress', 
		'com_ma_ip_addy', 
		'com_ma_tree_version', 
		'com_ma_unique_str', 
		'com_ma_is_mobile', 
		'com_ma_no_natural_gas', 
		'com_ma_no_renewable_electricity', 
		'com_ma_unit_natural_gas', 
		'com_ma_unit_wood', 
		'com_ma_go_pro', 
		'com_ma_effic_production', 
		'com_ma_unit_generator', 
		'com_ma_ps_id', 
		'com_ma_include_renewables', 
		'com_ma_start_month', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
