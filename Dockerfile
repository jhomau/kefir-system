FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    icu-dev \
    icu-libs \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache \
    && apk del --no-cache icu-dev libzip-dev postgresql-dev

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

ENV PORT=8080

EXPOSE 8080

CMD ["start.sh"]
