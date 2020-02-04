<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsWaterSources extends Model
{
    protected $table      = 'rii_ps_water_sources';
    protected $primaryKey = 'ps_wtr_src_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_wtr_src_psid', 
		'ps_wtr_src_source', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
