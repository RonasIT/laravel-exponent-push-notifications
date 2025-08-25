<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support;

use ExponentPhpSDK\Exceptions\ExpoException;
use Illuminate\Notifications\Events\NotificationFailed;
use Mockery;

trait ChannelMockTrait
{
    protected function assertNotificationSend(string $expectedFixture): void
    {
        $this
            ->expoMock
            ->shouldReceive('notify')
            ->once()
            ->with(['interest_name'], $this->getJsonFixture($expectedFixture), true)
            ->andReturn(['status' => 'ok']);
    }

    protected function assertNotificationNotSend(string $expectedFixture): void
    {
        $this
            ->expoMock
            ->shouldReceive('notify')
            ->once()
            ->with(['interest_name'], $this->getJsonFixture($expectedFixture), true)
            ->andThrow(ExpoException::class);

        $this
            ->events
            ->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(NotificationFailed::class));
    }
}
