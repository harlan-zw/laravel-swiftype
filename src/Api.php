<?php

namespace Loonpwn\Swiftype;

use Loonpwn\Swiftype\Errors\MissingSwiftypeConfigException;

class Api
{
    private const REQUIRED_CONFIG = [
        'SWIFTYPE_API_PRIVATE_KEY' => 'apiPrivateKey',
        'SWIFTYPE_HOST_IDENTIFIER' => 'hostIdentifier',
    ];

    private $client;

    /**
     * Api constructor.
     *
     * @param $config
     *
     * @throws MissingSwiftypeConfigException
     */
    public function __construct()
    {
        $swiftypeConfig = config('swiftype');
        // check the environment configs that we need have been set
        foreach (self::REQUIRED_CONFIG as $env => $value) {
            if (empty($swiftypeConfig[$value])) {
                throw new MissingSwiftypeConfigException($env);
            }
        }

        $config = array_merge(config('swiftype'), [
            'apiUrl' => 'https://'.config('swiftype.hostIdentifier').'.api.swiftype.com/api/as/v1/',
        ]);
        $this->client = new SwiftypeClient($config);
    }

    /**
     * Retrieve an array of engines.
     *
     * @return array An array of the engines
     */
    public function listEngines()
    {
        $params = ['per_page' => 100];
        $response = $this->client->get('engines', ['query' => $params]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Retrieve a specific engine.
     *
     * @param string $name The name of the engine to be found
     * @return array The engine that was found
     */
    public function findEngine($name)
    {
        $response = $this->client->get('engines/'.$name);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Create an engine.
     *
     * @param array $name The name of the engine to create
     * @return array The engine that was created
     */
    public function createEngine($name)
    {
        $engine = ['name' => $name];
        $response = $this->client->post('engines', $engine);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Check whether the connection could be authenticated.
     * @return bool
     */
    public function authenticated()
    {
        return ! empty($this->listEngines());
    }
}
