<?php

namespace Loonpwn\Swiftype\Clients;

use Elastic\EnterpriseSearch\Client;
use Loonpwn\Swiftype\Exceptions\MissingSwiftypeConfigException;

class Api
{
    private const REQUIRED_CONFIG = [
        'SWIFTYPE_API_PRIVATE_KEY' => 'api_private_key',
        'SWIFTYPE_HOST_IDENTIFIER' => 'host_identifier',
    ];

    /**
     * @throws MissingSwiftypeConfigException
     */
    public static function build()
    {
        // check the environment configs that we need have been set
        foreach (self::REQUIRED_CONFIG as $env => $value) {
            if (empty(config('swiftype.'.$value))) {
                throw new MissingSwiftypeConfigException($env);
            }
        }

        $apiEndpoint = 'https://'.config('swiftype.host_identifier').'.api.swiftype.com/api/as/v1/';
        $apiKey = config('swiftype.api_private_key');

        $client = new Client([
            'host' => $apiEndpoint,
            'app-search' => [
                'token' => $apiKey,
            ],
        ]);

        return $client->appSearch();
    }
}
