<?php

use App\Exceptions\DataService\MissingUsername;
use App\Models\Core\Department;
use App\Models\User;

if (! function_exists('get_department_by_code')) {
    /**
     * @throws Throwable
     */
    function get_department_by_code(string $code = ''): ?Department
    {
        $message = 'Department shortname is required';

//        throw_if(mb_strlen($code) === 0, new Exception($message));

        return Department::where('code', '=', $code)->first();
    }
}

if (! function_exists('get_user_by_username')) {
    /**
     * @throws Throwable
     */
    function get_user_by_username(string $username = ''): ?User
    {
        throw_if(mb_strlen($username) === 0, new MissingUsername());

        return User::whereUsername($username)->first();
    }
}
