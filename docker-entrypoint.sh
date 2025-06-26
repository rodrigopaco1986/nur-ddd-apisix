#!/bin/bash

set -e

cd /var/www/html

echo "Adding permissions..."

mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Wait until MySQL is accepting connections:
echo "Waiting for MySQL to be ready at ${DB_HOST}:${DB_PORT}..."
until php -r "new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" &> /dev/null
do
    echo "  → still waiting for database..."
    sleep 2
done
echo "MySQL is up – continuing."

#/usr/bin/composer install --prefer-dist --ignore-platform-req=ext-ffi
#cp /var/www/html/.env.docker /var/www/html/.env

#chmod -R 775 /var/www/html/.env

echo "Generating passport keys..."
php artisan passport:keys --force

echo "Pushing oauth keys to vault..."
php artisan vault:push-oauth-keys

echo "Migrating..."
php artisan migrate --force

echo "Generating default user and oauth keys for him..."
php artisan auth:oauth-setup

echo "Generating default user and client keys for him and pushing to vault..."
php artisan auth:setup-personal-client

echo "Pushing routes to apisix..."
php artisan apisix:push-routes

echo "Updating permissions to passport keys..."
chmod -R 660 /var/www/html/storage/oauth-private.key
chmod -R 660 /var/www/html/storage/oauth-public.key

echo "Starting PHP-FPM......"
exec php-fpm
