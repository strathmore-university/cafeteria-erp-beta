<?php

namespace App\Exceptions\DataService;

use Exception;

class MissingDepartmentId extends Exception
{
    public function __construct()
    {
        parent::__construct('Department ID is required.');
    }
}
