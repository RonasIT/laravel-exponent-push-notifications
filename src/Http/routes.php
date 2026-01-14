<?php

use Illuminate\Support\Facades\Route;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;

Route::prefix('exponent/devices')
    ->middleware('expo.middleware')
    ->group(function () {
        Route::controller(ExpoController::class)->group(function () {
            Route::post('subscribe', 'subscribe');
            Route::post('unsubscribe', 'unsubscribe');
        });
    });
