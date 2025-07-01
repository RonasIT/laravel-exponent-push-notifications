<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Notifications;

use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    public function toExpoPush($notifiable): ExpoMessage
    {
        return new ExpoMessage();
    }
}
