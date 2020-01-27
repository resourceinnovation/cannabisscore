<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPowerScore extends Model
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
		'ps_climate_label',
		'ps_county', 
		'ps_state', 
		'ps_country', 
		'ps_ashrae', 
		'ps_email', 
		'ps_name', 
		'ps_characterize', 
		'ps_effic_overall', 
		'ps_effic_over_similar', 
		'ps_effic_facility', 
		'ps_effic_production', 
		'ps_effic_lighting', 
		'ps_effic_hvac', 
		'ps_effic_carbon', 
		'ps_effic_water', 
		'ps_effic_waste', 
		'ps_effic_facility_status', 
		'ps_effic_production_status', 
		'ps_effic_lighting_status', 
		'ps_effic_hvac_status', 
		'ps_effic_carbonStatus', 
		'ps_effic_water_status', 
		'ps_effic_waste_status', 
		'ps_grams', 
		'ps_kwh', 
		'ps_total_size', 
		'ps_total_canopy_size', 
		'ps_harvests_per_year', 
		'ps_harvest_batch', 
		'ps_has_water_pump', 
		'ps_cures_indoor', 
		'ps_cures_outdoor', 
		'ps_cures_offsite', 
		'ps_is_integrated', 
		'ps_source_utility', 
		'ps_source_renew', 
		'ps_other_power', 
		'ps_mother_loc', 
		'ps_processing_onsite', 
		'ps_source_utility_other', 
		'ps_vertical_stack', 
		'ps_upload_energy_bills', 
		'ps_energy_non_farm', 
		'ps_energy_non_farm_perc', 
		'ps_hvac_other', 
		'ps_heat_water', 
		'ps_extracting_onsite', 
		'ps_controls', 
		'ps_controls_auto', 
		'ps_ip_addy', 
		'ps_unique_str', 
		'ps_tree_version', 
		'ps_version_ab', 
		'ps_is_mobile', 
		'ps_submission_progress', 
		'ps_notes', 
		'ps_water_innovation', 
		'ps_green_waste_lbs', 
		'ps_green_waste_mixed', 
		'ps_compliance_waste_track', 
		'ps_lighting_error',
		'ps_crop',
		'ps_crop_other',
		'ps_onsite_type',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
