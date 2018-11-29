<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSCommunications extends Model
{
    protected $table      = 'RII_PSCommunications';
    protected $primaryKey = 'PsComID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsComPSID', 
		'PsComUser', 
		'PsComDescription', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
