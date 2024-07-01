<?php

use App\Models\Core\Setting;

if (!function_exists('setting')) {
    /**
     * @throws Throwable
     */
    function setting(string $key, string $group = ''): string
    {
        $setting = Setting::where('key', $key)
            ->when(filled($group), fn($query) => $query->where('group', $group))
            ->first();

        fire(blank($setting), 'Setting:' . $key . ' not found!');

        return $setting->value();
    }
}
