<?php
namespace ResourceInnovation\CannabisScore;

use ResourceInnovation\CannabisScore\CannabisScoreFacade;
use Illuminate\Support\ServiceProvider;

class CannabisScoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('cannabisscore', function($app) {
            return new CannabisScoreFacade();
        });
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        //require __DIR__ . '/routes.php';
        //$this->loadViewsFrom(__DIR__.'/Views', 'cannabisscore');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                  __DIR__.'/Views'   => base_path('resources/views/vendor/cannabisscore'),
                  __DIR__.'/Public'  => base_path('public/cannabisscore'),
                  __DIR__.'/Models'  => base_path('app/Models'),
                  
                  base_path('resourceinnovation/cannabisscore-images/src') 
                      => base_path('storage/app/up/cannabisscore'),

                  __DIR__.'/Database/2019_10_21_000000_create_rii_tables' 
                      => base_path('database/migrations/2019_10_21_000000_create_rii_tables'),
                  __DIR__.'/Database/RIISeeder.php' => base_path('database/seeders/RIISeeder.php')
            ]);
        }
    }
}