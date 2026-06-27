FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    default-mysql-client \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite headers expires

COPY ./docker/apache/ports.conf /etc/apache2/ports.conf
COPY ./docker/apache/vhosts/*.conf /etc/apache2/sites-available/

RUN a2dissite 000-default.conf \
    && a2ensite pos.conf \
    && a2ensite rms.conf \
    && a2ensite landing.conf

RUN mkdir -p /var/www/html/POS /var/www/html/RMS /var/www/html/landing \
    && chown -R 1000:1000 /var/www/html \
    && chown -R 1000:1000 /var/log/apache2 /var/run/apache2 /var/lock/apache2

WORKDIR /var/www/html