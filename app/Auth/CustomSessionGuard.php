<?php

namespace App\Auth;

use Illuminate\Auth\SessionGuard;

class CustomSessionGuard extends SessionGuard
{
    // The number of minutes that the "remember me" cookie should be valid for.
    protected $rememberDuration = 7200; // 5 ngay
}
