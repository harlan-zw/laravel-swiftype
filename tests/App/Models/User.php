<?php

namespace Loonpwn\Swiftype\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class User extends Model
{
    use Searchable;

    public $table = 'users';

    public $guarded = [];

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->email;
    }

    public function shouldBeSearchable()
    {
        return true;
    }
}
