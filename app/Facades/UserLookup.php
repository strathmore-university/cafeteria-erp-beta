<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserLookup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\DataService\UserLookup::class;
    }
}
