<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPublicProcess extends Model
{
    protected $table      = 'rii_public_process';
    protected $primaryKey = 'pub_prc_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'pub_prc_like_producers', 
		'pub_prc_like_producers_visual', 
		'pub_prc_raffle_prizes', 
		'pub_prc_prize_other', 
		'pub_prc_grower_other_value', 
		'pub_prc_feedback1', 
		'pub_prc_user_id', 
		'pub_prc_submission_progress', 
		'pub_prc_tree_version', 
		'pub_prc_version_ab', 
		'pub_prc_unique_str', 
		'pub_prc_ip_addy', 
		'pub_prc_is_mobile', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
