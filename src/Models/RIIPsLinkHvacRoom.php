<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsLinkHvacRoom extends Model
{
    protected $table      = 'rii_ps_link_hvac_room';
    protected $primaryKey = 'ps_lnk_hv_rm_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ps_lnk_hv_rm_room_id', 
		'ps_lnk_hv_rm_hvac', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
