<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIILightModels extends Model
{
    protected $table      = 'rii_light_models';
    protected $primaryKey = 'lgt_mod_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'lgt_mod_manu_id', 
		'lgt_mod_name', 
		'lgt_mod_tech', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
