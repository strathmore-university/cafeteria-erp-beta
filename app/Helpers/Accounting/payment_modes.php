<?php

use App\Models\Accounting\PaymentMode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

if ( ! function_exists('payment_modes')) {
    function payment_modes(): Collection
    {
        $key = payment_mode_cache_key();

        //Cache::forget($key);
        return Cache::rememberForever($key, fn () => PaymentMode::get());
    }
}

if ( ! function_exists('cache_payment_modes')) {
    function cache_payment_modes(): void
    {
        $key = payment_mode_cache_key();

        Cache::forget($key);
        Cache::rememberForever($key, fn () => PaymentMode::all());
    }
}

if ( ! function_exists('payment_mode')) {
    function payment_mode(string $mode): PaymentMode
    {
        return payment_modes()->firstWhere('name', '=', $mode);
    }
}

if ( ! function_exists('payment_mode_cache_key')) {
    function payment_mode_cache_key(): string
    {
        return 'payment_modes';
    }
}
