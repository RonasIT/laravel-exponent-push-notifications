<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExpoCustomMacroCallerProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::expo(
            prefix: 'custom/prefix',
            middleware: ['custom.middleware'],
        );
    }
}
