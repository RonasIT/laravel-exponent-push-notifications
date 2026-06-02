<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExpoRoutesWithCustomPrefixProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api')->group(fn () => Route::expo());
    }
}
