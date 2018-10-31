<?php
namespace Loonpwn\Swiftype\Tests\Feature;

use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;
use Loonpwn\Swiftype\Tests\BaseSwiftypeTest;
use Loonpwn\Swiftype\Tests\TestModel;

class SwiftypeEngineFacadeTest extends BaseSwiftypeTest
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDocumentListWorks()
    {
    	$documents = SwiftypeEngine::listDocuments();
        $this->assertArrayHasKey('results', $documents, 'We can list engine documents');
        var_dump('Found documents', $documents['results']);
    }

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSearchWorks()
	{
		$documents = SwiftypeEngine::search('test');
		$this->assertArrayHasKey('results', $documents, 'We can search engine documents');
		var_dump('Found documents', $documents['results']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreateWorks()
	{
		$document = new TestModel();
		$document->save();

		$this->assertArrayHasKey('results', $documents, 'We can search engine documents');
		var_dump('Found documents', $documents['results']);
	}


}
