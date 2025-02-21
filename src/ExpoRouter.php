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

            $defaultOptions = [
                'subscribe' => true,
                'unsubscribe' => true,
            ];

            if (!empty($options)) {
                $options = collect($options);

                $defaultOptions = Arr::map($defaultOptions, fn ($value, $defaultOption) => $options->contains('value', $defaultOption));
            }

            $this->controller(ExpoController::class)->group(
                ['prefix' => 'exponent/devices', 'middleware' => 'expo.middleware'],
                function () use ($defaultOptions) {
                    when($defaultOptions['subscribe'], fn() => $this->post('subscribe', 'subscribe'));
                    when($defaultOptions['unsubscribe'], fn() => $this->post('unsubscribe', 'unsubscribe'));
                }
            );
        };
    }
}
