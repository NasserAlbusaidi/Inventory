<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Cache::rememberForever('app_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });
    }

    public function get($key, $default = null)
    {
        return $this->settings->get($key, $default);
    }

    public function flushCache()
    {
        Cache::forget('app_settings');
    }
}
