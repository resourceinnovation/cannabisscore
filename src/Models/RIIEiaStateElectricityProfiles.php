<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIEiaStateElectricityProfiles extends Model
{
    protected $table      = 'rii_eia_state_electricity_profiles';
    protected $primaryKey = 'eia_state_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'eia_state_year', 
		'eia_state_state', 
		'eia_state_sulfur_dioxide_lbs_mwh', 
		'eia_state_nitrogen_oxide_lbs_mwh', 
		'eia_state_carbon_dioxide_lbs_mwh', 
        'eia_state_avg_retail_price_cents_kwh', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
