#!/bin/bash

echo "Adding permissions..."

mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 660 /var/www/html/storage/oauth-private.key
chmod -R 660 /var/www/html/storage/oauth-public.key
chmod -R 775 /var/www/html/bootstrap/cache

/usr/bin/composer install --prefer-dist --ignore-platform-req=ext-ffi
cp /var/www/html/.env.docker /var/www/html/.env

chmod -R 775 /var/www/html/.env

php artisan migrate

echo "Starting PHP-FPM..."
exec php-fpm
