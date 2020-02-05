<?php
namespace Loonpwn\Swiftype\Tests\Integration;

use Loonpwn\Swiftype\Tests\App\Models\User;
use Loonpwn\Swiftype\Tests\BaseTestCase;

class ModelTests extends BaseTestCase
{
    /**
     * @test
     */
    public function deletes_model_on_delete()
    {
        // create a user, will trigger it to exist in Swiftype
        /** @var User $user */
        $user = factory(User::class)->create();

        $documents = $this->engine->listDocuments();

        $this->assertEquals($user->getKey(), $documents['results'][0]['id']);

        // delete the user
        $user->delete();

        $documents = $this->engine->listDocuments();

        $this->assertEmpty($documents['results']);
    }

    /**
     * @test
     */
    public function where_query_works()
    {
        // create a user, will trigger it to exist in Swiftype
        /** @var User $user */
        factory(User::class)->create([
            'email' => 'test@test.com'
        ]);

        $user = User::search()->where('email', 'test@test.com')->first();

        $this->assertEquals($user->email, 'test@test.com');
    }

    /**
     * @test
     */
    public function engine_query_works()
    {
        $user = factory(User::class)->times(5)->create()->first();

        $users = User::search(explode(' ', $user->name)[1], function ($options) {
            return $options;
        })->get();

        $this->assertNotEmpty($users);
    }


}
