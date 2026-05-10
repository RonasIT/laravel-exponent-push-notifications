<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

class ExpoRouterTest extends TestCase
{
    public function testDefaultRoutesAreRegisteredWhenMacroNotCalled(): void
    {
        $this->assertExpoRoutesRegistered();
    }
}
