<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIManufacturers extends Model
{
    protected $table      = 'rii_manufacturers';
    protected $primaryKey = 'manu_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
        'manu_name', 
        'manu_cnt_flower', 
        'manu_cnt_veg', 
        'manu_cnt_clone', 
        'manu_cnt_mother', 
        'manu_ids_flower', 
        'manu_ids_veg', 
        'manu_ids_clone', 
        'manu_ids_mother', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
