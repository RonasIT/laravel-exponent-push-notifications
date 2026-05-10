<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;
use NotificationChannels\ExpoPushNotifications\Test\Support\Providers\ExpoCustomMacroCallerProvider;

class ExpoRouterCustomizationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ExpoPushNotificationsServiceProvider::class,
            ExpoCustomMacroCallerProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        ExpoRouter::$isBlockedBaseRoutes = false;

        parent::tearDown();
    }

    public function testRoutesAreRegisteredWithCustomPrefixAndMiddleware(): void
    {
        $this->assertExpoRoutesRegistered(
            prefix: 'custom/prefix',
            middleware: ['custom.middleware'],
        );
    }
}
