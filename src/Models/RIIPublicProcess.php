<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPublicProcess extends Model
{
    protected $table      = 'RII_PublicProcess';
    protected $primaryKey = 'PubPrcID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PubPrcLikeProducers', 
		'PubPrcLikeProducersVisual', 
		'PubPrcRafflePrizes', 
		'PubPrcPrizeOther', 
		'PubPrcGrowerOtherValue', 
		'PubPrcFeedback1', 
		'PubPrcUserID', 
		'PubPrcSubmissionProgress', 
		'PubPrcTreeVersion', 
		'PubPrcVersionAB', 
		'PubPrcUniqueStr', 
		'PubPrcIPaddy', 
		'PubPrcIsMobile', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
