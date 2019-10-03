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
		'PsAreaType', 
		'PsAreaHasStage', 
		'PsAreaSize', 
		'PsAreaDaysCycle', 
		'PsAreaLgtSun', 
		'PsAreaLgtDep', 
		'PsAreaLgtArtif', 
		'PsAreaTotalLightWatts', 
		'PsAreaLightingEffic', 
		'PsAreaVertStack', 
		'PsAreaHvacType', 
		'PsAreaHvacOther', 
		'PsAreaHvacEffic', 
		'PsAreaCalcWatts', 
		'PsAreaCalcSize', 
		'PsAreaLgtPattern', 
		'PsAreaLgtFixSize1', 
		'PsAreaLgtFixSize2', 
		'PsAreaGallons', 
		'PsAreaWaterFreq', 
		'PsAreaWateringMethod', 
		'PsAreaWaterEffic', 
        'PsAreaSqFtPerFix1', 
        'PsAreaSqFtPerFix2', 
        'PsAreaCommissioning', 
        'PsAreaTemperature', 
        'PsAreaHumidity', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
