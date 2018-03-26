<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIICompetitors extends Model
{
    protected $table      = 'RII_Competitors';
    protected $primaryKey = 'CmptID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'CmptYear', 
		'CmptCompetition', 
		'CmptName', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
