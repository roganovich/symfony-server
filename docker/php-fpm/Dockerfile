FROM php:8.1-fpm
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && pecl install apcu amqp\
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && docker-php-ext-enable apcu amqp

RUN apt-get install --update linux-headers
# Postgres
RUN apt-get install --no-cache libpq-dev && docker-php-ext-install pdo_pgsql

WORKDIR /var/www
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

