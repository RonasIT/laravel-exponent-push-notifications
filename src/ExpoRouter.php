<?php

namespace NotificationChannels\ExpoPushNotifications;

class ExpoRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public static function routes(): void
    {
        static::$isBlockedBaseRoutes = true;

        require __DIR__ . '/Http/routes.php';
    }
}
