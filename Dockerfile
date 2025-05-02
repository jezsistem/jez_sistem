FROM php:8.3-fpm
RUN apt-get update && apt-get install -y \
    nano \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    gettext \
    git \
    unzip \
    wget \
    nginx \
    supervisor \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) opcache bcmath gd mbstring pdo_mysql exif zip sockets
RUN rm /etc/nginx/nginx.conf
RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = -1" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 3600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 3600" >> /usr/local/etc/php/conf.d/uploads.ini
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
WORKDIR /var/www/jezsistem_app
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY composer.json composer.lock /var/www/jezsistem_app/
RUN composer install --no-interaction --no-scripts --no-autoloader
COPY . /var/www/jezsistem_app
RUN composer dump-autoload
RUN chown -R www-data:www-data /var/www/jezsistem_app
RUN chown -R www-data:www-data /var/www/jezsistem_app/storage
RUN chmod -R 755 /var/www/jezsistem_app
RUN chmod -R 775 /var/www/jezsistem_app/storage
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]