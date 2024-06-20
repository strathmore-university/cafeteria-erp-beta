<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ApiClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\DataService\ApiClient::class;
    }
}
