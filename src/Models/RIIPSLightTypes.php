<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSLightTypes extends Model
{
    protected $table      = 'rii_ps_light_types';
    protected $primaryKey = 'ps_lg_typ_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_lg_typ_area_id', 
		'ps_lg_typ_light', 
		'ps_lg_typ_count', 
		'ps_lg_typ_wattage', 
		'ps_lg_typ_hours', 
		'ps_lg_typ_make', 
		'ps_lg_typ_model', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
