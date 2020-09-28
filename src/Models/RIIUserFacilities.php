<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserFacilities extends Model
{
    protected $table      = 'rii_user_facilities';
    protected $primaryKey = 'usr_fac_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'usr_fac_name', 
		'usr_fac_slug', 
		'usr_fac_count', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
