<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsLightTypes extends Model
{
    protected $table      = 'rii_ps_light_types';
    protected $primaryKey = 'ps_lg_typ_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_lg_typ_psid', 
		'ps_lg_typ_area_id', 
        'ps_lg_typ_room_id',
		'ps_lg_typ_light', 
		'ps_lg_typ_count', 
		'ps_lg_typ_wattage', 
		'ps_lg_typ_hours', 
		'ps_lg_typ_make', 
		'ps_lg_typ_model', 
        'ps_lg_typ_complete',
        'ps_lg_typ_days_per_year',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
