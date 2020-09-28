<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsFeedback extends Model
{
    protected $table      = 'rii_ps_feedback';
    protected $primaryKey = 'psf_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'psf_version_ab', 
		'psf_submission_progress', 
		'psf_ip_addy', 
		'psf_tree_version', 
		'psf_unique_str', 
		'psf_user_id', 
		'psf_is_mobile', 
		'psf_psid', 
		'psf_feedback1', 
		'psf_feedback2', 
		'psf_feedback3', 
		'psf_feedback4', 
		'psf_feedback5', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
