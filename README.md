# LiveVote

Laravel application for LiveVote. PHP **8.3+**, Laravel **13**, Vite, and Tailwind CSS.

## Stack

- **MySQL** (or another SQL database) for application data: migrations, Eloquent models, and the `users` table. Laravel needs a relational database here; **Redis cannot replace this**.
- **Redis** for **cache**, **sessions**, and **queues** (`CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION`).

Automated tests (`phpunit.xml`) still use **SQLite in-memory** and in-memory drivers so the suite does not require MySQL or Redis.

## Requirements

- [PHP](https://www.php.net/) **8.3+** with extensions: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) **18+** and npm
- **MySQL** 8+ (or MariaDB / PostgreSQL if you change `DB_CONNECTION` in `.env`)
- **Redis** 6+ and either the **phpredis** PHP extension **or** the `predis/predis` Composer package with `REDIS_CLIENT=predis` in `.env`

## Setup

Clone the repository and open the project root:

```bash
git clone git@github.com:ishanshah1991/livevote.git
cd livevote
```

If you use a separate SSH key for your personal GitHub account, use the host alias from your `~/.ssh/config` instead of `github.com` in the clone URL.

### 1. Start MySQL and Redis

Examples (adjust for your environment):

```bash
# Example: Docker
docker run -d --name livevote-mysql -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=livevote -p 3306:3306 mysql:8
docker run -d --name livevote-redis -p 6379:6379 redis:7-alpine
```

Create the MySQL database and user if you manage them yourself (match `DB_*` in `.env`). If you used the Docker MySQL example above, set `DB_PASSWORD=secret` (or change the container env to match your `.env`).

### 2. Environment file

```bash
cp .env.example .env
```

Edit **`.env`**: set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (and host/port if not local). Confirm `REDIS_HOST` / `REDIS_PORT` match your Redis instance.

### 3. PHP dependencies

```bash
composer install
```

If you do not have the **phpredis** extension:

```bash
composer require predis/predis
```

Then in `.env` set `REDIS_CLIENT=predis`.

### 4. Application key

```bash
php artisan key:generate
```

### 5. Database schema

```bash
php artisan migrate
```

### 6. Front-end dependencies and assets

```bash
npm install
```

Development (Vite hot reload):

```bash
npm run dev
```

Production asset build:

```bash
npm run build
```

### 7. Run the application

Terminal 1 — PHP server:

```bash
php artisan serve
```

Terminal 2 — Vite (while developing):

```bash
npm run dev
```

If you use the **Redis** queue driver in local development, run a worker when processing queued jobs:

```bash
php artisan queue:work redis
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) (or the URL `artisan serve` prints).

---

## One-liner alternative

After MySQL and Redis are running and `.env` has correct `DB_*` and Redis settings:

```bash
composer run setup
```

`setup` runs `composer install`, copies `.env` from `.env.example` if missing, `key:generate`, `migrate --force`, `npm install --ignore-scripts`, and `npm run build`.

---

## Useful commands

| Command | Description |
|--------|-------------|
| `php artisan serve` | Local HTTP server |
| `npm run dev` | Vite dev server |
| `npm run build` | Production front-end build |
| `php artisan queue:work redis` | Process queued jobs |
| `composer run dev` | Concurrently runs `serve`, queue worker, logs, and Vite (see `composer.json`) |
| `composer run test` | Clears config cache and runs `php artisan test` |

---

## Learning Laravel

Documentation: [https://laravel.com/docs](https://laravel.com/docs)

## License

This application skeleton follows the [MIT license](https://opensource.org/licenses/MIT) used by Laravel.
