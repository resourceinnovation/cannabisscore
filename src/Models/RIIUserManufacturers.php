<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIUserManufacturers extends Model
{
    protected $table      = 'rii_user_manufacturers';
    protected $primaryKey = 'usr_man_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'usr_man_company_id', 
		'usr_man_user_id', 
		'usr_man_manu_id', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
