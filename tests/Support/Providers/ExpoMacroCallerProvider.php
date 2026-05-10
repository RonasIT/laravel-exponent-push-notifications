<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExpoMacroCallerProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::expo();
    }
}
