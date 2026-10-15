# Laravel API Starter

A Laravel 13 starter for JSON APIs with a Filament admin panel. It comes with authentication, roles, 2FA, an audit log, health checks, error tracking, OpenAPI docs and TypeScript types already set up, and one small example domain (notes) that shows the conventions end to end.

## Stack

| Area | Package |
| --- | --- |
| Framework | Laravel 13, PHP 8.5 |
| API auth | Laravel Sanctum (bearer tokens) |
| Use cases | lorisleiva/laravel-actions |
| DTOs, validation, responses | spatie/laravel-data |
| List filtering and sorting | spatie/laravel-query-builder |
| Roles and permissions | spatie/laravel-permission |
| Audit log | spatie/laravel-activitylog |
| Files and images | spatie/laravel-medialibrary, Flysystem S3 |
| Translatable columns | spatie/laravel-translatable |
| TypeScript types | spatie/laravel-typescript-transformer |
| Health checks | spatie/laravel-health |
| Admin panel | Filament 5, with jobs monitor, activity log, Excel export and environment badge |
| API docs | dedoc/scramble with scramble-extras (OpenAPI) and Scalar (UI) |
| Errors | Sentry |
| Mail | Resend |
| Tests and quality | Pest, Larastan, Pint |

## What's included

- **API under `/api/v1`.** Routes point straight at action classes. Token login (`POST /api/v1/auth/token`), logout, and a notes CRUD with filtering, sorting and cursor pagination.
- **Admin panel at `/admin`.** It uses its own `admin` session guard, requires app-based 2FA, and can be locked to an IP allowlist. Two roles ship by default:
  - `owner`: manage users, manage notes, view the activity log, view the jobs monitor.
  - `support`: manage notes.
- **Security headers** on every response, trusted proxies, JSON errors for `api/*`, and a rate-limited login.
- **API docs at `/api`.** They're only visible to admins signed in to the panel.
- **Health checks** for the database, cache, queue and scheduler, plus debug mode and disk space in production. They run every minute and send alerts by mail.
- **Scheduled cleanup** of failed jobs, expired tokens, old activity log entries and health history.
- **CI** on GitHub Actions: Pint, Larastan and Pest.

## Setup

Requirements: PHP 8.4+, Composer, Node 20+.

```bash
git clone https://github.com/HishamM1/laravel-api-starter.git my-api
cd my-api
composer setup
composer dev
```

`composer setup` installs the dependencies, creates `.env`, migrates a SQLite database and seeds it. Seeding creates the admin roles and an `owner` user:

```
email:    test@example.com
password: password
```

Then open:

- Admin panel: http://localhost:8000/admin (you'll be asked to set up 2FA on first login)
- API docs: http://localhost:8000/api (sign in to the admin panel first)

To use MySQL or Postgres, change the `DB_*` values in `.env` and run `php artisan migrate --seed`.

## Environment

These variables come on top of the Laravel defaults in `.env.example`:

| Variable | Purpose |
| --- | --- |
| `CORS_ALLOWED_ORIGINS` | Comma-separated origins allowed to call the API |
| `ADMIN_IP_ALLOWLIST` | Comma-separated IPs or CIDRs allowed into `/admin`. Leave empty to allow all |
| `SANCTUM_EXPIRATION` | API token lifetime in minutes. Leave empty for no expiry |
| `MEDIA_DISK` | Disk for uploaded media (`public` locally, `s3` in production) |
| `RESEND_API_KEY` | Resend key, used when `MAIL_MAILER=resend` |
| `SENTRY_LARAVEL_DSN` | Sentry project DSN |
| `SENTRY_TRACES_SAMPLE_RATE` | Share of requests to trace, from 0 to 1 |
| `HEALTH_NOTIFICATIONS_ENABLED`, `HEALTH_TO_ADDRESS` | Mail health check failures to this address |

## Conventions

Code is grouped by domain:

```
app/
  Domain/{Auth,Notes,...}/
    Actions/    one class per use case (laravel-actions)
    Data/       input and output DTOs (laravel-data)
    Policies/
  Models/       all Eloquent models
  Filament/     admin panel
```

- Routes point at actions: `Route::post('notes', CreateNote::class)`. Controllers hold no logic.
- An action holds the logic in `handle()`. Its `asController()` only maps the request to `handle()` and returns a Data object. `authorize()` delegates to the policy.
- Data classes validate input with attributes and shape every response. There are no FormRequests or API Resources.
- List actions filter and sort through `QueryBuilder::for(...)`, with every filter and sort whitelisted.
- Policies live in the domain and are registered on the model with `#[UsePolicy(...)]`.
- Filament resources check one permission each through the `AuthorizesWithPermission` trait.

The full rules are in `.ai/rules` and `.ai/guidelines`, and Laravel Boost compiles them into `AGENTS.md`.

Use the generators for new files:

```bash
php artisan make:model Project -mf
php artisan make:action 'App\Domain\Projects\Actions\CreateProject'
php artisan make:data CreateProjectData --namespace='Domain\Projects\Data'
php artisan make:policy 'App\Domain\Projects\Policies\ProjectPolicy' --model=Project
php artisan make:filament-resource Project --generate
```

## TypeScript types

Mark a Data class with `#[TypeScript]`, then run:

```bash
php artisan typescript:transform
```

This writes `resources/types/api.ts`. To write the types straight into a frontend repo, change `outputDirectory` in `app/Providers/TypeScriptTransformerServiceProvider.php`.

## Admin roles

Add a permission or role in `database/seeders/AdminRoleSeeder.php`, and add new admin roles to `User::ADMIN_ROLES`. Then run:

```bash
php artisan db:seed --class=AdminRoleSeeder
```

Assign roles to users from the Users screen in the panel.

## Testing

```bash
php artisan test
vendor/bin/phpstan analyse
vendor/bin/pint
```

Tests run on in-memory SQLite.

## Deployment checklist

1. Set `APP_ENV=production`, `APP_DEBUG=false` and a real `APP_URL`.
2. Point `DB_*`, `QUEUE_CONNECTION` and `CACHE_STORE` at production services, and set `MEDIA_DISK=s3` with the `AWS_*` values.
3. Set `ADMIN_IP_ALLOWLIST`, `CORS_ALLOWED_ORIGINS`, `SENTRY_LARAVEL_DSN` and the health alert address.
4. Run a queue worker and the scheduler (`php artisan schedule:work`, or cron running `schedule:run` every minute).
5. Run `php artisan migrate --force` and `php artisan db:seed --class=AdminRoleSeeder --force`.

## License

MIT
