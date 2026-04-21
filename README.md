# GameSphere

A template for community-driven platform where gamers can review games, discover new titles, track progress on upcoming games and discuss about games.

## Stack

- Laravel
- Vue
- Inertia.js
- Tailwind
- TypeScript
- PostgreSQL
- shadcn/vue
- VueUse
- Lucide Vue
- ziggy.js

## Installation

### Clone repository

```
git clone https://github.com/buczekmatthias/GameSphere.git
```

### Change directory

```
cd GameSphere
```

### Install dependencies

```
composer install
```

```
npm install
```

### Create .env from .env.example

```
copy .env.example .env
```

### Generate app key

```
php artisan key:generate
```

### Fill database credentials in .env

```
DB_USERNAME=
DB_PASSWORD=
```

### Migrate and seed database

```
php artisan migrate
```

```
php artisan db:seed
```

### Start your postgres server and create database matching the name from .env file

### Start application server

```
composer run dev
```

### Open browser and head to http://localhost:8000

### Login using these admin account credentials

```
admin@test.com

password
```
