<?php

namespace Loonpwn\Swiftype\Tests\Integration;

use Elastic\EnterpriseSearch\AppSearch\Request\ListEngines;
use Loonpwn\Swiftype\Tests\BaseTestCase;

class FacadeTestCase extends BaseTestCase
{
    /**
     * @test
     */
    public function check_list_engines_has_results()
    {
        $result = $this->client->listEngines(new ListEngines);

        $this->assertArrayHasKey('results', $result, 'Can List engines');
        $this->log('Found engines', count($result['results']));
    }
}
