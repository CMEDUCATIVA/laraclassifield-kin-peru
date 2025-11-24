#!/usr/bin/env bash
set -euo pipefail

# Ensure runtime directories exist
mkdir -p /run/php

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link --ansi || true
fi

# Ensure APP_KEY exists
if [ -f /var/www/html/.env ]; then
    app_key_value=$(grep -E '^APP_KEY=' /var/www/html/.env | cut -d '=' -f2-)
    if [ -z "$app_key_value" ] || [[ "$app_key_value" == base64:ReplaceThisWithYourKeyGeneratedInProd* ]] || [[ "$app_key_value" == SomeRandomStringWith32Characters ]]; then
        php artisan key:generate --force --ansi || true
    fi
fi

for dir in /var/www/html/config /var/www/html/public /var/www/html/lang; do
    if [ -d "$dir" ]; then
        chown -R www-data:www-data "$dir"
        chmod -R 775 "$dir"
        find "$dir" -type f -exec chmod 664 {} \;
    fi
done

php artisan config:clear --ansi || true
php artisan cache:clear --ansi || true

php-fpm --daemonize

exec nginx -g "daemon off;"
