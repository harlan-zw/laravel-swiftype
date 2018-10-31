<?php

namespace Loonpwn\Swiftype;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SwiftypeClient extends Client
{
    const CLIENT_TYPE_APP_SEARCH = 'as';

    const SWIFTYPE_VERSION = 'v1';
    const DEFAULT_USER_AGENT = 'Swiftype Laravel Plugin/' .  self::SWIFTYPE_VERSION;

    private const DEFAULT_HEADERS = [
        'User-Agent'   => self::DEFAULT_USER_AGENT,
        'Content-Type' => 'application/json',
        'Accept-Encoding' => 'gzip',
    ];

    /**
     * The URL endpoint that is the basis for all calls to the Swiftype API
     */


    /**
     * Swiftype constructor.
     *
     * @param $options
     */
    public function __construct(array $options) {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        // add our default headers
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($options) {
            foreach (self::DEFAULT_HEADERS as $header => $val) {
                $request = $request->withHeader($header, $val);
            }
            $request = $request->withHeader('Authorization', 'Bearer ' . $options['apiPrivateKey']);
            return $request;
        }), 'add_default_headers');
        // handle unsuccessful requests
        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Unknown response from Swiftype ' . $response->getStatusCode() . ' Message: ' . $response->getBody()->getContents());
            }
            return $response;
        }), 'check_unsuccessful');

        parent::__construct([
            // base url is from the host identifier and is only setup for the app search v1
            'base_uri' => $options['apiUrl'],
            'handler'  => $stack,
        ]);
    }


}
