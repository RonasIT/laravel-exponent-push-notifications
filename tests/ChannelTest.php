<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Expo;
use Illuminate\Events\Dispatcher;
use Mockery;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Test\Support\ChannelMockTrait;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotifiable;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotification;

class ChannelTest extends TestCase
{
    use ChannelMockTrait;

    protected TestNotifiable $notifiable;
    protected Expo $expoMock;
    protected Dispatcher $events;
    protected ExpoChannel $expoChannel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notification = new TestNotification();
        $this->expoMock = Mockery::mock(Expo::class);
        $this->events = Mockery::mock(Dispatcher::class);
        $this->expoChannel = new ExpoChannel($this->expoMock, $this->events);
    }

    public function testItCanSendANotification()
    {
        $this->assertNotificationSend('channel_message.json');

        $this->expoChannel->send(new TestNotifiable(), new TestNotification());
    }

    public function testItFiresFailureEventOnFailure()
    {
        $this->assertNotificationNotSend('channel_message.json');

        $this->expoChannel->send(new TestNotifiable(), new TestNotification());
    }
}
