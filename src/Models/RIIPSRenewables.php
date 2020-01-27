<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSRenewables extends Model
{
    protected $table      = 'rii_ps_renewables';
    protected $primaryKey = 'ps_rnw_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_rnw_psid', 
		'ps_rnw_renewable', 
		'ps_rnw_load_percent', 
		'ps_rnw_kwh', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
