<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserInfo extends Model
{
    protected $table      = 'rii_user_info';
    protected $primaryKey = 'usr_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'usr_user_id', 
		'usr_company_name', 
        'usr_membership_expiration',
        'usr_level',
        'usr_invite_email',
        'usr_trial_start',
        'usr_referral_slug',
        'usr_manu_ids',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
