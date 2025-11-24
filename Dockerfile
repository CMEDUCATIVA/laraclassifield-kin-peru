# syntax=docker/dockerfile:1.6

############################################
# Frontend assets builder
############################################
FROM node:20-alpine AS frontend

WORKDIR /var/www/html

COPY package*.json ./
RUN npm install

COPY webpack.mix*.js ./
COPY resources ./resources
COPY public ./public

# Ensure legacy Mix configs referencing "/public" keep working
RUN ln -s /var/www/html/public /public

# Build all configured asset bundles (default + RTL + combined)
RUN npm run prod

############################################
# Application image (PHP-FPM + Nginx)
############################################
FROM composer:2 AS composer

FROM php:8.2-fpm-bookworm AS app

ARG DEBIAN_FRONTEND=noninteractive
ENV APP_ENV=production \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PATH="/var/www/html/vendor/bin:${PATH}" \
    DB_HOST=${DB_HOST:-mysql} \
    DB_PORT=${DB_PORT:-3306} \
    DB_DATABASE=${DB_DATABASE:-laraclassified} \
    DB_USERNAME=${DB_USERNAME:-laraclassified} \
    DB_PASSWORD=${DB_PASSWORD:-secret} \
    REDIS_HOST=${REDIS_HOST:-redis} \
    REDIS_PORT=${REDIS_PORT:-6379}

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libxslt1-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libbz2-dev \
    pkg-config \
    build-essential \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    ftp \
    gd \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo_mysql \
    zip

RUN pecl install redis \
 && docker-php-ext-enable redis

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN ln -s /var/www/html/public /public

RUN mkdir -p storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader

COPY --from=frontend /var/www/html/public ./public

# Nginx logs to stdout/stderr for Docker
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log \
 && chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/start-container.sh /usr/local/bin/start-container.sh

RUN chmod +x /usr/local/bin/start-container.sh

EXPOSE 8080

CMD ["/usr/local/bin/start-container.sh"]
