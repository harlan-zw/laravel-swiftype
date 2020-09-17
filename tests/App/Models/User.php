<?php

namespace Loonpwn\Swiftype\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Loonpwn\Swiftype\Models\Traits\IsSwiftypeDocument;
use Loonpwn\Swiftype\Tests\App\Factories\UserFactory;

class User extends Model
{
    use IsSwiftypeDocument, HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public $table = 'users';

    public $guarded = [];
}
