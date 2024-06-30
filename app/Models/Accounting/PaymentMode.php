<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        parent::created(fn () => cache_payment_modes());
        parent::updated(fn () => cache_payment_modes());
        parent::deleted(fn () => cache_payment_modes());
    }
}
