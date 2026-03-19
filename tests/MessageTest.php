<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use Illuminate\Support\Arr;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class MessageTest extends TestCase
{
    /**
     * @var ExpoMessage
     */
    protected $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new ExpoMessage();
    }

    public function testProvidesACreateMethod()
    {
        $message = ExpoMessage::create('myMessage');

        $this->assertEquals('myMessage', Arr::get($message->toArray(), 'body'));
    }

    public function testCanAcceptsABodyWhenConstructingAMessage()
    {
        $message = new ExpoMessage('myMessage');

        $this->assertEquals('myMessage', Arr::get($message->toArray(), 'body'));
    }

    public function testProvidesACreateMethodThatAcceptsAMessageBody()
    {
        $message = new ExpoMessage('myMessage');

        $this->assertEquals('myMessage', Arr::get($message->toArray(), 'body'));
    }

    public function testSetsABodyToTheMessage()
    {
        $this->message->body('myMessage');

        $this->assertEquals('myMessage', Arr::get($this->message->toArray(), 'body'));
    }

    public function testSetsADefaultSound()
    {
        $this->assertEquals('default', Arr::get($this->message->toArray(), 'sound'));
    }

    public function testCanMuteSound()
    {
        $this->message->disableSound();

        $this->assertNull(Arr::get($this->message->toArray(), 'sound'));
    }

    public function testCanEnableSound()
    {
        $this->message->disableSound();
        $this->message->enableSound();

        $this->assertNotNull(Arr::get($this->message->toArray(), 'sound'));
    }

    public function testCanSetTheBadge()
    {
        $this->message->badge(5);

        $this->assertEquals(5, Arr::get($this->message->toArray(), 'badge'));
    }

    public function testCanSetTimeToLive()
    {
        $this->message->setTtl(60);

        $this->assertEquals(60, Arr::get($this->message->toArray(), 'ttl'));
    }

    public function testCanSetChannelId()
    {
        $this->message->setChannelId('some-channel');

        $this->assertEquals('some-channel', Arr::get($this->message->toArray(), 'channelId'));
    }

    public function testCanSetJSONData()
    {
        $this->message->setJsonData('{"name":"Aly"}');

        $this->assertEquals('{"name":"Aly"}', Arr::get($this->message->toArray(), 'data'));
    }
}
