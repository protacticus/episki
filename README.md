episki
========================

episk / a brand new way to engage in governance 

Requirements
------------

  * PHP 5.5.9 or higher;
  * and the [usual Symfony application requirements](https://symfony.com/doc/current/reference/requirements.html).

If unsure about meeting these requirements, download the demo application and
browse the `http://localhost:8000/config.php` script to get more detailed
information.

Installation
------------

First, install the [Symfony Installer](https://github.com/symfony/symfony-installer)
if you haven't already. Then, install the clone the repository to your machine:

```bash
$ git clone https://github.com/episki/episki
$ cd episki
$ composer install --no-interaction
```

Also you need to modify the `.env` file to point to your database of choice.

```bash
$ cp .env.dist .env
```

> **NOTE**
>
> The `DATABASE_URL` needs an absolute path to work. Replace the `PATHTODIR` 
> to the episki directory to reference the included SQLite instance.
>
> DATABASE_URL="sqlite:////PATHTODIR/episki/var/data/blog.sqlite"

Usage
-----

There is no need to configure a virtual host in your web server to access the application.
Just use the built-in web server:

```bash
$ cd episki/
$ php bin/console server:run
```

This command will start a web server for the application. Now you can
access the application in your browser at <http://localhost:8000>. You can
stop the built-in web server by pressing `Ctrl + C` while you're in the
terminal.

> **NOTE**
>
> If you want to use a fully-featured web server (like Nginx or Apache) to run
> episki, configure it to point at the `web/` directory of the project.
> For more details, see:
> https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html

Currently the user manage interface is through the `console` application. To add users to the 
database you need execute the following:

```bash
$ cd episki/
$ php bin/console episki:add-user
```

Troubleshooting
---------------

Please submit any bugs or issues to the GitHub issue tracker. Thanks!
