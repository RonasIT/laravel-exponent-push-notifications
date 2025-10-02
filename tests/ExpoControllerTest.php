<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Repositories\ExpoFileDriver;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;
use NotificationChannels\ExpoPushNotifications\Test\database\Models\User;
use ExponentPhpSDK\ExpoRepository;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use NotificationChannels\ExpoPushNotifications\Models\Interest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected static User $user;

    protected function setUp(): void
    {
        parent::setUp();

        self::$user = User::create([
            'email' => 'test@email.ru',
        ]);

        $this->bindExpoRepository();
    }

    protected function bindExpoRepository(): void
    {
        $expoRepository = new ExpoDatabaseDriver();

        $this->app->bind(ExpoRepository::class, fn () => $expoRepository);

        $this->app->bind(ExpoChannel::class, fn ($app) => new ExpoChannel(
            expo: new Expo(new ExpoRegistrar($expoRepository)),
            events: $app['events']
        ));
    }

    /**
     * Data provider to help test the expo controller with the different repositories.
     *
     * @return array
     */
    public static function availableRepositories()
    {
        return [
            [new ExpoDatabaseDriver],
            [new ExpoFileDriver],
        ];
    }

    /**
     * @test
     *
     * @param $expoRepository
     *
     * @dataProvider availableRepositories
     */

    public function aDeviceCanSubscribeToTheSystem($expoRepository)
    {
        $data = ['expo_token' => 'ExponentPushToken[fakeToken]'];

        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/subscribe', $data);

        $response->assertOk();

        $this->assertEquals('succeeded', $response['status']);

        $this->assertEquals($data['expo_token'], $response['expo_token']);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseHas('exponent_push_notification_interests', [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.1',
                'value' => $data['expo_token'],
            ]);
        }
    }

    /**
     * @test
     *
     * @param $expoRepository
     *
     * @dataProvider availableRepositories
     */

    public function subscribeReturnsErrorResponseIfTokenInvalid($expoRepository)
    {
        $data = ['expo_token' => null];

        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/subscribe', $data);

        $response->assertUnprocessable();

        $response->assertJson([
            'status' => 'failed',
            'errors' => [
                'expo_token' => [
                    'The expo token field is required.',
                ],
            ],
        ]);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseMissing('exponent_push_notification_interests', [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.1',
            ]);
        }
    }

    /**
     * @test
     *
     * @dataProvider availableRepositories
     *
     * @param $expoRepository
     */
    public function aDeviceCanUnsubscribeSingleTokenFromTheSystem($expoRepository)
    {
        Interest::create([
            'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.1',
            'value' => 'ExponentPushToken[1]',
        ]);

        $data = ['expo_token' => 'ExponentPushToken[1]'];

        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/unsubscribe', $data);

        $response->assertOk();

        $this->assertTrue($response['deleted']);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseMissing(config('exponent-push-notifications.interests.database.table_name'), [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.2',
                'value' => $data['expo_token'],
            ]);
        }
    }

    /**
     * @test
     *
     * @dataProvider availableRepositories
     *
     * @param $expoRepository
     */
    public function unsubscribeReturnsErrorResponseIfExceptionIsThrown($expoRepository)
    {
        Interest::create([
            'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.1',
            'value' => 'ExponentPushToken[1]',
        ]);

        $data = ['expo_token' => null];

        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/unsubscribe', $data);

        $response->assertUnprocessable();

        $response->assertJson([
            'status' => 'failed',
            'errors' => [
                'expo_token' => [
                    'The expo token field must be a string.',
                ],
            ],
        ]);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseHas('exponent_push_notification_interests', [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.database.Models.User.1',
            ]);
        }
    }
}
