<?php

namespace Loonpwn\Swiftype\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Loonpwn\Swiftype\Models\Traits\IsSwiftypeDocument;

class User extends Model
{
    use IsSwiftypeDocument;

    public $table = 'users';

    public $guarded = [];

}
