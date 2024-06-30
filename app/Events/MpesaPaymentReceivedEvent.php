<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class MpesaPaymentReceivedEvent
{
    use Dispatchable;

    public function __construct()
    {
    }

    public static function broadcast(): void
    {

    }
}
