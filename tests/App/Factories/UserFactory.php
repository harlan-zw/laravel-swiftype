<?php

namespace Loonpwn\Swiftype\Tests\App\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Loonpwn\Swiftype\Tests\App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'created_at' => $this->faker->dateTimeBetween('-12 months'),
            'updated_at' => $this->faker->dateTimeBetween('-12 months'),
        ];
    }
}
