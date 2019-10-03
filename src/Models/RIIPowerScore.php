<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPowerScore extends Model
{
    protected $table      = 'RII_PowerScore';
    protected $primaryKey = 'PsID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsUserID', 
		'PsStatus', 
		'PsIsPro',
		'PsTimeType', 
		'PsYear', 
		'PsPrivacy', 
		'PsZipCode', 
		'PsClimateLabel',
		'PsCounty', 
		'PsState', 
		'PsCountry', 
		'PsAshrae', 
		'PsEmail', 
		'PsName', 
		'PsCharacterize', 
		'PsEfficOverall', 
		'PsEfficOverSimilar', 
		'PsEfficFacility', 
		'PsEfficProduction', 
		'PsEfficLighting', 
		'PsEfficHvac', 
		'PsEfficCarbon', 
		'PsEfficWater', 
		'PsEfficWaste', 
		'PsEfficFacilityStatus', 
		'PsEfficProductionStatus', 
		'PsEfficLightingStatus', 
		'PsEfficHvacStatus', 
		'PsEfficCarbonStatus', 
		'PsEfficWaterStatus', 
		'PsEfficWasteStatus', 
		'PsGrams', 
		'PsKWH', 
		'PsTotalSize', 
		'PsTotalCanopySize', 
		'PsHavestsPerYear', 
		'PsHarvestBatch', 
		'PsHasWaterPump', 
		'PsCuresIndoor', 
		'PsCuresOutdoor', 
		'PsCuresOffsite', 
		'PsIsIntegrated', 
		'PsSourceUtility', 
		'PsSourceRenew', 
		'PsOtherPower', 
		'PsMotherLoc', 
		'PsProcessingOnsite', 
		'PsSourceUtilityOther', 
		'PsVerticalStack', 
		'PsUploadEnergyBills', 
		'PsEnergyNonFarm', 
		'PsEnergyNonFarmPerc', 
		'PsHvacOther', 
		'PsHeatWater', 
		'PsExtractingOnsite', 
		'PsControls', 
		'PsControlsAuto', 
		'PsIPaddy', 
		'PsUniqueStr', 
		'PsTreeVersion', 
		'PsVersionAB', 
		'PsIsMobile', 
		'PsSubmissionProgress', 
		'PsNotes', 
		'PsWaterInnovation', 
		'PsGreenWasteLbs', 
		'PsGreenWasteMixed', 
		'PsComplianceWasteTrack', 
		'PsLightingError',
		'PsCrop',
		'PsCropOther',
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
