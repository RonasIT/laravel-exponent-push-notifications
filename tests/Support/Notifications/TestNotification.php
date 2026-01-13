<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class TestNotification extends Notification
{
    public function toExpoPush($notifiable): ExpoMessage
    {
        return new ExpoMessage();
    }
}
