<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;
use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessToken extends SanctumToken
{
    protected $connection = 'mongodb';  // Conectar a MongoDB
}
