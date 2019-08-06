# Laravel Swiftype

[![Total Downloads](https://img.shields.io/packagist/vpre/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![Total Downloads](https://img.shields.io/packagist/dt/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![StyleCI](https://github.styleci.io/repos/155632347/shield?branch=master)](https://github.styleci.io/repos/155632347)

This package provides a wrapper around the [Swiftype App Search API](https://swiftype.com/documentation/app-search) as well
as specific Laravel helpers such as a Eloquent model trait and Jobs.

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

A trait is available which will hook into the model events to push updates to Swiftype. Simply add the below to
any model which you'd like it's data to be pushed. 

`use ExistsAsSwiftypeDocument`

Note: Swiftype only supports one document type per engine, there should only be one model which uses this trait.


### API

#### Swiftype

The package provides some Facades for you to interact with the Swiftype api 

`Swiftype::listEngines()` - Show all engines available

`Swiftype::findEngine($name)` - Find an engine based on name

`Swiftype::createEngine()` - Create a new engine

`Swiftype::authenticated()` - Checks that the authenticated worked 

#### SwiftypeEngine

Accessing SwiftypeEngine will have all requests routed to the engine you've provided as the default.

`SwiftypeEngine::searchWithQuery($query, $options)` - Search documents within the engine

`SwiftypeEngine::search($options)` - Search documents within the engine. This should include the query

`SwiftypeEngine::createOrUpdateDocument($document)` - Creates a new document, or updates an existing, based on the primary 
key. This function will use a transformer to make sure the primary key is transformed to just `id`. 

`SwiftypeEngine::createOrUpdateDocuments($document)` - Similar as the above but will take a list of models and chunk them
to 100 per request

`SwiftypeEngine::deleteDocument($documentId)` - Removes a document.

`SwiftypeEngine::deleteDocuments($documentIds)` - Takes an array of document ids and removes them. 

`SwiftypeEngine::listDocuments($page = 1, $pageSize = 100)` - Lists documents that belong to the engine, with pagination.

`SwiftypeEngine::listAllDocumentsByPages($action, $page = 1, $pageSize = 100)` - Lists documents that belong to the engine, 
will iterate through all pages and call your custom action.

`SwiftypeEngine::purgeAllDocuments()` - Will remove all documents from Swiftype



## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email harlan@harlanzw.com instead of using the issue tracker.

