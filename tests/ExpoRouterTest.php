<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use Illuminate\Support\Facades\Route;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;

class ExpoRouterTest extends TestCase
{
    protected function tearDown(): void
    {
        ExpoRouter::$isBlockedBaseRoutes = false;

        parent::tearDown();
    }

    public function testDefaultRoutesAreRegisteredAutomatically(): void
    {
        $this->assertExpoRoutesRegistered();

        $this->assertFalse(ExpoRouter::$isBlockedBaseRoutes);
    }

    public function testDefaultRoutesAreBlockedWhenExpoMacroIsCalled(): void
    {
        Route::expo();

        $this->assertExpoRoutesRegistered();

        $this->assertTrue(ExpoRouter::$isBlockedBaseRoutes);
    }
}
