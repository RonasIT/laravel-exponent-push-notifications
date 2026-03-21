# Exponent push notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-exponent-push-notifications/badge.svg?branch=master)](https://coveralls.io/github/RonasIT/laravel-exponent-push-notifications?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)

This package provides an Expo push notification channel for Laravel, enabling you to send notifications to mobile devices via the [Expo SDK](https://docs.expo.dev/push-notifications/overview/).

## Contents

-   [Installation](#installation)
-   [Usage](#usage)
    -   [Available Message methods](#available-message-methods)
    -   [Managing Recipients](#managing-recipients)
    -   [Routing a message](#routing-a-message)
    -   [Custom Routes](#custom-routes)
-   [Changelog](#changelog)
-   [Testing](#testing)
-   [Security](#security)
-   [Contributing](#contributing)
-   [Credits](#credits)
-   [License](#license)

## Installation

You can install the package via composer:

```bash
composer require ronasit/laravel-exponent-push-notifications
```

If you are using Laravel 5.5 or higher this package will automatically register itself using [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery). For older versions of Laravel you must install the service provider manually:

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider::class,
],
```

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'debug' => env('EXPONENT_PUSH_NOTIFICATION_DEBUG', true), // log requests to Expo via SDK package

    'middleware' => ['auth:api', 'bindings'],

    'interests' => [
        'driver' => env('EXPONENT_PUSH_NOTIFICATION_INTERESTS_STORAGE_DRIVER', 'file'),

        'database' => [
            'events' => [],
            'table_name' => 'exponent_push_notification_interests',
        ],
    ],
];
```

### Storage Drivers

| Driver | When to use |
|--------|-------------|
| `file` (default) | Simple setups, single-server, no database required |
| `database` | Multi-server deployments, persistent storage, flexible queries |

When using the `database` driver, add the following to your `.env`:

```dotenv
EXPONENT_PUSH_NOTIFICATION_INTERESTS_STORAGE_DRIVER=database
```

Then publish and run the migration:

```bash
php artisan vendor:publish --provider="NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider" --tag="migrations"
php artisan migrate
```

> The migration uses the table name defined in `interests.database.table_name` from the config (`exponent_push_notification_interests` by default).

## Usage

```php
use App\Models\User;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via(User $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush(User $notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->badge(1)
            ->enableSound()
            ->title("Congratulations!")
            ->body("Your {$notifiable->service} account was approved!");
    }
}
```

### Available Message methods

-   `title('Your title')`: Sets the notification title.
-   `body('Your message')`: Sets the notification body.
-   `enableSound()`: Enables the notification sound.
-   `disableSound()`: Mutes the notification sound.
-   `badge(1)`: Sets the badge count on the app icon.
-   `setTtl(60)`: Sets the time to live in seconds.
-   `setJsonData(['key' => 'value'])`: Sets additional payload data (JSON string or array). Throws `CouldNotCreateMessage` on invalid JSON.
-   `setChannelId('default')`: Sets the Android notification channel ID.
-   `priority(PriorityEnum::High)`: Sets the notification priority. Available: `PriorityEnum::Default`, `::Normal`, `::High`.
-   `setInterruptionLevel(InterruptionLevelEnum::Active)`: Sets the iOS interruption level. Available: `InterruptionLevelEnum::Active`, `::Critical`, `::Passive`, `::TimeSensitive`.

For example, to send a high-priority time-sensitive notification on iOS:

```php
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use NotificationChannels\ExpoPushNotifications\Enums\PriorityEnum;
use NotificationChannels\ExpoPushNotifications\Enums\InterruptionLevelEnum;

ExpoMessage::create()
    ->title('Important!')
    ->body('This is a critical alert.')
    ->priority(PriorityEnum::High)
    ->setInterruptionLevel(InterruptionLevelEnum::TimeSensitive);
```

### Managing Recipients

The package automatically registers two endpoints for managing device subscriptions:

```
POST /exponent/devices/subscribe
POST /exponent/devices/unsubscribe
```

**Request body:**

```json
{
    "expo_token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]"
}
```

The middleware applied to these endpoints is configured via the `middleware` key in the config file.

### Routing a message

By default, the Expo "interest" that a message is sent to is derived from the notifiable model using the `{ClassName}.{id}` convention (e.g. `App\Models\User.1`).

You can override this by adding a `routeNotificationForExpoPushNotifications()` method to your notifiable model:

```php
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;

    public function routeNotificationForExpoPushNotifications(): string
    {
        return 'App.User.' . $this->id;
    }
}
```

### Custom Routes

By default, both endpoints are registered automatically by the ServiceProvider. Calling `Route::expo()` in your own routes file disables the default route registration, giving you full control over middleware, prefixes, and guards:

```php
use NotificationChannels\ExpoPushNotifications\Enum\ExpoRouterActionEnum;

// Register both routes:
Route::expo();

// Subscribe only:
Route::expo(ExpoRouterActionEnum::Subscribe);

// Unsubscribe only:
Route::expo(ExpoRouterActionEnum::Unsubscribe);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please use the [GitHub issue tracker](https://github.com/RonasIT/laravel-exponent-push-notifications/issues).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Aly Suleiman](https://github.com/Alymosul) — original author
-   [RonasIT](https://github.com/RonasIT)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
