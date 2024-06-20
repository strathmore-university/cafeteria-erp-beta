<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BooleanEvaluator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\Core\BooleanEvaluator::class;
    }
}
