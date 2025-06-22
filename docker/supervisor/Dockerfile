FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libpq-dev \
    libssl-dev \
    cron supervisor

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

RUN mkdir -p /var/www /var/log/supervisor

WORKDIR /var/www

COPY . /var/www
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
