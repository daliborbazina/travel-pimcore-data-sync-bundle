ARG PHP_FPM_IMAGE
FROM ${PHP_FPM_IMAGE} as php-fpm

FROM nginx:1.27-alpine
COPY --from=php-fpm /var/www/html/public /var/www/html/public
COPY dev/infra/nginx/default.standalone.conf /etc/nginx/conf.d/default.conf
