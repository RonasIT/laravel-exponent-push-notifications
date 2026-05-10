<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\ExpoRouter;

class ExpoRouterTest extends TestCase
{
    public function testDefaultRoutesAreRegisteredAutomatically(): void
    {
        $this->assertExpoRoutesRegistered();

        $this->assertFalse(ExpoRouter::$isBlockedBaseRoutes);
    }
}
