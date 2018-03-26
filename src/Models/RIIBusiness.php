<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIBusiness extends Model
{
    protected $table      = 'RII_Business';
    protected $primaryKey = 'BusID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'BusName', 
		'BusState', 
		'BusZipCode', 
		'BusSector', 
		'BusESG', 
		'BusOrganic', 
		'BusFairTrade', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
