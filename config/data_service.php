<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data Service Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration manages endpoints and URLs for the data service.
    |
    */

    'url' => [
        'live' => env('DATA_SERVICE_LIVE_URL'),
        'test' => env('DATA_SERVICE_TEST_URL'),
    ],

    'endpoints' => [
        'departments' => [
            'all' => env('ALL_DEPARTMENTS'),
            'single' => env('DEPARTMENT'),
        ],
        'staff' => [
            'all' => env('ALL_STAFF'),
            'by_username' => env('STAFF_BY_USERNAME'),
            'by_number' => env('STAFF_BY_NUMBER'),
        ],
        'students' => [
            'single' => env('STUDENT'),
            'all_current' => env('ALL_CURRENT_STUDENTS'),
            'all_with_open_accounts' => env('ALL_STUDENTS_WITH_OPEN_ACCOUNTS'),
        ],
        'kfs' => [
            'specific_kfs_vendor' => env('SPECIFIC_KFS_VENDOR'),
            'all_kfs_vendors' => env('ALL_KFS_VENDORS'),
        ],
    ],
];
