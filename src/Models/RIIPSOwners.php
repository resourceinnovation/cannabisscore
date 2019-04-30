<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSOwners extends Model
{
    protected $table      = 'RII_PSOwners';
    protected $primaryKey = 'PsOwnID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
        'PsOwnPartnerUser', 
        'PsOwnClientUser', 
        'PsOwnType', 
        'PsOwnClientName'
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
