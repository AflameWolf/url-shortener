FROM php:8.4-fpm-alpine
#образ

WORKDIR /var/www/laravel

RUN docker-php-ext-install pdo pdo_mysql #установка pdo для работы с бд