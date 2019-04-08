<?php

namespace Loonpwn\Swiftype\Tests;

use Loonpwn\Swiftype\Traits\ExistsAsSwiftypeDocument;

class TestModel
{
    use ExistsAsSwiftypeDocument;

    public function getAttributes()
    {
        return [
            'field_1' => 'test-1',
            'field_2' => 'test-2',
            'field_3' => 'test-3',
            'field_4' => 'test-4',
        ];
    }

    public function getKeyName()
    {
        return 'test_model_id';
    }

    public function getKey()
    {
        return rand(10000, 99999);
    }
}
