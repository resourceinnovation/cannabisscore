<?php
namespace CannabisScore;

use Illuminate\Support\ServiceProvider;

class CannabisScoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        /*
        $this->app->bind('wikiworldorder-survloop', function()
        {
            return new Demo;
        });
        */
    }

    public function boot()
    {
        require __DIR__ . '/routes.php';
        $this->publishes([
              __DIR__.'/Views'         => base_path('resources/views/vendor/cannabisscore'),
              __DIR__.'/Public'        => base_path('public/cannabisscore'),
              __DIR__.'/Models'        => base_path('app/Models'),
              __DIR__.'/Uploads'       => base_path('storage/app/up/cannabisscore'),
              __DIR__.'/Database/2018_11_30_000000_rii_create_tables.php' 
                => base_path('database/migrations/2018_11_30_000000_rii_create_tables.php'),
              __DIR__.'/Database/RIISeeder.php' => base_path('database/seeds/RIISeeder.php')
        ]);
        //$this->loadViewsFrom(__DIR__ . '/Views', 'survloop');
    }
}