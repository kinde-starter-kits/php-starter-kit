# php-base - PHP Slim 4 Server library for Kinde Management API

* [OpenAPI Generator](https://openapi-generator.tech)
* [Slim 4 Documentation](https://www.slimframework.com/docs/v4/)

This server has been generated with [Slim PSR-7](https://github.com/slimphp/Slim-Psr7) implementation.
[PHP-DI](https://php-di.org/doc/frameworks/slim.html) package used as dependency container.

## Requirements

* Web server with URL rewriting
* PHP 7.4 or newer

This package contains `.htaccess` for Apache configuration.
If you use another server(Nginx, HHVM, IIS, lighttpd) check out [Web Servers](https://www.slimframework.com/docs/v3/start/web-servers.html) doc.

## Installation via [Composer](https://getcomposer.org/)

Navigate into your project's root directory and execute the bash command shown below.
This command downloads the Slim Framework and its third-party dependencies into your project's `vendor/` directory.
```bash
$ composer install
```

## Add configs

You should change `Kinde config` in the `config/prod/default.inc.php`:
```
...
    'kinde' => [
        'HOST' => 'YOUR_KINDE_HOST',
        'REDIRECT_URL' => 'http://localhost:8888/callback', // Please make sure that the value has already been configured in the Kinde
        'CLIENT_ID' => 'YOUR_KINDE_CLIENT_ID',
        'CLIENT_SECRET' => 'YOUR_KINDE_CLIENT_SECRET',
    ]
...
```

## Start devserver

Run the following command in terminal to start localhost web server, assuming `public/` is public-accessible directory with `index.php` file:
```bash
$ php -S localhost:8888 -t public
```

Now, you can open the browser and go to http://localhost:8888

For more information, please checkout `src/Api/UserApi.php`