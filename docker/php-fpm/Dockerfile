FROM php:8.1-fpm-alpine

# Install packages
RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash
RUN apk add --update linux-headers
RUN apk add icu-dev
RUN docker-php-ext-configure intl && docker-php-ext-install intl
# Postgres
RUN apk add --no-cache libpq-dev && docker-php-ext-install pdo_pgsql
RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions @fix_letsencrypt

# Configure non-root user.
ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Nginx
RUN apk add --update --no-cache nginx
COPY ./docker/php-fpm/nginx.conf /etc/nginx/
RUN chown -Rf www-data:www-data /var/lib/nginx

# Source code
RUN chown www-data:www-data /var/www
COPY --chown=www-data:www-data ./ /var/www
WORKDIR /var/www

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ARG BUILD_MODE=dev
RUN if [ ${BUILD_MODE} = dev ]; \
    then \
      composer install --no-interaction; \
    else \
      composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader; \
    fi;
RUN chown -R www-data:www-data /var/www/vendor/

EXPOSE 8080

CMD ["/bin/sh", "./docker/php-fpm/run.sh"]


