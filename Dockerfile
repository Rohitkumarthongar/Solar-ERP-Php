FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
ENV PUPPETEER_SKIP_DOWNLOAD=true
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build && npm prune --omit=dev --ignore-scripts --no-audit --no-fund

FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-gd

FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends chromium libpng-dev \
    && docker-php-ext-install gd pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=assets /usr/local/bin/node /usr/local/bin/node
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY --from=assets /app/node_modules ./node_modules
COPY docker/laravel.conf /etc/apache2/conf-enabled/laravel.conf
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi
