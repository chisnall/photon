# Install

The easiest way to evaluate Photon is to use the ready built Docker image.

If you want to manually install Photon, then this guide provides some tips.

You will need to be familiar with configuring a Linux system as a web server.

All commands listed below need to be run from the shell as the root user.


## Requirements

You will need a Linux based system or VM.

* Linux OS
* Web server: Nginx / Apache
* PHP 8.3+
* Database


## Database Support

Photon defaults to SQLite which is a file based database.

Alternatively you can store all user data on dedicated database server.

These databases are supported:

* SQLite
* MariaDB
* MySQL
* PostgreSQL


## Web Server

This document does not provide details on how to install a web server.

We recommend Nginx. Consult your Linux distributions documentation on how to setup Nginx or an alternative web server.

The root directory of the web server should be set to the `/public` subdirectory of the source code.


## PHP

Version 8.3 or later **must be installed**.  
If your Linux distribution does not include version 8.3, then add a repo.  
For example, Debian 12 ships with version 8.2 but can be upgraded to version 8.3 with a 3rd party repo.

Installation of PHP is specific to the Linux distribution. Consult your Linux distributions documentation on how to install PHP.

If you are using Nginx as the web server, install PHP-FPM (FastCGI Process Manager).

These additional modules need to be installed:

* curl
* pcntl
* pdo_mysql
* pdo_pgsql
* pdo_sqlite

Confirm they are installed with this command:

`php -m`


## Database

If you are not using a dedicated database server, you need to install SQLite.

This is a file based database. It is the easiest way to add database support to Photon.

Installation of SQLite is specific to your Linux distribution.

Confirm it is installed with this command:

`sqlite3 --version`


## Web User Account

Establish the user account for the web server.

For example, on Debian based systems this is:  
`www-data`

It may be `apache` or `httpd` on other systems.

Once you have established the user account for the web server, run these commands as the root user.

Substitute `www-data` for the actual user account for the web server.

`export webuser=www-data`  
`mkdir -p /var/lib/photon/logs /var/lib/photon/output /var/lib/photon/sessions`  
`chown -R $webuser:$webuser /var/lib/photon/`

If the web server user account differs from the default of `www-data`, you need to edit this file in the Photon root directory:

`config.php`

Set the value of:

app -> httpUser

to the value of the web server user account.


## Composer

We recommend you install the latest version of Composer, rather than the version provided by your Linux distribution.

Follow the instructions here:

<https://getcomposer.org/download/>

You can install Composer using these commands:

`cd /tmp`  
`curl -s https://getcomposer.org/installer -o composer-setup.php`  
`php composer-setup.php`  
`mv composer.phar /usr/local/bin/composer`  
`rm -f composer-setup.php`

Confirm Composer is installed with this command:

`composer -V`


## Install Source

Install the source code to:

`/var/www`

The root directory for the web server configuration is:

`/var/www/public`

Run Composer:

`cd /var/www`  
`composer install`

The next step is to create the database.

`php migrations.php`

that will create the initial database and tables.

That completes the installation.
