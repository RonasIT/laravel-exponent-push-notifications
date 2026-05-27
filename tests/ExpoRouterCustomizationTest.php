<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;
use NotificationChannels\ExpoPushNotifications\Test\Support\Providers\ExpoRoutesWithCustomPrefixProvider;

class ExpoRouterCustomizationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ExpoPushNotificationsServiceProvider::class,
            ExpoRoutesWithCustomPrefixProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        ExpoRouter::$isBlockedBaseRoutes = false;

        parent::tearDown();
    }

    public function testRoutesCanBeRegisteredWithCustomPrefix(): void
    {
        $this->assertTrue(ExpoRouter::$isBlockedBaseRoutes);

        $this->assertRouteRegistered('POST', 'api/exponent/devices/subscribe');
        $this->assertRouteRegistered('POST', 'api/exponent/devices/unsubscribe');

        $this->assertRouteNotRegistered('POST', 'exponent/devices/subscribe');
        $this->assertRouteNotRegistered('POST', 'exponent/devices/unsubscribe');
    }
}
