# About Photon

Photon is a REST client for testing REST APIs.

Written in PHP with some JavaScript for the frontend. Tailwind is used for CSS. The app runs on a custom built MVC framework.

The easiest way to try Photon is to use the live demo or download the Docker image. Links are below.


## Features

* All HTTP methods for requests
* Collections for grouping requests
* Unit testing for requests
* Group testing of multiple requests in sequence
* Variables
* Multi-user
* Multiple databases are supported


## Requirements

If you are installing from source code, you will need a Linux based system or VM.

* Linux OS
* Web server: Nginx / Apache
* PHP 8.3+
* Database


## Database Support

Photon defaults to SQLite which is a file based database.

Alternatively you can store all user data on a dedicated database server.

These databases are supported:

* SQLite
* MariaDB
* MySQL
* PostgreSQL


## Live Demo

A live demo is hosted on AWS here:

<https://photon.chisnall.net>


## Docker Image

A Docker image is available here:

<https://hub.docker.com/r/chisnall/photon>


## Manual Install

See the INSTALL.md file for instructions on manual installation.


## License

Photon is licensed under the [MIT license](https://opensource.org/license/MIT).
