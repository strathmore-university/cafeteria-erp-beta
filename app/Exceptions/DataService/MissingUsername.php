<?php

namespace App\Exceptions\DataService;

use Exception;

class MissingUsername extends Exception
{
    public function __construct()
    {
        parent::__construct('Username is required.');
    }
}
