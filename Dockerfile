FROM php:7.4-apache

# Включить mod_rewrite
RUN a2enmod rewrite

# Установить build-зависимости и расширения
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
# Копируем конфиг Apache
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Закомментировать глобальный DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|#DocumentRoot /var/www/html|g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
