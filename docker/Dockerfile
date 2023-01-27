FROM php:8.2-cli

RUN pecl install pcov

RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev

RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-enable pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["php"]
