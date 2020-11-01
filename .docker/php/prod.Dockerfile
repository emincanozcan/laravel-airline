#composer

FROM composer:2 as composer-build

WORKDIR /var/www/html

COPY composer.json composer.lock /var/www/html/

RUN mkdir -p /var/www/html/database/{factories,seed} \
  && composer install --no-dev --prefer-dist --no-scripts --no-autoloader --no-progress --ignore-platform-reqs

# npm

FROM node:12 AS npm-builder

WORKDIR /var/www/html

COPY package.json package-lock.json webpack.mix.js /var/www/html/
COPY resources /var/www/html/resources/
COPY public /var/www/html/

RUN npm ci
RUN npm run production


FROM php:7.4-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install --quiet --yes --no-install-recommends libzip-dev unzip \
    && docker-php-ext-install opcache zip pdo pdo_mysql \
    && pecl install -o -f redis-5.1.1 \
    && docker-php-ext-enable redis

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .docker/php/opcache.ini $PHP_INI_DIR/conf.d/


COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --chown=www-data --from=composer-build /var/www/html/vendor/ /var/www/html/vendor/
COPY --chown=www-data --from=npm-builder /var/www/html/public/ /var/www/html/public/
COPY --chown=www-data . /var/www/html/

RUN composer dump -o \
  && composer check-platform-reqs \
  && rm -rf /usr/bin/composer