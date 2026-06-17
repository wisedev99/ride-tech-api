# RideTech API

RESTful API for **RideTech**, a ride-sharing service. Passengers request trips,
drivers accept and complete them, and passengers review drivers after a completed ride.

## Live API

- **Base URL:** https://rideapi.wisedev.io
- **Swagger UI:** https://rideapi.wisedev.io/api/documentation
- Opening the base URL automatically redirects to the Swagger docs.

## Stack

- PHP 8.3, Laravel 10
- Laravel Sanctum (token auth)
- spatie/laravel-permission (roles: `passenger` / `driver`)
- MySQL (app), SQLite in-memory (tests)
- darkaonline/l5-swagger (API docs)

## Requirements

- PHP **8.1+** (developed on 8.3)
- Composer 2
- MySQL 5.7+ / 8.x
- Git

## Setup (clone & run)

Follow these steps on any machine to get the API running from scratch.

**1. Clone the repository**

```bash
git clone <repo-url>
cd ride-tech-api
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Create your environment file**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Create the database**

Create an empty MySQL database (default name is `ridetech`):

```bash
mysql -u root -p -e "CREATE DATABASE ridetech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Then open `.env` and set your DB credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ridetech
DB_USERNAME=root
DB_PASSWORD=
```

**5. Run migrations and seed demo data**

```bash
php artisan migrate --seed
```

This creates all tables, seeds the roles (`passenger`, `driver`) and some demo
users/trips/reviews.

> The seeders are idempotent — `php artisan db:seed` can be re-run safely and will
> reuse existing rows instead of duplicating demo data.

**6. Generate the API documentation**

```bash
php artisan l5-swagger:generate
```

**7. Start the server**

```bash
php artisan serve
```

The API is now live at **`http://127.0.0.1:8000`**, and opening the root URL
redirects to the Swagger UI at **`/api/documentation`**.

### One-line setup (after editing `.env`)

```bash
composer install && cp .env.example .env && php artisan key:generate \
  && php artisan migrate --seed && php artisan l5-swagger:generate && php artisan serve
```

## Authentication

Send the token from `register` / `login` as a bearer header:

```
Authorization: Bearer <token>
Accept: application/json
```

## API Endpoints

### Auth
| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | public | Register and receive a token |
| POST | `/api/login` | public | Log in and receive a token |
| POST | `/api/logout` | auth | Revoke the current token |
| GET | `/api/me` | auth | Current authenticated user |

### Trips
| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| GET | `/api/trips` | auth | List my trips. Filters: `status`, `date_from`, `date_to`, `passenger_id`, `driver_id`; pagination via `per_page` |
| GET | `/api/trips/{trip}` | passenger/driver of trip | Trip details |
| POST | `/api/trips` | passenger | Create a trip request |
| PUT | `/api/trips/{trip}` | passenger owner | Update a still-`requested` trip |
| DELETE | `/api/trips/{trip}` | passenger owner | Cancel a trip |
| GET | `/api/trips/available` | driver | List open (unassigned) trips |
| POST | `/api/trips/{trip}/accept` | driver | Accept an open trip |
| POST | `/api/trips/{trip}/reject` | driver | Reject an open trip |
| POST | `/api/trips/{trip}/complete` | assigned driver | Complete a trip |

### Cars
| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| GET | `/api/cars` | driver | List my cars |
| POST | `/api/cars` | driver | Add a car |
| DELETE | `/api/cars/{car}` | owner driver | Delete a car |

### Reviews
| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| GET | `/api/reviews/{driver}` | auth | List a driver's reviews (paginated) |
| POST | `/api/reviews/{driver}` | passenger | Review a driver after a completed trip |

Trip statuses: `requested`, `accepted`, `completed`, `cancelled`.

## API Documentation (Swagger)

Generate and view the interactive docs:

```bash
php artisan l5-swagger:generate
```

Then open **`http://127.0.0.1:8000/api/documentation`**.

All OpenAPI annotations live in a single file: `app/Swagger/ApiDoc.php`.

## Real-time trip updates (WebSockets)

When a driver **accepts**, **rejects**, or **completes** a trip, a
`TripStatusUpdated` event is broadcast on the private channel `trip.{id}`.
Only the trip's passenger or driver may subscribe (authorized in
`routes/channels.php`).

By default `BROADCAST_DRIVER=log`, so the REST API works without any extra
server. To enable live updates, switch the driver to `pusher` and run the
bundled websocket server (`beyondcode/laravel-websockets`):

```env
BROADCAST_DRIVER=pusher
```

```bash
# Terminal 1 — the API
php artisan serve

# Terminal 2 — the websocket server (port 6001)
php artisan websockets:serve
```

Open the debug dashboard at **`http://127.0.0.1:8000/laravel-websockets`**,
click **Connect**, then accept a trip — you'll see the `trip.status` event on
channel `private-trip.{id}`.

> Note: with `BROADCAST_DRIVER=pusher` the websocket server must be running,
> otherwise trip accept/reject/complete will fail when they try to broadcast.

## Running Tests

Tests use an in-memory SQLite database (configured in `.env.testing`), so they
never touch your MySQL data:

```bash
php artisan test
```

## Architecture notes

- **Validation** lives in Form Requests (`app/Http/Requests`).
- **Output** is shaped by API Resources (`app/Http/Resources`).
- **Authorization / ownership** is enforced by Policies (`CarPolicy`, `TripPolicy`).
- **Role gating** uses spatie's `role:` middleware on route groups.
- Controllers stay thin. All DB access uses Eloquent / query bindings (no raw SQL).
- Auth routes are rate-limited (`throttle:6,1`).
