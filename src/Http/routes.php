<?php

use Illuminate\Support\Facades\Route;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;

Route::prefix(ExpoRouter::DEFAULT_PREFIX)
    ->middleware(ExpoRouter::DEFAULT_MIDDLEWARE)
    ->group(function () {
        Route::controller(ExpoController::class)->group(function () {
            foreach (ExpoRouter::ROUTES as $route) {
                Route::{$route['method']}($route['uri'], $route['action']);
            }
        });
    });
