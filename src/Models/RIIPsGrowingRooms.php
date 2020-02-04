<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsGrowingRooms extends Model
{
    protected $table      = 'rii_ps_growing_rooms';
    protected $primaryKey = 'ps_room_id';
    public $timestamps    = true;
    protected $fillable   = 
    [    
        'ps_room_psid', 
        'ps_room_name',
        'ps_room_canopy_sqft', 
        'ps_room_farm_type', 
        'ps_room_lgt_sun', 
        'ps_room_lgt_dep', 
        'ps_room_lgt_artif', 
        'ps_room_vert_stack', 
        'ps_room_lgt_pattern', 
        'ps_room_lgt_fix_size_calced', 
        'ps_room_total_light_watts', 
        'ps_room_lighting_effic', 
        'ps_room_water_gallons', 
        'ps_room_watering_method', 
        'ps_room_water_freq', 
        'ps_room_water_effic', 
        'ps_room_temperature', 
        'ps_room_humidity', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
