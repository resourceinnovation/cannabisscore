<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsWaterUses extends Model
{
    protected $table      = 'rii_ps_water_uses';
    protected $primaryKey = 'ps_wtr_use_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_wtr_use_ps_id', 
		'ps_wtr_use_how', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
