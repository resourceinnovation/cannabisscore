<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIBusinessTagLinks extends Model
{
    protected $table      = 'RII_BusinessTagLinks';
    protected $primaryKey = 'BusTagLnkID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'BusTagLnkBusID', 
		'BusTagLnkTagID', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
