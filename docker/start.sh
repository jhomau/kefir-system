#!/bin/sh
set -e

if [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
    export DB_URL="$DATABASE_URL"
fi

# Railway inyecta el dominio publico automaticamente.
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://${RAILWAY_PUBLIC_DOMAIN}"
fi

export APP_ENV="${APP_ENV:-production}"
export DB_CONNECTION="pgsql"
export DB_SSLMODE="require"
export SESSION_DRIVER="file"
export CACHE_STORE="file"
export QUEUE_CONNECTION="sync"
export LOG_CHANNEL="stderr"
export LOG_LEVEL="${LOG_LEVEL:-error}"

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no esta configurada. Genera una con: php artisan key:generate --show"
    exit 1
fi

echo "APP_URL=${APP_URL:-no-configurada}"
echo "DB_CONNECTION=${DB_CONNECTION}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

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

echo "Verificando conexion a base de datos..."
php artisan db:show --no-interaction || {
    echo "ERROR: no hay conexion a PostgreSQL. Revisa DATABASE_URL."
    exit 1
}

echo "Iniciando servidor en puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
