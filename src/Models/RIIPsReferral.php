<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsReferral extends Model
{
    protected $table      = 'rii_ps_referral';
    protected $primaryKey = 'ps_ref_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_ref_version_ab', 
		'ps_ref_submission_progress', 
		'ps_ref_ip_addy', 
		'ps_ref_tree_version', 
		'ps_ref_unique_str', 
		'ps_ref_user_id', 
		'ps_ref_is_mobile', 
		'ps_ref_powerscore', 
		'ps_ref_utility', 
		'ps_ref_address', 
		'ps_ref_email', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
