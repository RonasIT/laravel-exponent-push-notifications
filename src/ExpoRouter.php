<?php

namespace NotificationChannels\ExpoPushNotifications;

use Closure;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;

class ExpoRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public const string DEFAULT_PREFIX = 'exponent/devices';
    public const string DEFAULT_MIDDLEWARE = 'expo.middleware';

    public const array ROUTES = [
        ['method' => 'post', 'uri' => 'subscribe', 'action' => 'subscribe'],
        ['method' => 'post', 'uri' => 'unsubscribe', 'action' => 'unsubscribe'],
    ];

    public function expo(): Closure
    {
        return function (
            string $prefix = ExpoRouter::DEFAULT_PREFIX,
            array|string $middleware = ExpoRouter::DEFAULT_MIDDLEWARE,
        ) {
            ExpoRouter::$isBlockedBaseRoutes = true;

            $this->group([
                'prefix' => $prefix,
                'middleware' => $middleware,
            ], function () {
                $this->controller(ExpoController::class)->group(function () {
                    foreach (ExpoRouter::ROUTES as $route) {
                        $method = $route['method'];

                        $this->{$method}($route['uri'], $route['action']);
                    }
                });
            });
        };
    }
}
