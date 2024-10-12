FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache libzip-dev libjpeg-turbo-dev freetype-dev && docker-php-ext-install zip pdo_mysql 
RUN docker-php-ext-install gd


# Instalar o composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

# Instalar as depend�ncias do Composer
RUN composer install --no-dev --optimize-autoloader

COPY . .

# Expor a porta 80
EXPOSE 80

ENV USERNAME=www-data
ENV HOME=/var/www/html
RUN usermod -u 1000 $USERNAME

USER $USERNAME

CMD [ "php", "-S", "0.0.0.0:9000", "-t", "public" ]