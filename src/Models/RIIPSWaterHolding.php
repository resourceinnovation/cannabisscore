<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSWaterHolding extends Model
{
    protected $table      = 'rii_ps_water_holding';
    protected $primaryKey = 'ps_wtr_hld_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_wtr_hld_psid', 
		'ps_wtr_hld_holding', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
