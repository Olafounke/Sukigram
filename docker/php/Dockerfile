FROM php:8.2-fpm-bookworm

ARG APP_ENV=dev
ENV APP_ENV=${APP_ENV}

RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    opcache \
    zip \
    && apt-get purge -y --auto-remove \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/symfony

COPY . .

RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --classmap-authoritative --no-interaction; \
    else \
        composer install --optimize-autoloader --no-interaction; \
    fi

RUN chown -R www-data:www-data var/

EXPOSE 9000
CMD ["php-fpm"]