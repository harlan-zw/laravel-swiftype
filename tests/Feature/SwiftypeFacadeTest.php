<?php

namespace Loonpwn\Swiftype\Tests\Feature;

use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Tests\BaseSwiftypeTest;

class SwiftypeFacadeTest extends BaseSwiftypeTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCheckAuthentication()
    {
        $this->log('Starting testCheckAuthentication');
        $this->assertTrue(Swiftype::authenticated(), 'Swiftype Authentication is working');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanListEngines()
    {
        $this->log('Starting testCanListEngines');
        $engines = Swiftype::listEngines();
        $this->assertArrayHasKey('results', $engines, 'Can List engines');
        $this->log('Found engines', count($engines['results']));
    }
}
