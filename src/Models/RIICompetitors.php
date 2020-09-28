<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIICompetitors extends Model
{
    protected $table      = 'rii_competitors';
    protected $primaryKey = 'cmpt_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'cmpt_year', 
		'cmpt_competition', 
		'cmpt_name', 
    ];
    
    // END Survloop auto-generated portion of Model
    
}
