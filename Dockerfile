FROM composer:latest AS builder
WORKDIR /usr/src/mercator-api
COPY . .
RUN composer i --ignore-platform-reqs --no-dev --no-scripts -o -n

FROM php:7.3-apache
RUN apt-get update \
    && apt-get install libgmp3-dev zlib1g-dev libicu-dev g++ -y \
    && a2enmod rewrite \
    && docker-php-ext-install -j$(nproc) gmp \
    && docker-php-ext-install -j$(nproc) bcmath \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql
COPY --from=builder /usr/src/mercator-api /var/www
COPY --from=builder /usr/src/mercator-api/public /var/www/html
RUN  chown -R www-data:www-data /var/www
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
