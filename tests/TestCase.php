<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use Illuminate\Support\Arr;
use NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ExpoPushNotificationsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getDatabaseDirectory().'/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * Sets up the database.
     *
     * @return void
     */
    protected function setUpDatabase()
    {
        $this->resetDatabase();

        $this->createExponentPushNotificationInterestsTable();
    }

    /**
     * Drops the database.
     *
     * @return void
     */
    protected function resetDatabase()
    {
        file_put_contents(__DIR__.'/temp'.'/database.sqlite', null);
    }

    /**
     * Creates the interests table.
     *
     * @return void
     */
    protected function createExponentPushNotificationInterestsTable()
    {
        include_once '__DIR__'.'/../migrations/create_exponent_push_notification_interests_table.php.stub';

        (new \CreateExponentPushNotificationInterestsTable())->up();
    }

    /**
     * Gets the directory path for the testing database.
     *
     * @return string
     */
    public function getDatabaseDirectory(): string
    {
        return __DIR__.'/temp';
    }

    protected function getJsonFixture(string $fixtureName): array
    {
        $path = $this->getFixturePath($fixtureName);

        return json_decode(file_get_contents($path), true);
    }

    public function getFixturePath(string $fixtureName): string
    {
        $class = get_class($this);
        $explodedClass = explode('\\', $class);
        $className = Arr::last($explodedClass);

        return __DIR__ . "/fixtures/{$className}/{$fixtureName}";
    }
}
