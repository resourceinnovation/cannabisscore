<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web']], function () {
    
    Route::get(
        '/frame/animate/guage/{percent}',       
        'ResourceInnovation\CannabisScore\Controllers\CannabisScore@printFrameAnimPerc'
    );
    Route::get(
        '/frame/animate/meter/{percent}/{row}', 
        'ResourceInnovation\CannabisScore\Controllers\CannabisScore@printFrameAnimPercMeter'
    );
    
    Route::get(
        '/start-for-{prtnSlug}',       
        'ResourceInnovation\CannabisScore\Controllers\CannabisScore@startForPartner'
    );
    Route::get(
        '/go-pro-for-{prtnSlug}',       
        'ResourceInnovation\CannabisScore\Controllers\CannabisScore@startForPartner'
    );

});    

?>