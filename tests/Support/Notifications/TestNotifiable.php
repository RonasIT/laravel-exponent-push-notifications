<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Notifications;

use Illuminate\Notifications\Notifiable;

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForExpoPushNotifications(): string
    {
        return 'interest_name';
    }

    public function getKey(): int
    {
        return 1;
    }
}
