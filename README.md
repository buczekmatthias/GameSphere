# GameSphere

A community-driven platform where gamers can review games, discover new titles, track progress on upcoming games and discuss games.

## Stack

- PHP 8.4
- Laravel 12, Pest
- Vue 3 with shadcn/vue, VueUse, Lucide Vue and ziggy.js
- Inertia.js v2
- TypeScript
- PostgreSQL 18
- Tailwind v4
- Docker with Compose, nginx, php-fpm

## Quick start

Requires Docker with Compose and git.

```shell
git clone https://github.com/buczekmatthias/GameSphere.git
cd GameSphere
cp .env.example .env
```

Edit `.env` and set `DB_USERNAME` and `DB_PASSWORD` to any value.

Changing those credentials after first run won't have an effect until executing `docker compose down -v` which will reset data volume erasing whole database.

```shell
docker compose up -d --build
```

The first start takes a minute or two while dependencies install and migrations run.

To watch progress, run `docker compose logs -f app` (Ctrl+C stops following; the containers keep running).

When you see “ready to handle connections”, open http://localhost:8000/.

Demo admin account (seeded data, local use only): `admin@test.com` / `password`.

Stop everything with `docker compose down`.

## Running tests

Project Docker containers must be running.

```shell
docker compose exec app php artisan test
```
