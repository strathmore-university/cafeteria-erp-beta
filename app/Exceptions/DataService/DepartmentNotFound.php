<?php

namespace App\Exceptions\DataService;

use Exception;

class DepartmentNotFound extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'No Department found.');
    }
}
