<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsAreas extends Model
{
    protected $table      = 'rii_ps_areas';
    protected $primaryKey = 'ps_area_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_area_psid', 
		'ps_area_type', 
		'ps_area_has_stage', 
		'ps_area_size', 
		'ps_area_days_cycle', 
		'ps_area_lgt_sun', 
		'ps_area_lgt_dep', 
		'ps_area_lgt_artif', 
		'ps_area_vert_stack', 
		'ps_area_lgt_pattern', 
		'ps_area_sq_ft_per_fix2', 
		'ps_area_calc_size', 
		'ps_area_calc_watts', 
		'ps_area_total_light_watts', 
		'ps_area_lighting_effic', 
		'ps_area_hvac_type', 
		'ps_area_hvac_other', 
		'ps_area_hvac_effic', 
		'ps_area_gallons', 
		'ps_area_watering_method', 
		'ps_area_water_freq', 
		'ps_area_water_effic', 
		'ps_area_temperature', 
		'ps_area_temperature_max', 
		'ps_area_humidity', 
		'ps_area_humidity_max', 
		'ps_area_commissioning', 
		'ps_area_lpd', 
		'ps_area_wst_wtr_method', 
		'ps_area_how_many_plants_watered', 
		'ps_area_water_give_unknown', 
		'ps_area_water_give_max', 
		'ps_area_water_give_min', 
		'ps_area_water_drain_unknown', 
		'ps_area_water_drain_max', 
		'ps_area_water_drain_min', 
		'ps_area_water_recirc_unknown', 
		'ps_area_water_no_recirc', 
		'ps_area_water_recirc_max', 
		'ps_area_water_recirc_min', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
