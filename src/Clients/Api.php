<?php

namespace Loonpwn\Swiftype\Clients;

use Elastic\AppSearch\Client\ClientBuilder;
use Loonpwn\Swiftype\Exceptions\MissingSwiftypeConfigException;

class Api extends \Elastic\AppSearch\Client\Client
{
    private const REQUIRED_CONFIG = [
        'SWIFTYPE_API_PRIVATE_KEY' => 'api_private_key',
        'SWIFTYPE_HOST_IDENTIFIER' => 'host_identifier',
    ];

    /**
     * @return \Elastic\AppSearch\Client\Client
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

        $hostIdentifier = config('swiftype.host_identifier');

        if (preg_match('/^https?:\/\//i', $hostIdentifier) === 1) {
            $apiEndpoint = $hostIdentifier.'/api/as/v1/';
        } else {
            $apiEndpoint = 'https://'.$hostIdentifier.'.api.swiftype.com/api/as/v1/';
        }
        $apiKey = config('swiftype.api_private_key');
        $clientBuilder = ClientBuilder::create($apiEndpoint, $apiKey);

        return $clientBuilder->build();
    }
}
