<?php

namespace Loonpwn\Swiftype\Errors;

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
        $this->message = 'Missing Config Exception! Add the '.$key.' to your .env file.';
        $this->code = 500;
    }
}
