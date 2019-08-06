<?php

namespace Loonpwn\Swiftype\Tests\Models;

use Ramsey\Uuid\Uuid;
use Loonpwn\Swiftype\Traits\IsSwiftypeDocument;

class TestModel
{
    use IsSwiftypeDocument;

    public function getAttributes()
    {
        $faker = \Faker\Factory::create();

        return [
            'advisor_name' => $faker->name,
            'location' => $faker->address,
            'created_at' => $faker->dateTime(),
        ];
    }

    public function getKeyName()
    {
        return 'test_model_id';
    }

    public function getKey()
    {
        return Uuid::uuid4();
    }
}
