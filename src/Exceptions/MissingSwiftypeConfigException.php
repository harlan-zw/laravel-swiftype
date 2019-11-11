<?php

namespace Loonpwn\Swiftype\Exceptions;

use Exception;

class MissingSwiftypeConfigException extends Exception
{
    /**
     * MissingSwiftypeConfigException constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->message = 'Missing required config. Please add the '.$key.' to your .env file.';
        $this->code = 500;
    }
}
