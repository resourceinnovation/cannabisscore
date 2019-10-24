<?php
namespace CannabisScore;

use Illuminate\Support\ServiceProvider;

class CannabisScoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'cannabisscore');
        $this->publishes([
              __DIR__.'/Views'         => base_path('resources/views/vendor/cannabisscore'),
              __DIR__.'/Public'        => base_path('public/cannabisscore'),
              __DIR__.'/Models'        => base_path('storage/app/models/cannabisscore'),
              __DIR__.'/Models'        => base_path('app/Models/CannabisScore'),
              __DIR__.'/Models'        => base_path('app/Models'),
              
              __DIR__.'/Uploads'       => base_path('storage/app/up/cannabisscore'),
              __DIR__.'/Database/2019_10_21_000000_create_rii_tables' 
                => base_path('database/migrations/2019_10_21_000000_create_rii_tables'),
              __DIR__.'/Database/RIISeeder.php' => base_path('database/seeds/RIISeeder.php')
        ]);
    }
}