<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsHvac extends Model
{
    protected $table      = 'rii_ps_hvac';
    protected $primaryKey = 'ps_hvc_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_hvc_psid', 
		'ps_hvc_unit_type', 
		'ps_hvc_count', 
		'ps_hvc_size', 
		'ps_hvc_efficiency', 
		'ps_hvc_make', 
		'ps_hvc_model', 
		'ps_hvc_hours', 
		'ps_hvc_months', 
		'ps_hvc_unit_type_other', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
