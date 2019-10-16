<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIManufacturers extends Model
{
    protected $table      = 'RII_Manufacturers';
    protected $primaryKey = 'ManuID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
        'ManuName', 
        'ManuCntFlower', 
        'ManuCntVeg', 
        'ManuCntClone', 
        'ManuCntMother', 
        'ManuIDsFlower', 
        'ManuIDsVeg', 
        'ManuIDsClone', 
        'ManuIDsMother', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
