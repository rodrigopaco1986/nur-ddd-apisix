# --- Stage 1: The "Builder" ---
# Use official PHP 8.2 with FPM
FROM php:8.3-fpm as builder

WORKDIR /var/www/html

#Minimum setup
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip

# Copy Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer


# Copy only dependency manifests to leverage Docker's build cache.
# The 'RUN composer install' step will only be re-run if these files change.
COPY composer.json composer.lock ./

# Install composer dependencies.
# This stage needs a temporary installation of rdkafka dependencies to resolve platform requirements.
# We install librdkafka-dev, then the PECL extension, so composer is satisfied.
RUN apt-get update && apt-get install -y librdkafka-dev libffi-dev g++ \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && docker-php-ext-install ffi

# Install composer dependencies.
# --no-interaction, --no-plugins, --no-scripts are security and performance best practices.
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist


# --- Stage 2: The Final "Production" Image ---
# This stage creates the final, lean image for running the application.
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    librdkafka-dev \
    libffi-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring exif pcntl bcmath intl ffi zip sockets

# Copy the pre-built vendor directory from the builder stage
COPY --from=builder /var/www/html/vendor /var/www/html/vendor

# Copy the application code (excluding files in .dockerignore)
COPY . .

# Copy startup script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose PHP-FPM port
#EXPOSE 9000

# Use our startup script
CMD ["/usr/local/bin/docker-entrypoint.sh"]