<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use NotificationChannels\ExpoPushNotifications\Test\Support\ChannelMockTrait;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotifiable;
use NotificationChannels\ExpoPushNotifications\Test\Support\Notifications\TestNotification;

class ChannelTest extends TestCase
{
    use ChannelMockTrait;

    protected TestNotifiable $notifiable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notification = new TestNotification;
    }

    public function testItCanSendANotification()
    {
        $this->notification->toExpoPush(new TestNotifiable);

        $this->assertSendNotificationMessage('channel_message.json');
    }

    public function testItFiresFailureEventOnFailure()
    {
        $this->notification->toExpoPush(new TestNotifiable);

        $this->assertSendNotificationMessage(
            expectedFixture: 'channel_message.json',
            throwException: true,
        );
    }
}
