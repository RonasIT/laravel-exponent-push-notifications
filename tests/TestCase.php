<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use ExponentPhpSDK\ExpoRepository;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use NotificationChannels\ExpoPushNotifications\Http\ExpoController;
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

    protected function assertExpoRoutesRegistered(
        string $prefix = 'exponent/devices',
        array $middleware = ['expo.middleware'],
    ): void {
        $routes = [
            ['method' => 'POST', 'uri' => 'subscribe', 'action' => 'subscribe'],
            ['method' => 'POST', 'uri' => 'unsubscribe', 'action' => 'unsubscribe'],
        ];

        foreach ($routes as $route) {
            $expectedUri = ltrim("{$prefix}/{$route['uri']}", '/');
            $method = $route['method'];

            $registeredRoute = $this->findRegisteredRoute($method, $expectedUri);

            $this->assertNotNull(
                actual: $registeredRoute,
                message: "Route [{$method}] {$expectedUri} is not registered.",
            );

            $this->assertEquals(
                expected: ExpoController::class . '@' . $route['action'],
                actual: $registeredRoute->getActionName(),
                message: "Route [{$method}] {$expectedUri} has wrong action.",
            );

            foreach ($middleware as $middlewareEntry) {
                $this->assertContains(
                    needle: $middlewareEntry,
                    haystack: $registeredRoute->gatherMiddleware(),
                    message: "Route [{$method}] {$expectedUri} missing middleware [{$middlewareEntry}].",
                );
            }
        }
    }

    protected function assertExpoRoutesCount(): void
    {
        $expoRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (LaravelRoute $route) => str_contains($route->getActionName(), ExpoController::class));

        $this->assertCount(
            expectedCount: 2,
            haystack: $expoRoutes,
            message: 'Registered expo routes count does not match expected.',
        );
    }

    private function findRegisteredRoute(string $method, string $uri): ?LaravelRoute
    {
        return collect(Route::getRoutes()->getRoutesByMethod()[$method])
            ->first(fn (LaravelRoute $route) => $route->uri() === $uri);
    }
}
