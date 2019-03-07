
# resourceinnovation/cannabisscore

[![Laravel](https://img.shields.io/badge/Laravel-5.8-orange.svg?style=flat-square)](http://laravel.com)
[![SurvLoop](https://img.shields.io/badge/SurvLoop-0.1-orange.svg?style=flat-square)](https://github.com/wikiworldorder/survloop)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

Resource Innovation Institute's Cannabis PowerScore&trade; database is an open-source web app empowering the cannabis 
community, and the industry, to prepare, track, and grow in ever more sustainable directions. 
The Cannabis PowerScore database is built using 
<a href="https://github.com/wikiworldorder/survloop" target="_blank">SurvLoop</a>, atop 
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

* php: >=7.3
* <a href="https://packagist.org/packages/laravel/laravel" target="_blank">laravel/laravel</a>: 5.8.*
* <a href="https://packagist.org/packages/wikiworldorder/survloop" target="_blank">wikiworldorder/survloop</a>: 0.1.*

# <a name="getting-started"></a>Getting Started

The instructions below include the needed steps to install Laravel, SurvLoop, as well as the Cannabis PowerScore system.
For more on creating environments to host Laravel, you can find more instructions on
<a href="https://survloop.org/how-to-install-laravel-on-a-digital-ocean-server" target="_blank">SurvLoop.org</a>.

* Use Composer to install Laravel with default user authentication, one required package:

```
$ composer global require "laravel/installer"
$ composer create-project laravel/laravel PowerScore "5.8.*"
$ cd PowerScore
$ php artisan make:auth
$ php artisan vendor:publish --tag=laravel-notifications
```

* Update `composer.json` to add requirements and an easier SurvLoop and CannabisScore reference:

```
$ nano composer.json
```

```
...
"require": {
	...
    "resourceinnovation/cannabisscore": "^0.1.*",
	...
},
...
"autoload": {
	...
	"psr-4": {
		...
		"CannabisScore\\": "vendor/resourceinnovation/cannabisscore/src/",
	}
	...
},
...
```

```
$ composer update
```

* Add the package to your application service providers in `config/app.php`.

```
$ nano config/app.php
```

```php
...
'providers' => [
	...
	SurvLoop\SurvLoopServiceProvider::class,
	CannabisScore\CannabisScoreServiceProvider::class,
	...
],
...
'aliases' => [
	...
	'SurvLoop'	 => 'WikiWorldOrder\SurvLoop\SurvLoopFacade',
    'CannabisScore'  => 'ResourceInnovation\CannabisScore\CannabisScoreFacade',
	...
],
...
```

* Swap out the SurvLoop user model in `config/auth.php`.

```
$ nano config/auth.php
```

```php
...
'model' => App\Models\User::class,
...
```

* Update composer, publish the package migrations, etc...

```
$ php artisan vendor:publish --force
$ php artisan migrate
$ composer dump-autoload
$ php artisan db:seed --class=SurvLoopSeeder
$ php artisan db:seed --class=CannabisScoreSeeder
```

* For now, to apply database design changes to the same installation you are working in, depending on your server, 
you might also need something like this...

```
$ chown -R www-data:33 app/Models
$ chown -R www-data:33 database
```

* Browse to load the style sheets, etc.. /dashboard/css-reload

* Log into admin dashboard...

```
user: open@powerscore.resourceinnovation.org
password: powerscore
```


# <a name="documentation"></a>Documentation

Once installed, documentation of this system's database design can be found at /dashboard/db/all . This system's user 
experience design for data entry can be found at /dashboard/tree/map?all=1&alt=1 .


Utility companies linked to zip codes collected from <a href="https://openei.org/datasets/dataset/u-s-electric-utility-companies-and-rates-look-up-by-zipcode-feb-2011/resource/3f00482e-8ea0-4b48-8243-a212b6322e74"
target="_blank">OpenEI.org</a>.


# <a name="roadmap"></a>Roadmap

Here's the TODO list for the next release (**1.0**). It's my first time building on Laravel, or GitHub. So sorry.

* [ ] Code commenting, learning and implementing more community standards.
* [ ] More reports, filters, and graphs of the collected data.

# <a name="change-logs"></a>Change Logs


# <a name="contribution-guidelines"></a>Contribution Guidelines

Please help educate me on best practices for sharing code in this community.
Please report any issue you find in the issues page.

# <a name="security-help"></a>Reporting a security vulnerability

We want to ensure that the Cannabis PowerScore is a secure HTTP open data platform for everyone. 
If you've discovered a security vulnerability in powerscore.resourceinnovation.org, 
we appreciate your help in disclosing it to us in a responsible manner.

Publicly disclosing a vulnerability can put the entire community at risk. 
If you've discovered a security concern, please email us at wikiworldorder *at* protonmail.com. 
We'll work with you to make sure that we understand the scope of the issue, and that we fully address your concern. 
We consider correspondence sent to wikiworldorder *at* protonmail.com our highest priority, 
and work to address any issues that arise as quickly as possible.

After a security vulnerability has been corrected, a release will be deployed as soon as possible.
