<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserCompanies extends Model
{
    protected $table      = 'rii_user_companies';
    protected $primaryKey = 'usr_com_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'usr_com_name', 
		'usr_com_slug', 
		'usr_com_count', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
