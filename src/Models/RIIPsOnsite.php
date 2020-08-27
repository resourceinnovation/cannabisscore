<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsOnsite extends Model
{
    protected $table      = 'rii_ps_onsite';
    protected $primaryKey = 'ps_on_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_on_psid', 
		'ps_on_room_cnt', 
		'ps_on_commercial', 
		'ps_on_tissue_lab', 
		'ps_on_nursery', 
		'ps_on_starts_seeds', 
		'ps_on_storage', 
		'ps_on_extracting', 
		'ps_on_post_extraction', 
		'ps_on_processing', 
		'ps_on_packaging', 
		'ps_on_manu_product', 
		'ps_on_manu_package', 
		'ps_on_other', 
		'ps_on_cures_indoor', 
		'ps_on_cures_outdoor', 
		'ps_on_cures_offsite', 
		'ps_on_onsite_type', 
		'ps_on_utility_renewable', 
		'ps_on_produce_renewable', 
		'ps_on_produce_renewable_other', 
		'ps_on_natural_gas_utility_name', 
		'ps_on_water_by_months', 
		'ps_on_water_treat_source', 
		'ps_on_water_review_before', 
		'ps_on_water_recirc_treat', 
		'ps_on_water_review_before_recirc', 
		'ps_on_water_measure_method', 
		'ps_on_water_measure_method_other', 
		'ps_on_water_source_store_indoor', 
		'ps_on_water_recirc_store_indoor', 
		'ps_on_water_source_above_ground', 
		'ps_on_water_recirc_above_ground', 
		'ps_on_water_source_centralized', 
		'ps_on_water_recirc_centralized', 
		'ps_on_water_perc_cultivation', 
		'ps_on_water_recirc', 
		'ps_on_water_store_source',
		'ps_on_water_store_recirc',
		'ps_on_any_renewable',
		'ps_on_any_delivered_fuels',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
