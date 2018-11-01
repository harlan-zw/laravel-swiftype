# Laravel Swiftype

[![Total Downloads](https://img.shields.io/packagist/vpre/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![Total Downloads](https://img.shields.io/packagist/dt/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![StyleCI](https://github.styleci.io/repos/155632347/shield?branch=master)](https://github.styleci.io/repos/155632347)

This Laravel package provides a synchronization between your Eloquent models and a single Swiftype engine. 

## Installation

Via Composer

``` bash
composer require loonpwn/laravel-swiftype
```

If you do not run Laravel 5.5 (or higher), then add the service provider in `config/app.php`:

```
Loonpwn\Swiftype\SwiftypeServiceProvider::class,
```


## Usage

In your `.env` file, add the following variables.
```
SWIFTYPE_DEFAULT_ENGINE=
SWIFTYPE_API_PRIVATE_KEY=
SWIFTYPE_HOST_IDENTIFIER=
```

For your models to in sync with Swiftype simply add the trait in.

`use ExistsAsSwiftypeDocument`

This trait hooks into the creating, updating and deleting events of your model and will send an API request to swiftype
when they occur.


## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email harlan@harlanzw.com instead of using the issue tracker.

