# Use a lean official PHP 8.1 FPM image
FROM php:8.1-fpm-alpine

# Copy your custom PHP configuration file
COPY docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/

# Set working directory
WORKDIR /var/www/html

# Install system dependencies (git, common libraries) and ADD tzdata
RUN apk add --no-cache \
    git \
    build-base \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    mariadb-client \
    bash \
    # New: Install timezone data package for Alpine
    tzdata

# Install required PHP extensions for Laravel 8.1
# pdo_mysql is necessary for MariaDB connection
RUN docker-php-ext-install pdo pdo_mysql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd

# Clear build dependencies
RUN apk del build-base

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set user and group (alpine default is 82, which works fine, but we can set to www-data for Debian compatibility if needed)
# Since we are using alpine, let's stick with root or use the FPM user/group
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -D laravel
USER laravel

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# The container will run php-fpm automatically due to the base image