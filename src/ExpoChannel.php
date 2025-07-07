<?php

namespace NotificationChannels\ExpoPushNotifications;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Expo;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

class ExpoChannel
{
    public function __construct(
        public Expo $expo,
        private Dispatcher $events,
    ) {
    }

    public function send(mixed $notifiable, Notification $notification): array|bool
    {
        $interest = $notifiable->routeNotificationFor('ExpoPushNotifications')
            ?? $this->interestName($notifiable);

        try {
            return $this->expo->notify(
                [$interest],
                $notification->toExpoPush($notifiable)->toArray(),
                config('exponent-push-notifications.debug'),
            );
        } catch (ExpoException $e) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'expo-push-notifications', $e->getMessage())
            );
        }

        return false;
    }

    public function interestName($notifiable): string
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return "{$class}.{$notifiable->getKey()}";
    }
}
