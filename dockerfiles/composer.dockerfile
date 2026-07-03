FROM composer:latest

WORKDIR /var/www/laravel

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
#запускаем комозер с флагом игнора несоответствия версий