# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

Tests and tools must be run inside the Docker container (`nginx` service):

```bash
docker compose exec nginx php vendor/bin/phpunit                            # Run all tests
docker compose exec nginx php vendor/bin/phpunit --filter MethodName        # Run a single test method
docker compose exec nginx php vendor/bin/phpunit tests/ChannelTest.php      # Run a single test file
docker compose exec nginx php vendor/bin/pint                               # Fix code style
docker compose exec nginx php vendor/bin/pint --test                        # Check code style without fixing
```

Start the containers first if they are not running:

```bash
docker compose up -d
```

## Architecture

This is a **Laravel notification channel package** that bridges Laravel's notification system with the [Expo Push Notifications](https://expo.dev/notifications) service.

### Core notification flow

1. A `Notification` class declares `ExpoChannel::class` in its `via()` method
2. `ExpoChannel` calls `$notification->toExpoPush($notifiable)` to get an `ExpoMessage`
3. The message is serialized and dispatched via the Expo SDK (`alymosul/exponent-server-sdk-php`)

### Device token management ("interests")

The package associates device tokens with "interest names" (default pattern: `ClassName.UserId`, e.g. `App.User.1`). Tokens are stored via two interchangeable drivers configured in `config/exponent-push-notifications.php`:

- **File driver** (default) — provided by the Expo SDK
- **Database driver** — uses the `Interest` Eloquent model (`ExpoTokenRepository`)

HTTP endpoints for client apps to manage subscriptions:
- `POST /exponent/devices/subscribe` — requires `expo_token`
- `POST /exponent/devices/unsubscribe` — requires `expo_token`

These routes are registered by `ExpoPushNotificationsServiceProvider` using the `ExpoController`. Middleware defaults to `['auth:api', 'bindings']` but is configurable. Consumers can also use `Route::expo()` (provided by the `ExpoRouter` mixin) for custom route registration.

### Key source files

| File | Role |
|------|------|
| `src/ExpoChannel.php` | Notification channel — orchestrates sending |
| `src/ExpoMessage.php` | Fluent builder for push notification payloads |
| `src/ExpoPushNotificationsServiceProvider.php` | Registers channel, routes, drivers, publishes config/migrations |
| `src/Http/ExpoController.php` | Subscribe/unsubscribe HTTP endpoints |
| `src/Repositories/ExpoTokenRepository.php` | Database implementation of the token repository |
| `src/Models/Interest.php` | Eloquent model for stored device interests |
| `src/ExpoRouter.php` | Router mixin providing `Route::expo()` |
| `config/exponent-push-notifications.php` | Package configuration (driver, middleware, debug) |

### Enums and exceptions

- `Enums/PriorityEnum` — `Default`, `Normal`, `High`
- `Enums/InterruptionLevelEnum` — `Active`, `Critical`, `Passive`, `TimeSensitive` (iOS 15+)
- `Enum/ExpoRouterActionEnum` — `Subscribe`, `Unsubscribe` (note: different namespace `Enum` vs `Enums`)
- `Exceptions/CouldNotCreateMessage`, `Exceptions/CouldNotSendNotification`

## Testing

Tests use [Orchestra Testbench](https://packages.tools/testbench) and Mockery. The base `TestCase` wires up the service provider and in-memory SQLite database.

- `tests/ChannelTest.php` — tests notification dispatch via `ExpoChannel`
- `tests/MessageTest.php` — tests `ExpoMessage` builder methods
- `tests/ExpoControllerTest.php` — tests subscribe/unsubscribe HTTP endpoints
- `tests/Support/ChannelMockTrait.php` — shared mock setup for channel tests
- `tests/Support/ModelTestState.php` — DB assertion helpers