<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use Illuminate\Support\Facades\Route;
use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\ExpoRouter;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;
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

        $expoRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_contains($route->getActionName(), ExpoController::class));

        $this->assertCount(
            expectedCount: count(ExpoRouter::ROUTES),
            haystack: $expoRoutes,
            message: 'Default routes were not blocked, resulting in duplicate route registrations.',
        );
    }
}
