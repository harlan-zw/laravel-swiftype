# Laravel Swiftype

[![Total Downloads](https://img.shields.io/packagist/vpre/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![Total Downloads](https://img.shields.io/packagist/dt/loonpwn/laravel-swiftype.svg?style=flat)](https://packagist.org/packages/loonpwn/laravel-swiftype)
[![StyleCI](https://github.styleci.io/repos/155632347/shield?branch=master)](https://github.styleci.io/repos/155632347)

Laravel Swiftype is a wrapper for [elastic/app-search](https://www.elastic.co/products/app-search) with some Laravel
specific Laravel helpers to make integrating your Eloquent Models with Swiftype a breeze.

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

Get your keys from the [Swiftype Credentials](https://app.swiftype.com/as#/credentials) page.


### API

This packages has two Facades which give you access to the underlying client. 

#### Swiftype

The `Swiftype` facade is a direct wrapper for built client from https://github.com/elastic/app-search-php. Any command
from the base client can be used on this facade. Example:

- `Swiftype::listEngines($currentPage = null, $pageSize = null)` - Show all engines available

- `Swiftype::createEngine($name, $language = 'en')` - Find an engine based on name

With IDE auto-complete:

````php
/** @var \Loonpwn\Swiftype\Clients\Api $api */
$api = app(Swiftype::class);
````

#### SwiftypeEngine

The `SwiftypeEngine` is a wrapper on top of the `Swiftype` facade with a direct context of an engine. Many of the same 
functions from the core API is available in this facade without the need to specify an engine. 

`SwiftypeEngine::search($query, $options)` - Search documents within the engine

`SwiftypeEngine::indexDocument($document)` - Creates a new document, or updates an existing, based on the primary 
key. This function will use a transformer to make sure the primary key is transformed to just `id`. 

`SwiftypeEngine::indesDocuments($document)` - Similar as the above but will take a list of models and chunk them
to 100 per request

`SwiftypeEngine::deleteDocument($documentId)` - Removes a document.

`SwiftypeEngine::deleteDocuments($documentIds)` - Takes an array of document ids and removes them. 

`SwiftypeEngine::listDocuments($page = 1, $pageSize = 100)` - Lists documents that belong to the engine, with pagination.

`SwiftypeEngine::listAllDocumentsByPages($action, $page = 1, $pageSize = 100)` - Lists documents that belong to the engine, 
will iterate through all pages and call your custom action.

`SwiftypeEngine::purgeAllDocuments()` - Will remove all documents from Swiftype


### Traits
 
`use IsSwiftypeDocument` is a trait available which hooks into the models `saved` event hook. The following happens on
saved:
  - `shouldSyncSwiftypeOnSave` is checked and must pass true to continue
  - `getSwiftypeAttributes` is called to get the attributes to send to Swiftype 

The default logic of these functions defined in the trait looks like the below. You should override these functions for 
business specific logic.

```php
/**
 * Should model changes be pushed to Swiftype. Excludes deleting
 * @return bool
 */
public function shouldSyncSwiftypeOnSave()
{
    // by default all model changes are pushed to swiftype
    return true;
}

/**
 * Get the mapped attribute values for Swiftype
 * @return mixed|null
 */
public function getSwiftypeAttributes()
{
    // Document transformer is the default transformer, feel free to implement your own
    return transform($this, new DocumentTransformer());
}
```

### Jobs

Currently only jobs directly related to the Eloquent Model events are created. These can be used to queue the data sync.

- `DeleteDocument($documentId)` - Delete a particular document, takes the document id
- `IndexDocument($document)` - Pushes the individual document. Takes the mapped document 
- `SyncDocuments` - Iterates through all documents in Swiftype and local DB to find documents out of sync and eithers
adds or removes them. This uses the `swiftype.sync_models` configuration


## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email harlan@harlanzw.com instead of using the issue tracker.

