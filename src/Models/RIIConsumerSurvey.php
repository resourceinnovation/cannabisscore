<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIConsumerSurvey extends Model
{
    protected $table      = 'RII_ConsumerSurvey';
    protected $primaryKey = 'ConID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'ConVersionAB', 
		'ConAreYouConsumer', 
		'ConSubmissionProgress', 
		'ConIPaddy', 
		'ConTreeVersion', 
		'ConUniqueStr', 
		'ConUserID', 
		'ConIsMobile', 
		'ConAreYouConsumerOther', 
		'ConHowOften', 
		'ConWhatKindsOther', 
		'ConYouNotice', 
		'ConOftenSustainable', 
		'ConBusCommitment', 
		'ConKnowMore', 
		'ConSpendMore', 
		'ConIssuesMatter', 
		'ConGender', 
		'ConEducation', 
		'ConStudent', 
		'ConEmployed', 
		'ConMeaning', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
