<?php

namespace App\Models\User;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;
    // thiet lap lai duong dan file factory do User trong App\Models\User mac dinh factory lay duong dan Database\Factories\User
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}