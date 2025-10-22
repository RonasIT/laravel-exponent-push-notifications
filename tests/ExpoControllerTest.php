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
use NotificationChannels\ExpoPushNotifications\Test\Support\ModelTestState;

class ExpoControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function testSubscribe($expoRepository): void
    {
        $data = ['expo_token' => 'ExponentPushToken[fakeToken]'];

        $response = $this->actingAs(self::$user)->json('POST', 'exponent/devices/subscribe', $data);

        $response->assertOk();

        $this->assertEqualsFixture('subscribe', $response->json());

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('subscribe');
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
            self::$interestTestState->assertNotChanged();
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
        $data = ['expo_token' => 'ExponentPushToken[2]'];

        $response = $this->actingAs(self::$secondUser)->json('POST', 'exponent/devices/unsubscribe', $data);

        $response->assertOk();

        $this->assertTrue($response['deleted']);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            self::$interestTestState->assertChangesEqualsFixture('unsubscribe');
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
        $data = ['expo_token' => null];

        $response = $this->actingAs(self::$secondUser)->json('POST', 'exponent/devices/unsubscribe', $data);

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
            self::$interestTestState->assertNotChanged();
        }
    }
}
