<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIExternTypes extends Model
{
    protected $table      = 'RII_ExternTypes';
    protected $primaryKey = 'ExtTypID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ExtTypTypeName', 
		'ExtTypHasWatts', 
		'ExtTypHasBTUs', 
		'ExtTypHasCarbon', 
		'ExtTypHasGreenhouse', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
