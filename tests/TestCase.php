<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use ExponentPhpSDK\ExpoRepository;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\Test\database\Models\User;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RonasIT\Support\Traits\FixturesTrait;

abstract class TestCase extends OrchestraTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadTestDump();

        if (config('database.default') === 'pgsql') {
            $this->prepareSequences();
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            ExpoPushNotificationsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $this->setupDb($app);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('exponent-push-notifications.middleware', []);
    }

    protected function setupDb($app): void
    {
        $app['config']->set('database.default', env('DB_CONNECTION', 'pgsql'));
        $app['config']->set('database.connections.pgsql', [
            'driver' => env('DB_DRIVER', 'pgsql'),
            'host' => env('DB_HOST', 'pgsql'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', 'secret'),
        ]);
    }

    public function getFixturePath(string $fixtureName): string
    {
        list($className) = extract_last_part(get_class($this), '\\');

        return getcwd() . "/tests/fixtures/{$className}/{$fixtureName}";
    }

    protected function bindExpoRepository(ExpoRepository $driver): void
    {
        $this->app->bind(ExpoRepository::class, fn () => $driver);

        $this->app->bind(ExpoChannel::class, fn ($app) => new ExpoChannel(
            expo: new Expo(new ExpoRegistrar($driver)),
            events: $app['events'],
        ));
    }
}
