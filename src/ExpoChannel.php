<?php

namespace NotificationChannels\ExpoPushNotifications;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Expo;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\Exceptions\CouldNotSendNotification;

class ExpoChannel
{

    /**
     * ExpoChannel constructor.
     *
     * @param  Expo  $expo
     * @param  Dispatcher  $events
     */
    public function __construct(
        public Expo $expo,
        private Dispatcher $events,
    )
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('ExpoPushNotifications')
            ?: $this->interestName($notifiable);

        $interests = [$interest];

        try {
            $this->expo->notify(
                $interests,
                $notification->toExpoPush($notifiable)->toArray(),
                config('exponent-push-notifications.debug')
            );
        } catch (ExpoException $e) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'expo-push-notifications', $e->getMessage())
            );
        }
    }

    /**
     * Get the interest name for the notifiable.
     *
     * @param $notifiable
     * @return string
     */
    public function interestName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
