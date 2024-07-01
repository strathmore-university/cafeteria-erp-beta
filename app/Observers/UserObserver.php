<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AttributeSanitizers\UserSanitizer;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        self::formatAttributes($user);
    }

    public function updating(User $user): void
    {
        self::formatAttributes($user);
    }

    private static function formatAttributes(User $user): void
    {
        $user->email = Str::lower($user->email);

        $user->username = Str::lower($user->username);

        $user->first_name = Str::title(Str::lower($user->first_name));

        $user->last_name = Str::title(Str::lower($user->last_name));

        $user->other_names = UserSanitizer::otherNames($user->other_names);

        $name = $user->first_name . ' ' . $user->other_names;

        $name .= ' ' . $user->last_name;

        $user->name = Str::title(str_replace('  ', ' ', $name));

        $user->phone_number = UserSanitizer::phoneNumber($user->phone_number);
    }
}
