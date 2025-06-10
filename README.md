# Exponent push notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-exponent-push-notifications/badge.svg?branch=add-run-tess-wtih-coverage)](https://coveralls.io/github/RonasIT/laravel-exponent-push-notifications?branch=add-run-tess-wtih-coverage)
[![Total Downloads](https://img.shields.io/packagist/dt/ronasit/laravel-exponent-push-notifications.svg?style=flat-square)](https://packagist.org/packages/ronasit/laravel-exponent-push-notifications)

## Contents

-   [Installation](#installation)
-   [Usage](#usage) - [Available Message methods](#available-message-methods)
-   [Changelog](#changelog)
-   [Testing](#testing)
-   [Security](#security)
-   [Contributing](#contributing)
-   [Credits](#credits)
-   [License](#license)

## Installation

You can install the package via composer:

```bash
composer require alymosul/laravel-exponent-push-notifications
```

If you are using Laravel 5.5 or higher this package will automatically register itself using [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery). For older versions of Laravel you must install the service provider manually:

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider::class,
],

```

Before publish exponent notification migration you must add in .env file:

```bash
EXPONENT_PUSH_NOTIFICATION_INTERESTS_STORAGE_DRIVER=database
```

You can publish the migration with:

```bash
php artisan vendor:publish --provider="NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider" --tag="migrations"
```

After publishing the migration you can create the `exponent_push_notification_interests` table by running the migrations:

```bash
php artisan migrate
```

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="NotificationChannels\ExpoPushNotifications\ExpoPushNotificationsServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'interests' => [
        /*
         * Supported: "file", "database"
         */
        'driver' => env('EXPONENT_PUSH_NOTIFICATION_INTERESTS_STORAGE_DRIVER', 'file'),

        'database' => [
            'events' => [],

            'table_name' => 'exponent_push_notification_interests',
        ],
    ]
];
```

## Usage

```php
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable)
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

A list of all available options

-   `title('')`: Accepts a string value for the title.
-   `body('')`: Accepts a string value for the body.
-   `enableSound()`: Enables the notification sound.
-   `disableSound()`: Mutes the notification sound.
-   `badge(1)`: Accepts an integer value for the badge.
-   `ttl(60)`: Accepts an integer value for the time to live.
-   `setJsonData('')`: Accepts a json string or an array for additional.
-   `channelID('')`: Accepts a string to set the channelId of the notification for Android devices.
-   `priority('default')`: Accepts a string to set the priority of the notification, must be one of [default, normal, high].

### Managing Recipients

This package registers two endpoints that handle the subscription of recipients, the endpoints are defined in src/Http/routes.php file, used by ExpoController and all loaded through the package service provider.

### Routing a message

By default the exponent "interest" messages will be sent to will be defined using the {notifiable}.{id} convention, for example `App.User.1`, however you can change this behaviour by including a `routeNotificationForExpoPushNotifications()` in the notifiable class method that returns the interest name.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
$ composer test
```

## Security

If you discover any security related issues, please email alymosul@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Aly Suleiman](https://github.com/Alymosul)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
