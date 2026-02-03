FROM php:7.4-apache

# Включить mod_rewrite для Laravel
RUN a2enmod rewrite

# Установить зависимости и PHP-расширения
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Копируем ваш конфиг Apache
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# КРИТИЧНО: Закомментировать глобальный DocumentRoot в apache2.conf
RUN sed -i 's|DocumentRoot /var/www/html|#DocumentRoot /var/www/html|g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
