#!/bin/sh
set -e

if [ ! -f .env ]; then
    echo "Missing .env - run: cp .env.example .env">&2
    exit 1
fi

composer install --no-interaction --prefer-dist --optimize-autoloader

if ! grep -q '^APP_KEY=.\+' .env; then
    php artisan key:generate --force
fi

php artisan migrate --force

if [ "$(php artisan tinker --execute='echo App\Models\User::count();' 2>/dev/null | tail -n1)" = "0" ]; then
    php artisan db:seed --force
fi

if ! -f config/ziggy.php; then
    php artisan ziggy:generate
fi

exec "$@"