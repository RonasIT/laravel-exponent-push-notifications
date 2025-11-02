<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Repositories\ExpoFileDriver;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;
use NotificationChannels\ExpoPushNotifications\Test\database\Models\User;
use NotificationChannels\ExpoPushNotifications\Models\Interest;
use NotificationChannels\ExpoPushNotifications\Test\Support\ModelTestState;
use PHPUnit\Framework\Attributes\DataProvider;
use ExponentPhpSDK\ExpoRepository;

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

        $this->bindExpoRepository();
    }

    /**
     * Data provider to help test the expo controller with the different repositories.
     *
     * @return array
     */
    public static function getExpoDriver(): array
    {
        return [
            [new ExpoDatabaseDriver],
            [new ExpoFileDriver],
        ];
    }

    #[DataProvider('getExpoDriver')]
    public function testSubscribe(ExpoRepository $driver): void
    {
        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/subscribe', [
            'expo_token' => 'ExponentPushToken[fakeToken]',
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('subscribe', $response->json());

        if ($driver instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('subscribe');
        }
    }

    public function testSubscribeInvalidToken(): void
    {
        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/subscribe', [
            'expo_token' => null,
        ]);

        $response->assertUnprocessable();

        $response->assertJson([
            'status' => 'failed',
            'errors' => [
                'expo_token' => [
                    'The expo token field is required.',
                ],
            ],
        ]);

        self::$interestTestState->assertNotChanged();
    }

    #[DataProvider('getExpoDriver')]
    public function testUnsubscribe(ExpoRepository $driver): void
    {
        $response = $this->actingAs(self::$secondUser)->json('POST', 'exponent/devices/unsubscribe', [
            'expo_token' => 'ExponentPushToken[2]'
        ]);

        $response->assertOk();

        $this->assertTrue($response['deleted']);

        if ($driver instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('unsubscribe');
        }
    }

    public function testUnsubscribeInvalidToken(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('POST', 'exponent/devices/unsubscribe', [
            'expo_token' => null,
        ]);

        $response->assertUnprocessable();

        $response->assertJson([
            'status' => 'failed',
            'errors' => [
                'expo_token' => [
                    'The expo token field must be a string.',
                ],
            ],
        ]);

        self::$interestTestState->assertNotChanged();
    }
}
