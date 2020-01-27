<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSOwners extends Model
{
    protected $table      = 'rii_ps_owners';
    protected $primaryKey = 'ps_own_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_own_partner_user', 
		'ps_own_client_user', 
		'ps_own_type', 
		'ps_own_client_name', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
