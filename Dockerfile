FROM php:7-apache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ./ /var/www
RUN apt-get update \
    && apt-get install -y git unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/www/html \
    && ln -sf /var/www/www /var/www/html \
    && ln -sf /var/www/config.docker.php /var/www/config.php \
    && printf "display_errors=Off\nlog_errors=On\nerror_log=/dev/stderr\n" > /usr/local/etc/php/conf.d/hide-errors-from-frontend.ini \
    && printf "short_open_tag=Off\n" > /usr/local/etc/php/conf.d/no-short-php-tags.ini \
    && cd /var/www \
    && composer install
