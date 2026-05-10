<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;
use NotificationChannels\ExpoPushNotifications\Test\Support\Providers\ExpoMacroCallerProvider;

class ExpoRouterBlockingTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ExpoPushNotificationsServiceProvider::class,
            ExpoMacroCallerProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        ExpoRouter::$isBlockedBaseRoutes = false;

        parent::tearDown();
    }

    public function testDefaultRoutesAreBlockedWhenExpoMacroIsCalled(): void
    {
        $this->assertExpoRoutesRegistered();

        $this->assertTrue(ExpoRouter::$isBlockedBaseRoutes);

        $this->assertExpoRoutesCount();
    }
}
