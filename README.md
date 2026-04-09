# Exponent push notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-exponent-push-notifications/badge.svg?branch=master)](https://coveralls.io/github/RonasIT/laravel-exponent-push-notifications?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)

This package provides an Expo push notification channel for Laravel, enabling you to send notifications to mobile devices via the [Expo SDK](https://docs.expo.dev/push-notifications/overview/).

## Contents

-   [Installation](#installation)
-   [Usage](#usage)
    -   [Message customization](#message-customization)
    -   [Push tokens API](#push-tokens-api)
    -   [Routing a message](#routing-a-message)
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

### Storage Drivers

Package provides several drivers to store the push tokens:

| Driver | When to use |
|--------|-------------|
| `file` (default) | Simple setups, single-server, no database required |
| `database` | Multi-server deployments, persistent storage, flexible queries |

Storage driver may be configured via the `EXPONENT_PUSH_NOTIFICATION_INTERESTS_STORAGE_DRIVER` env variable.

#### Database driver

To use the `database` driver need to publish and run package migration:

```bash
php artisan vendor:publish --provider="NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider" --tag="migrations"
php artisan migrate
```

> [!IMPORTANT]
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

### Message customization

`ExpoMessage` may be customized using the following methods:

| Method | Description | Notes |
|--------|-------------|-------|
| `title(string $value)` | Sets the notification title | Default: `null` |
| `body(string $value)` | Sets the notification body | Default: `''` |
| `enableSound()` | Enables the notification sound | Sets sound to `'default'` |
| `disableSound()` | Mutes the notification sound | Sets sound to `null` |
| `badge(int $value)` | Sets the badge count on the app icon | Default: `0` |
| `setTtl(int $ttl)` | Sets the time to live in seconds | Default: `0` |
| `setJsonData(array\|string $data)` | Sets additional payload data | Accepts array or JSON string.<br>Throws `CouldNotCreateMessage` on invalid JSON |
| `setChannelId(string $channelId)` | Sets the notification channel ID | Android only |
| `priority(PriorityEnum $priority)` | Sets the delivery priority | Default: `PriorityEnum::Default`.<br>Available: `::Default`, `::Normal`, `::High` |
| `setInterruptionLevel(InterruptionLevelEnum $value)` | Sets the interruption level | iOS only.<br>Available: `InterruptionLevelEnum::Active`, `::Critical`, `::Passive`, `::TimeSensitive` |

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

### Push tokens API

The package automatically registers two endpoints for managing device subscriptions:

#### `POST /exponent/devices/subscribe`

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `expo_token` | string | Yes | The Expo push token of the device to subscribe |

#### `POST /exponent/devices/unsubscribe`

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `expo_token` | string | No | The Expo push token to remove. If omitted, all tokens for the authenticated user are removed |

The middleware applied to these endpoints is configured via the `middleware` key in the config file.

By default, both endpoints are registered automatically by the ServiceProvider. Calling `Route::expo()` in your own routes file registers the same routes, giving you full control over middleware, prefixes, and guards:

```php
use NotificationChannels\ExpoPushNotifications\Enum\ExpoRouterActionEnum;

// Register both routes:
Route::expo();

// Subscribe only:
Route::expo(ExpoRouterActionEnum::Subscribe);

// Unsubscribe only:
Route::expo(ExpoRouterActionEnum::Unsubscribe);
```

### Routing a message

An **interest** is a named subscription that links a device's push token to a logical target (e.g. a specific user). When sending a notification, the package looks up all tokens registered under that interest and delivers the message to them.

By default, the interest that a message is sent to is derived from the notifiable model using the `{ClassName}.{id}` convention (e.g. `App.Models.User.1`).

You can override this by adding a `routeNotificationForExpoPushNotifications()` method to your notifiable model:

```php
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;

    public function routeNotificationForExpoPushNotifications(): string
    {
        return 'App.Models.User.' . $this->id;
    }
}
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
