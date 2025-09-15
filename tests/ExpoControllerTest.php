<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use ExponentPhpSDK\ExpoRepository;
use ExponentPhpSDK\Repositories\ExpoFileDriver;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;
use NotificationChannels\ExpoPushNotifications\Http\Requests\SubscribeRequest;
use NotificationChannels\ExpoPushNotifications\Http\Requests\UnsubscribeRequest;
use RonasIT\Support\Testing\ModelTestState;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ExpoControllerTest extends TestCase
{
    /**
     * @var ExpoController
     */
    protected $expoController;

    protected static User $user;
    protected static User $secondUser;

    protected static ModelTestState $expoState;

    /**
     * Sets up the expo controller with the given expo channel.
     *
     * @param  ExpoRepository  $expoRepository
     * @return array
     */
    protected function setupExpo(ExpoRepository $expoRepository)
    {
        $expoChannel = new ExpoChannel(new Expo(new ExpoRegistrar($expoRepository)), new Dispatcher);
        $expoController = new ExpoController($expoChannel);

        return [$expoController, $expoChannel];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        // We will fake an authenticated user
        Auth::shouldReceive('user')->andReturn(new User());
    }

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
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
        [$expoController, $expoChannel] = $this->setupExpo($expoRepository);

        // We will fake a request with the following data
        $data = ['expo_token' => 'ExponentPushToken[fakeToken]'];
        $request = $this->mockRequest($data, SubscribeRequest::class);

        $request->shouldReceive('validated')
            ->once()
            ->andReturn($data);

        $user = new User();

        $request->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $request->shouldReceive('get')
            ->with('expo_token')
            ->andReturn($data['expo_token']);

        /** @var Request $request */
        $response = $expoController->subscribe($request)->resource;
        // The response should contain a succeeded status
        $this->assertEquals('succeeded', $response['status']);
        // The response should return the registered token
        $this->assertEquals($data['expo_token'], $response['expo_token']);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseHas(config('exponent-push-notifications.interests.database.table_name'), [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.User.'.(new User)->getKey(),
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
        [$expoController, $expoChannel] = $this->setupExpo($expoRepository);

        $data = ['expo_token' => null];
        $request = $this->mockRequest($data, SubscribeRequest::class);

        $request->shouldReceive('validated')
            ->once()
            ->andThrow(new UnprocessableEntityHttpException(json_encode([
                'status' => 'failed',
                'error'  => ['expo_token' => ['The expo token field is required.']],
            ])));

        try {
            $expoController->subscribe($request);
            $this->fail('Expected UnprocessableEntityHttpException was not thrown');
        } catch (UnprocessableEntityHttpException $exception) {
            $payload = json_decode($exception->getMessage(), true);

            $this->assertEquals('failed', $payload['status']);
            $this->assertArrayHasKey('expo_token', $payload['error']);
        }
    }

    /**
     * @test
     *
     *
     * @dataProvider availableRepositories
     *
     * @param $expoRepository
     */
    public function aDeviceCanUnsubscribeSingleTokenFromTheSystem($expoRepository)
    {
        [$expoController, $expoChannel] = $this->setupExpo($expoRepository);

        $data = ['expo_token' => 'ExponentPushToken[fakeToken]'];

        $user = new User();

        $token = 'ExponentPushToken[fakeToken]';
        $interest = $expoChannel->interestName($user);
        $expoChannel->expo->subscribe($interest, $token);

        // We will fake a request with the following data
        $request = $this->mockRequest($data, UnsubscribeRequest::class);

        $request->shouldReceive('validated')
            ->once()
            ->andReturn($data['expo_token']);

        $request->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $request->shouldReceive('get')
            ->with('expo_token')
            ->andReturn($data['expo_token']);

        // We will subscribe an interest to the server.
        $response = $expoController->unsubscribe($request)->resource;

        // The response should contain a deleted property with value true
        $this->assertTrue($response['deleted']);

        if ($expoRepository instanceof ExpoDatabaseDriver) {
            $this->assertDatabaseMissing(config('exponent-push-notifications.interests.database.table_name'), [
                'key' => 'NotificationChannels.ExpoPushNotifications.Test.User.'.(new User)->getKey(),
                'value' => $data['expo_token'],
            ]);
        }
    }

    /**
     * @test
     *
     *
     * @dataProvider availableRepositories
     *
     * @param $expoRepository
     */
    public function unsubscribeReturnsErrorResponseIfExceptionIsThrown($expoRepository)
    {
        [$expoController, $expoChannel] = $this->setupExpo($expoRepository);

        $data = ['expo_token' => null];
        $request = $this->mockRequest($data, UnsubscribeRequest::class);

        $request->shouldReceive('validated')
            ->once()
            ->andThrow(new UnprocessableEntityHttpException(json_encode([
                'status' => 'failed',
                'error'  => ['expo_token' => ['The expo token field is required.']],
            ])));

        $user = new User();

        $request->shouldReceive('user')
            ->once()
            ->andReturn($user);

        try {
            $expoController->unsubscribe($request);
            $this->fail('Expected UnprocessableEntityHttpException was not thrown');
        } catch (UnprocessableEntityHttpException $exception) {
            $payload = json_decode($exception->getMessage(), true);

            $this->assertEquals('failed', $payload['status']);
            $this->assertArrayHasKey('expo_token', $payload['error']);
        }
    }

    /**
     * Mocks a request for the ExpoController.
     *
     * @param $data
     * @return \Mockery\MockInterface
     */
    public function mockRequest($data, string $requestClass)
    {
        $request = \Mockery::mock($requestClass);
        $request->shouldReceive('all')->andReturn($data);

        return $request;
    }
}

class User
{
    public function getKey()
    {
        return 1;
    }
}
