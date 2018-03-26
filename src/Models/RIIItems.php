<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIItems extends Model
{
    protected $table      = 'RII_Items';
    protected $primaryKey = 'ItID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ItBookID', 
		'ItItemType', 
		'ItName', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
