<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DepartmentLookup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\DataService\DepartmentLookup::class;
    }
}
