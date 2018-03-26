<?php namespace App\Models;
// generated from /resources/views/vendor/survloop/admin/db/export-laravel-model-gen.blade.php

use Illuminate\Database\Eloquent\Model;

class RIIPsFeedback extends Model
{
    protected $table      = 'RII_PsFeedback';
    protected $primaryKey = 'PsfID';
    public $timestamps    = true;
    protected $fillable   = 
    [    
		'PsfVersionAB', 
		'PsfSubmissionProgress', 
		'PsfIPaddy', 
		'PsfTreeVersion', 
		'PsfUniqueStr', 
		'PsfUserID', 
		'PsfIsMobile', 
		'PsfPsID', 
		'PsfFeedback1', 
		'PsfFeedback2', 
		'PsfFeedback3', 
		'PsfFeedback4', 
		'PsfFeedback5', 
    ];
    
    // END SurvLoop auto-generated portion of Model
    
}
