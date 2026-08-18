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

echo "Esperando base de datos..."
for i in 1 2 3 4 5 6 7 8 9 10; do
    if php artisan migrate --force --no-interaction; then
        break
    fi
    echo "Reintento $i/10..."
    sleep 3
done

php artisan db:seed --force --no-interaction || true
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando servidor en puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
