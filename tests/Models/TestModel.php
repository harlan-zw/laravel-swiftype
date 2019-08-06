<?php

namespace Loonpwn\Swiftype\Tests\Models;

use Ramsey\Uuid\Uuid;
use Loonpwn\Swiftype\Traits\ExistsAsSwiftypeDocument;

class TestModel
{
    use ExistsAsSwiftypeDocument;

    public function getAttributes()
    {
        $faker = \Faker\Factory::create();

        return [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
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
