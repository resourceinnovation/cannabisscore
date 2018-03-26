<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIExternalities extends Model
{
    protected $table      = 'RII_Externalities';
    protected $primaryKey = 'ExternID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ExternItemID', 
		'ExternType', 
		'ExternMeasure', 
		'ExternUnit', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
