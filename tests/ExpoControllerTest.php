<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\ExpoRepository;
use ExponentPhpSDK\Repositories\ExpoFileDriver;
use NotificationChannels\ExpoPushNotifications\Models\Interest;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;
use NotificationChannels\ExpoPushNotifications\Test\database\Models\User;
use NotificationChannels\ExpoPushNotifications\Test\Support\ModelTestState;
use PHPUnit\Framework\Attributes\DataProvider;

class ExpoControllerTest extends TestCase
{
    protected static User $user;
    protected static User $secondUser;

    protected static ModelTestState $interestTestState;

    protected function setUp(): void
    {
        parent::setUp();

        self::$user ??= User::find(1);
        self::$secondUser ??= User::find(2);

        self::$interestTestState ??= new ModelTestState(Interest::class);
    }

    public static function getExpoDriver(): array
    {
        return [
            ['driver' => new ExpoDatabaseDriver()],
            ['driver' => new ExpoFileDriver()],
        ];
    }

    #[DataProvider('getExpoDriver')]
    public function testSubscribe(ExpoRepository $driver): void
    {
        $this->bindExpoRepository($driver);

        $response = $this->actingAs(self::$user)->json('post', 'exponent/devices/subscribe', [
            'expo_token' => 'ExponentPushToken[fakeToken]',
        ]);

        $response->assertNoContent();

        if ($driver instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('subscribe');
        }
    }

    public function testSubscribeInvalidToken(): void
    {
        $response = $this->actingAs(self::$user)->json('post', 'exponent/devices/subscribe', [
            'expo_token' => null,
        ]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The expo token field is required.']);

        self::$interestTestState->assertNotChanged();
    }

    #[DataProvider('getExpoDriver')]
    public function testUnsubscribe(ExpoRepository $driver): void
    {
        $this->bindExpoRepository($driver);

        $response = $this->actingAs(self::$secondUser)->json('post', 'exponent/devices/unsubscribe', [
            'expo_token' => 'ExponentPushToken[2]',
        ]);

        $response->assertNoContent();

        if ($driver instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('unsubscribe');
        }
    }

    public function testUnsubscribeInvalidToken(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('post', 'exponent/devices/unsubscribe', [
            'expo_token' => null,
        ]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The expo token field must be a string.']);

        self::$interestTestState->assertNotChanged();
    }
}
