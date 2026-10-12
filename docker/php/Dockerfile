# syntax=docker/dockerfile:1
FROM php:8.3-apache-bookworm AS runtime
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libzip-dev libonig-dev libsqlite3-dev unzip git \
    && docker-php-ext-install intl mbstring pdo_mysql pdo_sqlite zip \
    && a2enmod rewrite && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/.htaccess /var/www/public/.htaccess

FROM runtime AS development
CMD ["apache2-foreground"]

FROM runtime AS production
COPY --chown=www-data:www-data . .
RUN --mount=type=secret,id=proxy_ca \
    COMPOSER_CAFILE=${COMPOSER_CAFILE:-/run/secrets/proxy_ca} \
    sh -c 'if [ ! -f "$COMPOSER_CAFILE" ]; then unset COMPOSER_CAFILE; fi; composer install --no-dev --no-scripts --classmap-authoritative --prefer-dist --no-interaction' \
    && mkdir -p var/cache var/log public/uploads/images \
    && chown -R www-data:www-data var public/uploads \
    && chmod +x launchApp.sh
EXPOSE 80
CMD ["sh", "./launchApp.sh"]
