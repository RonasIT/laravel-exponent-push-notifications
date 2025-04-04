<?php

namespace NotificationChannels\ExpoPushNotifications;

use Illuminate\Support\Arr;
use NotificationChannels\ExpoPushNotifications\Enum\ExpoRouterActionEnum;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;

class ExpoRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public function expo()
    {
        return function (ExpoRouterActionEnum ...$options) {
            ExpoRouter::$isBlockedBaseRoutes = true;

            $routerActions = array_column(ExpoRouterActionEnum::cases(), 'value');

            $defaultOptions = array_fill_keys($routerActions, true);

            if (!empty($options)) {
                $options = collect($options);

                $defaultOptions = Arr::map($defaultOptions, fn ($value, $defaultOption) => $options->contains('value', $defaultOption));
            }

            $this->group([
                'prefix' => 'exponent/devices',
                'middleware' => 'expo.middleware',
            ], function () use ($defaultOptions) {
                $this->controller(ExpoController::class)->group(function () use ($defaultOptions) {
                    when($defaultOptions['subscribe'], fn () => $this->post('subscribe', 'subscribe'));
                    when($defaultOptions['unsubscribe'], fn () => $this->post('unsubscribe', 'unsubscribe'));
                });
            });
        };
    }
}
