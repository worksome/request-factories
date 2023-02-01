FROM php:8.2-cli

RUN pecl install xdebug

RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libzip-dev zip

RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install gd

RUN docker-php-ext-install zip

RUN docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["php"]
