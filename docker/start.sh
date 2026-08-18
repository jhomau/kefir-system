#!/bin/sh
set -e

if [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    export DB_URL="$DATABASE_URL"
fi

if [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=pgsql
fi

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no está configurada. Genera una con: php artisan key:generate --show"
    exit 1
fi

php artisan config:clear
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
