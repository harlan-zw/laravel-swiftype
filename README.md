# Laravel Swiftype

[![Total Downloads](https://img.shields.io/packagist/vpre/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![Total Downloads](https://img.shields.io/packagist/dt/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![StyleCI](https://github.styleci.io/repos/155632347/shield?branch=master)](https://github.styleci.io/repos/155632347)

This Laravel package provides a synchronization between your Eloquent models and a single Swiftype app engine. 

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

### Trait


For your models to in sync with Swiftype simply add the trait in.

`use ExistsAsSwiftypeDocument`

This trait hooks into the creating, updating and deleting events of your model and will send an API request to swiftype
when they occur.

### API

#### Swiftype

The package provides some Facades for you to interact with the Swiftype api 

`Swiftype::listEngines()` - Show all engines available

`Swiftype::findEngine($name)` - Find an engine based on name

`Swiftype::createEngine()` - Create a new engine

`Swiftype::authenticated()` - Checks that the authenticated worked 

#### SwiftypeEngine

Accessing SwiftypeEngine will have all requests routed to the engine you've provided as the default.

`SwiftypeEngine::search($query)` - Search documents within the engine

`SwiftypeEngine::createOrUpdateDocument($document)` - Creates a new document, or updates an existing, based on the primary 
key. This function will use a transformer to make sure the primary key is transformed to just `id`. This takes an
Eloquent model as the parameter 

`SwiftypeEngine::deleteDocument($document)` - Removes a document. This takes an Eloquent model as the parameter 

`SwiftypeEngine::listDocuments()` - Lists documents that belong to the engine, with pagination.



## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email harlan@harlanzw.com instead of using the issue tracker.

