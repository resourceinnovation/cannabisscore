<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsLicenses extends Model
{
    protected $table      = 'rii_ps_licenses';
    protected $primaryKey = 'ps_lic_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_lic_psid', 
		'ps_lic_license', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
