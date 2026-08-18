#!/bin/sh
set -e

resolve_database_config() {
    if [ -n "$DATABASE_URL" ]; then
        export DB_URL="$DATABASE_URL"
        unset DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD
        return 0
    fi

    if [ -n "$PGHOST" ] && [ "$PGHOST" != "127.0.0.1" ] && [ "$PGHOST" != "localhost" ]; then
        export DB_HOST="$PGHOST"
        export DB_PORT="${PGPORT:-5432}"
        export DB_DATABASE="$PGDATABASE"
        export DB_USERNAME="$PGUSER"
        export DB_PASSWORD="$PGPASSWORD"
        unset DB_URL
        return 0
    fi

    if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
        return 0
    fi

    echo "ERROR: PostgreSQL no configurado."
    echo "En Railway → servicio web → Variables:"
    echo "  1. Add Reference → PostgreSQL → DATABASE_URL"
    echo "  2. Elimina DB_HOST=127.0.0.1 si existe"
    exit 1
}

resolve_database_config

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
echo "DB_HOST=${DB_HOST:-desde DATABASE_URL}"
echo "DATABASE_URL configurada: $([ -n "$DB_URL" ] || [ -n "$PGHOST" ] && echo si || echo no)"

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
    echo "ERROR: no hay conexion a PostgreSQL. Revisa DATABASE_URL en Railway."
    exit 1
}

echo "Iniciando servidor en puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
