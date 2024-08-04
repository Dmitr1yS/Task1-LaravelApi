FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY . .

# Установка зависимостей Composer
RUN composer install

CMD ["php-fpm"]