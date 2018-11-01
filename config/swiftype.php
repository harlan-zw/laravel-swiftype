<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Swiftype API Config
    |--------------------------------------------------------------------------
    |
    | The API key used to authenticate requests made to Swiftypes API. This key
    | is required to make use of the Swiftype library. The host identifier is
    | the URL prefix for the API requests.
    |
    */
    'defaultEngine' => env('SWIFTYPE_DEFAULT_ENGINE'),

    /*
    |--------------------------------------------------------------------------
    | Swiftype API Config
    |--------------------------------------------------------------------------
    |
    | The API key used to authenticate requests made to Swiftypes API. This key
    | is required to make use of the Swiftype library. The host identifier is
    | the URL prefix for the API requests.
    |
    */
    'apiPrivateKey' => env('SWIFTYPE_API_PRIVATE_KEY'),
    'hostIdentifier' => env('SWIFTYPE_HOST_IDENTIFIER'),

];
