#!/bin/bash

echo "Adding permissions......"

mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

/usr/bin/composer install --prefer-dist --ignore-platform-req=ext-ffi
cp /var/www/html/.env.docker /var/www/html/.env

chmod -R 775 /var/www/html/.env

echo "Migrating..."
php artisan migrate --force

echo "Generating passport keys..."
php artisan passport:keys --force
echo "Pushing keys to vault..."
php artisan vault:push-oauth-keys
echo "Generating default users..."
php artisan auth:setup
echo "Pushing routes to apisix..."
php artisan apisix:push-routes

echo "Updating permissions to passport keys..."
chmod -R 660 /var/www/html/storage/oauth-private.key
chmod -R 660 /var/www/html/storage/oauth-public.key

echo "Starting PHP-FPM......"
exec php-fpm
