
# resourceinnovation/cannabisscore

[![Laravel](https://img.shields.io/badge/Laravel-5.8-orange.svg?style=flat-square)](http://laravel.com)
[![SurvLoop](https://img.shields.io/badge/SurvLoop-0.2-orange.svg?style=flat-square)](https://github.com/rockhopsoft/survloop)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

Resource Innovation Institute's Cannabis PowerScore&trade; database is an open-source web app empowering the cannabis 
community, and the industry, to prepare, track, and grow in ever more sustainable directions. 
The Cannabis PowerScore database is built using 
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
* <a href="https://packagist.org/packages/laravel/laravel" target="_blank">laravel/laravel</a>: 5.8.*
* <a href="https://packagist.org/packages/rockhopsoft/survloop" target="_blank">rockhopsoft/survloop</a>: >=0.2.1


# <a name="getting-started"></a>Getting Started

## Install a copy of Cannabis PowerScore with Laradock

First, <a href="https://www.docker.com/get-started" target="_blank">install Docker</a> on Mac, Windows, or an online server. 
Then grab a copy of Laravel (last tested with v5.8.3)...
```
$ git clone https://github.com/laravel/laravel.git survlooporg
$ cd survlooporg
```

Next, install and boot up Laradock (last tested with v7.14).
```
$ git submodule add https://github.com/Laradock/laradock.git
$ cd laradock
$ cp env-example .env
$ docker-compose up -d nginx mysql phpmyadmin redis workspace
```

After Docker finishes booting up your containers, enter the mysql container with the root password, "root". This seems to fix things for the latest version of MYSQL.
```
$ docker-compose exec mysql bash
# mysql --user=root --password=root default
mysql> ALTER USER 'default'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';
mysql> exit;
$ exit
```

At this point, you can optionally browse to <a href="http://localhost:8080" target="_blank">http://localhost:8080</a> for PhpMyAdmin.
```
Server: mysql
Username: default
Password: secret
```

Finally, enter Laradock's workspace container and install the PowerScore.
```
$ docker-compose exec workspace bash
# cp .env.example .env
# nano .env
```
Edit these few lines in Laravel's environment file:
```
DB_HOST=mysql
DB_DATABASE=default
DB_USERNAME=default
```
And continue the install...
```
# composer install
# php artisan key:generate
# php artisan make:auth
# composer require resourceinnovation/cannabisscore
# composer dump-autoload
# sed -i 's/App\\User::class/App\\Models\\User::class/g' config/auth.php
# echo "0" | php artisan vendor:publish --force
# php artisan migrate
# php artisan optimize
# composer dump-autoload
# php artisan db:seed --class=SurvLoopSeeder
$ php artisan db:seed --class=ZipCodeSeeder
# php artisan db:seed --class=CannabisScoreSeeder
```
And if all has gone well, you'll be asked to create a master admin user account when you browse to <a href="http://localhost/" target="_blank">http://localhost/</a>. If it loads, but looks janky (without CSS), reload the page once... and hopefully it looks like a fresh install.


## Install a copy of the Cannabis PowerScore without Laradock

* Use Composer to install Laravel with default user authentication, one required package:

```
$ composer global require "laravel/installer"
$ composer create-project laravel/laravel powerscore "5.8.*"
$ cd powerscore
$ php artisan key:generate
$ php artisan make:auth
$ composer require resourceinnovation/cannabisscore
$ sed -i 's/App\\User::class/App\\Models\\User::class/g' config/auth.php
```

* Update composer, publish the package migrations, etc...

```
$ echo "0" | php artisan vendor:publish --force
$ composer dump-autoload
$ php artisan optimize
$ php artisan migrate
$ php artisan db:seed --class=SurvLoopSeeder
$ php artisan db:seed --class=ZipCodeSeeder
$ php artisan db:seed --class=CannabisScoreSeeder
```

* For now, to apply database design changes to the same installation you are working in, depending on your server, 
you might also need something like this...

```
$ chown -R www-data:33 app/Models
$ chown -R www-data:33 database
```

* Load in the browser to create super admin account and get started.


# <a name="documentation"></a>Documentation

Once installed, documentation of this system's database design can be found at /dashboard/db/all . This system's user 
experience design for data entry can be found at /dashboard/tree/map?all=1&alt=1 .

More on the SurvLoop level is also starting here: <a href="https://survloop.org/package-files-folders-classes" target="_blank">https://survloop.org/package-files-folders-classes</a>.

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
If you've discovered a security concern, please email us at rockhoppers *at* runbox.com. 
We'll work with you to make sure that we understand the scope of the issue, and that we fully address your concern. 
We consider correspondence sent to rockhoppers *at* runbox.com our highest priority, 
and work to address any issues that arise as quickly as possible.

After a security vulnerability has been corrected, a release will be deployed as soon as possible.
