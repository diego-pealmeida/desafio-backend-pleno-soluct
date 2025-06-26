FROM php:8.2-fpm

RUN apt-get update -y \
    && apt-get install -y openssl zip unzip git \
    && apt-get install -y \
    libonig-dev \
    zlib1g-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_pgsql mbstring gd bcmath pcntl pgsql zip

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www

COPY --chown=www-data:www-data . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT [ "entrypoint.sh" ]
