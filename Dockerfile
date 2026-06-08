FROM php:7.4-apache

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

RUN sed -i 's|DocumentRoot /var/www/html|#DocumentRoot /var/www/html|g' /etc/apache2/apache2.conf

WORKDIR /var/www/html