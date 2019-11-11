<?php

// get from here: https://app.swiftype.com/as#/credentials
return [
    /*
     * The default engine used for the SwiftypeEngine Facade.
     */
    'default_engine' => env('SWIFTYPE_DEFAULT_ENGINE'),

    /*
     * The API key used to authenticate requests made to Swiftypes API. This key
     * is required to make use of the Swiftype library.
     */
    'api_private_key' => env('SWIFTYPE_API_PRIVATE_KEY'),
    /*
     * The host identifier is the URL prefix for the API requests.
     */
    'host_identifier' => env('SWIFTYPE_HOST_IDENTIFIER'),

    /*
     * Models used when syncing data
     */
    'sync_models' => [
        // User::class
    ]
];
