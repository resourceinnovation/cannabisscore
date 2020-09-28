<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserPsPerms extends Model
{
    protected $table      = 'rii_user_ps_perms';
    protected $primaryKey = 'usr_perm_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'usr_perm_user_id', 
		'usr_perm_permissions', 
		'usr_perm_company_id', 
		'usr_perm_facility_id', 
		'usr_perm_psid', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
