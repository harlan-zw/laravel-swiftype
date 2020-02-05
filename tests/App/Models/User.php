<?php

namespace Loonpwn\Swiftype\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class User extends Model
{
    use Searchable;

    public $table = 'users';

    public $guarded = [];

    public function toSearchableArray()
    {
        return collect($this->toArray())->toArray();
    }

    public function getScoutSearchFields()
    {
        return [
            'name' => [
                'weight' => 10,
            ],
            'email' => [
                'weight' => 7,
            ],
        ];
    }

}
