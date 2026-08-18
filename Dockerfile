FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    git \
    unzip \
    icu-libs \
    libzip \
    libpq \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache \
    && apk del --no-cache icu-dev libzip-dev postgresql-dev \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-scripts \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh \
    && sed -i 's/\r$//' /usr/local/bin/start.sh

ENV PORT=8080

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
