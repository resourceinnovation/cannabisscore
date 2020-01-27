<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSRanks extends Model
{
    protected $table      = 'rii_ps_ranks';
    protected $primaryKey = 'ps_rnk_id';
    public $timestamps    = true;
    protected $fillable   = 
    [
        'ps_rnk_manu_id',
		'ps_rnk_filters', 
		'ps_rnk_tot_cnt', 
		'ps_rnk_overall_avg', 
		'ps_rnk_facility', 
		'ps_rnk_production', 
		'ps_rnk_hvac', 
		'ps_rnk_lighting', 
        'ps_rnk_water',
        'ps_rnk_waste',
		'ps_rnk_avg_sqft_kwh',
		'ps_rnk_avg_sqft_grm', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
