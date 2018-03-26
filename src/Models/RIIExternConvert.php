<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIExternConvert extends Model
{
    protected $table      = 'RII_ExternConvert';
    protected $primaryKey = 'ExtConID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ExtConType1', 
		'ExtConType2', 
		'ExtConType1Measure', 
		'ExtConType2Measure', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
