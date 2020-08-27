<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsRankings extends Model
{
    protected $table      = 'rii_ps_rankings';
    protected $primaryKey = 'ps_rnk_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_rnk_psid', 
		'ps_rnk_manu_id', 
		'ps_rnk_filters', 
		'ps_rnk_overall', 
		'ps_rnk_overall_avg', 
		'ps_rnk_facility', 
        'ps_rnk_fac_non',
        'ps_rnk_fac_all',
		'ps_rnk_production', 
        'ps_rnk_prod_non',
        'ps_rnk_prod_all',
		'ps_rnk_hvac', 
		'ps_rnk_lighting', 
		'ps_rnk_water', 
		'ps_rnk_waste', 
		'ps_rnk_tot_cnt', 
		'ps_rnk_facility_cnt', 
        'ps_rnk_fac_non_cnt',
        'ps_rnk_fac_all_cnt',
		'ps_rnk_production_cnt', 
        'ps_rnk_prod_non_cnt',
        'ps_rnk_prod_all_cnt',
		'ps_rnk_hvac_cnt', 
		'ps_rnk_lighting_cnt', 
		'ps_rnk_water_cnt', 
		'ps_rnk_waste_cnt', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
