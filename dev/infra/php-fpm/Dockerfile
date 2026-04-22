ARG PIMCORE_IMAGE=pimcore/pimcore:php8.3-min-latest

############################################
### STAGE 1: BUILD SANDBOX APP + LOCAL BUNDLE
############################################
FROM composer:2.8.2 AS builder

WORKDIR /build

RUN apk add --no-cache \
    git \
    unzip \
    bash \
    $PHPIZE_DEPS \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    zlib-dev \
    curl-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    dom \
    simplexml \
    gd \
    exif \
    mbstring \
    zip \
    intl \
    curl

RUN COMPOSER_NO_SECURITY_BLOCKING=1 composer create-project pimcore/skeleton app --no-install --no-interaction --no-scripts --prefer-dist "^11.0"

WORKDIR /build/app

RUN composer config platform.php 8.3.30
RUN composer require pimcore/pimcore:v11.5.14.1 --no-update --no-interaction

COPY . /build/repo
RUN mkdir -p /build/app/config/packages/local
COPY dev/app-overlay/config/packages/doctrine.yaml /build/app/config/packages/doctrine.yaml

RUN composer config repositories.local path /build/repo
RUN composer require daliborbazina/travel-pimcore-data-sync-bundle:@dev --no-update --no-interaction --no-scripts
RUN COMPOSER_NO_SECURITY_BLOCKING=1 composer update --with-all-dependencies --no-interaction --no-scripts

############################################
### STAGE 2: RUNTIME
############################################
FROM ${PIMCORE_IMAGE} AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends locales gosu \
    && sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=en_US.UTF-8 LC_ALL=en_US.UTF-8 \
    && rm -rf /var/lib/apt/lists/*

ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

WORKDIR /var/www/html

RUN mkdir -p /build/repo \
    && chown -R www-data:www-data /build/repo \
    && chmod -R 775 /build/repo

COPY --from=builder --chown=www-data:www-data /build/app /var/www/html
COPY --from=builder --chown=www-data:www-data /build/repo /build/repo

RUN mkdir -p /var/www/html/var /var/www/html/public/var \
    && chown -R www-data:www-data /var/www/html/var /var/www/html/public/var \
    && chmod -R 775 /var/www/html/var /var/www/html/public/var

ENV DATABASE_URL="mysql://pimcore:pimcore@pim-db:3306/pimcore?charset=utf8mb4"

USER www-data
# Fali komentar
RUN bin/console assets:install public

USER root

COPY dev/infra/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-pimcore.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-pimcore.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint-pimcore.sh"]
CMD ["php-fpm"]
