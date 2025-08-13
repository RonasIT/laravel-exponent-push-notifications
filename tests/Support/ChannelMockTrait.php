<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Expo;
use Illuminate\Notifications\Events\NotificationFailed;
use Mockery;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotifiable;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotification;
use Illuminate\Events\Dispatcher;

trait ChannelMockTrait
{
    protected function assertSendNotificationMessage(string $expectedFixture, bool $throwException = false): void
    {
        $expectedData = $this->getJsonFixture($expectedFixture);

        $expoMock = Mockery::mock(Expo::class);
        $events = Mockery::mock(Dispatcher::class);

        $expo = $expoMock
            ->shouldReceive('notify')
            ->once()
            ->with(['interest_name'], $expectedData, true);

        if ($throwException) {
            $expo->andThrow(ExpoException::class);

            $events
                ->shouldReceive('dispatch')
                ->once()
                ->with(Mockery::type(NotificationFailed::class));
        }
        else {
            $expo->andReturn(['status' => 'ok']);
        }

        (new ExpoChannel($expoMock, $events))->send(new TestNotifiable, new TestNotification);
    }
}
