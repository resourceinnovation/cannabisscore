<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserPartnerActions extends Model
{
    protected $table      = 'rii_user_partner_actions';
    protected $primaryKey = 'usr_prt_act_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
        'usr_prt_act_user_id', 
        'usr_prt_act_type', 
        'usr_prt_act_amount',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
