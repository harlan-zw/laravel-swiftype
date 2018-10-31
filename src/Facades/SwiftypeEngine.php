<?php

namespace Loonpwn\Swiftype\Facades;

use Illuminate\Support\Facades\Facade;

class SwiftypeEngine extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swiftype-engine';
    }
}
