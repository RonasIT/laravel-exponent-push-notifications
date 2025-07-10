<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Expo;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Mockery;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotifiable;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotification;

class ChannelTest extends TestCase
{
    protected Expo $expo;

    protected Dispatcher $events;

    protected ExpoChannel $channel;

    protected TestNotification $notification;

    protected TestNotifiable $notifiable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->expo = Mockery::mock(Expo::class);

        $this->events = Mockery::mock(Dispatcher::class);

        $this->channel = new ExpoChannel($this->expo, $this->events);

        $this->notification = new TestNotification;

        $this->notifiable = new TestNotifiable;
    }

    public function testItCanSendANotification()
    {
        $message = $this->notification->toExpoPush($this->notifiable);

        $this
            ->expo
            ->shouldReceive('notify')
            ->once()
            ->with(['interest_name'], $message->toArray(), true)
            ->andReturn(['status' => 'ok']);

        $this->channel->send($this->notifiable, $this->notification);
    }

    public function testItFiresFailureEventOnFailure()
    {
        $message = $this->notification->toExpoPush($this->notifiable);

        $this
            ->expo
            ->shouldReceive('notify')
            ->once()
            ->with(['interest_name'], $message->toArray(), true)
            ->andThrow(ExpoException::class, '');

        $this
            ->events
            ->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(NotificationFailed::class));

        $this->channel->send($this->notifiable, $this->notification);
    }
}
