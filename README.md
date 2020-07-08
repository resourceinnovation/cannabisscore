
# resourceinnovation/cannabisscore

[![Laravel](https://img.shields.io/badge/Laravel-7.6-orange.svg?style=flat-square)](http://laravel.com)
[![SurvLoop](https://img.shields.io/badge/SurvLoop-0.2-orange.svg?style=flat-square)](https://github.com/rockhopsoft/survloop)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

Resource Innovation Institute's Cannabis PowerScore&trade; database is an open-source web app empowering the cannabis community, and the industry, to prepare, track, and grow in ever more sustainable directions. The Cannabis PowerScore database is built using 
<a href="https://github.com/rockhopsoft/survloop" target="_blank">SurvLoop</a>, atop 
<a href="https://laravel.com/" target="_blank">Laravel</a>. <br />
<a href="http://ResourceInnovation.org" target="_blank">ResourceInnovation.org</a><br />
<a href="http://CannabisPowerScore.org" target="_blank">CannabisPowerScore.org</a><br />
<a href="http://PowerScore.ResourceInnovation.org" target="_blank">PowerScore.ResourceInnovation.org</a><br />
SurvLoop is a Laravel-based engine for designing a database and creating a mobile-friendly user interface to fill it. 

# Table of Contents
* [Requirements](#requirements)
* [Getting Started](#getting-started)
* [Documentation](#documentation)
* [Roadmap](#roadmap)
* [Change Logs](#change-logs)
* [Contribution Guidelines](#contribution-guidelines)


# <a name="requirements"></a>Requirements

* php: >=7.2.15
* <a href="https://packagist.org/packages/laravel/laravel" target="_blank">laravel/laravel</a>: 7.6.*
* <a href="https://packagist.org/packages/rockhopsoft/survloop" target="_blank">rockhopsoft/survloop</a>: >=0.2.14


# <a name="getting-started"></a>Getting Started

## Install Laravel Using Composer
```
$ composer create-project laravel/laravel powerscore "7.6.*"
$ cd powerscore

```

Edit the environment file to connect the default MYSQL database:
```
$ nano .env
```
```
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Next, install Laravel's out-of-the-box user authentication tools, and push the vendor file copies where they need to be:
```
$ composer require laravel/ui
$ php artisan ui vue --auth
$ echo "0" | php artisan vendor:publish --tag=laravel-notifications
```

### Install ResourceInnovation/CannabisScore

From your Laravel installation's root directory, update `composer.json` to require and easily reference CannabisScore:
```
$ nano composer.json
```
```
...
"require": {
    ...
    "rockhopsoft/survloop": "^0.2.18",
    "resourceinnovation/cannabisscore": "^0.2.5",
    ...
},
...
"autoload": {
    ...
    "psr-4": {
        ...
        "SurvLoop\\": "vendor/rockhopsoft/survloop/src/",
        "CannabisScore\\": "vendor/resourceinnovation/cannabisscore/src/",
    }
    ...
}, ...
```

After saving the file, run the update to download CannabisScore, and any missing dependencies.
```
$ composer update
```

Add the package to your application service providers in `config/app.php`.
```
$ nano config/app.php
```
```
...
'providers' => [
    ...
    SurvLoop\SurvLoopServiceProvider::class,
    CannabisScore\CannabisScoreServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,
    ...
],
...
'aliases' => [
    ...
    'SurvLoop' => 'RockHopSoft\SurvLoop\SurvLoopFacade',
    'CannabisScore' => 'ResourceInnovation\CannabisScore\CannabisScoreFacade',
    'Image' => Intervention\Image\Facades\Image::class,
    ...
], ...
```

Swap out the CannabisScore user model in `config/auth.php`.
```
$ nano config/auth.php
```
```
...
'model' => App\Models\User::class,
...
```

Update composer, publish the package migrations, etc...
```
$ echo "0" | php artisan vendor:publish --force
$ php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"
$ cd ~/homestead
$ vagrant up
$ vagrant ssh
$ cd code/powerscore
$ php artisan optimize:clear
$ composer dump-autoload
$ php artisan migrate
# php artisan db:seed --class=SurvLoopSeeder
$ php artisan db:seed --class=ZipCodeSeeder
# php artisan db:seed --class=CannabisScoreSeeder
```

* For now, to apply database design changes to the same installation you are working in, depending on your server, 
you might also need something like this...

```
$ chown -R www-data:33 app/Models
$ chown -R www-data:33 database
$ chown -R www-data:33 storage
```

### Initialize SurvLoop Installation

Then browsing to the home page should prompt you to create the first admin user account:

http://powerscore.local

If everything looks janky, then manually load the style sheets, etc:

http://powerscore.local/css-reload

After logging in as an admin, this link rebuilds many supporting files:

http://powerscore.local/dashboard/settings?refresh=2


# <a name="documentation"></a>Documentation

Once installed, documentation of this system's database design can be found at /dashboard/db/all . This system's user 
experience design for data entry can be found at /dashboard/tree/map?all=1&alt=1 .

More on the SurvLoop level is also starting here: <a href="https://survloop.org/package-files-folders-classes" target="_blank">https://survloop.org/package-files-folders-classes</a>.

Utility companies linked to zip codes collected from <a href="https://openei.org/datasets/dataset/u-s-electric-utility-companies-and-rates-look-up-by-zipcode-feb-2011/resource/3f00482e-8ea0-4b48-8243-a212b6322e74"
target="_blank">OpenEI.org</a>.


# <a name="roadmap"></a>Roadmap

Here's the TODO list for the next release (**1.0**).

* [ ] Code commenting, learning and implementing more community standards.

# <a name="change-logs"></a>Change Logs


# <a name="contribution-guidelines"></a>Contribution Guidelines

Please help educate me on best practices for sharing code in this community.
Please report any issue you find in the issues page.

# <a name="security-help"></a>Reporting a security vulnerability

We want to ensure that the Cannabis PowerScore is a secure HTTP open data platform for everyone. 
If you've discovered a security vulnerability in powerscore.resourceinnovation.org, 
we appreciate your help in disclosing it to us in a responsible manner.

Publicly disclosing a vulnerability can put the entire community at risk. 
If you've discovered a security concern, please email us at rockhoppers *at* runbox.com. 
We'll work with you to make sure that we understand the scope of the issue, and that we fully address your concern. 
We consider correspondence sent to rockhoppers *at* runbox.com our highest priority, 
and work to address any issues that arise as quickly as possible.

After a security vulnerability has been corrected, a release will be deployed as soon as possible.
