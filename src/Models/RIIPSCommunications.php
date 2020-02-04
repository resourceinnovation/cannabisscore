<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsCommunications extends Model
{
    protected $table      = 'rii_ps_communications';
    protected $primaryKey = 'ps_com_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_com_psid', 
		'ps_com_user', 
		'ps_com_description', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
