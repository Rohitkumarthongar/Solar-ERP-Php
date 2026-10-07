<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

class AdminPassword
{
    public static function rule(): Password
    {
        return Password::min(12)->mixedCase()->numbers()->symbols();
    }
}
