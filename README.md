# LiveVote

Laravel application for LiveVote. PHP **8.3+**, Laravel **13**, Vite, and Tailwind CSS.

## Requirements

- [PHP](https://www.php.net/) **8.3** or newer (extensions used by Laravel: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) **18+** and npm

## Setup

Clone the repository and open the project root:

```bash
git clone git@github.com:ishanshah1991/livevote.git
cd livevote
```

If you use a separate SSH key for your personal GitHub account, use the host alias from your `~/.ssh/config` instead of `github.com` in the clone URL.

### 1. Environment file

```bash
cp .env.example .env
```

### 2. SQLite database file (default)

The project defaults to **SQLite** (`DB_CONNECTION=sqlite` in `.env`). Create the database file:

```bash
touch database/database.sqlite
```

To use **MySQL** or **PostgreSQL** instead, edit `.env`: set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` as needed, and comment out or remove the SQLite-only step above.

### 3. PHP dependencies

```bash
composer install
```

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

In one terminal (PHP server):

```bash
php artisan serve
```

If you use `npm run dev`, keep it running in a **second** terminal while you develop so Vite can serve and compile assets.

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) (or the URL `artisan serve` prints).

---

## One-liner alternative

After cloning, you can run Composer’s bundled setup script (still create the SQLite file first if you use SQLite):

```bash
touch database/database.sqlite
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
| `composer run dev` | Concurrently runs `serve`, queue worker, logs, and Vite (see `composer.json`) |
| `composer run test` | Clears config cache and runs `php artisan test` |

---

## Learning Laravel

Documentation: [https://laravel.com/docs](https://laravel.com/docs)

## License

This application skeleton follows the [MIT license](https://opensource.org/licenses/MIT) used by Laravel.
