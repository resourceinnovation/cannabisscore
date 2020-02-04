<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPowerscore extends Model
{
    protected $table      = 'rii_powerscore';
    protected $primaryKey = 'ps_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_user_id', 
		'ps_status', 
		'ps_is_pro', 
		'ps_time_type', 
		'ps_year', 
		'ps_privacy', 
		'ps_zip_code', 
		'ps_county', 
		'ps_state', 
		'ps_country', 
		'ps_ashrae', 
		'ps_climate_label', 
		'ps_email', 
		'ps_name', 
		'ps_characterize', 
		'ps_effic_overall', 
		'ps_effic_over_similar', 
		'ps_effic_facility', 
		'ps_effic_production', 
		'ps_effic_lighting', 
		'ps_effic_hvac', 
		'ps_grams', 
		'ps_kwh', 
		'ps_total_size', 
		'ps_total_canopy_size', 
		'ps_harvests_per_year', 
		'ps_harvest_batch', 
		'ps_has_water_pump', 
		'ps_is_integrated', 
		'ps_source_utility', 
		'ps_source_renew', 
		'ps_other_power', 
		'ps_mother_loc', 
		'ps_source_utility_other', 
		'ps_vertical_stack', 
		'ps_upload_energy_bills', 
		'ps_energy_non_farm', 
		'ps_energy_non_farm_perc', 
		'ps_hvac_other', 
		'ps_heat_water', 
		'ps_controls', 
		'ps_controls_auto', 
		'ps_ip_addy', 
		'ps_unique_str', 
		'ps_tree_version', 
		'ps_version_ab', 
		'ps_is_mobile', 
		'ps_submission_progress', 
		'ps_notes', 
		'ps_green_waste_lbs', 
		'ps_green_waste_mixed', 
		'ps_compliance_waste_track', 
		'ps_grams_are_wet_weight', 
		'ps_mobile_racking', 
		'ps_crop', 
		'ps_crop_other', 
		'ps_effic_water', 
		'ps_effic_waste', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
