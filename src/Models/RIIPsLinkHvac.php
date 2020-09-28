<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsLinkHvac extends Model
{
    protected $table      = 'rii_ps_link_hvac';
    protected $primaryKey = 'ps_lnk_hv_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_lnk_hv_psid', 
		'ps_lnk_hv_hvac', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
