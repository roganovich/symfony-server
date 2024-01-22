FROM php:8.1-fpm-alpine

# Install modules
RUN apk upgrade --update && apk add \
        zip \
        unzip \
        curl-dev \
        # xsl
        libxslt-dev \
        libgcrypt-dev \
        # postgresql
        postgresql-dev \
        # rabbitmq
        rabbitmq-c \
        rabbitmq-c-dev \
        # xsl
        libxslt-dev

 # Enable the Extension
RUN docker-php-ext-install intl opcache pdo pdo_pgsql xml xsl

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN pecl install amqp \
    && docker-php-ext-enable amqp

# Redis is not an extension  therefore you cannot use docker-php-ext-install
RUN pecl install --force redis \
&& rm -rf /tmp/pear \
&& docker-php-ext-enable redis

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

