<?php

namespace App\Exceptions\DataService;

use Exception;

class UserNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('User not found.');
    }
}
