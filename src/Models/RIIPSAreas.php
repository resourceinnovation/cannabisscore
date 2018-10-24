<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPSAreas extends Model
{
    protected $table      = 'RII_PSAreas';
    protected $primaryKey = 'PsAreaID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsAreaPSID', 
		'PsAreaSize', 
		'PsAreaType', 
		'PsAreaLgtDep', 
		'PsAreaCanopyArea', 
		'PsAreaDaysCycle', 
		'PsAreaRoomExisting', 
		'PsAreaRoomNew', 
		'PsAreaLgtArtif', 
		'PsAreaTotalLightWatts', 
		'PsAreaCalcWatts',
		'PsAreaCalcSize',
		'PsAreaLightingEffic', 
		'PsAreaHasStage', 
		'PsAreaLgtSun', 
		'PsAreaVertStack', 
		'PsAreaHvacType', 
		'PsAreaHvacOther', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
