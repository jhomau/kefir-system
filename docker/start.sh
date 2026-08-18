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

php artisan optimize:clear

echo "Esperando base de datos..."
MIGRATED=0
for i in 1 2 3 4 5 6 7 8 9 10; do
    if php artisan migrate --force --no-interaction; then
        MIGRATED=1
        break
    fi
    echo "Reintento $i/10..."
    sleep 3
done

if [ "$MIGRATED" -ne 1 ]; then
    echo "ERROR: no se pudieron ejecutar las migraciones."
    exit 1
fi

php artisan db:seed --force --no-interaction || true
php artisan permission:cache-reset --no-interaction 2>/dev/null || true
php artisan package:discover --ansi

# Filament/Livewire no funcionan bien con route:cache ni view:cache.
php artisan config:cache

echo "Iniciando servidor en puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
