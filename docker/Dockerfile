# Используем образ с PHP и Nginx
FROM php:8.2-fpm AS php

# Устанавливаем необходимые расширения PHP
RUN apt-get update && apt-get install -y \
    nginx git zip unzip \
    && rm -rf /var/lib/apt/lists/*

# Копируем конфигурацию Nginx
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

COPY ./ ./

# Создаем директорию для логов
RUN mkdir logs && chmod 775 ./logs/

# Устанавливаем зависимости Composer
RUN composer install --no-dev --optimize-autoloader

# Перезапускаем сервис Nginx
CMD service nginx restart && php-fpm
