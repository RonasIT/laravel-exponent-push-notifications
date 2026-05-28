<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\ExpoRouter;

class ExpoRouterTest extends TestCase
{
    public function testDefaultRoutesAreRegisteredAutomatically(): void
    {
        $this->assertFalse(ExpoRouter::$isBlockedBaseRoutes);

        $this->assertRouteRegistered('POST', 'exponent/devices/subscribe');
        $this->assertRouteRegistered('POST', 'exponent/devices/unsubscribe');
    }
}
