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

COPY .docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/99-timezone.ini /usr/local/etc/php/conf.d/99-timezone.ini
COPY .docker/zz-error-logging.ini /usr/local/etc/php/conf.d/zz-error-logging.ini

RUN sed -i 's|DocumentRoot /var/www/html|#DocumentRoot /var/www/html|g' /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
