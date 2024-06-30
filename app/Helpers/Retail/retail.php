<?php

use App\Models\Retail\RetailSession;
use Illuminate\Support\Facades\Cache;

if ( ! function_exists('retail_session')) {
    function retail_session(): ?RetailSession
    {
        $key = cache_retail_session_key();
        $expiresAt = now()->endOfDay();

        return Cache::remember($key, $expiresAt, function () {
            return RetailSession::whereCashierId(auth_id())
                ->whereDate('created_at', '=', today())
                ->whereIsOpen(true)
                ->first();
        });
    }
}

if ( ! function_exists('cache_retail_session')) {
    function cache_retail_session(RetailSession $session): void
    {
        $key = cache_retail_session_key();
        Cache::put($key, $session, now()->endOfDay());
    }
}

if ( ! function_exists('cache_retail_session_key')) {
    function cache_retail_session_key(): string
    {
        return 'user_' . auth_id() . 'retail_session';
    }
}
