################################################################
## First stage. Copy project files and run composer
################################################################
FROM composer:2 as composer_stage

RUN rm -rf /var/www && mkdir -p /var/www/html

WORKDIR /var/www/html

COPY . ./
# COPY composer.json composer.lock ./

# Install Zip
RUN apk add --no-cache zip libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql
RUN rm -rf /tmp/*

# RUN composer install
RUN composer install --ignore-platform-reqs --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --no-dev --no-autoloader
RUN composer dump-autoload --optimize --apcu --no-dev

# RUN composer run-script $NODEV post-install-cmd; \
#     chmod +x bin/console;

################################################################
## Second stage. Build NPM dependencies
################################################################
FROM node:12 as npm_builder

COPY --from=composer_stage /var/www/html /var/www/html
# COPY package.json gulpfile.js application/ /var/www/html/
# COPY gulpfile.js /var/www/html

WORKDIR /var/www/html

# COPY ./assets ./assets

RUN npm install
# RUN npm run build

################################################################
## Third stage, mostly copied from your original Dockerfile
################################################################
FROM php:7.4-apache

COPY --from=npm_builder /var/www/html /var/www/html

WORKDIR "/var/www/html"

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
        zip \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd gettext mysqli pdo_mysql zip
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN rm -rf /tmp/*

RUN a2enmod rewrite
