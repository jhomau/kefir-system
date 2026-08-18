#!/bin/sh
set -e

if [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    export DB_URL="$DATABASE_URL"
fi

if [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=pgsql
fi

export DB_SSLMODE="${DB_SSLMODE:-require}"
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
export LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
export LOG_LEVEL="${LOG_LEVEL:-error}"

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

echo "Verificando conexión a base de datos..."
php artisan db:show --no-interaction || {
    echo "ERROR: no hay conexión a PostgreSQL. Revisa DATABASE_URL y DB_SSLMODE."
    exit 1
}

echo "Iniciando servidor en puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
